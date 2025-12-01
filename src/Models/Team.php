<?php

namespace App\Models;

/**
 * Modèle pour les équipes
 */
class Team extends Model
{
    protected string $table = 'teams';
    protected array $fillable = ['name', 'password'];

    /**
     * Trouve une équipe par nom
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
