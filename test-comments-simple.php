<?php
/**
 * Test simple pour le système de commentaires
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Commentaires</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto; }
        .ok { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 10px; margin-top: 30px; }
        .test-item { padding: 10px; margin: 5px 0; border-left: 4px solid #ddd; }
    </style>
</head>
<body>
    <h1>🔍 Test du système de commentaires - ONG Manager</h1>
    <p><small>Test effectué le <?= date('Y-m-d H:i:s') ?></small></p>

<?php

// Test 1: Fichiers
echo "<h2>1️⃣ Vérification des fichiers PHP</h2>";
$files = [
    'index.php' => 'Point d\'entrée principal',
    'src/Models/Comment.php' => 'Modèle Comment',
    'src/Controllers/CommentController.php' => 'Contrôleur Comment',
    'src/Router.php' => 'Routeur',
    'src/Services/Database.php' => 'Service base de données',
];

$allFilesOk = true;
foreach ($files as $file => $description) {
    $exists = file_exists($file);
    $allFilesOk = $allFilesOk && $exists;
    $class = $exists ? 'ok' : 'error';
    $icon = $exists ? '✅' : '❌';
    echo "<div class='test-item'>$icon <span class='$class'>$file</span> - $description</div>";
}

// Test 2: Base de données
echo "<h2>2️⃣ Vérification de la base de données</h2>";
$dbPath = 'data/ong_manager.db';
if (!file_exists($dbPath)) {
    echo "<div class='test-item'>❌ <span class='error'>Base de données non trouvée à $dbPath</span></div>";
    echo "<p><strong>Action requise :</strong> Connectez-vous à l'application pour initialiser la base de données.</p>";
} else {
    echo "<div class='test-item'>✅ <span class='ok'>Base de données trouvée : $dbPath</span></div>";

    try {
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div class='test-item'>✅ <span class='ok'>Connexion à la base de données réussie</span></div>";

        // Vérifier la table comments
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='comments'")->fetchAll();
        if (count($result) > 0) {
            echo "<div class='test-item'>✅ <span class='ok'>Table 'comments' existe</span></div>";

            // Compter les commentaires
            $count = $db->query("SELECT COUNT(*) as cnt FROM comments")->fetch();
            echo "<div class='test-item'>📊 La table contient <strong>{$count['cnt']}</strong> commentaire(s)</div>";

            // Structure de la table
            $structure = $db->query("PRAGMA table_info(comments)")->fetchAll(PDO::FETCH_ASSOC);
            echo "<details><summary>🔍 Voir la structure de la table</summary><pre>";
            foreach ($structure as $col) {
                echo "- {$col['name']} ({$col['type']}) " . ($col['notnull'] ? 'NOT NULL' : 'NULL') . "\n";
            }
            echo "</pre></details>";
        } else {
            echo "<div class='test-item'>❌ <span class='error'>Table 'comments' n'existe pas</span></div>";
            echo "<p><strong>Action requise :</strong> La table doit être créée. Essayez de vous connecter à l'application.</p>";
        }

        // Vérifier les autres tables
        $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
        echo "<details><summary>📋 Tables existantes dans la base (" . count($tables) . ")</summary><pre>";
        print_r($tables);
        echo "</pre></details>";

    } catch (Exception $e) {
        echo "<div class='test-item'>❌ <span class='error'>Erreur base de données : " . htmlspecialchars($e->getMessage()) . "</span></div>";
    }
}

// Test 3: Contenu des fichiers clés
echo "<h2>3️⃣ Vérification du contenu des fichiers</h2>";

// Vérifier Router.php
if (file_exists('src/Router.php')) {
    $routerContent = file_get_contents('src/Router.php');
    $commentRoutes = ['list_comments', 'add_comment', 'delete_comment'];

    echo "<h3>Router.php</h3>";
    foreach ($commentRoutes as $route) {
        $exists = strpos($routerContent, "'$route'") !== false;
        $icon = $exists ? '✅' : '❌';
        $class = $exists ? 'ok' : 'error';
        echo "<div class='test-item'>$icon <span class='$class'>Route '$route'</span></div>";
    }

    $hasCommentController = strpos($routerContent, 'CommentController') !== false;
    $icon = $hasCommentController ? '✅' : '❌';
    $class = $hasCommentController ? 'ok' : 'error';
    echo "<div class='test-item'>$icon <span class='$class'>Import de CommentController</span></div>";
}

// Vérifier Comment.php
if (file_exists('src/Models/Comment.php')) {
    $commentContent = file_get_contents('src/Models/Comment.php');
    $hasGetByTask = strpos($commentContent, 'getByTask') !== false;
    $icon = $hasGetByTask ? '✅' : '❌';
    $class = $hasGetByTask ? 'ok' : 'error';
    echo "<h3>Comment.php (Modèle)</h3>";
    echo "<div class='test-item'>$icon <span class='$class'>Méthode getByTask()</span></div>";
}

// Vérifier CommentController.php
if (file_exists('src/Controllers/CommentController.php')) {
    $controllerContent = file_get_contents('src/Controllers/CommentController.php');
    $methods = ['list', 'add', 'delete'];

    echo "<h3>CommentController.php</h3>";
    foreach ($methods as $method) {
        $exists = strpos($controllerContent, "function $method") !== false;
        $icon = $exists ? '✅' : '❌';
        $class = $exists ? 'ok' : 'error';
        echo "<div class='test-item'>$icon <span class='$class'>Méthode $method()</span></div>";
    }
}

// Test 4: JavaScript
echo "<h2>4️⃣ Vérification du JavaScript</h2>";
if (file_exists('public/js/app.js')) {
    $jsContent = file_get_contents('public/js/app.js');
    $jsFunctions = ['loadComments', 'renderComment', 'addComment', 'deleteComment'];

    foreach ($jsFunctions as $func) {
        $exists = strpos($jsContent, "$func:") !== false || strpos($jsContent, "$func =") !== false;
        $icon = $exists ? '✅' : '❌';
        $class = $exists ? 'ok' : 'error';
        echo "<div class='test-item'>$icon <span class='$class'>Fonction $func()</span></div>";
    }

    // Vérifier les appels API
    $apiCalls = ['list_comments', 'add_comment', 'delete_comment'];
    foreach ($apiCalls as $call) {
        $exists = strpos($jsContent, "'$call'") !== false;
        $icon = $exists ? '✅' : '❌';
        $class = $exists ? 'ok' : 'error';
        echo "<div class='test-item'>$icon <span class='$class'>Appel API '$call'</span></div>";
    }
} else {
    echo "<div class='test-item'>❌ <span class='error'>public/js/app.js non trouvé</span></div>";
}

// Test 5: Modal HTML
echo "<h2>5️⃣ Vérification du HTML (modals.php)</h2>";
if (file_exists('views/modals.php')) {
    $modalContent = file_get_contents('views/modals.php');
    $elements = [
        'taskCommentsSection' => 'Section commentaires',
        'commentsList' => 'Liste des commentaires',
        'newCommentText' => 'Zone de texte nouveau commentaire',
        'btnAddComment' => 'Bouton ajouter commentaire'
    ];

    foreach ($elements as $id => $description) {
        $exists = strpos($modalContent, "id=\"$id\"") !== false;
        $icon = $exists ? '✅' : '❌';
        $class = $exists ? 'ok' : 'error';
        echo "<div class='test-item'>$icon <span class='$class'>$description (id=\"$id\")</span></div>";
    }
} else {
    echo "<div class='test-item'>❌ <span class='error'>views/modals.php non trouvé</span></div>";
}

// Résumé
echo "<h2>📊 Résumé et diagnostic</h2>";

if ($allFilesOk) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 5px solid #28a745;'>";
    echo "<strong>✅ Tous les fichiers sont présents</strong>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 5px solid #dc3545;'>";
    echo "<strong>❌ Des fichiers sont manquants</strong>";
    echo "<p>Uploadez tous les fichiers listés en rouge ci-dessus.</p>";
    echo "</div>";
}

echo "<h3>🔧 Actions recommandées :</h3>";
echo "<ol>";
echo "<li><strong>Videz le cache du navigateur</strong> : Appuyez sur <code>Ctrl+F5</code> (Windows) ou <code>Cmd+Shift+R</code> (Mac)</li>";
echo "<li><strong>Vérifiez la console</strong> : Ouvrez les outils de développement (<code>F12</code>) → onglet Console</li>";
echo "<li><strong>Testez l'API directement</strong> : Essayez d'ouvrir une tâche et regardez l'onglet Network dans les outils de développement</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>💡 Astuce : Pour voir les erreurs PHP, vérifiez les logs du serveur ou activez le mode debug dans config/config.php</small></p>";

?>
</body>
</html>
