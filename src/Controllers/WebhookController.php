<?php

namespace App\Controllers;

use App\Models\Webhook;
use App\Services\Auth;

/**
 * Contrôleur pour la gestion des webhooks
 */
class WebhookController extends Controller
{
    /**
     * Liste tous les webhooks de l'équipe
     */
    public function list(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $teamId = Auth::getTeamId();
        $webhookModel = new Webhook($this->db);
        $webhooks = $webhookModel->all(['team_id' => $teamId]);

        $this->success(['webhooks' => $webhooks]);
    }

    /**
     * Crée un nouveau webhook
     */
    public function create(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $required = ['name', 'url', 'events'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->error("Missing field: $field");
                return;
            }
        }

        // Valider l'URL
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $this->error('Invalid URL');
            return;
        }

        $teamId = Auth::getTeamId();
        $webhookModel = new Webhook($this->db);

        // Générer un secret pour la signature
        $secret = bin2hex(random_bytes(32));

        $webhookData = [
            'team_id' => $teamId,
            'name' => trim($data['name']),
            'url' => trim($data['url']),
            'events' => is_array($data['events']) ? implode(',', $data['events']) : $data['events'],
            'secret' => $secret,
            'is_active' => 1
        ];

        $id = $webhookModel->create($webhookData);
        $webhook = $webhookModel->find($id);

        $this->success(['webhook' => $webhook, 'message' => 'Webhook created successfully']);
    }

    /**
     * Met à jour un webhook
     */
    public function update(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['id'])) {
            $this->error('Missing webhook ID');
            return;
        }

        $webhookModel = new Webhook($this->db);
        $webhook = $webhookModel->find((int)$data['id']);

        if (!$webhook || $webhook['team_id'] != Auth::getTeamId()) {
            $this->error('Webhook not found', 404);
            return;
        }

        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = trim($data['name']);
        if (isset($data['url'])) {
            if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
                $this->error('Invalid URL');
                return;
            }
            $updateData['url'] = trim($data['url']);
        }
        if (isset($data['events'])) {
            $updateData['events'] = is_array($data['events']) ? implode(',', $data['events']) : $data['events'];
        }
        if (isset($data['is_active'])) $updateData['is_active'] = (int)$data['is_active'];

        $webhookModel->update((int)$data['id'], $updateData);

        $this->success(['message' => 'Webhook updated successfully']);
    }

    /**
     * Supprime un webhook
     */
    public function delete(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['id'])) {
            $this->error('Missing webhook ID');
            return;
        }

        $webhookModel = new Webhook($this->db);
        $webhook = $webhookModel->find((int)$data['id']);

        if (!$webhook || $webhook['team_id'] != Auth::getTeamId()) {
            $this->error('Webhook not found', 404);
            return;
        }

        $webhookModel->delete((int)$data['id']);

        $this->success(['message' => 'Webhook deleted successfully']);
    }

    /**
     * Teste un webhook en envoyant un événement de test
     */
    public function test(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['id'])) {
            $this->error('Missing webhook ID');
            return;
        }

        $webhookModel = new Webhook($this->db);
        $webhook = $webhookModel->find((int)$data['id']);

        if (!$webhook || $webhook['team_id'] != Auth::getTeamId()) {
            $this->error('Webhook not found', 404);
            return;
        }

        // Envoyer un événement de test
        $payload = [
            'event' => 'webhook.test',
            'timestamp' => date('c'),
            'data' => [
                'message' => 'This is a test webhook from ONG Manager'
            ]
        ];

        $signature = hash_hmac('sha256', json_encode($payload), $webhook['secret']);

        $ch = curl_init($webhook['url']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Webhook-Signature: ' . $signature
            ],
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->error('Webhook test failed: ' . $error);
            return;
        }

        $this->success([
            'message' => 'Webhook test sent',
            'http_code' => $httpCode,
            'response' => $response
        ]);
    }
}
