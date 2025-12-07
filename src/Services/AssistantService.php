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
    private Translation $translation;

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

    public function __construct(PDO $db, Translation $translation)
    {
        $this->db = $db;
        $this->translation = $translation;
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
        // Si l'API est activée, utiliser l'API pour générer le message de bienvenue
        if ($this->useApi && $this->apiService) {
            try {
                $systemPrompt = $this->translation->translate('assistant_api_prompt');

                $welcomeMessage = $this->apiService->sendMessage([], $systemPrompt);

                return [
                    'role' => 'assistant',
                    'content' => $welcomeMessage,
                    'suggestions' => [
                        $this->translation->translate('category_humanitarian'),
                        $this->translation->translate('category_environment'),
                        $this->translation->translate('category_education'),
                        $this->translation->translate('category_health'),
                        $this->translation->translate('category_development'),
                        $this->translation->translate('category_advocacy'),
                        $this->translation->translate('category_custom')
                    ]
                ];
            } catch (\Exception $e) {
                // En cas d'erreur API, fallback sur le message par défaut
            }
        }

        // Message par défaut (mode règles ou fallback si erreur API)
        return [
            'role' => 'assistant',
            'content' => $this->translation->translate('assistant_welcome_msg'),
            'suggestions' => [
                $this->translation->translate('category_humanitarian'),
                $this->translation->translate('category_environment'),
                $this->translation->translate('category_education'),
                $this->translation->translate('category_health'),
                $this->translation->translate('category_development'),
                $this->translation->translate('category_advocacy'),
                $this->translation->translate('category_custom')
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
                    'message' => $this->translation->translate('assistant_error_step'),
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
            'message' => sprintf($this->translation->translate('assistant_project_selected'), $this->getProjectTypeName($projectType)),
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
            'message' => sprintf($this->translation->translate('assistant_project_name_confirm'), $data['project_name']),
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
            'message' => $this->translation->translate('assistant_description_confirm'),
            'suggestions' => [
                $this->translation->translate('assistant_duration_3m'),
                $this->translation->translate('assistant_duration_6m'),
                $this->translation->translate('assistant_duration_1y'),
                $this->translation->translate('assistant_duration_18m'),
                $this->translation->translate('assistant_duration_2y')
            ],
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

        $milestonesList = implode("\n", array_map(fn($m, $i) => ($i+1) . ". " . $m, $suggestions, array_keys($suggestions)));

        return [
            'message' => sprintf($this->translation->translate('assistant_milestones_question'), $data['duration'], $milestonesList),
            'suggestions' => [
                $this->translation->translate('assistant_accept'),
                $this->translation->translate('assistant_custom_suggest')
            ],
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

        $groupsList = implode("\n", array_map(fn($g, $i) => ($i+1) . ". " . $g, $suggestions, array_keys($suggestions)));

        return [
            'message' => sprintf($this->translation->translate('assistant_groups_question'), count($data['milestones']), $groupsList),
            'suggestions' => [
                $this->translation->translate('assistant_accept'),
                $this->translation->translate('assistant_custom_groups')
            ],
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

        $deliverablesList = implode("\n", array_map(fn($d, $i) => ($i+1) . ". " . $d, $suggestions, array_keys($suggestions)));

        return [
            'message' => sprintf($this->translation->translate('assistant_deliverables_question'), count($data['groups']), $deliverablesList),
            'suggestions' => [
                $this->translation->translate('assistant_accept'),
                $this->translation->translate('assistant_custom_deliverables')
            ],
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
            'message' => sprintf($this->translation->translate('assistant_summary_confirm'), $summary),
            'suggestions' => [
                $this->translation->translate('assistant_confirm_yes'),
                $this->translation->translate('assistant_confirm_modify')
            ],
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

        if (strpos($message, 'oui') !== false || strpos($message, 'yes') !== false || strpos($message, 'sí') !== false || strpos($message, 'da') !== false || strpos($message, 'générer') !== false || strpos($message, 'generar') !== false || strpos($message, 'generate') !== false || strpos($message, 'ok') !== false) {
            return [
                'message' => $this->translation->translate('assistant_completed'),
                'context' => [
                    'step' => self::STEP_COMPLETED,
                    'data' => $data
                ]
            ];
        } else {
            return [
                'message' => $this->translation->translate('assistant_modify_what'),
                'suggestions' => [
                    $this->translation->translate('assistant_modify_name'),
                    $this->translation->translate('assistant_modify_description'),
                    $this->translation->translate('assistant_modify_duration'),
                    $this->translation->translate('assistant_modify_milestones'),
                    $this->translation->translate('assistant_modify_groups'),
                    $this->translation->translate('assistant_modify_deliverables')
                ],
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
        $summary = sprintf($this->translation->translate('assistant_summary_name'), $data['project_name'] ?? 'Non défini') . "\n";
        $summary .= sprintf($this->translation->translate('assistant_summary_type'), $this->getProjectTypeName($data['project_type'] ?? 'custom')) . "\n";
        $summary .= sprintf($this->translation->translate('assistant_summary_description'), $data['project_description'] ?? 'Non définie') . "\n";
        $summary .= sprintf($this->translation->translate('assistant_summary_duration'), $data['duration'] ?? 'Non définie') . "\n\n";

        $milestones = '';
        foreach ($data['milestones'] ?? [] as $i => $milestone) {
            $milestones .= ($i+1) . ". " . $milestone . ", ";
        }
        $summary .= sprintf($this->translation->translate('assistant_summary_milestones'), rtrim($milestones, ', ')) . "\n";

        $groups = '';
        foreach ($data['groups'] ?? [] as $i => $group) {
            $groups .= ($i+1) . ". " . $group . ", ";
        }
        $summary .= sprintf($this->translation->translate('assistant_summary_groups'), rtrim($groups, ', ')) . "\n";

        $deliverables = '';
        foreach ($data['deliverables'] ?? [] as $i => $deliverable) {
            $deliverables .= ($i+1) . ". " . $deliverable . ", ";
        }
        $summary .= sprintf($this->translation->translate('assistant_summary_deliverables'), rtrim($deliverables, ', ')) . "\n";

        return $summary;
    }

    /**
     * Retourne le nom lisible du type de projet
     */
    private function getProjectTypeName(string $type): string
    {
        $keys = [
            'humanitarian' => 'category_humanitarian',
            'environment' => 'category_environment',
            'education' => 'category_education',
            'health' => 'category_health',
            'development' => 'category_development',
            'advocacy' => 'category_advocacy',
            'custom' => 'category_custom'
        ];

        $key = $keys[$type] ?? 'category_custom';
        return $this->translation->translate($key);
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
