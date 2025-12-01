<?php

namespace App\Services;

use PDO;
use PDOException;

/**
 * Service de gestion de la base de données
 */
class Database
{
    private static ?PDO $instance = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Obtient l'instance PDO singleton
     */
    public function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dbPath = $this->config['database']['path'];
                $dbDir = dirname($dbPath);

                // Créer le dossier data s'il n'existe pas
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }

                self::$instance = new PDO('sqlite:' . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec("PRAGMA foreign_keys = ON;");

                $this->initializeTables();
            } catch (PDOException $e) {
                throw new \RuntimeException("Erreur de connexion à la base de données: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Initialise les tables de la base de données
     */
    private function initializeTables(): void
    {
        $db = self::$instance;

        // Table des équipes
        $db->exec("CREATE TABLE IF NOT EXISTS teams (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Table des membres
        $db->exec("CREATE TABLE IF NOT EXISTS members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            team_id INTEGER NOT NULL,
            fname TEXT NOT NULL,
            lname TEXT NOT NULL,
            email TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE
        )");

        // Table des projets
        $db->exec("CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            team_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            desc TEXT,
            owner_id INTEGER,
            start_date DATE,
            end_date DATE,
            status TEXT DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE,
            FOREIGN KEY(owner_id) REFERENCES members(id) ON DELETE SET NULL
        )");

        // Table des groupes
        $db->exec("CREATE TABLE IF NOT EXISTS groups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            color TEXT DEFAULT '#E5E7EB',
            owner_id INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(owner_id) REFERENCES members(id) ON DELETE SET NULL
        )");

        // Table des jalons
        $db->exec("CREATE TABLE IF NOT EXISTS milestones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            date DATE NOT NULL,
            status TEXT DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )");

        // Table des tâches
        $db->exec("CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL,
            group_id INTEGER,
            milestone_id INTEGER,
            title TEXT NOT NULL,
            desc TEXT,
            owner_id INTEGER,
            start_date DATE,
            end_date DATE,
            status TEXT DEFAULT 'todo',
            priority TEXT DEFAULT 'medium',
            tags TEXT,
            link TEXT,
            dependencies TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(group_id) REFERENCES groups(id) ON DELETE SET NULL,
            FOREIGN KEY(milestone_id) REFERENCES milestones(id) ON DELETE SET NULL,
            FOREIGN KEY(owner_id) REFERENCES members(id) ON DELETE SET NULL
        )");

        // Créer des données de démo si la base est vide
        $this->createDemoDataIfNeeded();
    }

    /**
     * Crée des données de démonstration si nécessaire
     */
    private function createDemoDataIfNeeded(): void
    {
        $db = self::$instance;
        $count = $db->query("SELECT COUNT(*) FROM teams")->fetchColumn();

        if ($count == 0) {
            $hashedPassword = password_hash('demo', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO teams (name, password) VALUES (?, ?)");
            $stmt->execute(['ONG Démo', $hashedPassword]);

            $teamId = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO members (team_id, fname, lname, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$teamId, 'Admin', 'System', 'admin@ong.org']);
        }
    }

    /**
     * Réinitialise complètement la base de données
     */
    public function reset(): void
    {
        $dbPath = $this->config['database']['path'];
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
        self::$instance = null;
    }
}
