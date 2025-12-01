<?php

namespace App\Controllers;

use App\Models\Member;
use App\Services\Auth;

/**
 * Contrôleur pour les membres
 */
class MemberController extends Controller
{
    /**
     * Sauvegarde un membre
     */
    public function save(array $data): void
    {
        if (!Auth::check()) {
            $this->error('Unauthorized', 401);
            return;
        }

        if (!$this->validate($data, ['fname', 'lname', 'email'])) {
            $this->error('Missing required fields');
            return;
        }

        $data = $this->sanitize($data);
        $memberModel = new Member($this->db);
        $teamId = Auth::getTeamId();

        $memberData = [
            'team_id' => $teamId,
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'email' => $data['email'],
        ];

        try {
            $id = $memberModel->create($memberData);
            $this->success(['id' => $id], 'Member added successfully');
        } catch (\Exception $e) {
            $this->error('Failed to add member: ' . $e->getMessage());
        }
    }
}
