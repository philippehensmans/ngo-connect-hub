<?php

namespace App\Controllers;

use PDO;

/**
 * Contrôleur de base
 */
abstract class Controller
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Envoie une réponse JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Envoie une réponse de succès
     */
    protected function success($data = null, string $message = 'Success'): void
    {
        $response = ['ok' => true, 'msg' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->json($response);
    }

    /**
     * Envoie une réponse d'erreur
     */
    protected function error(string $message = 'Error', int $statusCode = 400): void
    {
        $this->json(['ok' => false, 'msg' => $message], $statusCode);
    }

    /**
     * Valide les données requises
     */
    protected function validate(array $data, array $required): bool
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Nettoie les données d'entrée
     */
    protected function sanitize(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
}
