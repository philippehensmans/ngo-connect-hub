<?php
/**
 * Configuration principale de l'application ONG Manager
 */

return [
    // Configuration de la base de données
    'database' => [
        'driver' => 'sqlite',
        'path' => __DIR__ . '/../data/ong_manager.db',
    ],

    // Configuration de l'application
    'app' => [
        'name' => 'ONG Manager',
        'version' => '10.0',
        'debug' => true,
        'timezone' => 'Europe/Paris',
    ],

    // Configuration de la session
    'session' => [
        'name' => 'ong_manager_session',
        'lifetime' => 7200, // 2 heures
    ],

    // Configuration des langues supportées
    'languages' => [
        'supported' => ['fr', 'en', 'es', 'sl'],
        'default' => 'fr',
    ],

    // Configuration de sécurité
    'security' => [
        'password_algo' => PASSWORD_DEFAULT,
        'password_options' => ['cost' => 12],
    ],
];
