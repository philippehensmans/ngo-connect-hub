<?php

namespace App\Controllers;

use App\Models\Member;
use App\Services\Auth;

/**
 * Contrôleur pour les membres (compatible avec l'ancien système)
 * Les nouvelles opérations passent par AuthController
 */
class MemberController extends Controller
{
    /**
     * Sauvegarde un membre (création ou mise à jour)
     * Note: Pour les nouvelles fonctionnalités, utiliser AuthController::addMember/updateMember
     */
    public function save(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['fname', 'lname', 'email'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $orgId = Auth::getOrganizationId();

        try {
            if (empty($data['id'])) {
                // Création - nécessite les droits admin
                if (!Auth::isOrgAdmin()) {
                    $this->error('Admin access required to add members', 403);
                    return;
                }

                // Vérifier que l'email n'existe pas déjà
                $stmt = $this->db->prepare("SELECT id FROM members WHERE email = ?");
                $stmt->execute([$data['email']]);
                if ($stmt->fetch()) {
                    $this->error('Email already exists', 409);
                    return;
                }

                // Générer un mot de passe temporaire
                $tempPassword = bin2hex(random_bytes(8));
                $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

                $stmt = $this->db->prepare("
                    INSERT INTO members (organization_id, email, password, fname, lname, role)
                    VALUES (?, ?, ?, ?, ?, 'member')
                ");
                $stmt->execute([$orgId, $data['email'], $hashedPassword, $data['fname'], $data['lname']]);
                $id = $this->db->lastInsertId();

                $this->success([
                    'id' => $id,
                    'temporary_password' => $tempPassword
                ], 'Member added successfully');
            } else {
                // Mise à jour - vérifier que le membre appartient à l'organisation
                $memberId = (int)$data['id'];
                $stmt = $this->db->prepare("SELECT * FROM members WHERE id = ? AND organization_id = ?");
                $stmt->execute([$memberId, $orgId]);
                $member = $stmt->fetch();

                if (!$member) {
                    $this->error('Member not found or access denied', 404);
                    return;
                }

                // Un membre peut modifier son propre profil, ou un admin peut modifier n'importe qui
                if ($memberId !== Auth::getMemberId() && !Auth::isOrgAdmin()) {
                    $this->error('Permission denied', 403);
                    return;
                }

                // Ne pas modifier un super admin si on n'est pas super admin
                if ($member['role'] === 'super_admin' && !Auth::isSuperAdmin()) {
                    $this->error('Cannot modify super admin', 403);
                    return;
                }

                $stmt = $this->db->prepare("UPDATE members SET fname = ?, lname = ? WHERE id = ?");
                $stmt->execute([$data['fname'], $data['lname'], $memberId]);

                $this->success(null, 'Member updated successfully');
            }
        } catch (\Exception $e) {
            $this->error('Failed to save member: ' . $e->getMessage());
        }
    }
}
