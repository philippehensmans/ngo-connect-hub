<?php

namespace App\Models;

/**
 * Modèle pour les groupes
 */
class Group extends Model
{
    protected string $table = 'groups';
    protected array $fillable = ['project_id', 'name', 'description', 'color', 'owner_id'];
}
