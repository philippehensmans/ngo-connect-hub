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
    </style>
</head>
<body class="h-screen flex flex-col">

<div id="errorToast" class="fixed top-4 right-4 bg-red-600 text-white p-4 rounded shadow-lg hidden z-50"></div>

<header class="bg-white border-b px-4 py-2 flex justify-between items-center shrink-0">
    <div class="flex items-center gap-3">
        <div class="bg-blue-100 text-blue-700 w-8 h-8 rounded flex items-center justify-center font-bold">
            <?= substr($teamName, 0, 1) ?>
        </div>
        <span class="font-bold text-gray-700"><?= htmlspecialchars($teamName) ?></span>
    </div>
    <div class="flex gap-4 text-sm items-center">
        <select id="langSelect" onchange="ONG.setLang(this.value)" class="bg-transparent cursor-pointer">
            <option value="fr">FR</option>
            <option value="en">EN</option>
            <option value="es">ES</option>
            <option value="sl">SL</option>
        </select>
        <button id="btnTeam" class="hover:text-blue-600">
            <i class="fas fa-users"></i> <?= $t->translate('team') ?>
        </button>
        <button id="btnTemplates" class="hover:text-purple-600">
            <i class="fas fa-copy"></i> Modèles
        </button>
        <button id="btnSettings" class="hover:text-gray-800 text-lg text-gray-500" title="<?= $t->translate('settings') ?>">
            <i class="fas fa-cog"></i>
        </button>
        <a href="?action=download_db" class="hover:text-green-600">
            <i class="fas fa-download"></i> <?= $t->translate('backup') ?>
        </a>
        <button id="btnLogout" class="text-red-500 hover:text-red-700">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</header>

<div class="flex-1 flex overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r flex flex-col">
        <div class="p-3 border-b flex justify-between items-center bg-gray-50">
            <span class="font-bold text-gray-600 text-sm"><?= $t->translate('proj') ?></span>
            <button id="btnAddProject" class="text-blue-600 hover:bg-blue-100 p-1 rounded">
                <i class="fas fa-plus"></i>
            </button>
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
                <button id="btnAddTask" class="bg-blue-600 text-white px-3 py-1 rounded text-sm shadow hover:bg-blue-700">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>

        <!-- Filters Bar -->
        <div id="filtersBar" class="px-4 py-2 bg-gray-50 border-b flex gap-2 overflow-x-auto whitespace-nowrap items-center text-sm">
            <input type="text" id="filterSearch" placeholder="<?= $t->translate('search') ?>"
                   class="border rounded px-2 py-1 w-32 md:w-48 bg-white shrink-0">
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

<script src="public/js/app.js"></script>
</body>
</html>
