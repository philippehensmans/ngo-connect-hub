<?php

namespace App\Models;

/**
 * Modèle pour les groupes
 */
class Group extends Model
{
    protected string $table = 'groups';
    protected array $fillable = ['project_id', 'name', 'color', 'owner_id'];
}
