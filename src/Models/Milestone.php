<?php

namespace App\Models;

/**
 * Modèle pour les jalons
 */
class Milestone extends Model
{
    protected string $table = 'milestones';
    protected array $fillable = ['project_id', 'name', 'date', 'status', 'depends_on'];
}
