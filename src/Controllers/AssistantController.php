<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Services\AssistantService;

/**
 * Contrôleur pour l'assistant IA
 */
class AssistantController extends Controller
{
    private AssistantService $assistantService;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->assistantService = new AssistantService($db);
    }

    /**
     * Démarre une nouvelle conversation avec l'assistant
     */
    public function startConversation(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $teamId = Auth::getTeamId();
        $projectId = $data['project_id'] ?? null;

        try {
            $conversationId = $this->assistantService->startConversation($teamId, $projectId);
            $initialMessage = $this->assistantService->getInitialMessage();

            $this->success([
                'conversation_id' => $conversationId,
                'message' => $initialMessage['content'],
                'suggestions' => $initialMessage['suggestions']
            ]);
        } catch (\Exception $e) {
            $this->error('Failed to start conversation: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un message à l'assistant et reçoit une réponse
     */
    public function sendMessage(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['conversation_id', 'message'])) {
            $this->error('Missing required fields');
            return;
        }

        $conversationId = (int)$data['conversation_id'];
        $userMessage = $this->sanitize($data)['message'];
        $teamId = Auth::getTeamId();

        try {
            // Vérifier que la conversation appartient à l'équipe
            if (!$this->assistantService->verifyConversationOwnership($conversationId, $teamId)) {
                $this->error('Unauthorized access to conversation', 403);
                return;
            }

            // Enregistrer le message de l'utilisateur et obtenir la réponse
            $response = $this->assistantService->processMessage($conversationId, $userMessage);

            $this->success([
                'message' => $response['message'],
                'suggestions' => $response['suggestions'] ?? null,
                'completed' => $response['completed'] ?? false
            ]);
        } catch (\Exception $e) {
            $this->error('Failed to process message: ' . $e->getMessage());
        }
    }

    /**
     * Récupère l'historique d'une conversation
     */
    public function getConversation(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['conversation_id'])) {
            $this->error('Missing conversation_id');
            return;
        }

        $conversationId = (int)$data['conversation_id'];
        $teamId = Auth::getTeamId();

        try {
            // Vérifier que la conversation appartient à l'équipe
            if (!$this->assistantService->verifyConversationOwnership($conversationId, $teamId)) {
                $this->error('Unauthorized access to conversation', 403);
                return;
            }

            $conversation = $this->assistantService->getConversation($conversationId);

            $this->success($conversation);
        } catch (\Exception $e) {
            $this->error('Failed to retrieve conversation: ' . $e->getMessage());
        }
    }

    /**
     * Liste toutes les conversations de l'équipe
     */
    public function listConversations(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $teamId = Auth::getTeamId();

        try {
            $conversations = $this->assistantService->listConversations($teamId);
            $this->success(['conversations' => $conversations]);
        } catch (\Exception $e) {
            $this->error('Failed to list conversations: ' . $e->getMessage());
        }
    }

    /**
     * Génère automatiquement la structure du projet à partir de la conversation
     */
    public function generateStructure(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['conversation_id', 'project_id'])) {
            $this->error('Missing required fields');
            return;
        }

        $conversationId = (int)$data['conversation_id'];
        $projectId = (int)$data['project_id'];
        $teamId = Auth::getTeamId();

        try {
            // Vérifier que la conversation appartient à l'équipe
            if (!$this->assistantService->verifyConversationOwnership($conversationId, $teamId)) {
                $this->error('Unauthorized access to conversation', 403);
                return;
            }

            // Générer la structure
            $structure = $this->assistantService->generateProjectStructure($conversationId, $projectId);

            $this->success([
                'structure' => $structure,
                'message' => 'Structure generated successfully'
            ]);
        } catch (\Exception $e) {
            $this->error('Failed to generate structure: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une conversation
     */
    public function deleteConversation(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['conversation_id'])) {
            $this->error('Missing conversation_id');
            return;
        }

        $conversationId = (int)$data['conversation_id'];
        $teamId = Auth::getTeamId();

        try {
            // Vérifier que la conversation appartient à l'équipe
            if (!$this->assistantService->verifyConversationOwnership($conversationId, $teamId)) {
                $this->error('Unauthorized access to conversation', 403);
                return;
            }

            $this->assistantService->deleteConversation($conversationId);
            $this->success(null, 'Conversation deleted successfully');
        } catch (\Exception $e) {
            $this->error('Failed to delete conversation: ' . $e->getMessage());
        }
    }
}
