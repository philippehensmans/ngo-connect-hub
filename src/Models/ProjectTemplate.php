<?php

namespace App\Models;

/**
 * Modèle pour les templates de projets
 */
class ProjectTemplate extends Model
{
    protected string $table = 'project_templates';
    protected array $fillable = ['team_id', 'name', 'desc', 'category', 'template_data', 'is_predefined'];
}
