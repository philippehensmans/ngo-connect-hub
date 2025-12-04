<?php
/**
 * Script de test pour vérifier que l'API Claude fonctionne
 */

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Charger l'autoloader
require __DIR__ . '/vendor/autoload.php';

use App\Services\Database;
use App\Services\AssistantService;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test API Claude</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        pre { background: #f3f4f6; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 Test API Claude</h1>

    <div class="section">
        <h2>1. Connexion à la base de données</h2>
        <?php
        try {
            $config = require __DIR__ . '/config/config.php';
            $database = new Database($config);
            $db = $database->getConnection();
            echo '<p class="success">✓ Connexion réussie</p>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
            die();
        }
        ?>
    </div>

    <div class="section">
        <h2>2. Récupération de la configuration AI</h2>
        <?php
        try {
            $stmt = $db->prepare("SELECT id, name, ai_use_api, ai_api_provider, ai_api_key, ai_api_model FROM teams LIMIT 1");
            $stmt->execute();
            $team = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$team) {
                echo '<p class="error">✗ Aucune équipe trouvée</p>';
                die();
            }

            echo '<p>Équipe : <strong>' . htmlspecialchars($team['name']) . '</strong> (ID: ' . $team['id'] . ')</p>';
            echo '<ul>';
            echo '<li>ai_use_api : ' . ($team['ai_use_api'] ? '<span class="success">✓ Activé</span>' : '<span class="error">✗ Désactivé</span>') . '</li>';
            echo '<li>ai_api_provider : ' . htmlspecialchars($team['ai_api_provider']) . '</li>';
            echo '<li>ai_api_key : ' . (empty($team['ai_api_key']) ? '<span class="error">✗ Vide</span>' : '<span class="success">✓ Présente (' . substr($team['ai_api_key'], 0, 20) . '...)</span>') . '</li>';
            echo '<li>ai_api_model : ' . htmlspecialchars($team['ai_api_model']) . '</li>';
            echo '</ul>';

            $teamId = $team['id'];
        } catch (Exception $e) {
            echo '<p class="error">✗ Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
            die();
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Test du service Assistant</h2>
        <?php
        try {
            $assistantService = new AssistantService($db);
            echo '<p class="success">✓ Service Assistant créé</p>';

            // Créer une conversation de test
            $conversationId = $assistantService->startConversation($teamId, null);
            echo '<p class="success">✓ Conversation créée (ID: ' . $conversationId . ')</p>';

            // Obtenir le message initial
            echo '<h3>Message initial :</h3>';
            $initialMessage = $assistantService->getInitialMessage();
            echo '<pre>' . htmlspecialchars(print_r($initialMessage, true)) . '</pre>';

            echo '<div style="background: #e0f2fe; padding: 15px; border-left: 4px solid #0284c7; margin: 10px 0;">';
            echo '<strong>Message affiché :</strong><br>';
            echo nl2br(htmlspecialchars($initialMessage['content']));
            echo '</div>';

            // Analyser le message pour voir s'il vient de l'API ou des règles
            if (strpos($initialMessage['content'], 'Bonjour ! Je suis votre assistant de planification de projet') !== false) {
                echo '<p class="warning">⚠ Ce message semble être le message par défaut (mode règles)</p>';
            } else {
                echo '<p class="success">✓ Ce message semble être généré par l\'API (différent du message par défaut)</p>';
            }

        } catch (Exception $e) {
            echo '<p class="error">✗ Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Test direct de l'API</h2>
        <?php
        if ($team['ai_use_api'] && !empty($team['ai_api_key'])) {
            try {
                echo '<p>Tentative d\'appel direct à l\'API Claude...</p>';

                $apiKey = $team['ai_api_key'];
                $model = $team['ai_api_model'] ?: 'claude-3-5-sonnet-20241022';

                $data = [
                    'model' => $model,
                    'max_tokens' => 1024,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Dis bonjour en une phrase courte.'
                        ]
                    ]
                ];

                $ch = curl_init('https://api.anthropic.com/v1/messages');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'x-api-key: ' . $apiKey,
                    'anthropic-version: 2023-06-01'
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                echo '<p>Code HTTP : <strong>' . $httpCode . '</strong></p>';

                if ($curlError) {
                    echo '<p class="error">✗ Erreur cURL : ' . htmlspecialchars($curlError) . '</p>';
                } elseif ($httpCode === 200) {
                    echo '<p class="success">✓ Appel API réussi !</p>';
                    $result = json_decode($response, true);
                    echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                } else {
                    echo '<p class="error">✗ Échec de l\'appel API</p>';
                    echo '<pre>' . htmlspecialchars($response) . '</pre>';
                }

            } catch (Exception $e) {
                echo '<p class="error">✗ Exception : ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        } else {
            echo '<p class="warning">⚠ API non configurée, test ignoré</p>';
        }
        ?>
    </div>

    <div class="section">
        <p><em>Note : Ce script teste directement l'intégration de l'API Claude dans votre application.</em></p>
    </div>

</body>
</html>
