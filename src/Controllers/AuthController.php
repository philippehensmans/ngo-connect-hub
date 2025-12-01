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
            Auth::login($team['id'], $team['name']);
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
            // Mettre à jour la session
            Auth::login($teamId, $data['org_name']);
            $this->success(null, 'Settings updated successfully');
        } else {
            $this->error('Failed to update settings');
        }
    }
}
