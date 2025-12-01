<?php
/**
 * ONG Manager v10.0
 * Architecture MVC moderne et maintenable
 *
 * Point d'entrée principal de l'application
 */

// Autoloader simple pour les classes
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

// Charger la configuration
$config = require __DIR__ . '/config/config.php';

// Configuration de l'environnement
if ($config['app']['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

date_default_timezone_set($config['app']['timezone']);

// Initialiser les services
use App\Services\Database;
use App\Services\Auth;
use App\Services\Translation;
use App\Router;

// Démarrer la session
Auth::startSession();

// Gestion du reset de l'application
if (isset($_GET['reset_app'])) {
    $dbService = new Database($config);
    $dbService->reset();
    Auth::logout();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Initialiser la base de données
$dbService = new Database($config);
$db = $dbService->getConnection();

// Initialiser la traduction
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $config['languages']['default'];
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $lang;
}
$t = new Translation($lang);

// Gestion du téléchargement de la base de données
if (isset($_GET['action']) && $_GET['action'] === 'download_db' && Auth::check()) {
    $dbPath = $config['database']['path'];
    if (file_exists($dbPath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="backup_' . date('Y-m-d') . '.db"');
        readfile($dbPath);
        exit;
    }
}

// Gestion des requêtes API (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    ob_clean();
    $router = new Router($db);
    $router->dispatch($_POST['action'], $_POST);
    exit;
}

// Affichage de la vue appropriée
if (!Auth::check()) {
    // Afficher la page de login
    require __DIR__ . '/views/login.php';
} else {
    // Afficher l'application principale
    $teamName = Auth::getTeamName();
    require __DIR__ . '/views/app.php';
}
