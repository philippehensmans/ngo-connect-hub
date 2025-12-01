<?php

namespace App\Models;

/**
 * Modèle pour les projets
 */
class Project extends Model
{
    protected string $table = 'projects';
    protected array $fillable = ['team_id', 'name', 'desc', 'owner_id', 'start_date', 'end_date', 'status'];
}
