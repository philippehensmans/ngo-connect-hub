<?php

namespace App\Services;

use App\Models\Webhook;
use PDO;

/**
 * Service pour déclencher les webhooks
 */
class WebhookService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Déclenche les webhooks pour un événement donné
     */
    public function trigger(string $event, array $data, int $teamId): void
    {
        $webhookModel = new Webhook($this->db);
        $webhooks = $webhookModel->getActiveForEvent($teamId, $event);

        foreach ($webhooks as $webhook) {
            $this->send($webhook, $event, $data);
        }
    }

    /**
     * Envoie un webhook
     */
    private function send(array $webhook, string $event, array $data): void
    {
        $payload = [
            'event' => $event,
            'timestamp' => date('c'),
            'data' => $data
        ];

        $signature = hash_hmac('sha256', json_encode($payload), $webhook['secret']);

        // Envoyer de manière asynchrone (en arrière-plan)
        $cmd = sprintf(
            'curl -X POST %s -H "Content-Type: application/json" -H "X-Webhook-Signature: %s" -d %s > /dev/null 2>&1 &',
            escapeshellarg($webhook['url']),
            escapeshellarg($signature),
            escapeshellarg(json_encode($payload))
        );

        exec($cmd);

        // Log l'envoi (optionnel)
        error_log("Webhook sent to {$webhook['url']} for event: $event");
    }

    /**
     * Événements disponibles
     */
    public static function getAvailableEvents(): array
    {
        return [
            'task.created' => 'Nouvelle tâche créée',
            'task.updated' => 'Tâche mise à jour',
            'task.deleted' => 'Tâche supprimée',
            'task.status_changed' => 'Statut de tâche modifié',
            'project.created' => 'Nouveau projet créé',
            'project.updated' => 'Projet mis à jour',
            'project.deleted' => 'Projet supprimé',
            'comment.created' => 'Nouveau commentaire',
            'milestone.completed' => 'Jalon complété',
            '*' => 'Tous les événements'
        ];
    }
}
