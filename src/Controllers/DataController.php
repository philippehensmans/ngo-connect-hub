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
     * Charge toutes les données de l'organisation
     */
    public function loadAll(): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        $orgId = Auth::getOrganizationId();
        $memberId = Auth::getMemberId();

        // Charger les membres de l'organisation
        $stmt = $this->db->prepare("
            SELECT id, organization_id, email, fname, lname, role, is_active, created_at
            FROM members
            WHERE organization_id = ? AND is_active = 1
            ORDER BY lname, fname
        ");
        $stmt->execute([$orgId]);
        $members = $stmt->fetchAll();

        // Charger les projets de l'organisation
        $stmt = $this->db->prepare("
            SELECT * FROM projects
            WHERE organization_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$orgId]);
        $projects = $stmt->fetchAll();

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

        // Charger les équipes internes
        $stmt = $this->db->prepare("
            SELECT t.*,
                   (SELECT COUNT(*) FROM team_members WHERE team_id = t.id) as member_count
            FROM internal_teams t
            WHERE t.organization_id = ?
            ORDER BY t.name
        ");
        $stmt->execute([$orgId]);
        $internalTeams = $stmt->fetchAll();

        // Ajouter les membres de chaque équipe interne
        foreach ($internalTeams as &$team) {
            $stmt = $this->db->prepare("
                SELECT m.id, m.fname, m.lname, m.email
                FROM members m
                JOIN team_members tm ON m.id = tm.member_id
                WHERE tm.team_id = ?
            ");
            $stmt->execute([$team['id']]);
            $team['members'] = $stmt->fetchAll();
        }

        // Récupérer le membre actuel
        $stmt = $this->db->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->execute([$memberId]);
        $currentMember = $stmt->fetch();

        // Récupérer les données de l'organisation
        $stmt = $this->db->prepare("SELECT * FROM organizations WHERE id = ?");
        $stmt->execute([$orgId]);
        $organization = $stmt->fetch();

        $this->success([
            'members' => $members,
            'projects' => $projects,
            'groups' => $groups,
            'milestones' => $milestones,
            'tasks' => $tasks,
            'internalTeams' => $internalTeams,
            'currentMember' => $currentMember,
            'currentMemberId' => $memberId,
            'currentOrganizationId' => $orgId,
            'isOrgAdmin' => Auth::isOrgAdmin(),
            'isSuperAdmin' => Auth::isSuperAdmin(),
            'organization' => $organization,
            // Compatibilité avec l'ancien code
            'currentTeamId' => $orgId,
            'isAdmin' => Auth::isAdmin(),
            'team' => $organization,
        ]);
    }
}
