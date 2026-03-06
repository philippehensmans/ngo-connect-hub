<?php

namespace App\Models;

/**
 * Modèle pour les membres
 */
class Member extends Model
{
    protected string $table = 'members';
    protected array $fillable = ['organization_id', 'fname', 'lname', 'email', 'role', 'is_active'];
}
