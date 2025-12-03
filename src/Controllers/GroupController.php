<?php

namespace App\Controllers;

use App\Models\Group;
use App\Services\Auth;

/**
 * Contrôleur pour les groupes
 */
class GroupController extends Controller
{
    /**
     * Sauvegarde un groupe (création ou mise à jour)
     */
    public function save(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['name', 'project_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $groupModel = new Group($this->db);

        // Gérer les member_ids (convertir le tableau en JSON)
        $memberIds = null;
        if (isset($data['member_ids'])) {
            if (is_array($data['member_ids'])) {
                $memberIds = json_encode(array_map('intval', $data['member_ids']));
            } elseif (is_string($data['member_ids']) && !empty($data['member_ids'])) {
                // Si c'est déjà une chaîne JSON, la valider et la garder
                $decoded = json_decode($data['member_ids'], true);
                $memberIds = is_array($decoded) ? json_encode(array_map('intval', $decoded)) : null;
            }
        }

        $groupData = [
            'project_id' => $data['project_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'color' => $data['color'] ?? '#E5E7EB',
            'owner_id' => !empty($data['owner_id']) ? $data['owner_id'] : null,
            'member_ids' => $memberIds,
        ];

        try {
            if (empty($data['id'])) {
                // Création
                $id = $groupModel->create($groupData);
                $this->success(['id' => $id], 'Group created successfully');
            } else {
                // Mise à jour
                $groupModel->update((int)$data['id'], $groupData);
                $this->success(null, 'Group updated successfully');
            }
        } catch (\Exception $e) {
            $this->error('Failed to save group: ' . $e->getMessage());
        }
    }
}
