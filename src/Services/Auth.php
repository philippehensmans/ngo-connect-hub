<?php

namespace App\Services;

/**
 * Service de gestion de l'authentification
 * Authentification individuelle par email/mot de passe
 */
class Auth
{
    private const SESSION_KEY_MEMBER_ID = 'member_id';
    private const SESSION_KEY_MEMBER_EMAIL = 'member_email';
    private const SESSION_KEY_MEMBER_NAME = 'member_name';
    private const SESSION_KEY_ORG_ID = 'organization_id';
    private const SESSION_KEY_ORG_NAME = 'organization_name';
    private const SESSION_KEY_ROLE = 'role';

    // Rôles disponibles
    public const ROLE_MEMBER = 'member';
    public const ROLE_ORG_ADMIN = 'org_admin';
    public const ROLE_SUPER_ADMIN = 'super_admin';

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
        return isset($_SESSION[self::SESSION_KEY_MEMBER_ID]);
    }

    /**
     * Connecte un membre
     */
    public static function login(array $member, array $organization): void
    {
        self::startSession();
        $_SESSION[self::SESSION_KEY_MEMBER_ID] = $member['id'];
        $_SESSION[self::SESSION_KEY_MEMBER_EMAIL] = $member['email'];
        $_SESSION[self::SESSION_KEY_MEMBER_NAME] = $member['fname'] . ' ' . $member['lname'];
        $_SESSION[self::SESSION_KEY_ORG_ID] = $organization['id'];
        $_SESSION[self::SESSION_KEY_ORG_NAME] = $organization['name'];
        $_SESSION[self::SESSION_KEY_ROLE] = $member['role'];
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
     * Obtient l'ID du membre connecté
     */
    public static function getMemberId(): ?int
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_MEMBER_ID] ?? null;
    }

    /**
     * Obtient l'email du membre connecté
     */
    public static function getMemberEmail(): ?string
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_MEMBER_EMAIL] ?? null;
    }

    /**
     * Obtient le nom complet du membre connecté
     */
    public static function getMemberName(): ?string
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_MEMBER_NAME] ?? null;
    }

    /**
     * Obtient l'ID de l'organisation
     */
    public static function getOrganizationId(): ?int
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_ORG_ID] ?? null;
    }

    /**
     * Obtient le nom de l'organisation
     */
    public static function getOrganizationName(): ?string
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_ORG_NAME] ?? null;
    }

    /**
     * Obtient le rôle du membre connecté
     */
    public static function getRole(): ?string
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY_ROLE] ?? null;
    }

    /**
     * Vérifie si le membre est super admin
     */
    public static function isSuperAdmin(): bool
    {
        return self::getRole() === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Vérifie si le membre est admin de son organisation
     */
    public static function isOrgAdmin(): bool
    {
        $role = self::getRole();
        return $role === self::ROLE_ORG_ADMIN || $role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Vérifie si le membre est admin (org_admin ou super_admin)
     * Alias pour compatibilité avec l'ancien code
     */
    public static function isAdmin(): bool
    {
        return self::isOrgAdmin();
    }

    /**
     * Vérifie si le membre peut accéder à une organisation donnée
     */
    public static function canAccessOrganization(int $orgId): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }
        return self::getOrganizationId() === $orgId;
    }

    /**
     * Change temporairement l'organisation active (pour super admin)
     */
    public static function switchOrganization(array $organization): void
    {
        if (!self::isSuperAdmin()) {
            throw new \RuntimeException('Only super admins can switch organizations');
        }
        self::startSession();
        $_SESSION[self::SESSION_KEY_ORG_ID] = $organization['id'];
        $_SESSION[self::SESSION_KEY_ORG_NAME] = $organization['name'];
    }

    /**
     * Vérifie les credentials et retourne le membre si valides
     */
    public static function attempt(\PDO $db, string $email, string $password): ?array
    {
        $stmt = $db->prepare("
            SELECT m.*, o.id as org_id, o.name as org_name, o.is_active as org_active
            FROM members m
            JOIN organizations o ON m.organization_id = o.id
            WHERE m.email = ? AND m.is_active = 1
        ");
        $stmt->execute([$email]);
        $member = $stmt->fetch();

        if (!$member) {
            return null;
        }

        // Vérifier que l'organisation est active (sauf pour super admin)
        if (!$member['org_active'] && $member['role'] !== self::ROLE_SUPER_ADMIN) {
            return null;
        }

        // Vérifier le mot de passe
        if (password_verify($password, $member['password'])) {
            return $member;
        }

        return null;
    }

    /**
     * Retourne les informations de l'organisation pour la session
     */
    public static function getOrganizationInfo(\PDO $db, int $orgId): ?array
    {
        $stmt = $db->prepare("SELECT * FROM organizations WHERE id = ?");
        $stmt->execute([$orgId]);
        return $stmt->fetch() ?: null;
    }

    // ============ Méthodes de compatibilité avec l'ancien code ============

    /**
     * @deprecated Utiliser getOrganizationId() à la place
     */
    public static function getTeamId(): ?int
    {
        return self::getOrganizationId();
    }

    /**
     * @deprecated Utiliser getOrganizationName() à la place
     */
    public static function getTeamName(): ?string
    {
        return self::getOrganizationName();
    }
}
