<?php

namespace App\Controllers;

use App\Models\Project;
use App\Services\Auth;

/**
 * Contrôleur pour les projets
 */
class ProjectController extends Controller
{
    /**
     * Sauvegarde un projet (création ou mise à jour)
     */
    public function save(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['name'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $projectModel = new Project($this->db);
        $teamId = Auth::getTeamId();

        $projectData = [
            'team_id' => $teamId,
            'name' => $data['name'],
            'desc' => $data['desc'] ?? '',
            'start_date' => $data['start'] ?? null,
            'end_date' => $data['end'] ?? null,
            'owner_id' => !empty($data['owner_id']) ? $data['owner_id'] : null,
        ];

        try {
            if (empty($data['id'])) {
                // Création
                $id = $projectModel->create($projectData);
                $this->success(['id' => $id], 'Project created successfully');
            } else {
                // Mise à jour
                $projectModel->update((int)$data['id'], $projectData);
                $this->success(null, 'Project updated successfully');
            }
        } catch (\Exception $e) {
            $this->error('Failed to save project: ' . $e->getMessage());
        }
    }
}
