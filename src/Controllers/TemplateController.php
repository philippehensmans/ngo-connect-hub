<?php

namespace App\Controllers;

use App\Models\ProjectTemplate;
use App\Models\Project;
use App\Models\Group;
use App\Models\Milestone;
use App\Models\Task;
use App\Services\Auth;

/**
 * Contrôleur pour les templates de projets
 */
class TemplateController extends Controller
{
    /**
     * Liste tous les templates de l'équipe
     */
    public function list(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $templateModel = new ProjectTemplate($this->db);
        $teamId = Auth::getTeamId();

        try {
            $templates = $templateModel->all([
                'where' => ['team_id' => $teamId],
                'order' => 'created_at DESC'
            ]);

            $this->success(['templates' => $templates]);
        } catch (\Exception $e) {
            $this->error('Failed to load templates: ' . $e->getMessage());
        }
    }

    /**
     * Sauvegarde un projet comme template
     */
    public function saveFromProject(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['project_id', 'template_name'])) {
            $this->error('Missing required fields');
            return;
        }

        $projectId = (int)$data['project_id'];
        $teamId = Auth::getTeamId();

        $projectModel = new Project($this->db);
        $groupModel = new Group($this->db);
        $milestoneModel = new Milestone($this->db);
        $taskModel = new Task($this->db);

        try {
            // Récupérer le projet
            $project = $projectModel->find($projectId);
            if (!$project || $project['team_id'] != $teamId) {
                $this->error('Project not found');
                return;
            }

            // Récupérer toutes les données du projet
            $groups = $groupModel->all(['where' => ['project_id' => $projectId]]);
            $milestones = $milestoneModel->all(['where' => ['project_id' => $projectId]]);
            $tasks = $taskModel->all(['where' => ['project_id' => $projectId]]);

            // Créer les données du template
            $templateData = json_encode([
                'project' => [
                    'name' => $project['name'],
                    'desc' => $project['desc']
                ],
                'groups' => array_map(function($g) {
                    return [
                        'name' => $g['name'],
                        'color' => $g['color']
                    ];
                }, $groups),
                'milestones' => array_map(function($m) {
                    return [
                        'name' => $m['name']
                    ];
                }, $milestones),
                'tasks' => array_map(function($t) {
                    return [
                        'title' => $t['title'],
                        'desc' => $t['desc'],
                        'status' => $t['status'],
                        'priority' => $t['priority']
                    ];
                }, $tasks)
            ]);

            // Sauvegarder le template
            $templateModel = new ProjectTemplate($this->db);
            $templateId = $templateModel->create([
                'team_id' => $teamId,
                'name' => $data['template_name'],
                'desc' => $data['template_desc'] ?? '',
                'category' => $data['category'] ?? 'custom',
                'template_data' => $templateData,
                'is_predefined' => 0
            ]);

            $this->success(['id' => $templateId], 'Template saved successfully');
        } catch (\Exception $e) {
            $this->error('Failed to save template: ' . $e->getMessage());
        }
    }

    /**
     * Crée un nouveau projet depuis un template
     */
    public function createFromTemplate(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['template_id', 'project_name'])) {
            $this->error('Missing required fields');
            return;
        }

        $templateId = (int)$data['template_id'];
        $teamId = Auth::getTeamId();

        $templateModel = new ProjectTemplate($this->db);
        $projectModel = new Project($this->db);
        $groupModel = new Group($this->db);
        $milestoneModel = new Milestone($this->db);
        $taskModel = new Task($this->db);

        try {
            // Récupérer le template
            $template = $templateModel->find($templateId);
            if (!$template || $template['team_id'] != $teamId) {
                $this->error('Template not found');
                return;
            }

            // Décoder les données du template
            $templateData = json_decode($template['template_data'], true);

            // Créer le nouveau projet
            $projectId = $projectModel->create([
                'team_id' => $teamId,
                'name' => $data['project_name'],
                'desc' => $data['project_desc'] ?? $templateData['project']['desc'],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'owner_id' => $data['owner_id'] ?? null,
                'status' => 'active'
            ]);

            // Créer les groupes
            $groupMapping = [];
            foreach ($templateData['groups'] ?? [] as $groupData) {
                $groupId = $groupModel->create([
                    'project_id' => $projectId,
                    'name' => $groupData['name'],
                    'color' => $groupData['color'],
                    'owner_id' => null
                ]);
                $groupMapping[] = $groupId;
            }

            // Créer les jalons
            $milestoneMapping = [];
            foreach ($templateData['milestones'] ?? [] as $milestoneData) {
                $milestoneId = $milestoneModel->create([
                    'project_id' => $projectId,
                    'name' => $milestoneData['name'],
                    'date' => date('Y-m-d'),
                    'status' => 'active'
                ]);
                $milestoneMapping[] = $milestoneId;
            }

            // Créer les tâches
            foreach ($templateData['tasks'] ?? [] as $taskData) {
                $taskModel->create([
                    'project_id' => $projectId,
                    'title' => $taskData['title'],
                    'desc' => $taskData['desc'],
                    'status' => $taskData['status'] ?? 'todo',
                    'priority' => $taskData['priority'] ?? 'medium',
                    'owner_id' => null,
                    'group_id' => null,
                    'milestone_id' => null
                ]);
            }

            $this->success(['project_id' => $projectId], 'Project created from template successfully');
        } catch (\Exception $e) {
            $this->error('Failed to create project from template: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un template
     */
    public function delete(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['id'])) {
            $this->error('Missing template ID');
            return;
        }

        $templateId = (int)$data['id'];
        $teamId = Auth::getTeamId();

        $templateModel = new ProjectTemplate($this->db);

        try {
            $template = $templateModel->find($templateId);
            if (!$template || $template['team_id'] != $teamId) {
                $this->error('Template not found');
                return;
            }

            if ($template['is_predefined']) {
                $this->error('Cannot delete predefined templates');
                return;
            }

            $templateModel->delete($templateId);
            $this->success(null, 'Template deleted successfully');
        } catch (\Exception $e) {
            $this->error('Failed to delete template: ' . $e->getMessage());
        }
    }
}
