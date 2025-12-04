<?php

namespace App\Controllers;

use App\Models\Team;
use App\Services\Auth;

/**
 * Contrôleur pour l'authentification
 */
class AuthController extends Controller
{
    /**
     * Gère la connexion
     */
    public function login(array $data): void
    {
        if (!$this->validate($data, ['name', 'password'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $team = Auth::attempt($this->db, $data['name'], $data['password']);

        if ($team) {
            Auth::login($team['id'], $team['name'], (bool)($team['is_admin'] ?? 0));
            $this->success(null, 'Login successful');
        } else {
            $this->error('Invalid credentials', 401);
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
     * Met à jour les paramètres de l'équipe
     */
    public function updateSettings(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['org_name', 'current_password'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $teamId = Auth::getTeamId();

        // Vérifier le mot de passe actuel
        $stmt = $this->db->prepare("SELECT password FROM teams WHERE id = ?");
        $stmt->execute([$teamId]);
        $currentHash = $stmt->fetchColumn();

        if (!password_verify($data['current_password'], $currentHash)) {
            $this->error('Current password is incorrect', 403);
            return;
        }

        // Mettre à jour le nom de l'organisation
        $teamModel = new Team($this->db);
        $updateData = ['name' => $data['org_name']];

        // Si un nouveau mot de passe est fourni, le mettre à jour aussi
        if (!empty($data['new_password'])) {
            $updateData['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }

        if ($teamModel->update($teamId, $updateData)) {
            // Mettre à jour la session en préservant le statut admin
            Auth::login($teamId, $data['org_name'], Auth::isAdmin());
            $this->success(null, 'Settings updated successfully');
        } else {
            $this->error('Failed to update settings');
        }
    }

    /**
     * Liste toutes les équipes (admin uniquement)
     */
    public function listTeams(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        $stmt = $this->db->query("SELECT id, name, is_admin, created_at FROM teams ORDER BY name");
        $teams = $stmt->fetchAll();

        $this->success(['teams' => $teams]);
    }

    /**
     * Met à jour le rôle admin d'une équipe (admin uniquement)
     */
    public function updateTeamRole(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['team_id', 'is_admin'])) {
            $this->error('Missing required fields');
            return;
        }

        $teamId = (int)$data['team_id'];
        $isAdmin = (int)$data['is_admin'];
        $currentTeamId = Auth::getTeamId();

        // Empêcher de se retirer soi-même les droits admin
        if ($teamId === $currentTeamId && $isAdmin === 0) {
            $this->error('Cannot remove your own admin rights', 403);
            return;
        }

        $teamModel = new Team($this->db);
        if ($teamModel->update($teamId, ['is_admin' => $isAdmin])) {
            $this->success(null, 'Team role updated successfully');
        } else {
            $this->error('Failed to update team role');
        }
    }

    /**
     * Liste tous les membres de l'équipe courante (admin uniquement)
     */
    public function listMembers(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        $teamId = Auth::getTeamId();
        $stmt = $this->db->prepare("SELECT id, fname, lname, email, is_admin, created_at FROM members WHERE team_id = ? ORDER BY lname, fname");
        $stmt->execute([$teamId]);
        $members = $stmt->fetchAll();

        $this->success(['members' => $members]);
    }

    /**
     * Met à jour le rôle admin d'un membre (admin uniquement)
     */
    public function updateMemberRole(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        if (!$this->validate($data, ['member_id', 'is_admin'])) {
            $this->error('Missing required fields');
            return;
        }

        $memberId = (int)$data['member_id'];
        $isAdmin = (int)$data['is_admin'];
        $teamId = Auth::getTeamId();

        // Vérifier que le membre appartient bien à l'équipe courante
        $stmt = $this->db->prepare("SELECT team_id FROM members WHERE id = ?");
        $stmt->execute([$memberId]);
        $member = $stmt->fetch();

        if (!$member || $member['team_id'] != $teamId) {
            $this->error('Member not found or does not belong to your team', 404);
            return;
        }

        $memberModel = new \App\Models\Member($this->db);
        if ($memberModel->update($memberId, ['is_admin' => $isAdmin])) {
            $this->success(null, 'Member role updated successfully');
        } else {
            $this->error('Failed to update member role');
        }
    }

    /**
     * Met à jour la configuration de l'API de l'assistant IA
     */
    public function updateAIConfig(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!Auth::isAdmin()) {
            $this->error('Admin access required', 403);
            return;
        }

        $teamId = Auth::getTeamId();

        // Préparer les données
        $useApi = isset($data['ai_use_api']) ? (int)$data['ai_use_api'] : 0;
        $provider = $data['ai_api_provider'] ?? 'rules';
        $apiKey = $data['ai_api_key'] ?? '';
        $model = $data['ai_api_model'] ?? '';

        // Valider le provider
        $validProviders = ['rules', 'claude', 'openai', 'azure'];
        if (!in_array($provider, $validProviders)) {
            $this->error('Invalid AI provider');
            return;
        }

        // Si l'API est activée, la clé API est requise
        if ($useApi && empty($apiKey) && $provider !== 'rules') {
            $this->error('API key is required when using external API');
            return;
        }

        // Mettre à jour la configuration
        $stmt = $this->db->prepare("
            UPDATE teams
            SET ai_use_api = ?,
                ai_api_provider = ?,
                ai_api_key = ?,
                ai_api_model = ?
            WHERE id = ?
        ");

        if ($stmt->execute([$useApi, $provider, $apiKey, $model, $teamId])) {
            $this->success([
                'ai_use_api' => $useApi,
                'ai_api_provider' => $provider,
                'ai_api_model' => $model
            ], 'AI configuration updated successfully');
        } else {
            $this->error('Failed to update AI configuration');
        }
    }
}
