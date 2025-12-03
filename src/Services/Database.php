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

                // Optimisations SQLite pour meilleures performances
                self::$instance->exec("PRAGMA foreign_keys = ON;");
                self::$instance->exec("PRAGMA journal_mode = WAL;");        // Write-Ahead Logging
                self::$instance->exec("PRAGMA synchronous = NORMAL;");      // Balance sécurité/performance
                self::$instance->exec("PRAGMA cache_size = -10000;");       // ~10MB de cache
                self::$instance->exec("PRAGMA temp_store = MEMORY;");       // Stockage temporaire en RAM
                self::$instance->exec("PRAGMA mmap_size = 268435456;");     // Memory-mapped I/O (256MB)

                $this->initializeTables();
                $this->createIndexes();
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
            is_admin INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Table des membres
        $db->exec("CREATE TABLE IF NOT EXISTS members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            team_id INTEGER NOT NULL,
            fname TEXT NOT NULL,
            lname TEXT NOT NULL,
            email TEXT NOT NULL,
            is_admin INTEGER DEFAULT 0,
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
            description TEXT,
            color TEXT DEFAULT '#E5E7EB',
            owner_id INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(owner_id) REFERENCES members(id) ON DELETE SET NULL
        )");

        // Migration: Ajouter le champ description aux groupes s'il n'existe pas
        try {
            $result = $db->query("PRAGMA table_info(groups)")->fetchAll();
            $hasDescription = false;
            foreach ($result as $column) {
                if ($column['name'] === 'description') {
                    $hasDescription = true;
                    break;
                }
            }
            if (!$hasDescription) {
                $db->exec("ALTER TABLE groups ADD COLUMN description TEXT");
            }
        } catch (\Exception $e) {
            // Colonne déjà existante ou autre erreur, on continue
        }

        // Migration: Ajouter le champ is_admin aux équipes s'il n'existe pas
        try {
            $result = $db->query("PRAGMA table_info(teams)")->fetchAll();
            $hasIsAdmin = false;
            foreach ($result as $column) {
                if ($column['name'] === 'is_admin') {
                    $hasIsAdmin = true;
                    break;
                }
            }
            if (!$hasIsAdmin) {
                // Ajouter le champ et mettre toutes les équipes existantes comme admin
                $db->exec("ALTER TABLE teams ADD COLUMN is_admin INTEGER DEFAULT 1");
            }
        } catch (\Exception $e) {
            // Colonne déjà existante ou autre erreur, on continue
        }

        // Migration: Ajouter le champ is_admin aux membres s'il n'existe pas
        try {
            $result = $db->query("PRAGMA table_info(members)")->fetchAll();
            $hasIsAdmin = false;
            foreach ($result as $column) {
                if ($column['name'] === 'is_admin') {
                    $hasIsAdmin = true;
                    break;
                }
            }
            if (!$hasIsAdmin) {
                // Ajouter le champ (par défaut non-admin)
                $db->exec("ALTER TABLE members ADD COLUMN is_admin INTEGER DEFAULT 0");
            }
        } catch (\Exception $e) {
            // Colonne déjà existante ou autre erreur, on continue
        }

        // Migration: Ajouter le champ member_ids aux groupes s'il n'existe pas
        try {
            $result = $db->query("PRAGMA table_info(groups)")->fetchAll();
            $hasMemberIds = false;
            foreach ($result as $column) {
                if ($column['name'] === 'member_ids') {
                    $hasMemberIds = true;
                    break;
                }
            }
            if (!$hasMemberIds) {
                // Ajouter le champ pour stocker les IDs des membres assignés (format JSON)
                $db->exec("ALTER TABLE groups ADD COLUMN member_ids TEXT");
            }
        } catch (\Exception $e) {
            // Colonne déjà existante ou autre erreur, on continue
        }

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

        // Table des templates de projets
        $db->exec("CREATE TABLE IF NOT EXISTS project_templates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            team_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            desc TEXT,
            category TEXT DEFAULT 'custom',
            template_data TEXT NOT NULL,
            is_predefined BOOLEAN DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE
        )");

        // Table des commentaires sur les tâches
        $db->exec("CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL,
            member_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(member_id) REFERENCES members(id) ON DELETE CASCADE
        )");

        // Table des webhooks
        $db->exec("CREATE TABLE IF NOT EXISTS webhooks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            team_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            url TEXT NOT NULL,
            events TEXT DEFAULT '*',
            secret TEXT NOT NULL,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE
        )");

        // Créer des données de démo si la base est vide
        $this->createDemoDataIfNeeded();
    }

    /**
     * Crée les index de performance sur la base de données
     */
    private function createIndexes(): void
    {
        $db = self::$instance;

        // Index pour améliorer les performances des requêtes fréquentes
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_tasks_project ON tasks(project_id)",
            "CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status)",
            "CREATE INDEX IF NOT EXISTS idx_tasks_owner ON tasks(owner_id)",
            "CREATE INDEX IF NOT EXISTS idx_tasks_group ON tasks(group_id)",
            "CREATE INDEX IF NOT EXISTS idx_tasks_milestone ON tasks(milestone_id)",
            "CREATE INDEX IF NOT EXISTS idx_tasks_end_date ON tasks(end_date)",
            "CREATE INDEX IF NOT EXISTS idx_comments_task ON comments(task_id)",
            "CREATE INDEX IF NOT EXISTS idx_comments_member ON comments(member_id)",
            "CREATE INDEX IF NOT EXISTS idx_groups_project ON groups(project_id)",
            "CREATE INDEX IF NOT EXISTS idx_milestones_project ON milestones(project_id)",
            "CREATE INDEX IF NOT EXISTS idx_members_team ON members(team_id)",
            "CREATE INDEX IF NOT EXISTS idx_projects_team ON projects(team_id)",
            "CREATE INDEX IF NOT EXISTS idx_webhooks_team ON webhooks(team_id)",
            "CREATE INDEX IF NOT EXISTS idx_webhooks_active ON webhooks(is_active)",
        ];

        foreach ($indexes as $index) {
            $db->exec($index);
        }
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

    /**
     * Crée un backup de la base de données
     * @return string Le chemin du fichier de backup créé
     */
    public function createBackup(): string
    {
        $dbPath = $this->config['database']['path'];
        $backupDir = dirname($dbPath) . '/backups';

        // Créer le dossier de backups s'il n'existe pas
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Nom du fichier de backup avec timestamp
        $backupFile = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.db';

        // Copier la base de données
        if (file_exists($dbPath)) {
            copy($dbPath, $backupFile);

            // Nettoyer les anciens backups (garder les 7 derniers)
            $this->cleanOldBackups($backupDir, 7);

            return $backupFile;
        }

        throw new \RuntimeException("Database file not found");
    }

    /**
     * Backup automatique quotidien
     * Crée un backup seulement s'il n'y en a pas déjà un pour aujourd'hui
     */
    public function autoBackup(): void
    {
        $dbPath = $this->config['database']['path'];
        $backupDir = dirname($dbPath) . '/backups';

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Vérifier si un backup existe déjà pour aujourd'hui
        $todayPattern = $backupDir . '/backup_' . date('Y-m-d') . '_*.db';
        $existingBackups = glob($todayPattern);

        if (empty($existingBackups)) {
            $this->createBackup();
        }
    }

    /**
     * Nettoie les anciens backups en gardant les N plus récents
     * @param string $backupDir Le répertoire des backups
     * @param int $keepCount Nombre de backups à conserver
     */
    private function cleanOldBackups(string $backupDir, int $keepCount): void
    {
        $backups = glob($backupDir . '/backup_*.db');

        if (count($backups) > $keepCount) {
            // Trier par date de modification (plus ancien en premier)
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Supprimer les plus anciens
            $toDelete = array_slice($backups, 0, count($backups) - $keepCount);
            foreach ($toDelete as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Liste tous les backups disponibles
     * @return array Liste des backups avec leurs informations
     */
    public function listBackups(): array
    {
        $dbPath = $this->config['database']['path'];
        $backupDir = dirname($dbPath) . '/backups';
        $backups = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/backup_*.db');

            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'path' => $file,
                    'size' => filesize($file),
                    'date' => date('Y-m-d H:i:s', filemtime($file))
                ];
            }

            // Trier par date (plus récent en premier)
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        return $backups;
    }

    /**
     * Retourne une instance singleton de Database
     */
    public static function getInstance(): ?Database
    {
        return new self(require __DIR__ . '/../../config/config.php');
    }
}
