<?php

namespace App\Services;

use PDO;

class CalendarService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Génère un fichier iCalendar (.ics) pour un projet
     */
    public function generateProjectCalendar(int $projectId): string
    {
        $teamId = Auth::getTeamId();

        // Récupérer le projet
        $stmt = $this->db->prepare("
            SELECT * FROM projects
            WHERE id = ? AND team_id = ?
        ");
        $stmt->execute([$projectId, $teamId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            throw new \Exception('Project not found');
        }

        // Récupérer les jalons
        $stmt = $this->db->prepare("
            SELECT * FROM milestones
            WHERE project_id = ?
            ORDER BY date ASC
        ");
        $stmt->execute([$projectId]);
        $milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les tâches avec échéance
        $stmt = $this->db->prepare("
            SELECT t.*, g.name as group_name, m.name as milestone_name
            FROM tasks t
            LEFT JOIN groups g ON t.group_id = g.id
            LEFT JOIN milestones m ON t.milestone_id = m.id
            WHERE t.project_id = ? AND t.end_date IS NOT NULL
            ORDER BY t.end_date ASC
        ");
        $stmt->execute([$projectId]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->buildICalendar($project, $milestones, $tasks);
    }

    /**
     * Génère un fichier iCalendar pour tous les projets de l'équipe
     */
    public function generateTeamCalendar(): string
    {
        $teamId = Auth::getTeamId();

        // Récupérer tous les projets
        $stmt = $this->db->prepare("
            SELECT * FROM projects WHERE team_id = ?
        ");
        $stmt->execute([$teamId]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $allMilestones = [];
        $allTasks = [];

        foreach ($projects as $project) {
            // Récupérer les jalons
            $stmt = $this->db->prepare("
                SELECT * FROM milestones
                WHERE project_id = ?
                ORDER BY date ASC
            ");
            $stmt->execute([$project['id']]);
            $milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($milestones as $milestone) {
                $milestone['project_name'] = $project['name'];
                $allMilestones[] = $milestone;
            }

            // Récupérer les tâches avec échéance
            $stmt = $this->db->prepare("
                SELECT t.*, g.name as group_name, m.name as milestone_name
                FROM tasks t
                LEFT JOIN groups g ON t.group_id = g.id
                LEFT JOIN milestones m ON t.milestone_id = m.id
                WHERE t.project_id = ? AND t.end_date IS NOT NULL
                ORDER BY t.end_date ASC
            ");
            $stmt->execute([$project['id']]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tasks as $task) {
                $task['project_name'] = $project['name'];
                $allTasks[] = $task;
            }
        }

        return $this->buildICalendar(null, $allMilestones, $allTasks);
    }

    /**
     * Construit le contenu iCalendar
     */
    private function buildICalendar(?array $project, array $milestones, array $tasks): string
    {
        $teamId = Auth::getTeamId();

        // Récupérer le nom de l'équipe
        $stmt = $this->db->prepare("SELECT name FROM teams WHERE id = ?");
        $stmt->execute([$teamId]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        $calendarName = $project
            ? $this->escapeICalText($project['name'])
            : $this->escapeICalText($team['name'] ?? 'NGO Calendar');

        $ical = [];
        $ical[] = 'BEGIN:VCALENDAR';
        $ical[] = 'VERSION:2.0';
        $ical[] = 'PRODID:-//NGO Connect Hub//Calendar//EN';
        $ical[] = 'CALSCALE:GREGORIAN';
        $ical[] = 'METHOD:PUBLISH';
        $ical[] = 'X-WR-CALNAME:' . $calendarName;
        $ical[] = 'X-WR-TIMEZONE:UTC';

        // Ajouter les événements pour les jalons
        foreach ($milestones as $milestone) {
            $ical = array_merge($ical, $this->createMilestoneEvent($milestone, $project));
        }

        // Ajouter les événements pour les tâches
        foreach ($tasks as $task) {
            $ical = array_merge($ical, $this->createTaskEvent($task, $project));
        }

        $ical[] = 'END:VCALENDAR';

        return implode("\r\n", $ical);
    }

    /**
     * Crée un événement VEVENT pour un jalon
     */
    private function createMilestoneEvent(array $milestone, ?array $project): array
    {
        $event = [];
        $event[] = 'BEGIN:VEVENT';

        // UID unique
        $uid = 'milestone-' . $milestone['id'] . '@ngo-connect-hub';
        $event[] = 'UID:' . $uid;

        // Dates
        $dueDate = $this->formatICalDate($milestone['date']);
        $event[] = 'DTSTART;VALUE=DATE:' . $dueDate;
        $event[] = 'DTEND;VALUE=DATE:' . $dueDate;

        // Titre et description
        $projectPrefix = $project ? '' : '[' . $this->escapeICalText($milestone['project_name']) . '] ';
        $summary = $projectPrefix . '🎯 ' . $this->escapeICalText($milestone['name']);
        $event[] = 'SUMMARY:' . $summary;

        // Statut
        $status = $milestone['status'] === 'completed' ? 'COMPLETED' : 'CONFIRMED';
        $event[] = 'STATUS:' . $status;

        // Catégorie
        $event[] = 'CATEGORIES:Milestone';

        // Priorité (jalons sont importants)
        $event[] = 'PRIORITY:1';

        // Rappel 3 jours avant
        $event[] = 'BEGIN:VALARM';
        $event[] = 'TRIGGER:-P3D';
        $event[] = 'ACTION:DISPLAY';
        $event[] = 'DESCRIPTION:Rappel: Jalon à venir - ' . $this->escapeICalText($milestone['name']);
        $event[] = 'END:VALARM';

        // Dates de création et modification
        $now = $this->formatICalDateTime(time());
        $event[] = 'DTSTAMP:' . $now;
        $event[] = 'CREATED:' . $this->formatICalDateTime(strtotime($milestone['created_at']));

        $event[] = 'END:VEVENT';

        return $event;
    }

    /**
     * Crée un événement VEVENT pour une tâche
     */
    private function createTaskEvent(array $task, ?array $project): array
    {
        $event = [];
        $event[] = 'BEGIN:VEVENT';

        // UID unique
        $uid = 'task-' . $task['id'] . '@ngo-connect-hub';
        $event[] = 'UID:' . $uid;

        // Dates
        $dueDate = $this->formatICalDate($task['end_date']);
        $event[] = 'DTSTART;VALUE=DATE:' . $dueDate;
        $event[] = 'DTEND;VALUE=DATE:' . $dueDate;

        // Titre et description
        $projectPrefix = $project ? '' : '[' . $this->escapeICalText($task['project_name']) . '] ';
        $groupPrefix = $task['group_name'] ? $this->escapeICalText($task['group_name']) . ' - ' : '';
        $summary = $projectPrefix . $groupPrefix . $this->escapeICalText($task['title']);
        $event[] = 'SUMMARY:' . $summary;

        $description = [];
        if (!empty($task['desc'])) {
            $description[] = $task['desc'];
        }
        if (!empty($task['milestone_name'])) {
            $description[] = 'Jalon: ' . $task['milestone_name'];
        }
        if (!empty($description)) {
            $event[] = 'DESCRIPTION:' . $this->escapeICalText(implode("\n\n", $description));
        }

        // Statut
        $status = $task['status'] === 'completed' ? 'COMPLETED' : 'CONFIRMED';
        $event[] = 'STATUS:' . $status;

        // Catégorie
        $event[] = 'CATEGORIES:Task';

        // Priorité basée sur la priorité de la tâche
        $priority = $this->mapPriority($task['priority'] ?? 'medium');
        $event[] = 'PRIORITY:' . $priority;

        // Rappel 1 jour avant
        $event[] = 'BEGIN:VALARM';
        $event[] = 'TRIGGER:-P1D';
        $event[] = 'ACTION:DISPLAY';
        $event[] = 'DESCRIPTION:Rappel: Tâche à faire - ' . $this->escapeICalText($task['title']);
        $event[] = 'END:VALARM';

        // Dates de création et modification
        $now = $this->formatICalDateTime(time());
        $event[] = 'DTSTAMP:' . $now;
        $event[] = 'CREATED:' . $this->formatICalDateTime(strtotime($task['created_at']));

        $event[] = 'END:VEVENT';

        return $event;
    }

    /**
     * Formate une date au format iCalendar (YYYYMMDD)
     */
    private function formatICalDate(string $date): string
    {
        return date('Ymd', strtotime($date));
    }

    /**
     * Formate une date/heure au format iCalendar (YYYYMMDDTHHmmssZ)
     */
    private function formatICalDateTime(int $timestamp): string
    {
        return gmdate('Ymd\THis\Z', $timestamp);
    }

    /**
     * Échappe le texte pour iCalendar (RFC 5545)
     */
    private function escapeICalText(string $text): string
    {
        // Remplacer les caractères spéciaux
        $text = str_replace(['\\', ',', ';', "\n", "\r"], ['\\\\', '\\,', '\\;', '\\n', ''], $text);

        // Limiter la longueur des lignes à 75 caractères (RFC 5545)
        return $text;
    }

    /**
     * Convertit la priorité en format iCalendar (1-9)
     */
    private function mapPriority(string $priority): int
    {
        $map = [
            'high' => 1,
            'medium' => 5,
            'low' => 9
        ];

        return $map[$priority] ?? 5;
    }

    /**
     * Génère le nom du fichier .ics
     */
    public function generateFilename(?array $project = null): string
    {
        if ($project) {
            $slug = $this->slugify($project['name']);
            return 'calendar-' . $slug . '.ics';
        }

        return 'calendar-team.ics';
    }

    /**
     * Convertit une chaîne en slug
     */
    private function slugify(string $text): string
    {
        // Remplacer les caractères non-ASCII
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        // Remplacer les caractères non-alphanumériques par des tirets
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        // Supprimer les tirets en début et fin
        $text = trim($text, '-');
        // Tout en minuscules
        return strtolower($text);
    }
}
