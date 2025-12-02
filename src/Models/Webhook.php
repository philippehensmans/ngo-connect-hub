<?php

namespace App\Models;

/**
 * Modèle pour les webhooks
 */
class Webhook extends Model
{
    protected string $table = 'webhooks';
    protected array $fillable = ['team_id', 'name', 'url', 'events', 'secret', 'is_active'];

    /**
     * Récupère les webhooks actifs pour une équipe et un événement
     */
    public function getActiveForEvent(int $teamId, string $event): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE team_id = ?
            AND is_active = 1
            AND (events = '*' OR events LIKE ?)
        ");
        $stmt->execute([$teamId, "%$event%"]);
        return $stmt->fetchAll();
    }
}
