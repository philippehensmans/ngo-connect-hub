<?php
/**
 * Script pour ajouter un projet de démonstration complet
 * Campagne de sensibilisation sur la situation des Roms en Belgique
 *
 * Usage: php scripts/add_demo_project.php
 */

require_once __DIR__ . '/../index.php';

use App\Services\Database;
use App\Models\Project;
use App\Models\Group;
use App\Models\Milestone;
use App\Models\Task;

// Charger la configuration
$config = require __DIR__ . '/../config/config.php';

// Créer la connexion à la base de données
$dbService = new Database($config);
$db = $dbService->getConnection();

// Récupérer le premier team_id disponible
$teamId = $db->query("SELECT id FROM teams LIMIT 1")->fetchColumn();

if (!$teamId) {
    echo "❌ Erreur: Aucune équipe trouvée. Veuillez d'abord créer une équipe.\n";
    exit(1);
}

echo "📋 Création du projet de démonstration...\n\n";

// 1. Créer le projet principal
$projectModel = new Project($db);
$projectId = $projectModel->create([
    'team_id' => $teamId,
    'name' => 'Campagne de sensibilisation - Situation des Roms en Belgique',
    'desc' => 'Projet visant à sensibiliser le public belge sur les conditions de vie et les défis rencontrés par la communauté Rom en Belgique',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+6 months')),
    'status' => 'active'
]);

echo "✅ Projet créé (ID: $projectId)\n";

// 2. Créer les groupes (phases du projet)
$groupModel = new Group($db);

$groups = [
    'research' => $groupModel->create([
        'project_id' => $projectId,
        'name' => '🔍 Phase de recherche',
        'color' => '#3B82F6'
    ]),
    'legal' => $groupModel->create([
        'project_id' => $projectId,
        'name' => '⚖️ Analyse juridique',
        'color' => '#8B5CF6'
    ]),
    'content' => $groupModel->create([
        'project_id' => $projectId,
        'name' => '📝 Création de contenu',
        'color' => '#10B981'
    ]),
    'materials' => $groupModel->create([
        'project_id' => $projectId,
        'name' => '🎨 Matériel de campagne',
        'color' => '#F59E0B'
    ]),
    'digital' => $groupModel->create([
        'project_id' => $projectId,
        'name' => '💻 Présence digitale',
        'color' => '#EC4899'
    ]),
    'launch' => $groupModel->create([
        'project_id' => $projectId,
        'name' => '🚀 Lancement et actions',
        'color' => '#EF4444'
    ])
];

echo "✅ 6 groupes créés\n";

// 3. Créer les jalons
$milestoneModel = new Milestone($db);

$milestones = [
    'research_done' => $milestoneModel->create([
        'project_id' => $projectId,
        'name' => 'Recherche terminée',
        'date' => date('Y-m-d', strtotime('+1 month')),
        'status' => 'active'
    ]),
    'content_ready' => $milestoneModel->create([
        'project_id' => $projectId,
        'name' => 'Contenu et rapports prêts',
        'date' => date('Y-m-d', strtotime('+2 months')),
        'status' => 'active'
    ]),
    'materials_ready' => $milestoneModel->create([
        'project_id' => $projectId,
        'name' => 'Matériel de campagne finalisé',
        'date' => date('Y-m-d', strtotime('+3 months')),
        'status' => 'active'
    ]),
    'pre_launch' => $milestoneModel->create([
        'project_id' => $projectId,
        'name' => 'Pré-lancement campagne',
        'date' => date('Y-m-d', strtotime('+4 months')),
        'status' => 'active'
    ]),
    'campaign_launch' => $milestoneModel->create([
        'project_id' => $projectId,
        'name' => 'Lancement officiel de la campagne',
        'date' => date('Y-m-d', strtotime('+4 months 1 week')),
        'status' => 'active'
    ]),
    'evaluation' => $milestoneModel->create([
        'project_id' => $projectId,
        'name' => 'Évaluation finale',
        'date' => date('Y-m-d', strtotime('+6 months')),
        'status' => 'active'
    ])
];

echo "✅ 6 jalons créés\n";

// 4. Créer les tâches
$taskModel = new Task($db);

$tasks = [
    // Phase de recherche
    [
        'group' => 'research',
        'milestone' => 'research_done',
        'title' => 'Étude de terrain - Visites communautaires',
        'desc' => 'Organiser des visites dans les communautés Roms de Bruxelles, Anvers et Liège pour comprendre leurs conditions de vie',
        'start_date' => date('Y-m-d', strtotime('+1 week')),
        'end_date' => date('Y-m-d', strtotime('+3 weeks')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'research',
        'milestone' => 'research_done',
        'title' => 'Interviews avec les familles Roms',
        'desc' => 'Conduire des entretiens qualitatifs avec 20-30 familles pour recueillir leurs témoignages',
        'start_date' => date('Y-m-d', strtotime('+2 weeks')),
        'end_date' => date('Y-m-d', strtotime('+4 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'research',
        'milestone' => 'research_done',
        'title' => 'Collecte de données statistiques',
        'desc' => 'Rassembler les données officielles sur l\'emploi, l\'éducation, le logement et la santé',
        'start_date' => date('Y-m-d', strtotime('+1 week')),
        'end_date' => date('Y-m-d', strtotime('+3 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'research',
        'milestone' => 'research_done',
        'title' => 'Analyse des besoins et problématiques',
        'desc' => 'Synthétiser les données collectées et identifier les enjeux prioritaires',
        'start_date' => date('Y-m-d', strtotime('+4 weeks')),
        'end_date' => date('Y-m-d', strtotime('+5 weeks')),
        'status' => 'todo'
    ],

    // Analyse juridique
    [
        'group' => 'legal',
        'milestone' => 'research_done',
        'title' => 'Examen de la législation belge',
        'desc' => 'Analyser les lois belges relatives aux minorités, logement, éducation et non-discrimination',
        'start_date' => date('Y-m-d', strtotime('+1 week')),
        'end_date' => date('Y-m-d', strtotime('+3 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'legal',
        'milestone' => 'research_done',
        'title' => 'Étude des directives européennes',
        'desc' => 'Examiner les directives de l\'UE sur l\'inclusion des Roms et leur application en Belgique',
        'start_date' => date('Y-m-d', strtotime('+2 weeks')),
        'end_date' => date('Y-m-d', strtotime('+4 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'legal',
        'milestone' => 'content_ready',
        'title' => 'Consultation avec autres ONG',
        'desc' => 'Rencontrer Amnesty, ENAR, et autres ONG pour recueillir leurs recommandations',
        'start_date' => date('Y-m-d', strtotime('+3 weeks')),
        'end_date' => date('Y-m-d', strtotime('+5 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'legal',
        'milestone' => 'content_ready',
        'title' => 'Rédaction des recommandations légales',
        'desc' => 'Formuler des recommandations concrètes pour améliorer le cadre législatif',
        'start_date' => date('Y-m-d', strtotime('+5 weeks')),
        'end_date' => date('Y-m-d', strtotime('+7 weeks')),
        'status' => 'todo',
        'priority' => 'high'
    ],

    // Création de contenu
    [
        'group' => 'content',
        'milestone' => 'content_ready',
        'title' => 'Rédaction du rapport principal',
        'desc' => 'Créer un rapport de 40-50 pages sur la situation des Roms en Belgique',
        'start_date' => date('Y-m-d', strtotime('+5 weeks')),
        'end_date' => date('Y-m-d', strtotime('+8 weeks')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'content',
        'milestone' => 'content_ready',
        'title' => 'Création d\'une synthèse executive',
        'desc' => 'Résumé de 5 pages pour les décideurs politiques et médias',
        'start_date' => date('Y-m-d', strtotime('+8 weeks')),
        'end_date' => date('Y-m-d', strtotime('+9 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'content',
        'milestone' => 'content_ready',
        'title' => 'Fiches thématiques',
        'desc' => 'Créer 5 fiches sur l\'emploi, éducation, logement, santé, discrimination',
        'start_date' => date('Y-m-d', strtotime('+7 weeks')),
        'end_date' => date('Y-m-d', strtotime('+9 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'content',
        'milestone' => 'content_ready',
        'title' => 'Recueil de témoignages',
        'desc' => 'Compiler et anonymiser 15-20 témoignages pour publication',
        'start_date' => date('Y-m-d', strtotime('+6 weeks')),
        'end_date' => date('Y-m-d', strtotime('+8 weeks')),
        'status' => 'todo'
    ],

    // Matériel de campagne
    [
        'group' => 'materials',
        'milestone' => 'materials_ready',
        'title' => 'Design de l\'identité visuelle',
        'desc' => 'Créer le logo, charte graphique et visuels de la campagne',
        'start_date' => date('Y-m-d', strtotime('+8 weeks')),
        'end_date' => date('Y-m-d', strtotime('+10 weeks')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'materials',
        'milestone' => 'materials_ready',
        'title' => 'Création des affiches',
        'desc' => 'Designer 3 modèles d\'affiches A3 pour l\'affichage urbain',
        'start_date' => date('Y-m-d', strtotime('+10 weeks')),
        'end_date' => date('Y-m-d', strtotime('+11 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'materials',
        'milestone' => 'materials_ready',
        'title' => 'Production de dépliants',
        'desc' => 'Concevoir et imprimer 5000 dépliants informatifs',
        'start_date' => date('Y-m-d', strtotime('+10 weeks')),
        'end_date' => date('Y-m-d', strtotime('+12 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'materials',
        'milestone' => 'materials_ready',
        'title' => 'Vidéo de sensibilisation',
        'desc' => 'Produire une vidéo de 3-5 minutes avec témoignages (FR/NL sous-titré)',
        'start_date' => date('Y-m-d', strtotime('+9 weeks')),
        'end_date' => date('Y-m-d', strtotime('+12 weeks')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'materials',
        'milestone' => 'materials_ready',
        'title' => 'Infographies pour réseaux sociaux',
        'desc' => 'Créer 10 infographies percutantes avec chiffres clés',
        'start_date' => date('Y-m-d', strtotime('+11 weeks')),
        'end_date' => date('Y-m-d', strtotime('+12 weeks')),
        'status' => 'todo'
    ],

    // Présence digitale
    [
        'group' => 'digital',
        'milestone' => 'pre_launch',
        'title' => 'Création du site web de campagne',
        'desc' => 'Développer un site web bilingue avec toutes les ressources',
        'start_date' => date('Y-m-d', strtotime('+10 weeks')),
        'end_date' => date('Y-m-d', strtotime('+14 weeks')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'digital',
        'milestone' => 'pre_launch',
        'title' => 'Mise en place pages réseaux sociaux',
        'desc' => 'Créer/optimiser présence sur Facebook, Twitter, Instagram, LinkedIn',
        'start_date' => date('Y-m-d', strtotime('+12 weeks')),
        'end_date' => date('Y-m-d', strtotime('+13 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'digital',
        'milestone' => 'pre_launch',
        'title' => 'Calendrier éditorial réseaux sociaux',
        'desc' => 'Planifier 3 mois de publications (stories, posts, vidéos)',
        'start_date' => date('Y-m-d', strtotime('+13 weeks')),
        'end_date' => date('Y-m-d', strtotime('+14 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'digital',
        'milestone' => 'campaign_launch',
        'title' => 'Campagne publicitaire Facebook/Instagram',
        'desc' => 'Lancer des ads ciblés sur 3 semaines (budget: 2000€)',
        'start_date' => date('Y-m-d', strtotime('+16 weeks')),
        'end_date' => date('Y-m-d', strtotime('+19 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'digital',
        'milestone' => null,
        'title' => 'Newsletter mensuelle',
        'desc' => 'Envoyer une newsletter à nos 5000 contacts chaque mois',
        'start_date' => date('Y-m-d', strtotime('+15 weeks')),
        'end_date' => date('Y-m-d', strtotime('+24 weeks')),
        'status' => 'todo'
    ],

    // Lancement et actions
    [
        'group' => 'launch',
        'milestone' => 'pre_launch',
        'title' => 'Conférence de presse pré-lancement',
        'desc' => 'Organiser une conf de presse pour annoncer la campagne',
        'start_date' => date('Y-m-d', strtotime('+15 weeks')),
        'end_date' => date('Y-m-d', strtotime('+15 weeks 3 days')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'launch',
        'milestone' => 'campaign_launch',
        'title' => 'Événement de lancement public',
        'desc' => 'Organiser un événement public à Bruxelles avec témoignages et concert',
        'start_date' => date('Y-m-d', strtotime('+16 weeks')),
        'end_date' => date('Y-m-d', strtotime('+16 weeks 1 day')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'launch',
        'milestone' => 'campaign_launch',
        'title' => 'Actions de rue - Distribution',
        'desc' => 'Organiser 5 actions de rue dans les grandes villes (distribution, stands)',
        'start_date' => date('Y-m-d', strtotime('+16 weeks')),
        'end_date' => date('Y-m-d', strtotime('+20 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'launch',
        'milestone' => 'campaign_launch',
        'title' => 'Projection-débats dans les universités',
        'desc' => 'Organiser 4 soirées projection-débat (ULB, UCL, UGent, KUL)',
        'start_date' => date('Y-m-d', strtotime('+17 weeks')),
        'end_date' => date('Y-m-d', strtotime('+21 weeks')),
        'status' => 'todo'
    ],
    [
        'group' => 'launch',
        'milestone' => 'campaign_launch',
        'title' => 'Rencontre avec responsables politiques',
        'desc' => 'Présenter nos recommandations aux députés et ministres concernés',
        'start_date' => date('Y-m-d', strtotime('+17 weeks')),
        'end_date' => date('Y-m-d', strtotime('+20 weeks')),
        'status' => 'todo',
        'priority' => 'high'
    ],
    [
        'group' => 'launch',
        'milestone' => 'evaluation',
        'title' => 'Rapport d\'impact de la campagne',
        'desc' => 'Évaluer la portée (médias, réseaux sociaux, événements) et les résultats',
        'start_date' => date('Y-m-d', strtotime('+23 weeks')),
        'end_date' => date('Y-m-d', strtotime('+24 weeks')),
        'status' => 'todo'
    ]
];

// Insérer toutes les tâches
$taskCount = 0;
foreach ($tasks as $taskData) {
    $taskModel->create([
        'project_id' => $projectId,
        'group_id' => $groups[$taskData['group']],
        'milestone_id' => isset($taskData['milestone']) && $taskData['milestone'] ? $milestones[$taskData['milestone']] : null,
        'title' => $taskData['title'],
        'desc' => $taskData['desc'],
        'start_date' => $taskData['start_date'],
        'end_date' => $taskData['end_date'],
        'status' => $taskData['status'],
        'priority' => $taskData['priority'] ?? 'medium'
    ]);
    $taskCount++;
}

echo "✅ $taskCount tâches créées\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎉 Projet de démonstration créé avec succès!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "📊 Résumé:\n";
echo "   • 1 projet principal\n";
echo "   • 6 groupes (phases)\n";
echo "   • 6 jalons\n";
echo "   • $taskCount tâches détaillées\n\n";
echo "💡 Connectez-vous à l'application pour voir le projet!\n\n";
