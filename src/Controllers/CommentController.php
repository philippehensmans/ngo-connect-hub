<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Services\Auth;

/**
 * Contrôleur pour les commentaires
 */
class CommentController extends Controller
{
    /**
     * Liste les commentaires d'une tâche
     */
    public function list(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['task_id'])) {
            $this->error('Missing task_id');
            return;
        }

        $commentModel = new Comment($this->db);
        $comments = $commentModel->getByTask((int)$data['task_id']);

        $this->success(['comments' => $comments]);
    }

    /**
     * Ajoute un commentaire
     */
    public function add(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['task_id', 'content'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $commentModel = new Comment($this->db);

        // Récupérer l'ID du membre connecté
        $teamId = Auth::getTeamId();
        $stmt = $this->db->prepare("SELECT id FROM members WHERE team_id = ? LIMIT 1");
        $stmt->execute([$teamId]);
        $member = $stmt->fetch();

        if (!$member) {
            $this->error('Member not found');
            return;
        }

        $commentData = [
            'task_id' => $data['task_id'],
            'member_id' => $member['id'],
            'content' => trim($data['content'])
        ];

        try {
            $id = $commentModel->create($commentData);
            $this->success(['id' => $id, 'message' => 'Comment added']);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Modifie un commentaire
     */
    public function update(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['id', 'content'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $commentModel = new Comment($this->db);

        // Vérifier que le commentaire appartient à l'utilisateur
        $comment = $commentModel->getById((int)$data['id']);
        if (!$comment) {
            $this->error('Comment not found');
            return;
        }

        $teamId = Auth::getTeamId();
        $stmt = $this->db->prepare("SELECT id FROM members WHERE team_id = ? LIMIT 1");
        $stmt->execute([$teamId]);
        $member = $stmt->fetch();

        if (!$member || $comment['member_id'] != $member['id']) {
            $this->error('Unauthorized to update this comment', 403);
            return;
        }

        try {
            $commentModel->update((int)$data['id'], [
                'content' => trim($data['content'])
            ]);
            $this->success(['message' => 'Comment updated']);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Supprime un commentaire
     */
    public function delete(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['id'])) {
            $this->error('Missing comment id');
            return;
        }

        $commentModel = new Comment($this->db);

        // Vérifier que le commentaire appartient à l'utilisateur
        $comment = $commentModel->getById((int)$data['id']);
        if (!$comment) {
            $this->error('Comment not found');
            return;
        }

        $teamId = Auth::getTeamId();
        $stmt = $this->db->prepare("SELECT id FROM members WHERE team_id = ? LIMIT 1");
        $stmt->execute([$teamId]);
        $member = $stmt->fetch();

        if (!$member || $comment['member_id'] != $member['id']) {
            $this->error('Unauthorized to delete this comment', 403);
            return;
        }

        try {
            $commentModel->delete((int)$data['id']);
            $this->success(['message' => 'Comment deleted']);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
