<?php

namespace App\Models;

/**
 * Modèle pour les tâches
 */
class Task extends Model
{
    protected string $table = 'tasks';
    protected array $fillable = [
        'project_id',
        'group_id',
        'milestone_id',
        'title',
        'desc',
        'owner_id',
        'start_date',
        'end_date',
        'status',
        'priority',
        'tags',
        'link',
        'dependencies'
    ];

    /**
     * Récupère toutes les tâches d'un projet
     */
    public function getByProject(int $projectId): array
    {
        return $this->all(['project_id' => $projectId]);
    }
}
