<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Services\CalendarService;
use PDO;

class CalendarController extends Controller
{
    private CalendarService $calendarService;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->calendarService = new CalendarService($db);
    }

    /**
     * Exporte le calendrier d'un projet au format .ics
     */
    public function exportProject(array $data): void
    {
        try {
            if (!isset($data['project_id'])) {
                $this->error('Missing project_id', 400);
                return;
            }

            $projectId = (int)$data['project_id'];

            // Générer le fichier iCalendar
            $icsContent = $this->calendarService->generateProjectCalendar($projectId);

            // Récupérer le nom du projet pour le nom du fichier
            $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = ? AND team_id = ?");
            $stmt->execute([$projectId, Auth::getTeamId()]);
            $project = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$project) {
                $this->error('Project not found', 404);
                return;
            }

            $filename = $this->calendarService->generateFilename($project);

            // Envoyer le fichier
            $this->sendICalendarFile($icsContent, $filename);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Exporte le calendrier de toute l'équipe au format .ics
     */
    public function exportTeam(): void
    {
        try {
            // Générer le fichier iCalendar
            $icsContent = $this->calendarService->generateTeamCalendar();

            $filename = $this->calendarService->generateFilename();

            // Envoyer le fichier
            $this->sendICalendarFile($icsContent, $filename);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Envoie le contenu iCalendar comme fichier téléchargeable
     */
    private function sendICalendarFile(string $content, string $filename): void
    {
        // Headers pour le téléchargement du fichier .ics
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($content));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        echo $content;
        exit;
    }
}
