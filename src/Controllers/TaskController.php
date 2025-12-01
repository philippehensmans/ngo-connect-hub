<?php

namespace App\Controllers;

use App\Models\Task;
use App\Services\Auth;

/**
 * Contrôleur pour les tâches
 */
class TaskController extends Controller
{
    /**
     * Sauvegarde une tâche (création ou mise à jour)
     */
    public function save(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['title', 'project_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $taskModel = new Task($this->db);

        $taskData = [
            'project_id' => $data['project_id'],
            'group_id' => !empty($data['group_id']) ? $data['group_id'] : null,
            'milestone_id' => !empty($data['milestone_id']) ? $data['milestone_id'] : null,
            'title' => $data['title'],
            'desc' => $data['desc'] ?? '',
            'owner_id' => !empty($data['owner_id']) ? $data['owner_id'] : null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'] ?? 'todo',
            'priority' => $data['priority'] ?? 'medium',
            'tags' => $data['tags'] ?? '',
            'link' => $data['link'] ?? '',
            'dependencies' => $data['dependencies'] ?? '',
        ];

        try {
            if (empty($data['id'])) {
                // Création
                $id = $taskModel->create($taskData);
                $this->success(['id' => $id], 'Task created successfully');
            } else {
                // Mise à jour
                $taskModel->update((int)$data['id'], $taskData);
                $this->success(null, 'Task updated successfully');
            }
        } catch (\Exception $e) {
            $this->error('Failed to save task: ' . $e->getMessage());
        }
    }
}
