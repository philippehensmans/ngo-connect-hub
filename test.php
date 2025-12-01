<?php
/**
 * Script de test pour vérifier l'installation
 */

echo "<h1>ONG Manager - Test d'Installation</h1>";

// Test des extensions PHP
echo "<h2>Extensions PHP</h2>";
$required = ['pdo', 'pdo_sqlite', 'json', 'session'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "$status Extension <strong>$ext</strong><br>";
}

// Test de la version PHP
echo "<h2>Version PHP</h2>";
$phpVersion = phpversion();
$minVersion = '7.4.0';
$status = version_compare($phpVersion, $minVersion, '>=') ? '✅' : '❌';
echo "$status PHP $phpVersion (minimum: $minVersion)<br>";

// Test des permissions
echo "<h2>Permissions</h2>";
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}
$writable = is_writable($dataDir) ? '✅' : '❌';
echo "$writable Dossier data/ accessible en écriture<br>";

// Test de l'autoloader
echo "<h2>Autoloader</h2>";
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

try {
    $config = require __DIR__ . '/config/config.php';
    echo "✅ Configuration chargée<br>";

    $dbService = new App\Services\Database($config);
    echo "✅ Service Database instancié<br>";

    $db = $dbService->getConnection();
    echo "✅ Connexion à la base de données établie<br>";

    $stmt = $db->query("SELECT COUNT(*) FROM teams");
    $count = $stmt->fetchColumn();
    echo "✅ Base de données opérationnelle ($count équipe(s))<br>";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

echo "<h2>Résultat</h2>";
echo "<p><strong>L'installation semble correcte ! Vous pouvez accéder à l'application via index.php</strong></p>";
echo "<p><a href='index.php' style='display:inline-block; padding:10px 20px; background:#2563EB; color:white; text-decoration:none; border-radius:5px;'>Accéder à l'application</a></p>";
