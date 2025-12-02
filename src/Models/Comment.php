<?php

namespace App\Models;

/**
 * Modèle pour les commentaires sur les tâches
 */
class Comment extends Model
{
    protected string $table = 'comments';
    protected array $fillable = [
        'task_id',
        'member_id',
        'content'
    ];

    /**
     * Récupère tous les commentaires d'une tâche
     */
    public function getByTask(int $taskId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, m.fname, m.lname
            FROM {$this->table} c
            LEFT JOIN members m ON c.member_id = m.id
            WHERE c.task_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$taskId]);
        return $stmt->fetchAll();
    }
}
