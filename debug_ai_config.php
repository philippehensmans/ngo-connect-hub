<?php
/**
 * Script de diagnostic pour vérifier la configuration AI
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\Database;

$config = require __DIR__ . '/config/config.php';
$database = new Database($config);
$db = $database->getConnection();

echo "=== DIAGNOSTIC CONFIGURATION AI ===\n\n";

// 1. Vérifier la structure de la table teams
echo "1. Structure de la table 'teams':\n";
echo "-----------------------------------\n";
$result = $db->query("PRAGMA table_info(teams)")->fetchAll();
$columns = [];
foreach ($result as $col) {
    $columns[] = $col['name'];
    echo "  - {$col['name']} ({$col['type']})\n";
}
echo "\n";

// 2. Vérifier si les colonnes AI existent
echo "2. Colonnes AI présentes:\n";
echo "-------------------------\n";
$aiColumns = ['ai_use_api', 'ai_api_provider', 'ai_api_key', 'ai_api_model'];
foreach ($aiColumns as $col) {
    $exists = in_array($col, $columns) ? '✓ OUI' : '✗ NON';
    echo "  - $col: $exists\n";
}
echo "\n";

// 3. Lire la configuration actuelle
echo "3. Configuration AI actuelle:\n";
echo "-----------------------------\n";
try {
    $stmt = $db->prepare("SELECT id, name, ai_use_api, ai_api_provider, ai_api_key, ai_api_model FROM teams");
    $stmt->execute();
    $teams = $stmt->fetchAll();

    if (empty($teams)) {
        echo "  Aucune équipe trouvée.\n";
    } else {
        foreach ($teams as $team) {
            echo "  Équipe: {$team['name']} (ID: {$team['id']})\n";
            echo "    - ai_use_api: " . ($team['ai_use_api'] ?? 'NULL') . "\n";
            echo "    - ai_api_provider: " . ($team['ai_api_provider'] ?? 'NULL') . "\n";
            echo "    - ai_api_key: " . (empty($team['ai_api_key']) ? '(vide)' : substr($team['ai_api_key'], 0, 15) . '...') . "\n";
            echo "    - ai_api_model: " . ($team['ai_api_model'] ?? 'NULL') . "\n";
        }
    }
} catch (Exception $e) {
    echo "  ERREUR: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Test de la méthode initializeApiService
echo "4. Test de lecture de la configuration:\n";
echo "---------------------------------------\n";
try {
    $teamId = 1; // Supposons que l'équipe ID = 1
    $stmt = $db->prepare("SELECT ai_use_api, ai_api_provider, ai_api_key, ai_api_model FROM teams WHERE id = ?");
    $stmt->execute([$teamId]);
    $config = $stmt->fetch();

    if ($config) {
        echo "  Configuration lue pour l'équipe ID=$teamId:\n";
        echo "    - ai_use_api: " . var_export($config['ai_use_api'], true) . "\n";
        echo "    - ai_api_provider: " . var_export($config['ai_api_provider'], true) . "\n";
        echo "    - ai_api_key présente: " . (!empty($config['ai_api_key']) ? 'OUI' : 'NON') . "\n";
        echo "    - ai_api_model: " . var_export($config['ai_api_model'], true) . "\n";
        echo "\n";

        if ($config['ai_use_api'] && $config['ai_api_key']) {
            echo "  ✓ L'API DEVRAIT être utilisée\n";
        } else {
            echo "  ✗ L'API NE SERA PAS utilisée\n";
            if (!$config['ai_use_api']) {
                echo "    Raison: ai_use_api = " . var_export($config['ai_use_api'], true) . "\n";
            }
            if (!$config['ai_api_key']) {
                echo "    Raison: ai_api_key est vide\n";
            }
        }
    } else {
        echo "  ERREUR: Aucune configuration trouvée pour l'équipe ID=$teamId\n";
    }
} catch (Exception $e) {
    echo "  ERREUR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
