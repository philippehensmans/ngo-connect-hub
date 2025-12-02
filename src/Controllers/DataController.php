<?php

namespace App\Controllers;

use App\Models\Member;
use App\Models\Project;
use App\Models\Group;
use App\Models\Milestone;
use App\Models\Task;
use App\Services\Auth;

/**
 * Contrôleur pour charger toutes les données
 */
class DataController extends Controller
{
    /**
     * Charge toutes les données de l'équipe
     */
    public function loadAll(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $teamId = Auth::getTeamId();

        // Charger les membres
        $memberModel = new Member($this->db);
        $members = $memberModel->all(['team_id' => $teamId]);

        // Charger les projets
        $projectModel = new Project($this->db);
        $projects = $projectModel->all(['team_id' => $teamId]);

        $projectIds = array_column($projects, 'id');

        $groups = [];
        $milestones = [];
        $tasks = [];

        if (!empty($projectIds)) {
            $placeholders = implode(',', array_fill(0, count($projectIds), '?'));

            // Charger les groupes
            $stmt = $this->db->prepare("SELECT * FROM groups WHERE project_id IN ($placeholders)");
            $stmt->execute($projectIds);
            $groups = $stmt->fetchAll();

            // Charger les jalons
            $stmt = $this->db->prepare("SELECT * FROM milestones WHERE project_id IN ($placeholders)");
            $stmt->execute($projectIds);
            $milestones = $stmt->fetchAll();

            // Charger les tâches
            $stmt = $this->db->prepare("SELECT * FROM tasks WHERE project_id IN ($placeholders)");
            $stmt->execute($projectIds);
            $tasks = $stmt->fetchAll();
        }

        // Récupérer le membre actuel (premier membre de l'équipe pour la démo)
        $currentMember = !empty($members) ? $members[0] : null;

        $this->success([
            'members' => $members,
            'projects' => $projects,
            'groups' => $groups,
            'milestones' => $milestones,
            'tasks' => $tasks,
            'currentMember' => $currentMember,
        ]);
    }
}
