<?php

namespace App\Controllers;

use App\Models\Member;
use App\Services\Auth;

/**
 * Contrôleur pour l'authentification et la gestion des organisations
 */
class AuthController extends Controller
{
    /**
     * Gère la connexion par email/mot de passe
     */
    public function login(array $data): void
    {
        if (!$this->validate($data, ['email', 'password'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $member = Auth::attempt($this->db, $data['email'], $data['password']);

        if ($member) {
            $organization = [
                'id' => $member['org_id'],
                'name' => $member['org_name']
            ];
            Auth::login($member, $organization);
            $this->success([
                'member' => [
                    'id' => $member['id'],
                    'email' => $member['email'],
                    'name' => $member['fname'] . ' ' . $member['lname'],
                    'role' => $member['role']
                ],
                'organization' => $organization
            ], 'Login successful');
        } else {
            $this->error('Invalid credentials or account disabled', 401);
        }
    }

    /**
     * Inscription d'une nouvelle organisation avec son admin
     */
    public function register(array $data): void
    {
        if (!$this->validate($data, ['org_name', 'email', 'password', 'fname', 'lname'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);

        // Vérifier que l'email n'existe pas déjà
        $stmt = $this->db->prepare("SELECT id FROM members WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            $this->error('Email already exists', 409);
            return;
        }

        // Vérifier que le nom d'organisation n'existe pas déjà
        $stmt = $this->db->prepare("SELECT id FROM organizations WHERE name = ?");
        $stmt->execute([$data['org_name']]);
        if ($stmt->fetch()) {
            $this->error('Organization name already exists', 409);
            return;
        }

        $this->db->beginTransaction();
        try {
            // Créer l'organisation
            $slug = $this->generateSlug($data['org_name']);
            $stmt = $this->db->prepare("INSERT INTO organizations (name, slug) VALUES (?, ?)");
            $stmt->execute([$data['org_name'], $slug]);
            $orgId = $this->db->lastInsertId();

            // Créer l'admin de l'organisation
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO members (organization_id, email, password, fname, lname, role)
                VALUES (?, ?, ?, ?, ?, 'org_admin')
            ");
            $stmt->execute([$orgId, $data['email'], $hashedPassword, $data['fname'], $data['lname']]);
            $memberId = $this->db->lastInsertId();

            $this->db->commit();

            // Connecter automatiquement l'utilisateur
            $member = [
                'id' => $memberId,
                'email' => $data['email'],
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'role' => 'org_admin'
            ];
            $organization = [
                'id' => $orgId,
                'name' => $data['org_name']
            ];
            Auth::login($member, $organization);

            $this->success([
                'member' => $member,
                'organization' => $organization
            ], 'Registration successful');

        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->error('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Gère la déconnexion
     */
    public function logout(): void
    {
        Auth::logout();
        $this->success(null, 'Logged out successfully');
    }

    /**
     * Met à jour les paramètres de l'organisation
     */
    public function updateSettings(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['org_name', 'current_password'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $memberId = Auth::getMemberId();
        $orgId = Auth::getOrganizationId();

        // Vérifier le mot de passe actuel du membre
        $stmt = $this->db->prepare("SELECT password FROM members WHERE id = ?");
        $stmt->execute([$memberId]);
        $currentHash = $stmt->fetchColumn();

        if (!password_verify($data['current_password'], $currentHash)) {
            $this->error('Current password is incorrect', 403);
            return;
        }

        // Mettre à jour le nom de l'organisation
        $stmt = $this->db->prepare("UPDATE organizations SET name = ? WHERE id = ?");
        $stmt->execute([$data['org_name'], $orgId]);

        // Si un nouveau mot de passe est fourni pour le membre
        if (!empty($data['new_password'])) {
            $newHash = password_hash($data['new_password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE members SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $memberId]);
        }

        $this->success(null, 'Settings updated successfully');
    }

    /**
     * Met à jour le profil du membre connecté
     */
    public function updateProfile(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $memberId = Auth::getMemberId();
        $updateFields = [];
        $params = [];
        $newFname = null;
        $newLname = null;
        $newEmail = null;

        if (!empty($data['fname'])) {
            $newFname = $this->sanitize(['fname' => $data['fname']])['fname'];
            $updateFields[] = 'fname = ?';
            $params[] = $newFname;
        }
        if (!empty($data['lname'])) {
            $newLname = $this->sanitize(['lname' => $data['lname']])['lname'];
            $updateFields[] = 'lname = ?';
            $params[] = $newLname;
        }
        if (!empty($data['email'])) {
            $newEmail = trim(strtolower($data['email']));
            $currentEmail = Auth::getMemberEmail();

            // Vérifier si l'email a changé
            if ($newEmail !== $currentEmail) {
                // Vérifier que le nouvel email n'est pas déjà utilisé
                $stmt = $this->db->prepare("SELECT id FROM members WHERE email = ? AND id != ?");
                $stmt->execute([$newEmail, $memberId]);
                if ($stmt->fetch()) {
                    $this->error('Cet email est déjà utilisé par un autre membre', 400);
                    return;
                }
                $updateFields[] = 'email = ?';
                $params[] = $newEmail;
            }
        }
        if (!empty($data['new_password']) && !empty($data['current_password'])) {
            // Vérifier l'ancien mot de passe
            $stmt = $this->db->prepare("SELECT password FROM members WHERE id = ?");
            $stmt->execute([$memberId]);
            if (!password_verify($data['current_password'], $stmt->fetchColumn())) {
                $this->error('Mot de passe actuel incorrect', 403);
                return;
            }
            $updateFields[] = 'password = ?';
            $params[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }

        if (empty($updateFields)) {
            $this->error('Aucun champ à mettre à jour');
            return;
        }

        $params[] = $memberId;
        $sql = "UPDATE members SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        // Mettre à jour la session si nécessaire
        if ($newEmail) {
            Auth::setMemberEmail($newEmail);
        }
        if ($newFname || $newLname) {
            // Récupérer le nom complet mis à jour
            $stmt = $this->db->prepare("SELECT fname, lname FROM members WHERE id = ?");
            $stmt->execute([$memberId]);
            $member = $stmt->fetch();
            if ($member) {
                Auth::setMemberName($member['fname'] . ' ' . $member['lname']);
            }
        }

        $this->success(null, 'Profil mis à jour avec succès');
    }

    /**
     * Liste toutes les organisations (super admin uniquement)
     */
    public function listOrganizations(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isSuperAdmin()) {
            $this->error('Super admin access required', 403);
            return;
        }

        $stmt = $this->db->query("
            SELECT o.*,
                   (SELECT COUNT(*) FROM members WHERE organization_id = o.id) as member_count,
                   (SELECT COUNT(*) FROM projects WHERE organization_id = o.id) as project_count
            FROM organizations o
            ORDER BY o.name
        ");
        $organizations = $stmt->fetchAll();

        $this->success(['organizations' => $organizations]);
    }

    /**
     * Active/désactive une organisation (super admin uniquement)
     */
    public function toggleOrganization(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isSuperAdmin()) {
            $this->error('Super admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['org_id', 'is_active'])) {
            $this->error('Missing required fields');
            return;
        }

        $orgId = (int)$data['org_id'];
        $isActive = (int)$data['is_active'];

        $stmt = $this->db->prepare("UPDATE organizations SET is_active = ? WHERE id = ?");
        $stmt->execute([$isActive, $orgId]);

        $this->success(null, $isActive ? 'Organization activated' : 'Organization deactivated');
    }

    /**
     * Supprime une organisation (super admin uniquement)
     */
    public function deleteOrganization(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isSuperAdmin()) {
            $this->error('Super admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['org_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $orgId = (int)$data['org_id'];

        // Empêcher de supprimer l'organisation courante
        if ($orgId === Auth::getOrganizationId()) {
            $this->error('Cannot delete your current organization', 403);
            return;
        }

        // Vérifier que l'organisation existe
        $stmt = $this->db->prepare("SELECT name FROM organizations WHERE id = ?");
        $stmt->execute([$orgId]);
        $org = $stmt->fetch();

        if (!$org) {
            $this->error('Organization not found', 404);
            return;
        }

        // Supprimer l'organisation (les cascades supprimeront les données liées)
        $stmt = $this->db->prepare("DELETE FROM organizations WHERE id = ?");
        $stmt->execute([$orgId]);

        $this->success(null, 'Organization deleted successfully');
    }

    /**
     * Switch vers une autre organisation (super admin uniquement)
     */
    public function switchOrganization(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isSuperAdmin()) {
            $this->error('Super admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['org_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $orgId = (int)$data['org_id'];
        $org = Auth::getOrganizationInfo($this->db, $orgId);

        if (!$org) {
            $this->error('Organization not found', 404);
            return;
        }

        Auth::switchOrganization($org);

        $this->success(['organization' => $org], 'Switched to ' . $org['name']);
    }

    /**
     * Liste les membres de l'organisation courante (admin uniquement)
     */
    public function listMembers(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        $orgId = Auth::getOrganizationId();
        $stmt = $this->db->prepare("
            SELECT id, email, fname, lname, role, is_active, created_at
            FROM members
            WHERE organization_id = ?
            ORDER BY lname, fname
        ");
        $stmt->execute([$orgId]);
        $members = $stmt->fetchAll();

        $this->success(['members' => $members]);
    }

    /**
     * Ajoute un nouveau membre à l'organisation (admin uniquement)
     */
    public function addMember(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['email', 'fname', 'lname'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $orgId = Auth::getOrganizationId();

        // Vérifier que l'email n'existe pas déjà
        $stmt = $this->db->prepare("SELECT id FROM members WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            $this->error('Email already exists', 409);
            return;
        }

        // Mot de passe par défaut ou généré
        $password = $data['password'] ?? bin2hex(random_bytes(8));
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'member';

        // Seul un super admin peut créer un autre super admin
        if ($role === 'super_admin' && !Auth::isSuperAdmin()) {
            $role = 'org_admin';
        }

        $stmt = $this->db->prepare("
            INSERT INTO members (organization_id, email, password, fname, lname, role)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$orgId, $data['email'], $hashedPassword, $data['fname'], $data['lname'], $role]);

        $this->success([
            'member_id' => $this->db->lastInsertId(),
            'temporary_password' => $password
        ], 'Member added successfully');
    }

    /**
     * Met à jour un membre (admin uniquement)
     */
    public function updateMember(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['member_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $memberId = (int)$data['member_id'];
        $orgId = Auth::getOrganizationId();

        // Vérifier que le membre appartient à l'organisation
        $stmt = $this->db->prepare("SELECT * FROM members WHERE id = ? AND organization_id = ?");
        $stmt->execute([$memberId, $orgId]);
        $member = $stmt->fetch();

        if (!$member) {
            $this->error('Member not found', 404);
            return;
        }

        // Empêcher de modifier un super admin si on n'est pas super admin
        if ($member['role'] === 'super_admin' && !Auth::isSuperAdmin()) {
            $this->error('Cannot modify super admin', 403);
            return;
        }

        $updateFields = [];
        $params = [];

        if (isset($data['fname'])) {
            $updateFields[] = 'fname = ?';
            $params[] = $this->sanitize(['fname' => $data['fname']])['fname'];
        }
        if (isset($data['lname'])) {
            $updateFields[] = 'lname = ?';
            $params[] = $this->sanitize(['lname' => $data['lname']])['lname'];
        }
        if (isset($data['role'])) {
            $role = $data['role'];
            // Seul super admin peut promouvoir en super admin
            if ($role === 'super_admin' && !Auth::isSuperAdmin()) {
                $role = 'org_admin';
            }
            $updateFields[] = 'role = ?';
            $params[] = $role;
        }
        if (isset($data['is_active'])) {
            // Empêcher de se désactiver soi-même
            if ($memberId === Auth::getMemberId() && !$data['is_active']) {
                $this->error('Cannot deactivate yourself', 403);
                return;
            }
            $updateFields[] = 'is_active = ?';
            $params[] = (int)$data['is_active'];
        }
        if (!empty($data['new_password'])) {
            $updateFields[] = 'password = ?';
            $params[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }

        if (empty($updateFields)) {
            $this->error('No fields to update');
            return;
        }

        $params[] = $memberId;
        $sql = "UPDATE members SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $this->success(null, 'Member updated successfully');
    }

    /**
     * Supprime un membre (admin uniquement)
     */
    public function deleteMember(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['member_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $memberId = (int)$data['member_id'];
        $orgId = Auth::getOrganizationId();

        // Empêcher de se supprimer soi-même
        if ($memberId === Auth::getMemberId()) {
            $this->error('Cannot delete yourself', 403);
            return;
        }

        // Vérifier que le membre appartient à l'organisation
        $stmt = $this->db->prepare("SELECT role FROM members WHERE id = ? AND organization_id = ?");
        $stmt->execute([$memberId, $orgId]);
        $member = $stmt->fetch();

        if (!$member) {
            $this->error('Member not found', 404);
            return;
        }

        // Empêcher de supprimer un super admin
        if ($member['role'] === 'super_admin') {
            $this->error('Cannot delete super admin', 403);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$memberId]);

        $this->success(null, 'Member deleted successfully');
    }

    /**
     * Met à jour la configuration de l'API IA
     */
    public function updateAIConfig(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        $orgId = Auth::getOrganizationId();

        $useApi = isset($data['ai_use_api']) ? (int)$data['ai_use_api'] : 0;
        $provider = $data['ai_api_provider'] ?? 'rules';
        $apiKey = $data['ai_api_key'] ?? '';
        $model = $data['ai_api_model'] ?? '';

        $validProviders = ['rules', 'claude', 'openai', 'azure'];
        if (!in_array($provider, $validProviders)) {
            $this->error('Invalid AI provider');
            return;
        }

        if ($useApi && empty($apiKey) && $provider !== 'rules') {
            $this->error('API key is required when using external API');
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE organizations
            SET ai_use_api = ?, ai_api_provider = ?, ai_api_key = ?, ai_api_model = ?
            WHERE id = ?
        ");
        $stmt->execute([$useApi, $provider, $apiKey, $model, $orgId]);

        $this->success([
            'ai_use_api' => $useApi,
            'ai_api_provider' => $provider,
            'ai_api_model' => $model
        ], 'AI configuration updated successfully');
    }

    // ============ Gestion des équipes internes ============

    /**
     * Liste les équipes internes de l'organisation
     */
    public function listInternalTeams(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $orgId = Auth::getOrganizationId();
        $stmt = $this->db->prepare("
            SELECT t.*,
                   (SELECT COUNT(*) FROM team_members WHERE team_id = t.id) as member_count
            FROM internal_teams t
            WHERE t.organization_id = ?
            ORDER BY t.name
        ");
        $stmt->execute([$orgId]);
        $teams = $stmt->fetchAll();

        // Ajouter les membres de chaque équipe
        foreach ($teams as &$team) {
            $stmt = $this->db->prepare("
                SELECT m.id, m.fname, m.lname, m.email
                FROM members m
                JOIN team_members tm ON m.id = tm.member_id
                WHERE tm.team_id = ?
            ");
            $stmt->execute([$team['id']]);
            $team['members'] = $stmt->fetchAll();
        }

        $this->success(['teams' => $teams]);
    }

    /**
     * Crée une équipe interne
     */
    public function createInternalTeam(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['name'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $orgId = Auth::getOrganizationId();

        $stmt = $this->db->prepare("
            INSERT INTO internal_teams (organization_id, name, description, color)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $orgId,
            $data['name'],
            $data['description'] ?? '',
            $data['color'] ?? '#3B82F6'
        ]);

        $this->success(['team_id' => $this->db->lastInsertId()], 'Team created successfully');
    }

    /**
     * Met à jour une équipe interne
     */
    public function updateInternalTeam(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['team_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $teamId = (int)$data['team_id'];
        $orgId = Auth::getOrganizationId();

        // Vérifier que l'équipe appartient à l'organisation
        $stmt = $this->db->prepare("SELECT id FROM internal_teams WHERE id = ? AND organization_id = ?");
        $stmt->execute([$teamId, $orgId]);
        if (!$stmt->fetch()) {
            $this->error('Team not found', 404);
            return;
        }

        $updateFields = [];
        $params = [];

        if (isset($data['name'])) {
            $updateFields[] = 'name = ?';
            $params[] = $this->sanitize(['name' => $data['name']])['name'];
        }
        if (isset($data['description'])) {
            $updateFields[] = 'description = ?';
            $params[] = $data['description'];
        }
        if (isset($data['color'])) {
            $updateFields[] = 'color = ?';
            $params[] = $data['color'];
        }

        if (empty($updateFields)) {
            $this->error('No fields to update');
            return;
        }

        $params[] = $teamId;
        $sql = "UPDATE internal_teams SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $this->success(null, 'Team updated successfully');
    }

    /**
     * Supprime une équipe interne
     */
    public function deleteInternalTeam(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['team_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $teamId = (int)$data['team_id'];
        $orgId = Auth::getOrganizationId();

        $stmt = $this->db->prepare("DELETE FROM internal_teams WHERE id = ? AND organization_id = ?");
        $stmt->execute([$teamId, $orgId]);

        $this->success(null, 'Team deleted successfully');
    }

    /**
     * Ajoute un membre à une équipe interne
     */
    public function addTeamMember(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['team_id', 'member_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $teamId = (int)$data['team_id'];
        $memberId = (int)$data['member_id'];
        $orgId = Auth::getOrganizationId();

        // Vérifier que l'équipe appartient à l'organisation
        $stmt = $this->db->prepare("SELECT id FROM internal_teams WHERE id = ? AND organization_id = ?");
        $stmt->execute([$teamId, $orgId]);
        if (!$stmt->fetch()) {
            $this->error('Team not found', 404);
            return;
        }

        // Vérifier que le membre appartient à l'organisation
        $stmt = $this->db->prepare("SELECT id FROM members WHERE id = ? AND organization_id = ?");
        $stmt->execute([$memberId, $orgId]);
        if (!$stmt->fetch()) {
            $this->error('Member not found', 404);
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO team_members (team_id, member_id) VALUES (?, ?)");
            $stmt->execute([$teamId, $memberId]);
            $this->success(null, 'Member added to team');
        } catch (\Exception $e) {
            $this->error('Member already in team', 409);
        }
    }

    /**
     * Retire un membre d'une équipe interne
     */
    public function removeTeamMember(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isOrgAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['team_id', 'member_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $teamId = (int)$data['team_id'];
        $memberId = (int)$data['member_id'];

        $stmt = $this->db->prepare("DELETE FROM team_members WHERE team_id = ? AND member_id = ?");
        $stmt->execute([$teamId, $memberId]);

        $this->success(null, 'Member removed from team');
    }

    // ============ Méthodes de compatibilité ============

    /**
     * @deprecated Utiliser listOrganizations()
     */
    public function listTeams(): void
    {
        $this->listOrganizations();
    }

    /**
     * @deprecated Utiliser toggleOrganization()
     */
    public function updateTeamRole(array $data): void
    {
        if (isset($data['team_id'])) {
            $data['org_id'] = $data['team_id'];
        }
        if (isset($data['is_admin'])) {
            $data['is_active'] = $data['is_admin'];
        }
        $this->toggleOrganization($data);
    }

    /**
     * @deprecated Utiliser updateMember()
     */
    public function updateMemberRole(array $data): void
    {
        if (isset($data['is_admin'])) {
            $data['role'] = $data['is_admin'] ? 'org_admin' : 'member';
        }
        $this->updateMember($data);
    }

    /**
     * Génère un slug à partir d'un nom
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Vérifier l'unicité et ajouter un suffixe si nécessaire
        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $stmt = $this->db->prepare("SELECT id FROM organizations WHERE slug = ?");
            $stmt->execute([$slug]);
            if (!$stmt->fetch()) {
                break;
            }
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug ?: 'org-' . time();
    }
}
