<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Services\Database;

/**
 * Contrôleur pour la gestion des backups
 */
class BackupController extends Controller
{
    /**
     * Crée un backup manuel de la base de données
     */
    public function create(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        try {
            $dbService = new Database(require __DIR__ . '/../../config/config.php');
            $backupFile = $dbService->createBackup();

            $this->success([
                'message' => 'Backup créé avec succès',
                'filename' => basename($backupFile)
            ]);
        } catch (\Exception $e) {
            $this->error('Erreur lors de la création du backup: ' . $e->getMessage());
        }
    }

    /**
     * Liste tous les backups disponibles
     */
    public function list(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        try {
            $dbService = new Database(require __DIR__ . '/../../config/config.php');
            $backups = $dbService->listBackups();

            $this->success(['backups' => $backups]);
        } catch (\Exception $e) {
            $this->error('Erreur lors de la récupération des backups: ' . $e->getMessage());
        }
    }

    /**
     * Télécharge un backup spécifique
     */
    public function download(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['filename'])) {
            $this->error('Filename required');
            return;
        }

        $config = require __DIR__ . '/../../config/config.php';
        $backupDir = dirname($config['database']['path']) . '/backups';
        $backupFile = $backupDir . '/' . basename($data['filename']); // basename pour la sécurité

        if (!file_exists($backupFile)) {
            $this->error('Backup file not found');
            return;
        }

        // Envoyer le fichier en téléchargement
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
        header('Content-Length: ' . filesize($backupFile));
        readfile($backupFile);
        exit;
    }
}
