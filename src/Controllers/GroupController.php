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

        $groupData = [
            'project_id' => $data['project_id'],
            'name' => $data['name'],
            'color' => $data['color'] ?? '#E5E7EB',
            'owner_id' => !empty($data['owner_id']) ? $data['owner_id'] : null,
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
