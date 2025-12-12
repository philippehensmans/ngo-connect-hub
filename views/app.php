<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t->translate('app') ?> v10.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Frappe Gantt -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css">
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
    <!-- FullCalendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <style>
        :root {
            --primary-color: #2563EB;
            --primary-light: #DBEAFE;
            --primary-dark: #1E40AF;
            --accent-color: #10B981;
        }

        /* Thèmes */
        [data-theme="blue"] { --primary-color: #2563EB; --primary-light: #DBEAFE; --primary-dark: #1E40AF; --accent-color: #10B981; }
        [data-theme="green"] { --primary-color: #10B981; --primary-light: #D1FAE5; --primary-dark: #047857; --accent-color: #3B82F6; }
        [data-theme="purple"] { --primary-color: #8B5CF6; --primary-light: #EDE9FE; --primary-dark: #6D28D9; --accent-color: #EC4899; }
        [data-theme="orange"] { --primary-color: #F97316; --primary-light: #FFEDD5; --primary-dark: #C2410C; --accent-color: #EAB308; }
        [data-theme="red"] { --primary-color: #EF4444; --primary-light: #FEE2E2; --primary-dark: #B91C1C; --accent-color: #F59E0B; }

        body { font-family: 'Segoe UI', sans-serif; background-color: #f3f4f6; }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; justify-content: center; align-items: center; }
        .modal.active { display: flex; }
        .tab-active { border-bottom: 2px solid var(--primary-color); color: var(--primary-color); font-weight: 600; }
        .compact-td { padding: 4px 12px !important; font-size: 0.875rem; }
        .btn-primary { background-color: var(--primary-color) !important; }
        .btn-primary:hover { background-color: var(--primary-dark) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-primary-light { background-color: var(--primary-light) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }

        /* Gantt Chart Customization */
        #gantt-chart-wrapper {
            overflow-x: scroll !important;
            overflow-y: scroll !important;
            width: 100%;
            max-width: calc(100vw - 350px);
            height: 550px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            flex-shrink: 0;
        }
        #gantt-chart-wrapper::-webkit-scrollbar { height: 14px; width: 14px; }
        #gantt-chart-wrapper::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 7px; margin: 10px; }
        #gantt-chart-wrapper::-webkit-scrollbar-thumb { background: #888; border-radius: 7px; }
        #gantt-chart-wrapper::-webkit-scrollbar-thumb:hover { background: #555; }
        #gantt-chart { min-width: max-content; display: block; }
        .gantt .bar { fill: var(--primary-color) !important; }
        .gantt .bar-progress { fill: var(--primary-dark) !important; }
        .gantt .bar-milestone { fill: var(--accent-color) !important; stroke: var(--accent-color) !important; }
        .gantt .bar-label { fill: #fff; font-size: 12px; }
        .gantt .today-highlight { fill: rgba(252, 211, 77, 0.2) !important; }
        .gantt-view-mode { display: flex; gap: 8px; }
        .gantt-view-mode button { padding: 6px 12px; border: 1px solid #e5e7eb; border-radius: 4px; background: white; cursor: pointer; font-size: 13px; transition: all 0.2s; }
        .gantt-view-mode button:hover { background: #f9fafb; }
        .gantt-view-mode button.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }

        /* FullCalendar Customization */
        .fc { height: 100%; }
        .fc .fc-button-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
        .fc .fc-button-primary:hover { background-color: var(--primary-dark) !important; }
        .fc .fc-button-active { background-color: var(--primary-dark) !important; }
        .fc-event { cursor: pointer; }
        .fc-event-milestone { background-color: var(--accent-color) !important; border-color: var(--accent-color) !important; }

        /* Toast Notifications */
        #toastContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
            color: white;
            font-size: 14px;
            position: relative;
        }

        .toast.hiding {
            animation: slideOut 0.3s ease-out forwards;
        }

        .toast-success { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
        .toast-error { background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); }
        .toast-warning { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
        .toast-info { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); }

        .toast-icon {
            font-size: 20px;
            flex-shrink: 0;
        }

        .toast-close {
            position: absolute;
            top: 8px;
            right: 8px;
            background: transparent;
            border: none;
            color: white;
            opacity: 0.7;
            cursor: pointer;
            font-size: 18px;
            padding: 4px 8px;
            line-height: 1;
        }

        .toast-close:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Button Loading State */
        button.loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        button.loading::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 0.6s linear infinite;
        }

        /* Form Validation */
        .input-error {
            border-color: #EF4444 !important;
            background-color: #FEE2E2 !important;
        }

        .input-success {
            border-color: #10B981 !important;
        }

        .error-message {
            color: #EF4444;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        /* Responsive Design pour Mobile */
        @media (max-width: 1023px) {
            /* Gantt chart responsive */
            #gantt-chart-wrapper {
                max-width: 100% !important;
                height: 400px;
            }

            /* Tables responsive */
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table {
                min-width: 600px;
            }

            /* Toast notifications */
            #toastContainer {
                top: 70px;
                right: 10px;
                left: 10px;
                max-width: none;
            }

            .toast {
                font-size: 13px;
                padding: 12px 14px;
            }

            /* Modals responsive */
            .modal > div {
                max-width: 95% !important;
                margin: 10px;
                max-height: 90vh;
                overflow-y: auto;
            }

            /* Filters bar */
            #filtersBar {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Boutons plus compacts sur mobile */
            button {
                font-size: 14px;
            }

            /* Top bar responsive */
            .bg-white.border-b.px-4.py-2 {
                flex-wrap: wrap;
            }

            /* Gantt view mode buttons plus petits */
            .gantt-view-mode button {
                padding: 4px 8px;
                font-size: 11px;
            }
        }

        @media (max-width: 640px) {
            /* Texte encore plus compact sur petit mobile */
            body {
                font-size: 14px;
            }

            h1 {
                font-size: 20px;
            }

            h2 {
                font-size: 18px;
            }

            h3 {
                font-size: 16px;
            }

            /* Padding réduits */
            .p-4 {
                padding: 12px !important;
            }

            .p-6 {
                padding: 16px !important;
            }

            /* Gantt encore plus petit */
            #gantt-chart-wrapper {
                height: 300px;
            }

            /* Tables encore plus scrollables */
            table {
                font-size: 13px;
            }

            table th,
            table td {
                padding: 8px 6px !important;
            }
        }

        /* Empêcher le zoom lors du focus sur input (iOS) */
        @media (max-width: 1023px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="number"],
            input[type="date"],
            textarea,
            select {
                font-size: 16px !important;
            }
        }

        /* Optimisation pour l'orientation paysage (landscape) */
        @media (orientation: landscape) and (max-height: 600px) {
            /* Body et HTML scrollables horizontalement */
            html, body {
                overflow-x: auto !important;
                overflow-y: hidden !important; /* Pas de scroll vertical au niveau body */
                -webkit-overflow-scrolling: touch;
            }

            /* Container principal avec largeur étendue */
            body > div,
            .flex-1.flex.overflow-hidden {
                min-width: 1200px !important; /* Largeur minimale pour toute la page */
                width: max-content;
            }

            /* Masquer le menu hamburger en paysage, afficher la sidebar */
            #btnToggleSidebar {
                display: none !important;
            }

            /* Sidebar toujours visible en paysage, plus large */
            #sidebar {
                position: relative !important;
                transform: translateX(0) !important;
                width: 250px !important; /* Plus large avec scroll horizontal */
                flex-shrink: 0;
            }

            /* Overlay caché en paysage */
            #sidebarOverlay {
                display: none !important;
            }

            /* Header avec largeur étendue */
            header {
                min-width: 1200px;
                width: max-content;
            }

            /* Header plus compact */
            header {
                padding-top: 8px !important;
                padding-bottom: 8px !important;
            }

            /* Top bar plus compact */
            .bg-white.border-b.px-4.py-2 {
                padding-top: 8px !important;
                padding-bottom: 8px !important;
            }

            /* Filters bar plus compact */
            #filtersBar {
                padding-top: 8px !important;
                padding-bottom: 8px !important;
            }

            /* Gantt adapté en paysage */
            #gantt-chart-wrapper {
                height: 350px !important;
                max-width: none !important; /* Permettre la largeur naturelle */
                width: 100% !important;
                min-width: 800px; /* Largeur minimale pour scroll horizontal */
            }

            /* Contenu principal scrollable verticalement ET horizontalement */
            #viewContainer {
                height: calc(100vh - 120px) !important;
                overflow-x: auto !important; /* Scroll horizontal */
                overflow-y: auto !important; /* Scroll vertical */
                -webkit-overflow-scrolling: touch; /* Scroll fluide iOS */
            }

            /* Main content étendu pour bénéficier du scroll horizontal de la page */
            main {
                flex: 1;
                min-width: 900px !important; /* Plus large pour profiter de l'espace */
                max-width: none !important;
            }

            /* Permettre aux tableaux leur largeur complète sans contrainte */
            table {
                width: auto !important;
                min-width: auto !important;
            }

            /* Wrapper pour les sections - pas de scroll interne, on utilise le scroll de la page */
            .table-container,
            .chart-container {
                overflow-x: visible !important;
                width: auto;
            }

            /* Navigation tabs avec plus d'espace */
            #navTabs {
                min-width: 600px;
            }

            /* Toast plus petit en paysage */
            #toastContainer {
                top: 10px;
            }

            .toast {
                padding: 8px 12px;
                font-size: 12px;
            }

            /* Modals adaptées en paysage */
            .modal > div {
                max-height: 80vh !important;
            }

            /* Boutons plus compacts */
            button, a {
                padding: 6px 10px !important;
                font-size: 13px !important;
            }

            /* Sidebar liste projets */
            #listProjects {
                font-size: 13px;
            }

            /* Textes plus compacts */
            h1 { font-size: 18px !important; }
            h2 { font-size: 16px !important; }
            h3 { font-size: 14px !important; }

            /* Scrollbars visibles et stylées en paysage - pour toute la page */
            body::-webkit-scrollbar,
            #viewContainer::-webkit-scrollbar,
            main::-webkit-scrollbar {
                width: 10px;
                height: 10px;
            }

            body::-webkit-scrollbar-track,
            #viewContainer::-webkit-scrollbar-track,
            main::-webkit-scrollbar-track {
                background: #e5e7eb;
                border-radius: 5px;
            }

            body::-webkit-scrollbar-thumb,
            #viewContainer::-webkit-scrollbar-thumb,
            main::-webkit-scrollbar-thumb {
                background: var(--primary-color);
                border-radius: 5px;
            }

            body::-webkit-scrollbar-thumb:hover,
            #viewContainer::-webkit-scrollbar-thumb:hover,
            main::-webkit-scrollbar-thumb:hover {
                background: var(--primary-dark);
            }

            /* Indication visuelle de scroll horizontal disponible */
            body::after {
                content: '↔ Swipe horizontal';
                position: fixed;
                bottom: 10px;
                right: 10px;
                background: var(--primary-color);
                color: white;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11px;
                z-index: 1000;
                opacity: 0.7;
                animation: fadeInOut 3s ease-in-out;
            }

            @keyframes fadeInOut {
                0%, 100% { opacity: 0; }
                50% { opacity: 0.7; }
            }
        }

        /* Orientation paysage sur tablettes (plus de hauteur disponible) */
        @media (orientation: landscape) and (min-height: 601px) and (max-width: 1023px) {
            /* Sidebar visible mais possibilité de la cacher reste */
            #sidebar {
                width: 220px;
            }

            #gantt-chart-wrapper {
                height: 450px;
                max-width: calc(100vw - 250px);
            }
        }
    </style>
</head>
<body class="h-screen flex flex-col">

<!-- Toast Notifications Container -->
<div id="toastContainer"></div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmation</h3>
                <p id="confirmMessage" class="text-gray-600 text-sm"></p>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <button id="confirmCancel" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 text-gray-700">
                Annuler
            </button>
            <button id="confirmOk" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Confirmer
            </button>
        </div>
    </div>
</div>

<header class="bg-white border-b px-4 py-2 flex justify-between items-center shrink-0">
    <div class="flex items-center gap-3">
        <!-- Menu Hamburger (visible sur mobile uniquement) -->
        <button id="btnToggleSidebar" class="lg:hidden text-gray-600 hover:text-gray-900 -ml-2">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <?php
        $logoPath = __DIR__ . '/../public/images/logo.png';
        if (file_exists($logoPath)) {
            // Afficher le logo s'il existe
            echo '<img src="images/logo.png" alt="Logo" class="h-10 w-auto object-contain">';
        } else {
            // Afficher le carré avec l'initiale si pas de logo
            echo '<div class="bg-blue-100 text-blue-700 w-8 h-8 rounded flex items-center justify-center font-bold">';
            echo substr($teamName, 0, 1);
            echo '</div>';
        }
        ?>
        <span class="font-bold text-gray-700 hidden sm:inline"><?= htmlspecialchars($teamName) ?></span>
    </div>

    <!-- Desktop Navigation -->
    <div class="hidden lg:flex gap-4 text-sm items-center">
        <select id="langSelect" onchange="ONG.setLang(this.value)" class="bg-transparent cursor-pointer">
            <option value="fr">FR</option>
            <option value="en">EN</option>
            <option value="es">ES</option>
            <option value="sl">SL</option>
        </select>

        <!-- Menu déroulant Nouveau -->
        <div class="relative">
            <button id="btnNew" class="hover:text-green-600 font-semibold flex items-center gap-1">
                <i class="fas fa-plus-circle"></i> <?= $t->translate('new') ?>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div id="newDropdown" class="hidden absolute top-full left-0 mt-1 bg-white border rounded-lg shadow-xl z-50 w-48">
                <div class="py-1">
                    <button onclick="ONG.openModalProject()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-folder text-blue-600"></i> <?= $t->translate('new_proj') ?>
                    </button>
                    <button onclick="ONG.openTaskModal()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-tasks text-green-600"></i> <?= $t->translate('new_task') ?>
                    </button>
                    <button onclick="ONG.openMilestoneModal()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-flag text-purple-600"></i> <?= $t->translate('new_milestone') ?>
                    </button>
                    <button onclick="ONG.openGroupModal()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-users text-orange-600"></i> <?= $t->translate('new_group') ?>
                    </button>
                </div>
            </div>
        </div>

        <button id="btnTeam" class="hover:text-blue-600">
            <i class="fas fa-users"></i> <?= $t->translate('team') ?>
        </button>
        <button id="btnTemplates" class="hover:text-purple-600">
            <i class="fas fa-copy"></i> <?= $t->translate('templates') ?>
        </button>
        <button id="btnExportProject" class="hover:text-green-600" title="<?= $t->translate('export_project') ?>">
            <i class="fas fa-file-export"></i>
        </button>
        <button id="btnImportProject" class="hover:text-blue-600" title="<?= $t->translate('import_project') ?>">
            <i class="fas fa-file-import"></i>
        </button>
        <button id="btnExportCalendar" class="hover:text-purple-600" title="<?= $t->translate('export_calendar') ?>">
            <i class="fas fa-calendar-alt"></i>
        </button>
        <a href="?action=help" target="_blank" class="hover:text-blue-600" title="<?= $t->translate('help') ?>">
            <i class="fas fa-question-circle"></i>
        </a>
        <button id="btnSettings" class="hover:text-gray-800 text-lg text-gray-500" title="<?= $t->translate('settings') ?>">
            <i class="fas fa-cog"></i>
        </button>
        <a href="?action=download_db" class="hover:text-green-600">
            <i class="fas fa-download"></i> <?= $t->translate('backup') ?>
        </a>
        <button id="btnLogout" class="text-red-500 hover:text-red-700" title="<?= $t->translate('logout') ?>">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>

    <!-- Mobile Menu Button -->
    <button id="btnMobileMenu" class="lg:hidden text-gray-600 hover:text-gray-900">
        <i class="fas fa-ellipsis-v text-xl"></i>
    </button>
</header>

<!-- Mobile Menu Dropdown -->
<div id="mobileMenuDropdown" class="hidden lg:hidden absolute top-14 right-4 bg-white border rounded-lg shadow-xl z-50 w-56">
    <div class="py-2">
        <select id="langSelectMobile" onchange="ONG.setLang(this.value)" class="w-full px-4 py-2 text-left hover:bg-gray-100 cursor-pointer border-0">
            <option value="fr">🌐 Français</option>
            <option value="en">🌐 English</option>
            <option value="es">🌐 Español</option>
            <option value="sl">🌐 Slovenščina</option>
        </select>
        <div class="border-t my-2"></div>
        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase"><?= $t->translate('new') ?></div>
        <button onclick="ONG.openModalProject()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-folder text-blue-600 w-5"></i> <?= $t->translate('new_proj') ?>
        </button>
        <button onclick="ONG.openTaskModal()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-tasks text-green-600 w-5"></i> <?= $t->translate('new_task') ?>
        </button>
        <button onclick="ONG.openMilestoneModal()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-flag text-purple-600 w-5"></i> <?= $t->translate('new_milestone') ?>
        </button>
        <button onclick="ONG.openGroupModal()" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-users text-orange-600 w-5"></i> <?= $t->translate('new_group') ?>
        </button>
        <div class="border-t my-2"></div>
        <button id="btnTeamMobile" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-users w-5"></i> <?= $t->translate('team') ?>
        </button>
        <button id="btnTemplatesMobile" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-copy w-5"></i> <?= $t->translate('templates') ?>
        </button>
        <button id="btnExportProjectMobile" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-file-export w-5"></i> <?= $t->translate('export_project') ?>
        </button>
        <button id="btnImportProjectMobile" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-file-import w-5"></i> <?= $t->translate('import_project') ?>
        </button>
        <button id="btnExportCalendarMobile" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-calendar-alt w-5"></i> <?= $t->translate('export_calendar') ?>
        </button>
        <a href="?action=help" target="_blank" class="block w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-question-circle w-5"></i> <?= $t->translate('help') ?>
        </a>
        <button id="btnSettingsMobile" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-cog w-5"></i> <?= $t->translate('settings') ?>
        </button>
        <a href="?action=download_db" class="block w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-download w-5"></i> <?= $t->translate('backup') ?>
        </a>
        <div class="border-t my-2"></div>
        <button id="btnLogoutMobile" class="w-full px-4 py-2 text-left hover:bg-gray-100 text-red-600 flex items-center gap-2">
            <i class="fas fa-sign-out-alt w-5"></i> <?= $t->translate('logout') ?>
        </button>
    </div>
</div>

<div class="flex-1 flex overflow-hidden relative">
    <!-- Sidebar Overlay (mobile uniquement) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white border-r flex flex-col fixed lg:relative inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="p-3 border-b flex justify-between items-center bg-gray-50">
            <span class="font-bold text-gray-600 text-sm"><?= $t->translate('proj') ?></span>
            <div class="flex gap-2 items-center">
                <button id="btnAddProject" class="text-blue-600 hover:bg-blue-100 p-1 rounded">
                    <i class="fas fa-plus"></i>
                </button>
                <!-- Bouton fermer sidebar (mobile uniquement) -->
                <button id="btnCloseSidebar" class="lg:hidden text-gray-600 hover:bg-gray-100 p-1 rounded">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div id="listProjects" class="flex-1 overflow-y-auto p-2 space-y-1"></div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative bg-gray-50">
        <!-- Top Bar -->
        <div class="bg-white border-b px-4 py-2 flex justify-between items-center shrink-0">
            <div class="flex gap-4 overflow-x-auto" id="navTabs"></div>
            <div class="flex gap-2">
                <button id="btnExport" class="text-green-600 border border-green-200 px-3 py-1 rounded text-sm hover:bg-green-50">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
        </div>

        <!-- Filters Bar -->
        <div id="filtersBar" class="px-4 py-2 bg-gray-50 border-b flex gap-2 overflow-x-auto whitespace-nowrap items-center text-sm">
            <div class="relative">
                <input type="text" id="filterSearch" placeholder="Rechercher (titre, description, tags, projet...)"
                       class="border rounded px-2 py-1 w-48 md:w-64 bg-white shrink-0 pr-8">
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <select id="filterResp" class="border rounded px-2 py-1 bg-white shrink-0">
                <option value=""><?= $t->translate('filter_resp') ?></option>
            </select>
            <select id="filterStatut" class="border rounded px-2 py-1 bg-white shrink-0">
                <option value=""><?= $t->translate('filter_status') ?></option>
                <option value="todo"><?= $t->translate('todo') ?></option>
                <option value="wip"><?= $t->translate('wip') ?></option>
                <option value="done"><?= $t->translate('done') ?></option>
            </select>
            <select id="filterTag" class="border rounded px-2 py-1 bg-white shrink-0">
                <option value=""><?= $t->translate('filter_tags') ?></option>
            </select>
            <button id="btnResetFilters" class="text-gray-500 hover:text-blue-600 underline shrink-0">
                <?= $t->translate('reset') ?>
            </button>
            <div class="ml-auto text-xs text-gray-400" id="tasksCount"></div>
        </div>

        <!-- View Container -->
        <div id="viewContainer" class="flex-1 overflow-auto p-4"></div>
    </main>
</div>

<!-- Modals -->
<?php include __DIR__ . '/modals.php'; ?>

<!-- Responsive Mobile Navigation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');
    const btnCloseSidebar = document.getElementById('btnCloseSidebar');
    const btnMobileMenu = document.getElementById('btnMobileMenu');
    const mobileMenuDropdown = document.getElementById('mobileMenuDropdown');

    // Toggle Sidebar sur mobile
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        sidebarOverlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        sidebarOverlay.classList.add('hidden');
    }

    if (btnToggleSidebar) {
        btnToggleSidebar.addEventListener('click', openSidebar);
    }

    if (btnCloseSidebar) {
        btnCloseSidebar.addEventListener('click', closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Toggle Menu Mobile
    function toggleMobileMenu() {
        mobileMenuDropdown.classList.toggle('hidden');
    }

    if (btnMobileMenu) {
        btnMobileMenu.addEventListener('click', toggleMobileMenu);
    }

    // Fermer le menu mobile quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (mobileMenuDropdown && !mobileMenuDropdown.contains(e.target) && e.target !== btnMobileMenu) {
            mobileMenuDropdown.classList.add('hidden');
        }
    });

    // Toggle Menu Nouveau (Desktop)
    const btnNew = document.getElementById('btnNew');
    const newDropdown = document.getElementById('newDropdown');

    function toggleNewDropdown(e) {
        e.stopPropagation();
        newDropdown.classList.toggle('hidden');
    }

    if (btnNew) {
        btnNew.addEventListener('click', toggleNewDropdown);
    }

    // Fermer le menu nouveau quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (newDropdown && !newDropdown.contains(e.target) && e.target !== btnNew) {
            newDropdown.classList.add('hidden');
        }
    });

    // Fermer le dropdown après avoir cliqué sur un item
    if (newDropdown) {
        newDropdown.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function() {
                newDropdown.classList.add('hidden');
            });
        });
    }

    // Fermer le menu mobile après avoir cliqué sur les items "Nouveau"
    if (mobileMenuDropdown) {
        const newItemButtons = [
            'button[onclick="ONG.openModalProject()"]',
            'button[onclick="ONG.openModal(\'modalTask\')"]',
            'button[onclick="ONG.openMilestoneModal()"]',
            'button[onclick="ONG.openGroupModal()"]'
        ];

        newItemButtons.forEach(selector => {
            const btns = mobileMenuDropdown.querySelectorAll(selector);
            btns.forEach(btn => {
                btn.addEventListener('click', function() {
                    mobileMenuDropdown.classList.add('hidden');
                });
            });
        });
    }

    // Dupliquer les événements des boutons desktop vers mobile
    const mobileBtnMap = {
        'btnTeamMobile': 'btnTeam',
        'btnTemplatesMobile': 'btnTemplates',
        'btnExportProjectMobile': 'btnExportProject',
        'btnImportProjectMobile': 'btnImportProject',
        'btnExportCalendarMobile': 'btnExportCalendar',
        'btnSettingsMobile': 'btnSettings',
        'btnLogoutMobile': 'btnLogout'
    };

    Object.keys(mobileBtnMap).forEach(mobileId => {
        const mobileBtn = document.getElementById(mobileId);
        const desktopBtn = document.getElementById(mobileBtnMap[mobileId]);
        if (mobileBtn && desktopBtn) {
            mobileBtn.addEventListener('click', function() {
                desktopBtn.click();
                mobileMenuDropdown.classList.add('hidden');
            });
        }
    });

    // Synchroniser les sélecteurs de langue
    const langSelect = document.getElementById('langSelect');
    const langSelectMobile = document.getElementById('langSelectMobile');
    if (langSelect && langSelectMobile) {
        langSelect.addEventListener('change', function() {
            langSelectMobile.value = this.value;
        });
        langSelectMobile.value = langSelect.value;
    }
});
</script>

<script src="public/js/app.js?v=<?= time() ?>"></script>
</body>
</html>
