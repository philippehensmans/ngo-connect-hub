<?php
/**
 * Script de test pour vérifier que l'API Claude fonctionne
 */

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
        pre { background: #f3f4f6; padding: 10px; border-radius: 5px; overflow-x: auto; max-height: 400px; }
        h1 { color: #1f2937; }
        h2 { color: #374151; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🧪 Test API Claude</h1>

    <div class="section">
        <h2>1. Connexion à la base de données</h2>
        <?php
        // Trouver la base de données
        $possiblePaths = [
            __DIR__ . '/data/ong_manager.db',
            __DIR__ . '/data/ngo.db',
            __DIR__ . '/../data/ong_manager.db',
        ];

        $dbPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $dbPath = $path;
                break;
            }
        }

        if (!$dbPath) {
            echo '<p class="error">✗ Base de données introuvable</p>';
            die();
        }

        try {
            $db = new PDO('sqlite:' . $dbPath);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo '<p class="success">✓ Connexion réussie</p>';
            echo '<p>Base de données : <code>' . htmlspecialchars($dbPath) . '</code></p>';
        } catch (PDOException $e) {
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
            echo '<table style="width: 100%; border-collapse: collapse;">';
            echo '<tr><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>ai_use_api</strong></td><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">' . ($team['ai_use_api'] ? '<span class="success">✓ Activé (' . $team['ai_use_api'] . ')</span>' : '<span class="error">✗ Désactivé (' . $team['ai_use_api'] . ')</span>') . '</td></tr>';
            echo '<tr><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>ai_api_provider</strong></td><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($team['ai_api_provider']) . '</td></tr>';
            echo '<tr><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>ai_api_key</strong></td><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">' . (empty($team['ai_api_key']) ? '<span class="error">✗ Vide</span>' : '<span class="success">✓ Présente (' . substr($team['ai_api_key'], 0, 20) . '...)</span>') . '</td></tr>';
            echo '<tr><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>ai_api_model</strong></td><td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($team['ai_api_model']) . '</td></tr>';
            echo '</table>';

            $teamId = $team['id'];
        } catch (Exception $e) {
            echo '<p class="error">✗ Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
            die();
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Vérification des fichiers de service</h2>
        <?php
        $requiredFiles = [
            'src/Services/AssistantService.php',
            'src/Services/AIApiService.php',
            'src/Controllers/AssistantController.php'
        ];

        foreach ($requiredFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            if (file_exists($fullPath)) {
                echo '<p class="success">✓ ' . htmlspecialchars($file) . '</p>';
            } else {
                echo '<p class="error">✗ ' . htmlspecialchars($file) . ' - MANQUANT</p>';
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Test direct de l'API Claude</h2>
        <?php
        if ($team['ai_use_api'] && !empty($team['ai_api_key'])) {
            try {
                echo '<p>Tentative d\'appel direct à l\'API Claude...</p>';

                $apiKey = $team['ai_api_key'];
                $model = $team['ai_api_model'] ?: 'claude-3-5-sonnet-20241022';

                $data = [
                    'model' => $model,
                    'max_tokens' => 150,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Dis bonjour en une phrase courte et souhaite une bonne journée.'
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

                echo '<p><strong>Code HTTP :</strong> ' . $httpCode . '</p>';

                if ($curlError) {
                    echo '<p class="error">✗ Erreur cURL : ' . htmlspecialchars($curlError) . '</p>';
                } elseif ($httpCode === 200) {
                    echo '<p class="success">✓ Appel API réussi !</p>';
                    $result = json_decode($response, true);

                    if (isset($result['content'][0]['text'])) {
                        echo '<div style="background: #e0f2fe; padding: 15px; border-left: 4px solid #0284c7; margin: 10px 0;">';
                        echo '<strong>Réponse de Claude :</strong><br>';
                        echo nl2br(htmlspecialchars($result['content'][0]['text']));
                        echo '</div>';
                    }

                    echo '<details><summary>Réponse complète JSON</summary>';
                    echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                    echo '</details>';
                } else {
                    echo '<p class="error">✗ Échec de l\'appel API</p>';
                    $errorData = json_decode($response, true);
                    if ($errorData && isset($errorData['error'])) {
                        echo '<p><strong>Type d\'erreur :</strong> ' . htmlspecialchars($errorData['error']['type']) . '</p>';
                        echo '<p><strong>Message :</strong> ' . htmlspecialchars($errorData['error']['message']) . '</p>';
                    }
                    echo '<details><summary>Réponse complète</summary>';
                    echo '<pre>' . htmlspecialchars($response) . '</pre>';
                    echo '</details>';
                }

            } catch (Exception $e) {
                echo '<p class="error">✗ Exception : ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        } else {
            echo '<p class="warning">⚠ API non configurée, test ignoré</p>';
            if (!$team['ai_use_api']) {
                echo '<p>Raison : ai_use_api = ' . var_export($team['ai_use_api'], true) . '</p>';
            }
            if (empty($team['ai_api_key'])) {
                echo '<p>Raison : ai_api_key est vide</p>';
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>5. Analyse</h2>
        <?php
        if ($team['ai_use_api'] && !empty($team['ai_api_key'])) {
            echo '<p class="success">✓ Configuration complète détectée</p>';
            echo '<p>L\'API devrait être utilisée par l\'application.</p>';

            echo '<p><strong>Points à vérifier :</strong></p>';
            echo '<ul>';
            echo '<li>Les fichiers <code>src/Services/AssistantService.php</code> et <code>src/Controllers/AssistantController.php</code> sont-ils à jour sur le serveur ?</li>';
            echo '<li>Avez-vous vidé le cache de votre navigateur ?</li>';
            echo '<li>Si l\'appel API direct ci-dessus a réussi, le problème vient du code PHP de l\'application</li>';
            echo '</ul>';
        } else {
            echo '<p class="error">✗ Configuration incomplète</p>';
            echo '<p>L\'API ne sera pas utilisée tant que la configuration n\'est pas complète.</p>';
        }
        ?>
    </div>

</body>
</html>
