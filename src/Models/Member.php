<?php

namespace App\Models;

/**
 * Modèle pour les membres
 */
class Member extends Model
{
    protected string $table = 'members';
    protected array $fillable = ['team_id', 'fname', 'lname', 'email', 'is_admin'];
}
