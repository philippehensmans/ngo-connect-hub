<?php

namespace App\Services;

use PDO;

/**
 * Service de l'assistant IA basé sur des règles intelligentes
 * Supporte le mode hybride : règles par défaut, API externe si configurée
 */
class AssistantService
{
    private PDO $db;
    private ?AIApiService $apiService = null;
    private bool $useApi = false;

    // Étapes du questionnaire
    private const STEP_WELCOME = 'welcome';
    private const STEP_PROJECT_TYPE = 'project_type';
    private const STEP_PROJECT_NAME = 'project_name';
    private const STEP_PROJECT_DESCRIPTION = 'project_description';
    private const STEP_DURATION = 'duration';
    private const STEP_MILESTONES = 'milestones';
    private const STEP_GROUPS = 'groups';
    private const STEP_DELIVERABLES = 'deliverables';
    private const STEP_CONFIRMATION = 'confirmation';
    private const STEP_COMPLETED = 'completed';

    // Types de projets avec suggestions spécifiques
    private const PROJECT_TYPES = [
        'humanitarian' => [
            'groups' => ['Logistique', 'Santé', 'Nutrition', 'Abris', 'Eau et Assainissement'],
            'milestones' => ['Évaluation des besoins', 'Mobilisation des ressources', 'Déploiement sur le terrain', 'Distribution', 'Rapport final'],
            'deliverables' => ['Rapport d\'évaluation', 'Plan d\'intervention', 'Rapports de distribution', 'Rapport d\'impact']
        ],
        'environment' => [
            'groups' => ['Recherche', 'Sensibilisation', 'Actions terrain', 'Suivi et Évaluation', 'Communication'],
            'milestones' => ['Diagnostic initial', 'Planification des actions', 'Mise en œuvre', 'Évaluation d\'impact', 'Capitalisation'],
            'deliverables' => ['Étude d\'impact', 'Plan d\'action', 'Rapports de terrain', 'Documentation']
        ],
        'education' => [
            'groups' => ['Pédagogie', 'Infrastructure', 'Formation', 'Matériel', 'Suivi'],
            'milestones' => ['Diagnostic éducatif', 'Conception du programme', 'Formation des formateurs', 'Déploiement', 'Évaluation'],
            'deliverables' => ['Programme pédagogique', 'Matériel de formation', 'Rapports de formation', 'Évaluation des acquis']
        ],
        'health' => [
            'groups' => ['Prévention', 'Soins', 'Équipement', 'Formation', 'Suivi'],
            'milestones' => ['Évaluation sanitaire', 'Mise en place des infrastructures', 'Formation du personnel', 'Campagne de prévention', 'Évaluation'],
            'deliverables' => ['Protocoles de soins', 'Rapports sanitaires', 'Données épidémiologiques', 'Documentation']
        ],
        'development' => [
            'groups' => ['Économie', 'Infrastructure', 'Capacitation', 'Gouvernance', 'Suivi'],
            'milestones' => ['Diagnostic territorial', 'Co-construction du projet', 'Mise en œuvre', 'Accompagnement', 'Pérennisation'],
            'deliverables' => ['Étude de faisabilité', 'Plan de développement', 'Rapports d\'activité', 'Bilan']
        ],
        'advocacy' => [
            'groups' => ['Recherche', 'Lobbying', 'Communication', 'Mobilisation', 'Évaluation'],
            'milestones' => ['Recherche et documentation', 'Stratégie de plaidoyer', 'Campagne', 'Dialogue politique', 'Suivi'],
            'deliverables' => ['Dossier de plaidoyer', 'Briefs politiques', 'Rapports de campagne', 'Évaluation d\'impact']
        ],
        'custom' => [
            'groups' => ['Groupe 1', 'Groupe 2', 'Groupe 3'],
            'milestones' => ['Démarrage', 'Mi-parcours', 'Finalisation'],
            'deliverables' => ['Livrable 1', 'Livrable 2', 'Livrable 3']
        ]
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Initialise l'API service si l'équipe a configuré une API
     */
    private function initializeApiService(int $teamId): void
    {
        $stmt = $this->db->prepare(
            "SELECT ai_use_api, ai_api_provider, ai_api_key, ai_api_model
             FROM teams
             WHERE id = ?"
        );
        $stmt->execute([$teamId]);
        $config = $stmt->fetch();

        if ($config && $config['ai_use_api'] && $config['ai_api_key']) {
            $this->useApi = true;
            $this->apiService = new AIApiService(
                $config['ai_api_provider'] ?: 'claude',
                $config['ai_api_key'],
                $config['ai_api_model']
            );
        } else {
            $this->useApi = false;
            $this->apiService = null;
        }
    }

    /**
     * Démarre une nouvelle conversation
     */
    public function startConversation(int $teamId, ?int $projectId): int
    {
        // Initialiser l'API si configurée
        $this->initializeApiService($teamId);
        $messages = json_encode([]);
        $context = json_encode([
            'step' => self::STEP_WELCOME,
            'data' => []
        ]);

        $stmt = $this->db->prepare(
            "INSERT INTO ai_conversations (team_id, project_id, messages, context, status)
             VALUES (?, ?, ?, ?, 'active')"
        );
        $stmt->execute([$teamId, $projectId, $messages, $context]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Retourne le message initial de bienvenue
     */
    public function getInitialMessage(): array
    {
        return [
            'role' => 'assistant',
            'content' => "Bonjour ! Je suis votre assistant de planification de projet. Je vais vous aider à structurer votre projet en vous posant quelques questions.\n\nPour commencer, quel type de projet souhaitez-vous réaliser ?",
            'suggestions' => [
                'Action humanitaire',
                'Environnement et climat',
                'Éducation',
                'Santé',
                'Développement local',
                'Plaidoyer et advocacy',
                'Autre (projet personnalisé)'
            ]
        ];
    }

    /**
     * Traite un message de l'utilisateur et génère une réponse
     */
    public function processMessage(int $conversationId, string $userMessage): array
    {
        // Récupérer la conversation
        $conversation = $this->getConversation($conversationId);

        // Initialiser l'API si configurée pour cette équipe
        $this->initializeApiService((int)$conversation['team_id']);

        // Décoder le contexte
        $context = json_decode($conversation['context'], true);
        $messages = json_decode($conversation['messages'], true);

        // Ajouter le message de l'utilisateur
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Générer la réponse : API si disponible, sinon règles
        if ($this->useApi && $this->apiService) {
            $response = $this->generateApiResponse($messages, $context);
        } else {
            $response = $this->generateResponse($context, $userMessage);
        }

        // Ajouter la réponse de l'assistant
        $messages[] = [
            'role' => 'assistant',
            'content' => $response['message'],
            'suggestions' => $response['suggestions'] ?? null,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Mettre à jour le contexte
        $context = $response['context'];

        // Sauvegarder la conversation
        $this->updateConversation($conversationId, $messages, $context);

        return [
            'message' => $response['message'],
            'suggestions' => $response['suggestions'] ?? null,
            'completed' => $context['step'] === self::STEP_COMPLETED
        ];
    }

    /**
     * Génère une réponse en utilisant l'API externe
     */
    private function generateApiResponse(array $messages, array $context): array
    {
        // Préparer le prompt système
        $systemPrompt = "Tu es un assistant IA spécialisé dans la planification de projets pour des ONG. " .
            "Tu aides les utilisateurs à structurer leurs projets en les guidant à travers des questions. " .
            "Tu dois collecter les informations suivantes : " .
            "1. Type de projet (humanitaire, environnement, éducation, santé, développement, plaidoyer) " .
            "2. Nom du projet " .
            "3. Description du projet " .
            "4. Durée du projet " .
            "5. Jalons (milestones) importants " .
            "6. Groupes de travail " .
            "7. Livrables principaux. " .
            "\n\nContexte actuel : " . json_encode($context['data'] ?? []) .
            "\n\nQuand toutes les informations sont collectées, indique clairement que la structure peut être générée.";

        // Préparer les messages pour l'API (sans timestamp)
        $apiMessages = array_map(function($msg) {
            return [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }, $messages);

        try {
            // Appeler l'API
            $responseText = $this->apiService->sendMessage($apiMessages, $systemPrompt);

            // Parser la réponse pour extraire les données structurées si possible
            $parsedData = $this->parseApiResponse($responseText, $context);

            return [
                'message' => $responseText,
                'suggestions' => $parsedData['suggestions'] ?? null,
                'context' => [
                    'step' => $parsedData['step'] ?? $context['step'],
                    'data' => $parsedData['data'] ?? $context['data']
                ]
            ];
        } catch (\Exception $e) {
            // En cas d'erreur API, fallback sur le système basé sur règles
            return $this->generateResponse($context, end($messages)['content']);
        }
    }

    /**
     * Parse la réponse de l'API pour extraire des données structurées
     */
    private function parseApiResponse(string $response, array $context): array
    {
        $data = $context['data'] ?? [];
        $step = $context['step'];
        $suggestions = null;

        // Détecter si toutes les informations sont collectées
        if (preg_match('/(structure peut être générée|prêt à générer|toutes les informations)/i', $response)) {
            $step = self::STEP_COMPLETED;
        }

        return [
            'step' => $step,
            'data' => $data,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Génère une réponse intelligente basée sur le contexte et le message
     */
    private function generateResponse(array $context, string $userMessage): array
    {
        $step = $context['step'];
        $data = $context['data'];

        switch ($step) {
            case self::STEP_WELCOME:
                return $this->handleProjectType($userMessage, $data);

            case self::STEP_PROJECT_TYPE:
                return $this->handleProjectName($userMessage, $data);

            case self::STEP_PROJECT_NAME:
                return $this->handleProjectDescription($userMessage, $data);

            case self::STEP_PROJECT_DESCRIPTION:
                return $this->handleDuration($userMessage, $data);

            case self::STEP_DURATION:
                return $this->handleMilestones($userMessage, $data);

            case self::STEP_MILESTONES:
                return $this->handleGroups($userMessage, $data);

            case self::STEP_GROUPS:
                return $this->handleDeliverables($userMessage, $data);

            case self::STEP_DELIVERABLES:
                return $this->handleConfirmation($userMessage, $data);

            case self::STEP_CONFIRMATION:
                return $this->handleCompletion($userMessage, $data);

            default:
                return [
                    'message' => "Je ne comprends pas où nous en sommes. Recommençons depuis le début.",
                    'context' => ['step' => self::STEP_WELCOME, 'data' => []]
                ];
        }
    }

    /**
     * Gère le choix du type de projet
     */
    private function handleProjectType(string $message, array $data): array
    {
        $message = strtolower($message);

        // Détecter le type de projet
        $projectType = 'custom';
        if (strpos($message, 'humanitaire') !== false || strpos($message, 'humanitarian') !== false) {
            $projectType = 'humanitarian';
        } elseif (strpos($message, 'environnement') !== false || strpos($message, 'climat') !== false || strpos($message, 'environment') !== false) {
            $projectType = 'environment';
        } elseif (strpos($message, 'éducation') !== false || strpos($message, 'education') !== false || strpos($message, 'école') !== false) {
            $projectType = 'education';
        } elseif (strpos($message, 'santé') !== false || strpos($message, 'health') !== false || strpos($message, 'médical') !== false) {
            $projectType = 'health';
        } elseif (strpos($message, 'développement') !== false || strpos($message, 'development') !== false) {
            $projectType = 'development';
        } elseif (strpos($message, 'plaidoyer') !== false || strpos($message, 'advocacy') !== false) {
            $projectType = 'advocacy';
        }

        $data['project_type'] = $projectType;

        return [
            'message' => "Excellent ! Vous avez choisi un projet de type « " . $this->getProjectTypeName($projectType) . " ».\n\nMaintenant, quel nom souhaitez-vous donner à votre projet ?",
            'context' => [
                'step' => self::STEP_PROJECT_TYPE,
                'data' => $data
            ]
        ];
    }

    /**
     * Gère le nom du projet
     */
    private function handleProjectName(string $message, array $data): array
    {
        $data['project_name'] = trim($message);

        return [
            'message' => "Parfait ! Le projet s'appellera « " . $data['project_name'] . " ».\n\nPouvez-vous me donner une brève description de ce projet ? (Objectifs principaux, contexte, etc.)",
            'context' => [
                'step' => self::STEP_PROJECT_NAME,
                'data' => $data
            ]
        ];
    }

    /**
     * Gère la description du projet
     */
    private function handleProjectDescription(string $message, array $data): array
    {
        $data['project_description'] = trim($message);

        return [
            'message' => "Merci pour ces précisions !\n\nQuelle est la durée prévue de votre projet ? Indiquez la période (exemple : 6 mois, 1 an, 18 mois, etc.)",
            'suggestions' => ['3 mois', '6 mois', '1 an', '18 mois', '2 ans'],
            'context' => [
                'step' => self::STEP_PROJECT_DESCRIPTION,
                'data' => $data
            ]
        ];
    }

    /**
     * Gère la durée du projet
     */
    private function handleDuration(string $message, array $data): array
    {
        $data['duration'] = trim($message);

        $projectType = $data['project_type'] ?? 'custom';
        $suggestions = self::PROJECT_TYPES[$projectType]['milestones'];

        return [
            'message' => "Compris ! Le projet durera " . $data['duration'] . ".\n\nMaintenant, parlons des jalons (milestones) importants. Les jalons sont les étapes clés de votre projet.\n\nVoici des suggestions basées sur votre type de projet. Vous pouvez les accepter, les modifier, ou proposer les vôtres :\n\n" .
                         implode("\n", array_map(fn($m, $i) => ($i+1) . ". " . $m, $suggestions, array_keys($suggestions))) .
                         "\n\nRépondez « OK » pour accepter ces jalons, ou proposez vos propres jalons séparés par des virgules.",
            'suggestions' => ['OK', 'Je propose mes propres jalons'],
            'context' => [
                'step' => self::STEP_DURATION,
                'data' => $data
            ]
        ];
    }

    /**
     * Gère les jalons du projet
     */
    private function handleMilestones(string $message, array $data): array
    {
        $message = trim($message);
        $projectType = $data['project_type'] ?? 'custom';

        if (strtolower($message) === 'ok' || strtolower($message) === 'oui') {
            $data['milestones'] = self::PROJECT_TYPES[$projectType]['milestones'];
        } else {
            // Parser les jalons proposés par l'utilisateur
            $milestones = array_map('trim', explode(',', $message));
            $data['milestones'] = array_filter($milestones);
        }

        $suggestions = self::PROJECT_TYPES[$projectType]['groups'];

        return [
            'message' => "Parfait ! J'ai noté " . count($data['milestones']) . " jalons.\n\nPassons maintenant à l'organisation de votre équipe. Les groupes vous permettent d'organiser les tâches par thématique ou par équipe de travail.\n\nVoici des suggestions de groupes pour votre projet :\n\n" .
                         implode("\n", array_map(fn($g, $i) => ($i+1) . ". " . $g, $suggestions, array_keys($suggestions))) .
                         "\n\nRépondez « OK » pour accepter ces groupes, ou proposez vos propres groupes séparés par des virgules.",
            'suggestions' => ['OK', 'Je propose mes propres groupes'],
            'context' => [
                'step' => self::STEP_MILESTONES,
                'data' => $data
            ]
        ];
    }

    /**
     * Gère les groupes du projet
     */
    private function handleGroups(string $message, array $data): array
    {
        $message = trim($message);
        $projectType = $data['project_type'] ?? 'custom';

        if (strtolower($message) === 'ok' || strtolower($message) === 'oui') {
            $data['groups'] = self::PROJECT_TYPES[$projectType]['groups'];
        } else {
            // Parser les groupes proposés par l'utilisateur
            $groups = array_map('trim', explode(',', $message));
            $data['groups'] = array_filter($groups);
        }

        $suggestions = self::PROJECT_TYPES[$projectType]['deliverables'];

        return [
            'message' => "Excellent ! J'ai créé " . count($data['groups']) . " groupes de travail.\n\nPour finir, quels sont les principaux livrables (outputs/deliverables) attendus de ce projet ?\n\nVoici quelques suggestions :\n\n" .
                         implode("\n", array_map(fn($d, $i) => ($i+1) . ". " . $d, $suggestions, array_keys($suggestions))) .
                         "\n\nRépondez « OK » pour accepter ces livrables, ou proposez vos propres livrables séparés par des virgules.",
            'suggestions' => ['OK', 'Je propose mes propres livrables'],
            'context' => [
                'step' => self::STEP_GROUPS,
                'data' => $data
            ]
        ];
    }

    /**
     * Gère les livrables du projet
     */
    private function handleDeliverables(string $message, array $data): array
    {
        $message = trim($message);
        $projectType = $data['project_type'] ?? 'custom';

        if (strtolower($message) === 'ok' || strtolower($message) === 'oui') {
            $data['deliverables'] = self::PROJECT_TYPES[$projectType]['deliverables'];
        } else {
            // Parser les livrables proposés par l'utilisateur
            $deliverables = array_map('trim', explode(',', $message));
            $data['deliverables'] = array_filter($deliverables);
        }

        // Générer un résumé
        $summary = $this->generateSummary($data);

        return [
            'message' => "Parfait ! J'ai toutes les informations nécessaires.\n\n📋 **Résumé de votre projet :**\n\n" . $summary .
                         "\n\nEst-ce que ce résumé vous convient ? Répondez « Oui » pour générer la structure, ou « Modifier » si vous voulez changer quelque chose.",
            'suggestions' => ['Oui, générer la structure', 'Modifier quelque chose'],
            'context' => [
                'step' => self::STEP_DELIVERABLES,
                'data' => $data
            ]
        ];
    }

    /**
     * Gère la confirmation
     */
    private function handleConfirmation(string $message, array $data): array
    {
        $message = strtolower(trim($message));

        if (strpos($message, 'oui') !== false || strpos($message, 'générer') !== false || strpos($message, 'ok') !== false) {
            return [
                'message' => "✅ Excellent ! Vous pouvez maintenant cliquer sur le bouton « Générer la structure » pour créer automatiquement les groupes, jalons et tâches de votre projet.\n\nL'assistant a terminé la collecte d'informations. Merci et bon projet !",
                'context' => [
                    'step' => self::STEP_COMPLETED,
                    'data' => $data
                ]
            ];
        } else {
            return [
                'message' => "D'accord, que souhaitez-vous modifier ? Dites-moi ce que vous voulez changer (nom, description, durée, jalons, groupes, ou livrables).",
                'suggestions' => ['Nom du projet', 'Description', 'Durée', 'Jalons', 'Groupes', 'Livrables'],
                'context' => [
                    'step' => self::STEP_CONFIRMATION,
                    'data' => $data
                ]
            ];
        }
    }

    /**
     * Gère la finalisation
     */
    private function handleCompletion(string $message, array $data): array
    {
        return [
            'message' => "La structure de votre projet est prête ! Utilisez le bouton « Générer la structure » pour l'appliquer.",
            'context' => [
                'step' => self::STEP_COMPLETED,
                'data' => $data
            ]
        ];
    }

    /**
     * Génère un résumé du projet
     */
    private function generateSummary(array $data): string
    {
        $summary = "**Nom :** " . ($data['project_name'] ?? 'Non défini') . "\n";
        $summary .= "**Type :** " . $this->getProjectTypeName($data['project_type'] ?? 'custom') . "\n";
        $summary .= "**Description :** " . ($data['project_description'] ?? 'Non définie') . "\n";
        $summary .= "**Durée :** " . ($data['duration'] ?? 'Non définie') . "\n\n";

        $summary .= "**Jalons (" . count($data['milestones'] ?? []) . ") :**\n";
        foreach ($data['milestones'] ?? [] as $i => $milestone) {
            $summary .= "  " . ($i+1) . ". " . $milestone . "\n";
        }

        $summary .= "\n**Groupes de travail (" . count($data['groups'] ?? []) . ") :**\n";
        foreach ($data['groups'] ?? [] as $i => $group) {
            $summary .= "  " . ($i+1) . ". " . $group . "\n";
        }

        $summary .= "\n**Livrables (" . count($data['deliverables'] ?? []) . ") :**\n";
        foreach ($data['deliverables'] ?? [] as $i => $deliverable) {
            $summary .= "  " . ($i+1) . ". " . $deliverable . "\n";
        }

        return $summary;
    }

    /**
     * Retourne le nom lisible du type de projet
     */
    private function getProjectTypeName(string $type): string
    {
        $names = [
            'humanitarian' => 'Action humanitaire',
            'environment' => 'Environnement et climat',
            'education' => 'Éducation',
            'health' => 'Santé',
            'development' => 'Développement local',
            'advocacy' => 'Plaidoyer et advocacy',
            'custom' => 'Projet personnalisé'
        ];

        return $names[$type] ?? 'Autre';
    }

    /**
     * Génère la structure du projet (groupes, jalons, tâches)
     */
    public function generateProjectStructure(int $conversationId, int $projectId): array
    {
        $conversation = $this->getConversation($conversationId);
        $context = json_decode($conversation['context'], true);
        $data = $context['data'] ?? [];

        if (empty($data)) {
            throw new \RuntimeException('No data available to generate structure');
        }

        $structure = [
            'groups_created' => 0,
            'milestones_created' => 0,
            'tasks_created' => 0
        ];

        $groupIds = [];
        $milestoneIds = [];

        // Créer les groupes dans la base de données
        foreach ($data['groups'] ?? [] as $groupName) {
            $stmt = $this->db->prepare(
                "INSERT INTO groups (project_id, name, description, color)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $projectId,
                $groupName,
                'Groupe créé automatiquement par l\'assistant IA',
                $this->getRandomColor()
            ]);
            $groupIds[] = (int)$this->db->lastInsertId();
            $structure['groups_created']++;
        }

        // Créer les jalons dans la base de données
        $milestoneCount = count($data['milestones'] ?? []);
        foreach ($data['milestones'] ?? [] as $i => $milestoneName) {
            // Calculer une date approximative pour chaque jalon
            $daysOffset = (int)(($i + 1) / $milestoneCount * 180); // Répartir sur 6 mois
            $milestoneDate = date('Y-m-d', strtotime("+{$daysOffset} days"));

            $stmt = $this->db->prepare(
                "INSERT INTO milestones (project_id, name, date, status)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $projectId,
                $milestoneName,
                $milestoneDate,
                'active'
            ]);
            $milestoneIds[] = (int)$this->db->lastInsertId();
            $structure['milestones_created']++;
        }

        // Créer des tâches pour chaque livrable
        foreach ($data['deliverables'] ?? [] as $i => $deliverable) {
            $groupId = !empty($groupIds) ? $groupIds[$i % count($groupIds)] : null;
            $milestoneId = !empty($milestoneIds) ? $milestoneIds[$i % count($milestoneIds)] : null;

            $stmt = $this->db->prepare(
                "INSERT INTO tasks (project_id, group_id, milestone_id, title, desc, status, priority)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $projectId,
                $groupId,
                $milestoneId,
                $deliverable,
                'Tâche créée automatiquement par l\'assistant IA',
                'todo',
                'medium'
            ]);
            $structure['tasks_created']++;
        }

        // Marquer la conversation comme complétée
        $stmt = $this->db->prepare(
            "UPDATE ai_conversations SET status = 'completed' WHERE id = ?"
        );
        $stmt->execute([$conversationId]);

        return $structure;
    }

    /**
     * Récupère une conversation
     */
    public function getConversation(int $conversationId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM ai_conversations WHERE id = ?");
        $stmt->execute([$conversationId]);
        $result = $stmt->fetch();

        if (!$result) {
            throw new \RuntimeException('Conversation not found');
        }

        return $result;
    }

    /**
     * Vérifie que la conversation appartient à l'équipe
     */
    public function verifyConversationOwnership(int $conversationId, int $teamId): bool
    {
        $stmt = $this->db->prepare("SELECT team_id FROM ai_conversations WHERE id = ?");
        $stmt->execute([$conversationId]);
        $result = $stmt->fetch();

        return $result && (int)$result['team_id'] === $teamId;
    }

    /**
     * Met à jour une conversation
     */
    private function updateConversation(int $conversationId, array $messages, array $context): void
    {
        $stmt = $this->db->prepare(
            "UPDATE ai_conversations
             SET messages = ?, context = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ?"
        );
        $stmt->execute([json_encode($messages), json_encode($context), $conversationId]);
    }

    /**
     * Liste toutes les conversations d'une équipe
     */
    public function listConversations(int $teamId): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, project_id, status, created_at, updated_at
             FROM ai_conversations
             WHERE team_id = ?
             ORDER BY updated_at DESC"
        );
        $stmt->execute([$teamId]);

        return $stmt->fetchAll();
    }

    /**
     * Supprime une conversation
     */
    public function deleteConversation(int $conversationId): void
    {
        $stmt = $this->db->prepare("DELETE FROM ai_conversations WHERE id = ?");
        $stmt->execute([$conversationId]);
    }

    /**
     * Retourne une couleur aléatoire pour les groupes
     */
    private function getRandomColor(): string
    {
        $colors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'];
        return $colors[array_rand($colors)];
    }
}
