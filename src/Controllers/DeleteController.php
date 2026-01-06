<?php

namespace App\Controllers;

use App\Services\Auth;

/**
 * Contrôleur pour la suppression d'éléments avec vérification d'ownership
 */
class DeleteController extends Controller
{
    /**
     * Supprime un élément en vérifiant qu'il appartient à l'organisation
     */
    public function delete(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['type', 'id'])) {
            $this->error('Missing required fields');
            return;
        }

        $allowedTypes = ['projects', 'tasks', 'groups', 'milestones', 'members'];
        $type = preg_replace('/[^a-z]/', '', $data['type']);

        if (!in_array($type, $allowedTypes)) {
            $this->error('Invalid type');
            return;
        }

        $id = (int)$data['id'];
        $orgId = Auth::getOrganizationId();

        try {
            // Vérifier que l'élément appartient à l'organisation
            if (!$this->verifyOwnership($type, $id, $orgId)) {
                $this->error('Item not found or access denied', 404);
                return;
            }

            // Suppression spéciale pour les membres (vérifications supplémentaires)
            if ($type === 'members') {
                if (!Auth::isOrgAdmin()) {
                    $this->error('Admin access required', 403);
                    return;
                }
                // Ne pas permettre de se supprimer soi-même
                if ($id === Auth::getMemberId()) {
                    $this->error('Cannot delete yourself', 403);
                    return;
                }
                // Ne pas supprimer un super admin
                $stmt = $this->db->prepare("SELECT role FROM members WHERE id = ?");
                $stmt->execute([$id]);
                $member = $stmt->fetch();
                if ($member && $member['role'] === 'super_admin') {
                    $this->error('Cannot delete super admin', 403);
                    return;
                }
            }

            // Pour les projets, supprimer d'abord les éléments liés
            if ($type === 'projects') {
                $this->db->prepare("DELETE FROM tasks WHERE project_id = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM groups WHERE project_id = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM milestones WHERE project_id = ?")->execute([$id]);
            }

            // Supprimer l'élément principal
            $stmt = $this->db->prepare("DELETE FROM $type WHERE id = ?");
            $stmt->execute([$id]);

            $this->success(null, 'Item deleted successfully');
        } catch (\Exception $e) {
            $this->error('Failed to delete item: ' . $e->getMessage());
        }
    }

    /**
     * Vérifie que l'élément appartient à l'organisation de l'utilisateur
     */
    private function verifyOwnership(string $type, int $id, int $orgId): bool
    {
        switch ($type) {
            case 'projects':
                $stmt = $this->db->prepare("SELECT id FROM projects WHERE id = ? AND organization_id = ?");
                $stmt->execute([$id, $orgId]);
                return (bool)$stmt->fetch();

            case 'members':
                $stmt = $this->db->prepare("SELECT id FROM members WHERE id = ? AND organization_id = ?");
                $stmt->execute([$id, $orgId]);
                return (bool)$stmt->fetch();

            case 'tasks':
                // Vérifier via le projet parent
                $stmt = $this->db->prepare("
                    SELECT t.id FROM tasks t
                    JOIN projects p ON t.project_id = p.id
                    WHERE t.id = ? AND p.organization_id = ?
                ");
                $stmt->execute([$id, $orgId]);
                return (bool)$stmt->fetch();

            case 'groups':
                // Vérifier via le projet parent
                $stmt = $this->db->prepare("
                    SELECT g.id FROM groups g
                    JOIN projects p ON g.project_id = p.id
                    WHERE g.id = ? AND p.organization_id = ?
                ");
                $stmt->execute([$id, $orgId]);
                return (bool)$stmt->fetch();

            case 'milestones':
                // Vérifier via le projet parent
                $stmt = $this->db->prepare("
                    SELECT m.id FROM milestones m
                    JOIN projects p ON m.project_id = p.id
                    WHERE m.id = ? AND p.organization_id = ?
                ");
                $stmt->execute([$id, $orgId]);
                return (bool)$stmt->fetch();

            default:
                return false;
        }
    }
}
