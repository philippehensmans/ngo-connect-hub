<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Group;
use App\Models\Milestone;
use App\Services\Auth;

/**
 * Contrôleur pour l'export/import de projets
 */
class ExportController extends Controller
{
    /**
     * Exporte un projet en JSON
     */
    public function exportProject(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['project_id'])) {
            $this->error('Missing project_id');
            return;
        }

        $projectId = (int)$data['project_id'];
        $projectModel = new Project($this->db);
        $taskModel = new Task($this->db);
        $groupModel = new Group($this->db);
        $milestoneModel = new Milestone($this->db);

        // Récupérer le projet
        $project = $projectModel->find($projectId);
        if (!$project) {
            $this->error('Project not found');
            return;
        }

        // Récupérer toutes les données associées
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $tasks = $stmt->fetchAll();

        $stmt = $this->db->prepare("SELECT * FROM groups WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $groups = $stmt->fetchAll();

        $stmt = $this->db->prepare("SELECT * FROM milestones WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $milestones = $stmt->fetchAll();

        // Créer la structure d'export
        $export = [
            'version' => '1.0',
            'exported_at' => date('Y-m-d H:i:s'),
            'project' => [
                'name' => $project['name'],
                'desc' => $project['desc'],
                'start_date' => $project['start_date'],
                'end_date' => $project['end_date']
            ],
            'tasks' => array_map(function($task) {
                return [
                    'title' => $task['title'],
                    'desc' => $task['desc'],
                    'status' => $task['status'],
                    'priority' => $task['priority'],
                    'start_date' => $task['start_date'],
                    'end_date' => $task['end_date'],
                    'tags' => $task['tags'],
                    'link' => $task['link'],
                    'group_ref' => $task['group_id'],
                    'milestone_ref' => $task['milestone_id']
                ];
            }, $tasks),
            'groups' => array_map(function($group) {
                return [
                    'id_ref' => $group['id'],
                    'name' => $group['name'],
                    'color' => $group['color']
                ];
            }, $groups),
            'milestones' => array_map(function($milestone) {
                return [
                    'id_ref' => $milestone['id'],
                    'name' => $milestone['name'],
                    'date' => $milestone['date'],
                    'status' => $milestone['status']
                ];
            }, $milestones)
        ];

        // Retourner le JSON
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="project_' . $project['name'] . '_' . date('Y-m-d') . '.json"');
        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Importe un projet depuis JSON
     */
    public function importProject(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!isset($data['json_data'])) {
            $this->error('Missing JSON data');
            return;
        }

        try {
            $import = json_decode($data['json_data'], true);
            if (!$import || !isset($import['project'])) {
                $this->error('Invalid JSON format');
                return;
            }

            $teamId = Auth::getTeamId();
            $projectModel = new Project($this->db);

            // Créer le projet
            $projectData = $import['project'];
            $projectData['team_id'] = $teamId;
            $newProjectId = $projectModel->create($projectData);

            // Mapping des anciens IDs vers les nouveaux
            $groupMapping = [];
            $milestoneMapping = [];

            // Importer les groupes
            if (isset($import['groups'])) {
                foreach ($import['groups'] as $group) {
                    $oldId = $group['id_ref'];
                    unset($group['id_ref']);
                    $group['project_id'] = $newProjectId;

                    $stmt = $this->db->prepare("INSERT INTO groups (project_id, name, color) VALUES (?, ?, ?)");
                    $stmt->execute([$newProjectId, $group['name'], $group['color']]);
                    $groupMapping[$oldId] = $this->db->lastInsertId();
                }
            }

            // Importer les jalons
            if (isset($import['milestones'])) {
                foreach ($import['milestones'] as $milestone) {
                    $oldId = $milestone['id_ref'];
                    unset($milestone['id_ref']);
                    $milestone['project_id'] = $newProjectId;

                    $stmt = $this->db->prepare("INSERT INTO milestones (project_id, name, date, status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$newProjectId, $milestone['name'], $milestone['date'], $milestone['status']]);
                    $milestoneMapping[$oldId] = $this->db->lastInsertId();
                }
            }

            // Importer les tâches
            if (isset($import['tasks'])) {
                foreach ($import['tasks'] as $task) {
                    $task['project_id'] = $newProjectId;
                    $task['group_id'] = isset($task['group_ref']) && isset($groupMapping[$task['group_ref']])
                        ? $groupMapping[$task['group_ref']]
                        : null;
                    $task['milestone_id'] = isset($task['milestone_ref']) && isset($milestoneMapping[$task['milestone_ref']])
                        ? $milestoneMapping[$task['milestone_ref']]
                        : null;

                    unset($task['group_ref'], $task['milestone_ref']);

                    $stmt = $this->db->prepare("
                        INSERT INTO tasks (project_id, group_id, milestone_id, title, desc, status, priority, start_date, end_date, tags, link)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $task['project_id'],
                        $task['group_id'],
                        $task['milestone_id'],
                        $task['title'],
                        $task['desc'],
                        $task['status'],
                        $task['priority'],
                        $task['start_date'],
                        $task['end_date'],
                        $task['tags'],
                        $task['link']
                    ]);
                }
            }

            $this->success([
                'message' => 'Project imported successfully',
                'project_id' => $newProjectId
            ]);
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
        }
    }
}
