<?php

namespace App\Services;

/**
 * Service de gestion de l'authentification
 */
class Auth
{
    private const SESSION_KEY_TEAM_ID = 'team_id';
    private const SESSION_KEY_TEAM_NAME = 'team_name';

    /**
     * Démarre la session si elle n'est pas déjà démarrée
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION[self::SESSION_KEY_TEAM_ID]);
    }

    /**
     * Connecte un utilisateur
     */
    public static function login(int $teamId, string $teamName): void
    {
        self::startSession();
        $_SESSION[self::SESSION_KEY_TEAM_ID] = $teamId;
        $_SESSION[self::SESSION_KEY_TEAM_NAME] = $teamName;
    }

    /**
     * Déconnecte l'utilisateur
     */
    public static function logout(): void
    {
        self::startSession();
        session_destroy();
    }

    /**
     * Obtient l'ID de l'équipe connectée
     */
    public static function getTeamId(): ?int
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_TEAM_ID] ?? null;
    }

    /**
     * Obtient le nom de l'équipe connectée
     */
    public static function getTeamName(): ?string
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_TEAM_NAME] ?? null;
    }

    /**
     * Vérifie les credentials et retourne l'équipe si valides
     */
    public static function attempt(\PDO $db, string $name, string $password): ?array
    {
        $stmt = $db->prepare("SELECT * FROM teams WHERE name = ?");
        $stmt->execute([$name]);
        $team = $stmt->fetch();

        if ($team && password_verify($password, $team['password'])) {
            return $team;
        }

        return null;
    }
}
