<?php
/**
 * Script de test pour le système de commentaires
 * Accédez à ce fichier via : https://www.k1m.be/exercices/projets/ong-manager/test-comments.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Test du système de commentaires</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .ok{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";

// 1. Vérifier les fichiers
echo "<h2>1. Vérification des fichiers</h2>";
$files = [
    'src/Models/Comment.php',
    'src/Controllers/CommentController.php',
    'src/Router.php',
    'src/Services/Database.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ <span class='ok'>$file existe</span><br>";
    } else {
        echo "❌ <span class='error'>$file est manquant</span><br>";
    }
}

// 2. Charger l'autoloader personnalisé
echo "<h2>2. Chargement de l'autoloader</h2>";
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
echo "✅ <span class='ok'>Autoloader personnalisé chargé</span><br>";

// 3. Vérifier la base de données
echo "<h2>3. Vérification de la base de données</h2>";
try {
    $dbPath = 'data/db.sqlite';
    if (!file_exists($dbPath)) {
        echo "⚠️ <span class='error'>Base de données non trouvée à $dbPath</span><br>";
        echo "Création de la base de données...<br>";
        \App\Services\Database::initialize();
        echo "✅ <span class='ok'>Base de données créée</span><br>";
    }

    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <span class='ok'>Connexion à la base de données réussie</span><br>";

    // Vérifier si la table comments existe
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='comments'")->fetchAll();
    if (count($tables) > 0) {
        echo "✅ <span class='ok'>Table 'comments' existe</span><br>";

        // Afficher la structure
        $columns = $db->query("PRAGMA table_info(comments)")->fetchAll();
        echo "<details><summary>Structure de la table</summary><pre>";
        print_r($columns);
        echo "</pre></details>";
    } else {
        echo "❌ <span class='error'>Table 'comments' n'existe pas</span><br>";
        echo "Essai de création de la table...<br>";
        $db->exec("CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL,
            member_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(member_id) REFERENCES members(id) ON DELETE CASCADE
        )");
        echo "✅ <span class='ok'>Table créée</span><br>";
    }
} catch (Exception $e) {
    echo "❌ <span class='error'>Erreur base de données: " . $e->getMessage() . "</span><br>";
}

// 4. Vérifier le Router
echo "<h2>4. Vérification du Router</h2>";
try {
    $router = new \App\Router($db);
    echo "✅ <span class='ok'>Router instancié</span><br>";

    // Utiliser la réflexion pour vérifier les routes
    $reflection = new ReflectionClass($router);
    $property = $reflection->getProperty('routes');
    $property->setAccessible(true);
    $routes = $property->getValue($router);

    $commentRoutes = ['list_comments', 'add_comment', 'delete_comment'];
    foreach ($commentRoutes as $route) {
        if (isset($routes[$route])) {
            echo "✅ <span class='ok'>Route '$route' enregistrée</span><br>";
        } else {
            echo "❌ <span class='error'>Route '$route' manquante</span><br>";
        }
    }

    echo "<details><summary>Toutes les routes disponibles</summary><pre>";
    print_r(array_keys($routes));
    echo "</pre></details>";
} catch (Exception $e) {
    echo "❌ <span class='error'>Erreur Router: " . $e->getMessage() . "</span><br>";
}

// 5. Test du modèle Comment
echo "<h2>5. Test du modèle Comment</h2>";
try {
    $commentModel = new \App\Models\Comment($db);
    echo "✅ <span class='ok'>Modèle Comment instancié</span><br>";

    // Vérifier la méthode getByTask
    if (method_exists($commentModel, 'getByTask')) {
        echo "✅ <span class='ok'>Méthode getByTask() existe</span><br>";

        // Tester avec une tâche fictive
        $comments = $commentModel->getByTask(999);
        echo "✅ <span class='ok'>getByTask(999) a retourné " . count($comments) . " commentaire(s)</span><br>";
    } else {
        echo "❌ <span class='error'>Méthode getByTask() manquante</span><br>";
    }
} catch (Exception $e) {
    echo "❌ <span class='error'>Erreur modèle: " . $e->getMessage() . "</span><br>";
}

// 6. Test du contrôleur
echo "<h2>6. Test du CommentController</h2>";
try {
    $controller = new \App\Controllers\CommentController($db);
    echo "✅ <span class='ok'>CommentController instancié</span><br>";

    $methods = ['list', 'add', 'delete'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "✅ <span class='ok'>Méthode $method() existe</span><br>";
        } else {
            echo "❌ <span class='error'>Méthode $method() manquante</span><br>";
        }
    }
} catch (Exception $e) {
    echo "❌ <span class='error'>Erreur contrôleur: " . $e->getMessage() . "</span><br>";
}

// 7. Résumé
echo "<h2>7. Résumé et recommandations</h2>";
echo "<p>Si tous les tests ci-dessus sont ✅ verts, le système de commentaires devrait fonctionner.</p>";
echo "<p>Si vous voyez toujours l'erreur 'action not found' :</p>";
echo "<ul>";
echo "<li>Videz le cache de votre navigateur (Ctrl+F5)</li>";
echo "<li>Vérifiez que <code>api.php</code> charge bien le nouveau Router</li>";
echo "<li>Vérifiez les logs d'erreur PHP de votre serveur</li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Test effectué le " . date('Y-m-d H:i:s') . "</small></p>";
