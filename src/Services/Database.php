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

        // Exécuter la migration vers le nouveau schéma si nécessaire
        $this->migrateToOrganizationSchema($db);

        // Table des organisations (associations)
        $db->exec("CREATE TABLE IF NOT EXISTS organizations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            slug TEXT NOT NULL UNIQUE,
            is_active INTEGER DEFAULT 1,
            ai_use_api INTEGER DEFAULT 0,
            ai_api_provider TEXT DEFAULT 'rules',
            ai_api_key TEXT,
            ai_api_model TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Table des membres (avec authentification individuelle)
        $db->exec("CREATE TABLE IF NOT EXISTS members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            organization_id INTEGER NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            fname TEXT NOT NULL,
            lname TEXT NOT NULL,
            role TEXT DEFAULT 'member',
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
        )");

        // Table des équipes internes
        $db->exec("CREATE TABLE IF NOT EXISTS internal_teams (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            organization_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            description TEXT,
            color TEXT DEFAULT '#3B82F6',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
        )");

        // Table de liaison équipes-membres
        $db->exec("CREATE TABLE IF NOT EXISTS team_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            team_id INTEGER NOT NULL,
            member_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(team_id) REFERENCES internal_teams(id) ON DELETE CASCADE,
            FOREIGN KEY(member_id) REFERENCES members(id) ON DELETE CASCADE,
            UNIQUE(team_id, member_id)
        )");

        // Table des projets (appartiennent à l'organisation)
        $db->exec("CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            organization_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            desc TEXT,
            owner_id INTEGER,
            start_date DATE,
            end_date DATE,
            status TEXT DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
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
                $db->exec("ALTER TABLE groups ADD COLUMN member_ids TEXT");
            }
        } catch (\Exception $e) {
            // Colonne déjà existante, on continue
        }

        // Table des jalons
        $db->exec("CREATE TABLE IF NOT EXISTS milestones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            date DATE NOT NULL,
            status TEXT DEFAULT 'active',
            depends_on INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(depends_on) REFERENCES milestones(id) ON DELETE SET NULL
        )");

        // Migration : Ajouter depends_on si la colonne n'existe pas
        try {
            $cols = $db->query("PRAGMA table_info(milestones)")->fetchAll(\PDO::FETCH_ASSOC);
            $hasDepends = false;
            foreach ($cols as $col) {
                if ($col['name'] === 'depends_on') {
                    $hasDepends = true;
                    break;
                }
            }
            if (!$hasDepends) {
                $db->exec("ALTER TABLE milestones ADD COLUMN depends_on INTEGER REFERENCES milestones(id) ON DELETE SET NULL");
            }
        } catch (\Exception $e) {
            // Colonne déjà existante ou autre erreur, on continue
        }

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
            organization_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            desc TEXT,
            category TEXT DEFAULT 'custom',
            template_data TEXT NOT NULL,
            is_predefined BOOLEAN DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
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
            organization_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            url TEXT NOT NULL,
            events TEXT DEFAULT '*',
            secret TEXT NOT NULL,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
        )");

        // Table des conversations de l'assistant IA
        $db->exec("CREATE TABLE IF NOT EXISTS ai_conversations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            organization_id INTEGER NOT NULL,
            project_id INTEGER,
            messages TEXT NOT NULL,
            context TEXT,
            status TEXT DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE SET NULL
        )");

        // Créer des données de démo si la base est vide
        $this->createDemoDataIfNeeded();
    }

    /**
     * Migration depuis l'ancien schéma (teams) vers le nouveau (organizations)
     */
    private function migrateToOrganizationSchema(\PDO $db): void
    {
        // Vérifier si l'ancienne table teams existe et si la nouvelle n'existe pas encore
        $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(\PDO::FETCH_COLUMN);

        $hasOldTeams = in_array('teams', $tables);
        $hasOrganizations = in_array('organizations', $tables);

        // Si on a l'ancien schéma et pas le nouveau, migrer
        if ($hasOldTeams && !$hasOrganizations) {
            $db->beginTransaction();
            try {
                // 1. Créer la table organizations
                $db->exec("CREATE TABLE organizations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL UNIQUE,
                    slug TEXT NOT NULL UNIQUE,
                    is_active INTEGER DEFAULT 1,
                    ai_use_api INTEGER DEFAULT 0,
                    ai_api_provider TEXT DEFAULT 'rules',
                    ai_api_key TEXT,
                    ai_api_model TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )");

                // 2. Migrer les données des teams vers organizations
                $teams = $db->query("SELECT * FROM teams")->fetchAll();
                foreach ($teams as $team) {
                    $slug = $this->generateSlug($team['name']);
                    $stmt = $db->prepare("INSERT INTO organizations (id, name, slug, is_active, ai_use_api, ai_api_provider, ai_api_key, ai_api_model, created_at) VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $team['id'],
                        $team['name'],
                        $slug,
                        $team['ai_use_api'] ?? 0,
                        $team['ai_api_provider'] ?? 'rules',
                        $team['ai_api_key'] ?? null,
                        $team['ai_api_model'] ?? null,
                        $team['created_at']
                    ]);
                }

                // 3. Créer la nouvelle table members avec authentification
                $db->exec("CREATE TABLE members_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    organization_id INTEGER NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    fname TEXT NOT NULL,
                    lname TEXT NOT NULL,
                    role TEXT DEFAULT 'member',
                    is_active INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
                )");

                // 4. Migrer les membres existants (le premier de chaque org devient admin)
                $members = $db->query("SELECT * FROM members ORDER BY team_id, id")->fetchAll();
                $orgAdmins = [];
                $defaultPassword = password_hash('changeme', PASSWORD_DEFAULT);

                foreach ($members as $member) {
                    $orgId = $member['team_id'];
                    $role = 'member';

                    // Le premier membre de chaque org devient admin
                    if (!isset($orgAdmins[$orgId])) {
                        $role = 'org_admin';
                        $orgAdmins[$orgId] = true;
                    }

                    // Si le membre était marqué is_admin dans l'ancien système
                    if (!empty($member['is_admin'])) {
                        $role = 'org_admin';
                    }

                    $stmt = $db->prepare("INSERT INTO members_new (id, organization_id, email, password, fname, lname, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)");
                    $stmt->execute([
                        $member['id'],
                        $orgId,
                        $member['email'],
                        $defaultPassword,
                        $member['fname'],
                        $member['lname'],
                        $role,
                        $member['created_at']
                    ]);
                }

                // 5. Créer la table internal_teams
                $db->exec("CREATE TABLE internal_teams (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    organization_id INTEGER NOT NULL,
                    name TEXT NOT NULL,
                    description TEXT,
                    color TEXT DEFAULT '#3B82F6',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
                )");

                // 6. Créer la table team_members
                $db->exec("CREATE TABLE team_members (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    team_id INTEGER NOT NULL,
                    member_id INTEGER NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(team_id) REFERENCES internal_teams(id) ON DELETE CASCADE,
                    FOREIGN KEY(member_id) REFERENCES members(id) ON DELETE CASCADE,
                    UNIQUE(team_id, member_id)
                )");

                // 7. Mettre à jour les projets : renommer team_id en organization_id
                $db->exec("CREATE TABLE projects_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    organization_id INTEGER NOT NULL,
                    name TEXT NOT NULL,
                    desc TEXT,
                    owner_id INTEGER,
                    start_date DATE,
                    end_date DATE,
                    status TEXT DEFAULT 'active',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
                    FOREIGN KEY(owner_id) REFERENCES members(id) ON DELETE SET NULL
                )");
                $db->exec("INSERT INTO projects_new SELECT id, team_id, name, desc, owner_id, start_date, end_date, status, created_at FROM projects");

                // 8. Mettre à jour les templates
                $db->exec("CREATE TABLE project_templates_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    organization_id INTEGER NOT NULL,
                    name TEXT NOT NULL,
                    desc TEXT,
                    category TEXT DEFAULT 'custom',
                    template_data TEXT NOT NULL,
                    is_predefined BOOLEAN DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
                )");
                $db->exec("INSERT INTO project_templates_new SELECT id, team_id, name, desc, category, template_data, is_predefined, created_at FROM project_templates");

                // 9. Mettre à jour les webhooks
                $db->exec("CREATE TABLE webhooks_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    organization_id INTEGER NOT NULL,
                    name TEXT NOT NULL,
                    url TEXT NOT NULL,
                    events TEXT DEFAULT '*',
                    secret TEXT NOT NULL,
                    is_active BOOLEAN DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE
                )");
                $db->exec("INSERT INTO webhooks_new SELECT id, team_id, name, url, events, secret, is_active, created_at FROM webhooks");

                // 10. Mettre à jour ai_conversations
                $db->exec("CREATE TABLE ai_conversations_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    organization_id INTEGER NOT NULL,
                    project_id INTEGER,
                    messages TEXT NOT NULL,
                    context TEXT,
                    status TEXT DEFAULT 'active',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
                    FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE SET NULL
                )");
                $db->exec("INSERT INTO ai_conversations_new SELECT id, team_id, project_id, messages, context, status, created_at, updated_at FROM ai_conversations");

                // 11. Supprimer les anciennes tables et renommer les nouvelles
                $db->exec("DROP TABLE teams");
                $db->exec("DROP TABLE members");
                $db->exec("DROP TABLE projects");
                $db->exec("DROP TABLE project_templates");
                $db->exec("DROP TABLE webhooks");
                $db->exec("DROP TABLE ai_conversations");

                $db->exec("ALTER TABLE members_new RENAME TO members");
                $db->exec("ALTER TABLE projects_new RENAME TO projects");
                $db->exec("ALTER TABLE project_templates_new RENAME TO project_templates");
                $db->exec("ALTER TABLE webhooks_new RENAME TO webhooks");
                $db->exec("ALTER TABLE ai_conversations_new RENAME TO ai_conversations");

                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                throw new \RuntimeException("Migration failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Génère un slug à partir d'un nom
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug ?: 'org-' . time();
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
            "CREATE INDEX IF NOT EXISTS idx_members_org ON members(organization_id)",
            "CREATE INDEX IF NOT EXISTS idx_members_email ON members(email)",
            "CREATE INDEX IF NOT EXISTS idx_members_role ON members(role)",
            "CREATE INDEX IF NOT EXISTS idx_projects_org ON projects(organization_id)",
            "CREATE INDEX IF NOT EXISTS idx_webhooks_org ON webhooks(organization_id)",
            "CREATE INDEX IF NOT EXISTS idx_webhooks_active ON webhooks(is_active)",
            "CREATE INDEX IF NOT EXISTS idx_internal_teams_org ON internal_teams(organization_id)",
            "CREATE INDEX IF NOT EXISTS idx_team_members_team ON team_members(team_id)",
            "CREATE INDEX IF NOT EXISTS idx_team_members_member ON team_members(member_id)",
            "CREATE INDEX IF NOT EXISTS idx_organizations_slug ON organizations(slug)",
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
        $count = $db->query("SELECT COUNT(*) FROM organizations")->fetchColumn();

        if ($count == 0) {
            // Créer une organisation démo
            $stmt = $db->prepare("INSERT INTO organizations (name, slug) VALUES (?, ?)");
            $stmt->execute(['ONG Démo', 'ong-demo']);
            $orgId = $db->lastInsertId();

            // Créer un admin pour cette organisation
            $hashedPassword = password_hash('demo', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO members (organization_id, email, password, fname, lname, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$orgId, 'admin@ong-demo.org', $hashedPassword, 'Admin', 'Démo', 'org_admin']);

            // Créer un super admin global
            $superAdminPassword = password_hash('superadmin', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO members (organization_id, email, password, fname, lname, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$orgId, 'superadmin@system.org', $superAdminPassword, 'Super', 'Admin', 'super_admin']);
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
