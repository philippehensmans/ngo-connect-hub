<?php

namespace App;

use PDO;
use App\Controllers\AuthController;
use App\Controllers\DataController;
use App\Controllers\ProjectController;
use App\Controllers\TaskController;
use App\Controllers\MemberController;
use App\Controllers\GroupController;
use App\Controllers\MilestoneController;
use App\Controllers\DeleteController;
use App\Controllers\TemplateController;
use App\Controllers\CommentController;
use App\Controllers\BackupController;
use App\Controllers\ExportController;
use App\Controllers\WebhookController;
use App\Controllers\AssistantController;
use App\Controllers\CalendarController;

/**
 * Routeur de l'application
 */
class Router
{
    private PDO $db;
    private array $routes = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->registerRoutes();
    }

    /**
     * Enregistre toutes les routes de l'API
     */
    private function registerRoutes(): void
    {
        // Routes d'authentification
        $this->routes['login'] = [AuthController::class, 'login'];
        $this->routes['logout'] = [AuthController::class, 'logout'];
        $this->routes['register'] = [AuthController::class, 'register'];
        $this->routes['update_settings'] = [AuthController::class, 'updateSettings'];
        $this->routes['update_profile'] = [AuthController::class, 'updateProfile'];
        $this->routes['update_ai_config'] = [AuthController::class, 'updateAIConfig'];

        // Routes pour les organisations (super admin)
        $this->routes['list_organizations'] = [AuthController::class, 'listOrganizations'];
        $this->routes['toggle_organization'] = [AuthController::class, 'toggleOrganization'];
        $this->routes['delete_organization'] = [AuthController::class, 'deleteOrganization'];
        $this->routes['switch_organization'] = [AuthController::class, 'switchOrganization'];

        // Routes pour les membres de l'organisation
        $this->routes['list_members'] = [AuthController::class, 'listMembers'];
        $this->routes['add_member'] = [AuthController::class, 'addMember'];
        $this->routes['update_member'] = [AuthController::class, 'updateMember'];
        $this->routes['delete_member'] = [AuthController::class, 'deleteMember'];

        // Routes pour les équipes internes
        $this->routes['list_internal_teams'] = [AuthController::class, 'listInternalTeams'];
        $this->routes['create_internal_team'] = [AuthController::class, 'createInternalTeam'];
        $this->routes['update_internal_team'] = [AuthController::class, 'updateInternalTeam'];
        $this->routes['delete_internal_team'] = [AuthController::class, 'deleteInternalTeam'];
        $this->routes['add_team_member'] = [AuthController::class, 'addTeamMember'];
        $this->routes['remove_team_member'] = [AuthController::class, 'removeTeamMember'];

        // Routes de compatibilité (deprecated)
        $this->routes['list_teams'] = [AuthController::class, 'listTeams'];
        $this->routes['update_team_role'] = [AuthController::class, 'updateTeamRole'];
        $this->routes['update_member_role'] = [AuthController::class, 'updateMemberRole'];

        // Route pour charger toutes les données
        $this->routes['load_all'] = [DataController::class, 'loadAll'];

        // Routes pour les projets
        $this->routes['save_project'] = [ProjectController::class, 'save'];

        // Routes pour les tâches
        $this->routes['save_task'] = [TaskController::class, 'save'];

        // Routes pour les membres
        $this->routes['save_member'] = [MemberController::class, 'save'];

        // Routes pour les groupes
        $this->routes['save_group'] = [GroupController::class, 'save'];

        // Routes pour les jalons
        $this->routes['save_milestone'] = [MilestoneController::class, 'save'];

        // Routes pour les templates
        $this->routes['list_templates'] = [TemplateController::class, 'list'];
        $this->routes['save_template'] = [TemplateController::class, 'saveFromProject'];
        $this->routes['create_from_template'] = [TemplateController::class, 'createFromTemplate'];
        $this->routes['delete_template'] = [TemplateController::class, 'delete'];

        // Routes pour les commentaires
        $this->routes['list_comments'] = [CommentController::class, 'list'];
        $this->routes['add_comment'] = [CommentController::class, 'add'];
        $this->routes['update_comment'] = [CommentController::class, 'update'];
        $this->routes['delete_comment'] = [CommentController::class, 'delete'];

        // Routes pour les backups
        $this->routes['create_backup'] = [BackupController::class, 'create'];
        $this->routes['list_backups'] = [BackupController::class, 'list'];
        $this->routes['download_backup'] = [BackupController::class, 'download'];

        // Routes pour l'export/import
        $this->routes['export_project'] = [ExportController::class, 'exportProject'];
        $this->routes['import_project'] = [ExportController::class, 'importProject'];

        // Routes pour les webhooks
        $this->routes['list_webhooks'] = [WebhookController::class, 'list'];
        $this->routes['create_webhook'] = [WebhookController::class, 'create'];
        $this->routes['update_webhook'] = [WebhookController::class, 'update'];
        $this->routes['delete_webhook'] = [WebhookController::class, 'delete'];
        $this->routes['test_webhook'] = [WebhookController::class, 'test'];

        // Routes pour l'assistant IA
        $this->routes['start_conversation'] = [AssistantController::class, 'startConversation'];
        $this->routes['send_message'] = [AssistantController::class, 'sendMessage'];
        $this->routes['get_conversation'] = [AssistantController::class, 'getConversation'];
        $this->routes['list_conversations'] = [AssistantController::class, 'listConversations'];
        $this->routes['generate_structure'] = [AssistantController::class, 'generateStructure'];
        $this->routes['delete_conversation'] = [AssistantController::class, 'deleteConversation'];

        // Routes pour l'export calendrier
        $this->routes['export_project_calendar'] = [CalendarController::class, 'exportProject'];
        $this->routes['export_team_calendar'] = [CalendarController::class, 'exportTeam'];

        // Route pour la suppression
        $this->routes['delete_item'] = [DeleteController::class, 'delete'];
    }

    /**
     * Dispatch une requête vers le bon contrôleur
     */
    public function dispatch(string $action, array $data): void
    {
        if (!isset($this->routes[$action])) {
            $this->sendError('Action not found', 404);
            return;
        }

        [$controllerClass, $method] = $this->routes[$action];

        try {
            $controller = new $controllerClass($this->db);
            $controller->$method($data);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Envoie une erreur JSON
     */
    private function sendError(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => $message]);
        exit;
    }
}
