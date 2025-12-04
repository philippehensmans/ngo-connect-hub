<?php
/**
 * Script de diagnostic pour vérifier la configuration AI
 */

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion directe à la base de données
$dbPath = __DIR__ . '/data/ngo.db';

if (!file_exists($dbPath)) {
    die("<h1>ERREUR</h1><p>La base de données n'existe pas à : $dbPath</p>");
}

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<h1>ERREUR</h1><p>Connexion impossible : " . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Diagnostic Configuration AI</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        h1 { color: #1f2937; }
        h2 { color: #374151; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic Configuration AI</h1>

    <!-- Section 1: Structure de la table -->
    <div class="section">
        <h2>1. Structure de la table 'teams'</h2>
        <?php
        $result = $db->query("PRAGMA table_info(teams)")->fetchAll(PDO::FETCH_ASSOC);
        $columns = array_column($result, 'name');
        ?>
        <table>
            <tr><th>Colonne</th><th>Type</th></tr>
            <?php foreach ($result as $col): ?>
                <tr>
                    <td><code><?= htmlspecialchars($col['name']) ?></code></td>
                    <td><?= htmlspecialchars($col['type']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Section 2: Colonnes AI -->
    <div class="section">
        <h2>2. Colonnes AI présentes</h2>
        <?php
        $aiColumns = ['ai_use_api', 'ai_api_provider', 'ai_api_key', 'ai_api_model'];
        ?>
        <table>
            <tr><th>Colonne</th><th>Status</th></tr>
            <?php foreach ($aiColumns as $col): ?>
                <tr>
                    <td><code><?= htmlspecialchars($col) ?></code></td>
                    <td>
                        <?php if (in_array($col, $columns)): ?>
                            <span class="success">✓ Présente</span>
                        <?php else: ?>
                            <span class="error">✗ Manquante</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Section 3: Configuration actuelle -->
    <div class="section">
        <h2>3. Configuration AI actuelle</h2>
        <?php
        try {
            $stmt = $db->prepare("SELECT id, name, ai_use_api, ai_api_provider, ai_api_key, ai_api_model FROM teams");
            $stmt->execute();
            $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($teams)) {
                echo '<p class="warning">⚠ Aucune équipe trouvée</p>';
            } else {
                foreach ($teams as $team) {
                    echo '<h3>Équipe: ' . htmlspecialchars($team['name']) . ' (ID: ' . $team['id'] . ')</h3>';
                    echo '<table>';
                    echo '<tr><td><strong>ai_use_api</strong></td><td>' . ($team['ai_use_api'] ?? '<em>NULL</em>') . '</td></tr>';
                    echo '<tr><td><strong>ai_api_provider</strong></td><td>' . htmlspecialchars($team['ai_api_provider'] ?? 'NULL') . '</td></tr>';
                    echo '<tr><td><strong>ai_api_key</strong></td><td>' . (empty($team['ai_api_key']) ? '<em>(vide)</em>' : substr($team['ai_api_key'], 0, 15) . '...') . '</td></tr>';
                    echo '<tr><td><strong>ai_api_model</strong></td><td>' . htmlspecialchars($team['ai_api_model'] ?? 'NULL') . '</td></tr>';
                    echo '</table>';
                }
            }
        } catch (Exception $e) {
            echo '<p class="error">ERREUR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <!-- Section 4: Test de lecture -->
    <div class="section">
        <h2>4. Test de lecture de la configuration</h2>
        <?php
        try {
            // Récupérer le premier team_id disponible
            $stmt = $db->query("SELECT id FROM teams LIMIT 1");
            $team = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$team) {
                echo '<p class="error">✗ Aucune équipe trouvée dans la base de données</p>';
            } else {
                $teamId = $team['id'];
                $stmt = $db->prepare("SELECT ai_use_api, ai_api_provider, ai_api_key, ai_api_model FROM teams WHERE id = ?");
                $stmt->execute([$teamId]);
                $config = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($config) {
                    echo '<p>Configuration lue pour l\'équipe ID=' . $teamId . ':</p>';
                    echo '<ul>';
                    echo '<li><strong>ai_use_api:</strong> ' . var_export($config['ai_use_api'], true) . '</li>';
                    echo '<li><strong>ai_api_provider:</strong> ' . var_export($config['ai_api_provider'], true) . '</li>';
                    echo '<li><strong>ai_api_key présente:</strong> ' . (!empty($config['ai_api_key']) ? 'OUI' : 'NON') . '</li>';
                    echo '<li><strong>ai_api_model:</strong> ' . var_export($config['ai_api_model'], true) . '</li>';
                    echo '</ul>';

                    // Verdict final
                    if ($config['ai_use_api'] && $config['ai_api_key']) {
                        echo '<p class="success">✓ L\'API DEVRAIT être utilisée</p>';
                    } else {
                        echo '<p class="error">✗ L\'API NE SERA PAS utilisée</p>';
                        echo '<p><strong>Raisons:</strong></p><ul>';
                        if (!$config['ai_use_api']) {
                            echo '<li>ai_use_api = ' . var_export($config['ai_use_api'], true) . ' (devrait être 1)</li>';
                        }
                        if (!$config['ai_api_key']) {
                            echo '<li>ai_api_key est vide</li>';
                        }
                        echo '</ul>';
                    }
                } else {
                    echo '<p class="error">✗ Aucune configuration trouvée</p>';
                }
            }
        } catch (Exception $e) {
            echo '<p class="error">ERREUR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>✅ Fin du diagnostic</h2>
        <p>Copiez tout le contenu de cette page et envoyez-le pour analyse.</p>
    </div>
</body>
</html>
