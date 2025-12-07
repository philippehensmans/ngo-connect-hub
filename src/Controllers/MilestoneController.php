<?php

namespace App\Controllers;

use App\Models\Milestone;
use App\Services\Auth;

/**
 * Contrôleur pour les jalons
 */
class MilestoneController extends Controller
{
    /**
     * Sauvegarde un jalon (création ou mise à jour)
     */
    public function save(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['name', 'project_id', 'date'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $milestoneModel = new Milestone($this->db);

        $milestoneData = [
            'project_id' => $data['project_id'],
            'name' => $data['name'],
            'date' => $data['date'],
            'status' => $data['status'] ?? 'active',
            'depends_on' => !empty($data['depends_on']) ? (int)$data['depends_on'] : null,
        ];

        try {
            if (empty($data['id'])) {
                // Création
                $id = $milestoneModel->create($milestoneData);
                $this->success(['id' => $id], 'Milestone created successfully');
            } else {
                // Mise à jour
                $milestoneModel->update((int)$data['id'], $milestoneData);
                $this->success(null, 'Milestone updated successfully');
            }
        } catch (\Exception $e) {
            $this->error('Failed to save milestone: ' . $e->getMessage());
        }
    }
}
