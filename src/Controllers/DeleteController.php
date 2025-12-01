<?php

namespace App\Controllers;

use App\Services\Auth;

/**
 * Contrôleur pour la suppression d'éléments
 */
class DeleteController extends Controller
{
    /**
     * Supprime un élément
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

        try {
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
}
