/**
 * Application ONG Manager - Frontend
 * Version 10.0 - Architecture modulaire
 */

window.ONG = {
    // Données de l'application
    data: {
        projects: [],
        tasks: [],
        groups: [],
        milestones: [],
        members: []
    },

    // État de l'application
    state: {
        pid: null,
        view: 'dashboard',
        lang: 'fr',
        sort: { col: 'end_date', dir: 'asc' },
        templates: [],
        currentTemplateTab: 'list'
    },

    // Dictionnaire de traductions
    dict: {
        fr: {
            todo: 'À faire',
            wip: 'En cours',
            done: 'Terminé',
            dashboard: 'Tableau de Bord',
            list: 'Liste',
            kanban: 'Kanban',
            groups: 'Groupes',
            gantt: 'Gantt',
            calendar: 'Calendrier',
            milestones: 'Jalons',
            global: 'Vue Globale'
        },
        en: {
            todo: 'To Do',
            wip: 'In Progress',
            done: 'Done',
            dashboard: 'Dashboard',
            list: 'List',
            kanban: 'Kanban',
            groups: 'Groups',
            gantt: 'Gantt',
            calendar: 'Calendar',
            milestones: 'Milestones',
            global: 'Global View'
        },
        es: {
            todo: 'Pendiente',
            wip: 'En curso',
            done: 'Hecho',
            dashboard: 'Panel',
            list: 'Lista',
            kanban: 'Kanban',
            groups: 'Grupos',
            gantt: 'Gantt',
            calendar: 'Calendario',
            milestones: 'Hitos',
            global: 'Global'
        },
        sl: {
            todo: 'Za narediti',
            wip: 'V teku',
            done: 'Končano',
            dashboard: 'Nadzorna plošča',
            list: 'Seznam',
            kanban: 'Kanban',
            groups: 'Skupine',
            gantt: 'Gantt',
            calendar: 'Kalendar',
            milestones: 'Mejniki',
            global: 'Globalno'
        }
    },

    /**
     * Met à jour le compteur de caractères
     * @param {HTMLElement} textarea - Element textarea
     * @param {string} counterId - ID de l'élément compteur
     */
    updateCharCount: (textarea, counterId) => {
        const counter = document.getElementById(counterId);
        if (!counter) return;

        const current = textarea.value.length;
        const max = textarea.maxLength || 1000;
        counter.textContent = `${current}/${max}`;

        // Changer la couleur en fonction de la progression
        if (current > max * 0.9) {
            counter.classList.add('text-red-500');
            counter.classList.remove('text-amber-500', 'text-gray-400');
        } else if (current > max * 0.75) {
            counter.classList.add('text-amber-500');
            counter.classList.remove('text-red-500', 'text-gray-400');
        } else {
            counter.classList.add('text-gray-400');
            counter.classList.remove('text-red-500', 'text-amber-500');
        }
    },

    /**
     * Convertit du Markdown simple en HTML
     * @param {string} text - Texte markdown
     * @return {string} HTML
     */
    markdownToHtml: (text) => {
        if (!text) return '';

        let html = ONG.escape(text);

        // Gras: **texte** ou __texte__
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');

        // Italique: *texte* ou _texte_
        html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
        html = html.replace(/_(.+?)_/g, '<em>$1</em>');

        // Code inline: `code`
        html = html.replace(/`(.+?)`/g, '<code class="bg-gray-200 px-1 rounded text-xs">$1</code>');

        // Lien: [texte](url)
        html = html.replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2" target="_blank" class="text-blue-600 hover:underline">$1</a>');

        // Liste à puces: - item ou * item
        html = html.replace(/^[\-\*] (.+)$/gm, '<li class="ml-4">$1</li>');

        // Entourer les listes de <ul>
        html = html.replace(/(<li class="ml-4">.+<\/li>\n?)+/g, '<ul class="list-disc list-inside my-1">$&</ul>');

        // Sauts de ligne
        html = html.replace(/\n/g, '<br>');

        return html;
    },

    /**
     * Affiche une notification toast
     * @param {string} message - Le message à afficher
     * @param {string} type - Type: success, error, warning, info
     * @param {number} duration - Durée en ms (défaut: 4000)
     */
    toast: (message, type = 'info', duration = 4000) => {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${icons[type]}</span>
            <div class="flex-1">${ONG.escape(message)}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        `;

        container.appendChild(toast);

        // Auto-remove après duration
        setTimeout(() => {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    /**
     * Affiche une confirmation personnalisée avec callbacks
     * @param {string} message - Message de confirmation
     * @param {function} onConfirm - Callback si confirmé
     * @param {function} onCancel - Callback si annulé (optionnel)
     */
    confirm: (message, onConfirm, onCancel = null) => {
        const modal = document.getElementById('confirmModal');
        const messageEl = document.getElementById('confirmMessage');
        const okBtn = document.getElementById('confirmOk');
        const cancelBtn = document.getElementById('confirmCancel');

        if (!modal || !messageEl || !okBtn || !cancelBtn) {
            // Fallback au confirm natif si le modal n'existe pas
            if (confirm(message)) {
                onConfirm();
            } else if (onCancel) {
                onCancel();
            }
            return;
        }

        // Afficher le message
        messageEl.textContent = message;

        // Afficher le modal
        modal.classList.add('active');

        // Gérer le clic sur Confirmer
        const handleConfirm = () => {
            modal.classList.remove('active');
            okBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
            onConfirm();
        };

        // Gérer le clic sur Annuler
        const handleCancel = () => {
            modal.classList.remove('active');
            okBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
            if (onCancel) onCancel();
        };

        // Attacher les événements
        okBtn.addEventListener('click', handleConfirm);
        cancelBtn.addEventListener('click', handleCancel);

        // Fermer avec Escape
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                handleCancel();
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
    },

    /**
     * Initialise l'application
     */
    init: async () => {
        // Charger le thème sauvegardé
        ONG.loadTheme();

        // Si on est sur la page de login, initialiser le formulaire de login
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.onsubmit = async (e) => {
                e.preventDefault();
                const fd = new FormData(loginForm);
                fd.append('action', 'login');
                const res = await fetch('', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.ok) {
                    location.reload();
                } else {
                    ONG.toast(data.msg, 'error');
                }
            };
            return;
        }

        // Charger les données
        await ONG.loadData();

        // Initialiser la langue
        const langSelect = ONG.el('langSelect');
        if (langSelect) {
            const urlParams = new URLSearchParams(window.location.search);
            ONG.state.lang = urlParams.get('lang') || 'fr';
            langSelect.value = ONG.state.lang;
        }

        // Attacher les événements
        ONG.attachEvents();
    },

    /**
     * Attache tous les événements aux éléments
     */
    attachEvents: () => {
        // Boutons principaux
        ONG.on('btnLogout', 'click', () => ONG.post('logout').then(() => location.reload()));
        ONG.on('btnTeam', 'click', () => ONG.openModal('modalTeam'));
        ONG.on('btnTemplates', 'click', () => ONG.openTemplatesModal());
        ONG.on('btnExportProject', 'click', () => ONG.exportProject(ONG.state.pid));
        ONG.on('btnImportProject', 'click', () => ONG.openImportModal());
        ONG.on('btnSettings', 'click', () => {
            ONG.openModal('modalSettings');
            ONG.loadBackupsList();
        });
        ONG.on('btnAddProject', 'click', () => ONG.openModalProject());
        ONG.on('btnAddTask', 'click', () => ONG.openTaskModal());
        ONG.on('btnExport', 'click', () => ONG.exportExcel());
        ONG.on('btnResetFilters', 'click', () => ONG.resetFilters());
        ONG.on('btnAddComment', 'click', () => ONG.addComment());

        // Changement de projet dans le modal de tâche
        ONG.on('taskProjectSelect', 'change', (e) => ONG.updateTaskModalDeps(e.target.value));

        // Filtres
        ['filterSearch', 'filterResp', 'filterStatut', 'filterTag'].forEach(id => {
            ONG.on(id, 'input', () => ONG.renderView());
        });

        // Boutons de fermeture des modaux
        document.querySelectorAll('.btn-close').forEach(b => {
            b.onclick = (e) => ONG.closeModal(e.target.closest('.modal').id);
        });

        // Formulaires
        ONG.attachFormHandlers();
    },

    /**
     * Attache les handlers de formulaires
     */
    attachFormHandlers: () => {
        // Formulaire des paramètres
        ONG.onSubmit('formSettings', async (fd) => {
            const r = await ONG.post('update_settings', fd);
            alert(r.msg);
            if (r.ok) location.reload();
        });

        // Formulaire de projet
        ONG.onSubmit('formProject', async (fd) => {
            await ONG.post('save_project', fd);
            ONG.closeModal('modalProject');
            ONG.loadData();
        });

        // Formulaire de groupe
        ONG.onSubmit('formGroup', async (fd) => {
            await ONG.post('save_group', fd);
            ONG.closeModal('modalGroup');
            ONG.loadData();
        });

        // Formulaire de jalon
        ONG.onSubmit('formMilestone', async (fd) => {
            await ONG.post('save_milestone', fd);
            ONG.closeModal('modalMilestone');
            ONG.loadData();
        });

        // Formulaire de membre
        ONG.onSubmit('formMember', async (fd) => {
            await ONG.post('save_member', fd);
            ONG.cancelEditMember();
            ONG.loadData();
        });

        // Formulaire de tâche
        ONG.onSubmit('formTask', async (fd) => {
            const deps = Array.from(document.querySelectorAll('.dep-check:checked'))
                .map(c => c.value)
                .join(',');
            fd.append('dependencies', deps);
            await ONG.post('save_task', fd);
            ONG.closeModal('modalTask');
            ONG.loadData();
        });

        // Formulaire de création de template
        ONG.onSubmit('formCreateTemplate', async (fd) => {
            const r = await ONG.post('save_template', fd);
            if (r.ok) {
                alert('Modèle créé avec succès !');
                ONG.loadTemplates();
                ONG.switchTemplateTab('list');
            }
        });

        // Formulaire d'utilisation de template
        ONG.onSubmit('formUseTemplate', async (fd) => {
            const r = await ONG.post('create_from_template', fd);
            if (r.ok) {
                alert('Projet créé avec succès depuis le modèle !');
                ONG.closeModal('modalTemplates');
                await ONG.loadData();
            }
        });
    },

    /**
     * Effectue une requête POST à l'API
     */
    post: async (action, data = new FormData()) => {
        if (!(data instanceof FormData)) {
            const fd = new FormData();
            for (const key in data) {
                fd.append(key, data[key]);
            }
            data = fd;
        }

        data.append('action', action);

        try {
            const r = await fetch('', { method: 'POST', body: data });
            const j = await r.json();
            if (!j.ok) throw new Error(j.msg);
            return j;
        } catch (e) {
            ONG.showError(e.message);
            return { ok: false };
        }
    },

    /**
     * Affiche un message d'erreur
     */
    showError: (message) => {
        const toast = document.getElementById('errorToast');
        if (toast) {
            toast.innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }
    },

    /**
     * Charge toutes les données depuis l'API
     */
    loadData: async () => {
        const r = await ONG.post('load_all');
        if (r.ok) {
            ONG.data = r.data;
            ONG.renderSidebar();
            ONG.fillFilters();
            ONG.renderView();
            ONG.fillTeamSelects();
            ONG.checkConflicts();
        }
    },

    /**
     * Rend la barre latérale avec les projets
     */
    renderSidebar: () => {
        const container = ONG.el('listProjects');
        if (!container) return;

        container.innerHTML = ONG.data.projects.map(p => `
            <div onclick="ONG.setProject(${p.id})"
                 class="p-2 rounded cursor-pointer flex justify-between items-center group hover:bg-gray-100
                        ${ONG.state.pid == p.id ? 'bg-blue-50 text-blue-600 font-bold' : ''}">
                <span class="truncate">${ONG.escape(p.name)}</span>
                <div class="flex gap-1">
                    <button onclick="event.stopPropagation(); ONG.editProject(${p.id})"
                            class="text-blue-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 px-1">✏️</button>
                    <button onclick="event.stopPropagation(); ONG.deleteItem('projects', ${p.id})"
                            class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 px-1">🗑️</button>
                </div>
            </div>
        `).join('');

        // Mettre à jour les selects de projets
        const opts = ONG.data.projects.map(p => `<option value="${p.id}">${ONG.escape(p.name)}</option>`).join('');
        ['taskProjectSelect'].forEach(id => {
            const el = ONG.el(id);
            if (el) el.innerHTML = opts;
        });
    },

    /**
     * Définit le projet actif
     */
    setProject: (id) => {
        ONG.state.pid = id;
        ONG.renderSidebar();
        ONG.renderView();
    },

    /**
     * Rend la vue principale
     */
    renderView: () => {
        const t = ONG.dict[ONG.state.lang];
        const tabs = ['dashboard', 'global', 'list', 'kanban', 'groups', 'gantt', 'calendar', 'milestones'];

        // Rendre les onglets
        const navTabs = ONG.el('navTabs');
        if (navTabs) {
            navTabs.innerHTML = tabs.map(k => `
                <button onclick="ONG.switchView('${k}')"
                        class="px-4 py-2 text-sm whitespace-nowrap ${ONG.state.view === k ? 'tab-active' : 'text-gray-500'}">
                    ${t[k]}
                </button>
            `).join('');
        }

        const container = ONG.el('viewContainer');
        if (!container) return;

        const tasks = ONG.getProcessedTasks();

        // Rendre la vue selon le type
        switch (ONG.state.view) {
            case 'dashboard':
                ONG.renderDashboardView(container);
                break;
            case 'list':
            case 'global':
                ONG.renderListView(container, tasks);
                break;
            case 'kanban':
                ONG.renderKanbanView(container, tasks);
                break;
            case 'groups':
                ONG.renderGroupsView(container, tasks);
                break;
            case 'milestones':
                ONG.renderMilestonesView(container, tasks);
                break;
            case 'gantt':
                ONG.renderGanttView(container, tasks);
                break;
            case 'calendar':
                ONG.renderCalendarView(container, tasks);
                break;
        }
    },

    /**
     * Rend la vue en liste
     */
    renderListView: (container, tasks) => {
        // Trier les tâches par date de départ
        tasks.sort((a, b) => {
            // Tâches sans date de départ vont à la fin
            if (!a.start_date && !b.start_date) return 0;
            if (!a.start_date) return 1;
            if (!b.start_date) return -1;
            return a.start_date.localeCompare(b.start_date);
        });

        // Calculer les niveaux de hiérarchie basés sur les dépendances
        const taskMap = new Map(tasks.map(t => [t.id, t]));
        const taskLevels = new Map();

        const calculateLevel = (task, visited = new Set()) => {
            if (taskLevels.has(task.id)) return taskLevels.get(task.id);
            if (visited.has(task.id)) return 0; // Éviter les boucles infinies

            visited.add(task.id);

            if (!task.dependencies || task.dependencies.trim() === '') {
                taskLevels.set(task.id, 0);
                return 0;
            }

            const deps = task.dependencies.split(',').map(d => parseInt(d.trim())).filter(d => !isNaN(d));
            let maxLevel = 0;

            deps.forEach(depId => {
                const depTask = taskMap.get(depId);
                if (depTask) {
                    const depLevel = calculateLevel(depTask, new Set(visited));
                    maxLevel = Math.max(maxLevel, depLevel + 1);
                }
            });

            taskLevels.set(task.id, maxLevel);
            return maxLevel;
        };

        tasks.forEach(t => calculateLevel(t));

        // Grouper les tâches par jalon
        const tasksByMilestone = new Map();
        const tasksWithoutMilestone = [];

        tasks.forEach(t => {
            if (t.milestone_id) {
                if (!tasksByMilestone.has(t.milestone_id)) {
                    tasksByMilestone.set(t.milestone_id, []);
                }
                tasksByMilestone.get(t.milestone_id).push(t);
            } else {
                tasksWithoutMilestone.push(t);
            }
        });

        // Récupérer et trier les jalons par date
        const milestones = ONG.data.milestones
            .filter(m => ONG.state.view === 'global' || m.project_id == ONG.state.pid)
            .sort((a, b) => (a.date || '').localeCompare(b.date || ''));

        // Fonction pour rendre une ligne de tâche
        const renderTaskRow = (t) => {
            const hasConflict = ONG.hasConflict(t);
            const rowClass = hasConflict ? 'bg-red-100 border-l-4 border-red-500' : 'hover:bg-gray-50';
            const conflictIcon = hasConflict ? '<span title="Conflit de date détecté">⚠️</span> ' : '';

            const level = taskLevels.get(t.id) || 0;
            const indent = level * 20;

            // Récupérer les noms des tâches dépendantes
            let depsInfo = '';
            if (t.dependencies && t.dependencies.trim() !== '') {
                const deps = t.dependencies.split(',').map(d => parseInt(d.trim())).filter(d => !isNaN(d));
                const depNames = deps.map(depId => {
                    const depTask = taskMap.get(depId);
                    return depTask ? ONG.escape(depTask.title) : `#${depId}`;
                });
                if (depNames.length > 0) {
                    depsInfo = `<span class="text-xs text-gray-500" title="${depNames.join(', ')}">🔗 ${depNames.length}</span>`;
                }
            }

            return `
                <tr class="border-b ${rowClass}">
                    <td class="compact-td font-medium">
                        <div style="padding-left: ${indent}px;">
                            ${level > 0 ? '<span class="text-gray-400">└─</span> ' : ''}
                            ${conflictIcon}${ONG.escape(t.title)}
                        </div>
                    </td>
                    <td class="compact-td text-gray-600">${ONG.getMemberName(t.owner_id)}</td>
                    <td class="compact-td text-gray-500">${t.start_date || ''}</td>
                    <td class="compact-td text-gray-500">${t.end_date || ''}</td>
                    <td class="compact-td">
                        <span class="px-2 rounded text-xs bg-gray-200">
                            ${ONG.dict[ONG.state.lang][t.status] || t.status}
                        </span>
                    </td>
                    <td class="compact-td">${depsInfo}</td>
                    <td class="compact-td text-right">
                        ${t.link ? `<a href="${ONG.escape(t.link)}" target="_blank" class="text-blue-500 mr-2">🔗</a>` : ''}
                        <button onclick="ONG.editTask(${t.id})" class="text-blue-600 mr-2">✏️</button>
                        <button onclick="ONG.deleteItem('tasks', ${t.id})" class="text-red-500">🗑️</button>
                    </td>
                </tr>
            `;
        };

        let html = `
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left bg-white shadow rounded">
                    <thead class="bg-gray-100 border-b cursor-pointer select-none">
                        <tr>
                            <th class="px-3 py-2" onclick="ONG.sortData('title')">Titre</th>
                            <th class="px-3 py-2" onclick="ONG.sortData('owner_id')">Responsable</th>
                            <th class="px-3 py-2" onclick="ONG.sortData('start_date')">Début</th>
                            <th class="px-3 py-2" onclick="ONG.sortData('end_date')">Fin</th>
                            <th class="px-3 py-2" onclick="ONG.sortData('status')">Statut</th>
                            <th class="px-3 py-2">Dépendances</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        // Afficher les jalons avec leurs tâches
        milestones.forEach(milestone => {
            const milestoneTasks = tasksByMilestone.get(milestone.id) || [];
            if (milestoneTasks.length > 0) {
                const doneCount = milestoneTasks.filter(t => t.status === 'done').length;
                const progress = Math.round((doneCount / milestoneTasks.length) * 100);

                html += `
                    <tr class="bg-indigo-50 border-t-2 border-indigo-300">
                        <td colspan="7" class="px-3 py-2 font-bold">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-indigo-600">📍</span>
                                    <span>${ONG.escape(milestone.name)}</span>
                                    <span class="text-xs font-normal text-gray-600">(${milestone.date})</span>
                                    <span class="text-xs font-normal text-gray-500">${milestoneTasks.length} tâche${milestoneTasks.length > 1 ? 's' : ''}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: ${progress}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">${progress}%</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;

                milestoneTasks.forEach(t => {
                    html += renderTaskRow(t);
                });
            }
        });

        // Afficher les tâches sans jalon
        if (tasksWithoutMilestone.length > 0) {
            html += `
                <tr class="bg-gray-50 border-t-2 border-gray-300">
                    <td colspan="7" class="px-3 py-2 font-bold text-gray-600">
                        <div class="flex items-center gap-2">
                            <span>📋</span>
                            <span>Tâches sans jalon</span>
                            <span class="text-xs font-normal">${tasksWithoutMilestone.length} tâche${tasksWithoutMilestone.length > 1 ? 's' : ''}</span>
                        </div>
                    </td>
                </tr>
            `;

            tasksWithoutMilestone.forEach(t => {
                html += renderTaskRow(t);
            });
        }

        html += `
                    </tbody>
                </table>
            </div>
            <div class="text-right text-xs text-gray-400 mt-2">${tasks.length} tâches</div>
        `;

        container.innerHTML = html;
    },

    /**
     * Rend la vue Kanban
     */
    renderKanbanView: (container, tasks) => {
        const cols = {
            todo: ONG.dict[ONG.state.lang].todo,
            wip: ONG.dict[ONG.state.lang].wip,
            done: ONG.dict[ONG.state.lang].done
        };

        container.className = "flex gap-4 h-full overflow-x-auto p-4";

        // Récupérer les groupes du projet actuel
        const groups = ONG.data.groups.filter(g =>
            ONG.state.view === 'global' || g.project_id == ONG.state.pid
        );

        let html = '';
        for (let status in cols) {
            const colTasks = tasks.filter(t => t.status === status);

            // Grouper les tâches par groupe
            const tasksByGroup = new Map();
            const tasksWithoutGroup = [];

            colTasks.forEach(t => {
                if (t.group_id) {
                    if (!tasksByGroup.has(t.group_id)) {
                        tasksByGroup.set(t.group_id, []);
                    }
                    tasksByGroup.get(t.group_id).push(t);
                } else {
                    tasksWithoutGroup.push(t);
                }
            });

            html += `
                <div class="w-80 bg-gray-100 rounded-lg p-3 flex-shrink-0 flex flex-col">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">
                        ${cols[status]} (${colTasks.length})
                    </h3>
                    <div class="space-y-3 overflow-y-auto flex-1">
            `;

            // Afficher les tâches groupées par groupe
            groups.forEach(group => {
                const groupTasks = tasksByGroup.get(group.id);
                if (groupTasks && groupTasks.length > 0) {
                    html += `
                        <div class="mb-2">
                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-600 mb-1 px-2">
                                <span class="w-2 h-2 rounded-full" style="background:${group.color}"></span>
                                <span>${ONG.escape(group.name)}</span>
                                <span class="text-gray-400">(${groupTasks.length})</span>
                            </div>
                    `;

                    groupTasks.forEach(t => {
                        const hasConflict = ONG.hasConflict(t);
                        const borderColor = hasConflict ? 'border-red-500' : `border-[${group.color}]`;
                        const bgColor = hasConflict ? 'bg-red-50' : 'bg-white';
                        const conflictIcon = hasConflict ? '<span title="Conflit de date">⚠️</span> ' : '';
                        const ownerName = ONG.getMemberName(t.owner_id);

                        html += `
                            <div class="${bgColor} p-3 rounded shadow cursor-pointer hover:shadow-md border-l-4 mb-2" style="border-left-color: ${group.color}"
                                 onclick="ONG.editTask(${t.id})">
                                <div class="text-sm font-medium mb-1">${conflictIcon}${ONG.escape(t.title)}</div>
                                <div class="text-xs text-gray-500 flex justify-between items-center">
                                    <span class="flex items-center gap-1">
                                        <span>👤</span>
                                        <span>${ownerName}</span>
                                    </span>
                                    <span>${t.end_date || ''}</span>
                                </div>
                                ${t.link ? '<div class="text-xs text-blue-500 mt-1">🔗 Lien</div>' : ''}
                            </div>
                        `;
                    });

                    html += `</div>`;
                }
            });

            // Afficher les tâches sans groupe
            if (tasksWithoutGroup.length > 0) {
                html += `
                    <div class="mb-2">
                        <div class="text-xs font-semibold text-gray-400 mb-1 px-2">Sans groupe (${tasksWithoutGroup.length})</div>
                `;

                tasksWithoutGroup.forEach(t => {
                    const hasConflict = ONG.hasConflict(t);
                    const borderColor = hasConflict ? 'border-red-500' : 'border-gray-400';
                    const bgColor = hasConflict ? 'bg-red-50' : 'bg-white';
                    const conflictIcon = hasConflict ? '<span title="Conflit de date">⚠️</span> ' : '';
                    const ownerName = ONG.getMemberName(t.owner_id);

                    html += `
                        <div class="${bgColor} p-3 rounded shadow cursor-pointer hover:shadow-md border-l-4 ${borderColor} mb-2"
                             onclick="ONG.editTask(${t.id})">
                            <div class="text-sm font-medium mb-1">${conflictIcon}${ONG.escape(t.title)}</div>
                            <div class="text-xs text-gray-500 flex justify-between items-center">
                                <span class="flex items-center gap-1">
                                    <span>👤</span>
                                    <span>${ownerName}</span>
                                </span>
                                <span>${t.end_date || ''}</span>
                            </div>
                            ${t.link ? '<div class="text-xs text-blue-500 mt-1">🔗 Lien</div>' : ''}
                        </div>
                    `;
                });

                html += `</div>`;
            }

            html += `
                    </div>
                </div>
            `;
        }

        container.innerHTML = html;
    },

    /**
     * Rend la vue des groupes
     */
    renderGroupsView: (container, tasks) => {
        if (!ONG.state.pid) {
            container.innerHTML = "<p class='text-center text-gray-400'>Sélectionnez un projet</p>";
            return;
        }

        const groups = ONG.data.groups.filter(g => g.project_id == ONG.state.pid);

        let html = `
            <div class="mb-4">
                <button onclick="ONG.openGroupModal()" class="bg-blue-600 text-white px-3 py-1 rounded shadow">
                    + Nouveau Groupe
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                ${groups.map(g => {
                    const gTasks = tasks.filter(t => t.group_id == g.id);
                    // Trier les tâches par date de début
                    const sortedTasks = gTasks.sort((a, b) => {
                        if (!a.start_date) return 1;
                        if (!b.start_date) return -1;
                        return new Date(a.start_date) - new Date(b.start_date);
                    });
                    const done = gTasks.filter(t => t.status === 'done').length;
                    const pct = gTasks.length ? Math.round((done / gTasks.length) * 100) : 0;

                    return `
                        <div class="bg-white p-4 rounded shadow border-l-4" style="border-color:${g.color}">
                            <div class="flex justify-between mb-2">
                                <h3 class="font-bold">${ONG.escape(g.name)}</h3>
                                <div>
                                    <button onclick='ONG.editGroup(${JSON.stringify(g)})' class="text-blue-500 mr-1">✏️</button>
                                    <button onclick="ONG.deleteItem('groups', ${g.id})" class="text-red-500">🗑️</button>
                                </div>
                            </div>
                            ${g.description ? `<div class="text-sm text-gray-600 mb-2 italic">${ONG.escape(g.description)}</div>` : ''}
                            <div class="text-xs text-gray-500 mb-2">Responsable: ${ONG.getMemberName(g.owner_id)}</div>
                            ${gTasks.length > 0 ? `
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${pct}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">${done}/${gTasks.length} tâches</div>
                                <div class="mt-3 border-t pt-3">
                                    <div class="text-sm font-semibold mb-2 text-gray-700">Tâches :</div>
                                    <div class="space-y-2">
                                        ${sortedTasks.map(t => {
                                            const statusIcon = t.status === 'done' ? '✅' : t.status === 'wip' ? '🔄' : '⭕';
                                            const statusClass = t.status === 'done' ? 'line-through text-gray-400' : '';
                                            return `
                                                <div class="flex items-center justify-between text-sm py-1 px-2 hover:bg-gray-50 rounded">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <span>${statusIcon}</span>
                                                        <span class="${statusClass} flex-1 text-xs">${ONG.escape(t.title)}</span>
                                                    </div>
                                                    ${t.start_date ? `<span class="text-xs text-gray-500">📅 ${t.start_date}</span>` : ''}
                                                    <button onclick="ONG.editTask(${t.id})" class="text-blue-500 text-xs ml-2">✏️</button>
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                </div>
                            ` : '<div class="text-sm text-gray-400 italic mt-2">Aucune tâche</div>'}
                        </div>
                    `;
                }).join('')}
            </div>
        `;

        container.innerHTML = html;
    },

    /**
     * Rend la vue des jalons
     */
    renderMilestonesView: (container, tasks) => {
        if (!ONG.state.pid) {
            container.innerHTML = "<p class='text-center text-gray-400'>Sélectionnez un projet</p>";
            return;
        }

        const milestones = ONG.data.milestones.filter(m => m.project_id == ONG.state.pid);

        let html = `
            <div class="mb-4">
                <button onclick="ONG.openMilestoneModal()" class="bg-indigo-600 text-white px-3 py-1 rounded shadow">
                    + Nouveau Jalon
                </button>
            </div>
            <div class="space-y-4">
                ${milestones.map(m => {
                    const mTasks = tasks.filter(t => t.milestone_id == m.id);
                    // Trier les tâches par date de début
                    const sortedTasks = mTasks.sort((a, b) => {
                        if (!a.start_date) return 1;
                        if (!b.start_date) return -1;
                        return new Date(a.start_date) - new Date(b.start_date);
                    });
                    const done = mTasks.filter(t => t.status === 'done').length;
                    const pct = mTasks.length ? Math.round((done / mTasks.length) * 100) : 0;

                    return `
                        <div class="bg-white p-4 rounded shadow">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-bold text-lg">
                                    ${ONG.escape(m.name)}
                                    <span class="text-xs font-normal text-gray-500">(${m.date})</span>
                                </h3>
                                <div>
                                    <button onclick='ONG.editMilestone(${JSON.stringify(m)})' class="text-blue-500 mr-2">✏️</button>
                                    <button onclick="ONG.deleteItem('milestones', ${m.id})" class="text-red-500">🗑️</button>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${pct}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mb-3">${done}/${mTasks.length} tâches</div>
                            ${sortedTasks.length > 0 ? `
                                <div class="mt-4 border-t pt-3">
                                    <div class="text-sm font-semibold mb-2 text-gray-700">Tâches :</div>
                                    <div class="space-y-2">
                                        ${sortedTasks.map(t => {
                                            const statusIcon = t.status === 'done' ? '✅' : t.status === 'wip' ? '🔄' : '⭕';
                                            const statusClass = t.status === 'done' ? 'line-through text-gray-400' : '';
                                            const priorityColor = t.priority === 'high' ? 'text-red-500' : t.priority === 'medium' ? 'text-orange-500' : 'text-green-500';
                                            return `
                                                <div class="flex items-center justify-between text-sm py-1 px-2 hover:bg-gray-50 rounded">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <span>${statusIcon}</span>
                                                        <span class="${statusClass} flex-1">${ONG.escape(t.title)}</span>
                                                        ${t.start_date ? `<span class="text-xs text-gray-500">📅 ${t.start_date}</span>` : ''}
                                                    </div>
                                                    <button onclick="ONG.editTask(${t.id})" class="text-blue-500 text-xs ml-2">✏️</button>
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                </div>
                            ` : '<div class="text-sm text-gray-400 italic mt-4">Aucune tâche</div>'}
                        </div>
                    `;
                }).join('')}
            </div>
        `;

        container.innerHTML = html;
    },

    /**
     * Rend la vue Gantt
     */
    renderGanttView: (container, tasks) => {
        console.log('=== DEBUG GANTT ===');
        console.log('Projet ID:', ONG.state.pid);
        console.log('Nombre de tâches:', tasks.length);
        console.log('Gantt chargé?', typeof Gantt !== 'undefined');

        if (!ONG.state.pid) {
            container.innerHTML = "<p class='text-center text-gray-400'>Sélectionnez un projet</p>";
            return;
        }

        // Vérifier que Frappe Gantt est chargé
        if (typeof Gantt === 'undefined') {
            container.innerHTML = "<p class='text-center text-red-500'>❌ Erreur: Bibliothèque Gantt non chargée</p>";
            console.error('Frappe Gantt non chargé !');
            return;
        }

        // Filtrer les tâches avec dates
        const tasksWithDates = tasks.filter(t => t.start_date && t.end_date);
        console.log('Tâches avec dates:', tasksWithDates.length);
        console.log('Exemples de tâches:', tasksWithDates.slice(0, 3));

        if (tasksWithDates.length === 0) {
            container.innerHTML = "<div class='bg-white p-6 rounded shadow'><p class='text-center text-orange-500'>⚠️ Aucune tâche avec dates de début et fin</p><p class='text-sm text-gray-500 mt-2'>Total de tâches: " + tasks.length + "</p></div>";
            return;
        }

        // Obtenir les jalons
        const milestones = ONG.data.milestones.filter(m => m.project_id == ONG.state.pid);

        // Créer une map des groupes pour les couleurs
        const groupColors = new Map();
        ONG.data.groups.filter(g => g.project_id == ONG.state.pid).forEach(g => {
            groupColors.set(g.id, g.color);
        });

        // Convertir les tâches au format Gantt
        const ganttTasks = tasksWithDates.map(t => {
            const groupColor = t.group_id ? groupColors.get(t.group_id) : '#2563EB';

            return {
                id: 't_' + t.id,
                name: t.title,
                start: t.start_date,
                end: t.end_date,
                progress: t.status === 'done' ? 100 : t.status === 'wip' ? 50 : 0,
                dependencies: t.dependencies ? t.dependencies.split(',').map(d => 't_' + d.trim()).join(',') : '',
                custom_class: 'task-bar',
                task_data: t
            };
        });

        // Ajouter les jalons
        milestones.forEach(m => {
            if (m.date) {
                ganttTasks.push({
                    id: 'm_' + m.id,
                    name: '◆ ' + m.name,
                    start: m.date,
                    end: m.date,
                    progress: 100,
                    dependencies: '',
                    custom_class: 'bar-milestone'
                });
            }
        });

        // Trier par date de début
        ganttTasks.sort((a, b) => a.start.localeCompare(b.start));

        // Créer le HTML
        let html = `
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex justify-between items-center mb-4">
                    <div class="gantt-view-mode">
                        <button class="mode-btn" data-mode="Day">Jour</button>
                        <button class="mode-btn active" data-mode="Week">Semaine</button>
                        <button class="mode-btn" data-mode="Month">Mois</button>
                    </div>
                    <div class="flex gap-2">
                        <button id="gantt-today" class="px-3 py-1 text-sm border rounded hover:bg-gray-50" title="Aller à aujourd'hui">
                            📅 Aujourd'hui
                        </button>
                    </div>
                </div>
                <div id="gantt-chart-wrapper" style="overflow-x: auto; overflow-y: hidden; cursor: grab;">
                    <div id="gantt-chart"></div>
                </div>
                <div class="mt-4 p-3 bg-gray-50 rounded text-xs space-y-1">
                    <p><strong>💡 Navigation :</strong></p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li><strong>Défilement horizontal :</strong> Molette de souris, glisser-déposer, ou barre de défilement</li>
                        <li><strong>Aujourd'hui :</strong> Cliquez sur le bouton "📅 Aujourd'hui" ci-dessus</li>
                        <li><strong>Modifier une tâche :</strong> Cliquez sur la barre de tâche</li>
                        <li><strong>Changer les dates :</strong> Glissez les barres horizontalement</li>
                        <li><strong>Jalons :</strong> Représentés par des losanges (◆)</li>
                        <li><strong>Dépendances :</strong> Montrées par des flèches entre tâches</li>
                    </ul>
                </div>
            </div>
        `;

        container.innerHTML = html;

        // Initialiser le Gantt
        let currentMode = 'Week';
        let ganttInstance = null;

        const initGantt = (viewMode) => {
            try {
                ganttInstance = new Gantt('#gantt-chart', ganttTasks, {
                    view_mode: viewMode,
                    language: ONG.state.lang === 'fr' ? 'fr' : 'en',
                    bar_height: 30,
                    bar_corner_radius: 3,
                    arrow_curve: 5,
                    padding: 18,
                    date_format: 'YYYY-MM-DD',
                    column_width: viewMode === 'Day' ? 40 : viewMode === 'Week' ? 120 : 180,
                    custom_popup_html: function(task) {
                        const taskData = task.task_data;
                        if (!taskData) {
                            // C'est un jalon
                            return `
                                <div class="p-2">
                                    <h5 class="font-bold">${ONG.escape(task.name)}</h5>
                                    <p class="text-xs text-gray-500">Jalon: ${task.start}</p>
                                </div>
                            `;
                        }

                        const owner = ONG.getMemberName(taskData.owner_id);
                        const statusLabel = taskData.status === 'done' ? '✅ Terminé' : taskData.status === 'wip' ? '🔄 En cours' : '⭕ À faire';

                        return `
                            <div class="p-2">
                                <h5 class="font-bold">${ONG.escape(task.name)}</h5>
                                <p class="text-xs text-gray-600 mt-1">${statusLabel}</p>
                                <p class="text-xs text-gray-600">👤 ${ONG.escape(owner)}</p>
                                <p class="text-xs text-gray-500 mt-1">${task.start} → ${task.end}</p>
                                <p class="text-xs text-blue-500 mt-2">Cliquez pour modifier</p>
                            </div>
                        `;
                    },
                    on_click: function(task) {
                        // Si c'est une tâche (pas un jalon)
                        if (task.id.startsWith('t_')) {
                            const taskId = parseInt(task.id.substring(2));
                            ONG.editTask(taskId);
                        }
                    },
                    on_date_change: function(task, start, end) {
                        // Mise à jour de la tâche quand on drag & drop
                        if (task.id.startsWith('t_')) {
                            const taskId = parseInt(task.id.substring(2));
                            const taskData = ONG.data.tasks.find(t => t.id === taskId);
                            if (taskData) {
                                taskData.start_date = start.toISOString().split('T')[0];
                                taskData.end_date = end.toISOString().split('T')[0];

                                // Sauvegarder via API
                                ONG.api('update_task', {
                                    id: taskId,
                                    start_date: taskData.start_date,
                                    end_date: taskData.end_date
                                }).then(() => {
                                    ONG.showToast('Dates mises à jour', 'success');
                                });
                            }
                        }
                    }
                });
            } catch (err) {
                console.error('Erreur Gantt:', err);
                container.innerHTML = `<div class='bg-white p-6 rounded shadow'><p class='text-center text-red-500'>Erreur lors de l'affichage du Gantt: ${err.message}</p></div>`;
            }
        };

        // Initialiser avec le mode par défaut
        initGantt(currentMode);

        // FORCER la largeur du wrapper via JavaScript
        const wrapper = container.querySelector('#gantt-chart-wrapper');
        if (wrapper) {
            // Calculer la largeur max (viewport - sidebar - padding)
            const maxWidth = window.innerWidth - 350;
            wrapper.style.maxWidth = maxWidth + 'px';
            wrapper.style.width = '100%';
            wrapper.style.overflowX = 'scroll';
            console.log('🔧 Wrapper forcé à max-width:', maxWidth + 'px');

            // Debug scroll après initialisation
            setTimeout(() => {
                console.log('=== DIMENSIONS GANTT ===');
                console.log('Wrapper width:', wrapper.clientWidth);
                console.log('Wrapper scrollWidth:', wrapper.scrollWidth);
                console.log('Scrollable?', wrapper.scrollWidth > wrapper.clientWidth);
                console.log('Max-width appliqué:', wrapper.style.maxWidth);
            }, 500);
        }

        // Gérer les changements de mode de vue
        container.querySelectorAll('.mode-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                container.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentMode = this.dataset.mode;
                initGantt(currentMode);
            });
        });

        // Bouton "Aujourd'hui" - scroll vers la date du jour
        const todayBtn = container.querySelector('#gantt-today');

        if (todayBtn && wrapper) {
            todayBtn.addEventListener('click', () => {
                const todayMarker = wrapper.querySelector('.today-highlight');
                if (todayMarker) {
                    // Scroll vers le marqueur du jour
                    const markerLeft = todayMarker.getBoundingClientRect().left;
                    const wrapperLeft = wrapper.getBoundingClientRect().left;
                    const scrollTarget = wrapper.scrollLeft + markerLeft - wrapperLeft - wrapper.clientWidth / 2;
                    wrapper.scrollTo({ left: scrollTarget, behavior: 'smooth' });
                } else {
                    // Si pas de marqueur, scroll vers le milieu
                    wrapper.scrollLeft = (wrapper.scrollWidth - wrapper.clientWidth) / 2;
                }
            });
        }

        // Améliorer le scroll horizontal avec la molette
        if (wrapper) {
            wrapper.addEventListener('wheel', (e) => {
                // Si scroll vertical (molette standard), convertir en horizontal
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    wrapper.scrollLeft += e.deltaY;
                }
            }, { passive: false });

            // Drag pour faire défiler
            let isDown = false;
            let startX;
            let scrollLeft;

            wrapper.addEventListener('mousedown', (e) => {
                // Ne pas intercepter les clics sur les barres de tâches
                if (e.target.closest('.bar-wrapper') || e.target.closest('.bar')) return;

                isDown = true;
                wrapper.style.cursor = 'grabbing';
                startX = e.pageX - wrapper.offsetLeft;
                scrollLeft = wrapper.scrollLeft;
            });

            wrapper.addEventListener('mouseleave', () => {
                isDown = false;
                wrapper.style.cursor = 'grab';
            });

            wrapper.addEventListener('mouseup', () => {
                isDown = false;
                wrapper.style.cursor = 'grab';
            });

            wrapper.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - wrapper.offsetLeft;
                const walk = (x - startX) * 2; // Vitesse de défilement
                wrapper.scrollLeft = scrollLeft - walk;
            });
        }
    },

    /**
     * Rend la vue Calendrier
     */
    renderCalendarView: (container, tasks) => {
        if (!ONG.state.pid) {
            container.innerHTML = "<p class='text-center text-gray-400'>Sélectionnez un projet</p>";
            return;
        }

        // Vérifier que FullCalendar est chargé
        if (typeof FullCalendar === 'undefined') {
            container.innerHTML = "<p class='text-center text-red-500'>❌ Erreur: Bibliothèque FullCalendar non chargée</p>";
            return;
        }

        // Créer le conteneur du calendrier
        container.innerHTML = '<div id="calendar-wrapper" class="h-full bg-white rounded-lg shadow p-4"></div>';

        const calendarEl = container.querySelector('#calendar-wrapper');

        // Convertir les tâches en événements calendar
        const events = [];

        // Ajouter les tâches
        tasks.forEach(task => {
            if (task.start_date && task.end_date) {
                const group = ONG.data.groups.find(g => g.id == task.group_id);
                const groupColor = group ? group.color : '#2563EB';

                events.push({
                    id: 't_' + task.id,
                    title: task.title,
                    start: task.start_date,
                    end: task.end_date,
                    backgroundColor: groupColor,
                    borderColor: groupColor,
                    extendedProps: {
                        type: 'task',
                        taskData: task,
                        owner: ONG.getMemberName(task.owner_id),
                        status: task.status
                    }
                });
            }
        });

        // Ajouter les jalons
        const milestones = ONG.data.milestones.filter(m => m.project_id == ONG.state.pid);
        milestones.forEach(milestone => {
            if (milestone.date) {
                events.push({
                    id: 'm_' + milestone.id,
                    title: '◆ ' + milestone.name,
                    start: milestone.date,
                    allDay: true,
                    backgroundColor: '#10B981',
                    borderColor: '#10B981',
                    classNames: ['fc-event-milestone'],
                    extendedProps: {
                        type: 'milestone',
                        milestoneData: milestone
                    }
                });
            }
        });

        // Initialiser FullCalendar
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: ONG.state.lang === 'fr' ? 'fr' : ONG.state.lang === 'es' ? 'es' : ONG.state.lang === 'sl' ? 'sl' : 'en',
            firstDay: 1, // Lundi
            events: events,
            editable: true, // Permet le drag & drop
            eventClick: function(info) {
                const eventType = info.event.extendedProps.type;
                if (eventType === 'task') {
                    const taskId = parseInt(info.event.id.substring(2));
                    ONG.editTask(taskId);
                }
                // Les jalons ne sont pas éditables pour l'instant
            },
            eventDrop: function(info) {
                // Mise à jour quand on déplace un événement
                if (info.event.extendedProps.type === 'task') {
                    const taskId = parseInt(info.event.id.substring(2));
                    const task = ONG.data.tasks.find(t => t.id === taskId);

                    if (task) {
                        // Calculer la nouvelle date de fin
                        const duration = new Date(task.end_date) - new Date(task.start_date);
                        const newStart = info.event.start.toISOString().split('T')[0];
                        const newEnd = new Date(info.event.start.getTime() + duration).toISOString().split('T')[0];

                        task.start_date = newStart;
                        task.end_date = newEnd;

                        // Sauvegarder via API
                        ONG.api('update_task', {
                            id: taskId,
                            start_date: newStart,
                            end_date: newEnd
                        }).then(() => {
                            ONG.showToast('Dates mises à jour', 'success');
                        }).catch(() => {
                            // Annuler le changement en cas d'erreur
                            info.revert();
                        });
                    }
                }
            },
            eventResize: function(info) {
                // Mise à jour quand on redimensionne un événement
                if (info.event.extendedProps.type === 'task') {
                    const taskId = parseInt(info.event.id.substring(2));
                    const task = ONG.data.tasks.find(t => t.id === taskId);

                    if (task) {
                        const newEnd = info.event.end ? info.event.end.toISOString().split('T')[0] : info.event.start.toISOString().split('T')[0];

                        task.end_date = newEnd;

                        // Sauvegarder via API
                        ONG.api('update_task', {
                            id: taskId,
                            end_date: newEnd
                        }).then(() => {
                            ONG.showToast('Date de fin mise à jour', 'success');
                        }).catch(() => {
                            info.revert();
                        });
                    }
                }
            },
            eventContent: function(arg) {
                const status = arg.event.extendedProps.status;
                let icon = '';
                if (status === 'done') icon = '✅ ';
                else if (status === 'wip') icon = '🔄 ';
                else if (status === 'todo') icon = '⭕ ';

                return { html: '<div class="fc-event-title">' + icon + arg.event.title + '</div>' };
            }
        });

        calendar.render();
    },

    /**
     * Obtient et filtre les tâches
     */
    getProcessedTasks: () => {
        let tasks = ONG.data.tasks || [];

        // Filtrer par projet si nécessaire
        if (ONG.state.view !== 'global' && ONG.state.pid) {
            tasks = tasks.filter(t => t.project_id == ONG.state.pid);
        }

        // Appliquer les filtres
        const search = ONG.el('filterSearch')?.value.toLowerCase();
        const resp = ONG.el('filterResp')?.value;
        const stat = ONG.el('filterStatut')?.value;
        const tag = ONG.el('filterTag')?.value;

        if (search || resp || stat || tag) {
            tasks = tasks.filter(t => {
                // Recherche full-text avancée
                if (search) {
                    const searchTerms = search.split(' ').filter(term => term.length > 0);
                    const searchable = [
                        t.title || '',
                        t.desc || '',
                        t.tags || '',
                        t.link || '',
                        // Nom du projet
                        ONG.data.projects.find(p => p.id == t.project_id)?.name || '',
                        // Nom du responsable
                        ONG.data.members.find(m => m.id == t.owner_id)?.fname || '',
                        ONG.data.members.find(m => m.id == t.owner_id)?.lname || '',
                        // Nom du groupe
                        ONG.data.groups.find(g => g.id == t.group_id)?.name || ''
                    ].join(' ').toLowerCase();

                    // Tous les termes doivent être présents (AND)
                    const matchesAll = searchTerms.every(term => searchable.includes(term));
                    if (!matchesAll) return false;
                }

                if (resp && t.owner_id != resp) return false;
                if (stat && t.status != stat) return false;
                if (tag && (!t.tags || !t.tags.includes(tag))) return false;
                return true;
            });
        }

        // Trier
        const col = ONG.state.sort.col;
        tasks.sort((a, b) => {
            let va = a[col] || '';
            let vb = b[col] || '';
            if (col == 'owner_id') {
                va = ONG.getMemberName(va);
                vb = ONG.getMemberName(vb);
            }
            if (ONG.state.sort.dir === 'asc') {
                return va.toString().localeCompare(vb.toString());
            }
            return vb.toString().localeCompare(va.toString());
        });

        return tasks;
    },

    /**
     * Change la vue
     */
    switchView: (view) => {
        ONG.state.view = view;
        ONG.renderView();
    },

    /**
     * Ouvre un modal
     */
    openModal: (id) => {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('active');
    },

    /**
     * Ferme un modal
     */
    closeModal: (id) => {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('active');
    },

    /**
     * Ouvre le modal de création de tâche
     */
    openTaskModal: () => {
        const form = document.querySelector('#modalTask form');
        if (form) form.reset();

        ONG.setVal('taskId', '');
        const title = document.getElementById('modalTaskTitle');
        if (title) title.textContent = "Nouvelle Tâche";

        // Réinitialiser l'ID de la tâche en cours d'édition
        ONG.state.editingTaskId = null;

        // Masquer la section commentaires pour une nouvelle tâche
        const commentsSection = ONG.el('taskCommentsSection');
        if (commentsSection) commentsSection.style.display = 'none';

        const pSel = ONG.el('taskProjectSelect');
        if (pSel) {
            pSel.innerHTML = '<option value="">-- Sélectionnez un projet --</option>' +
                ONG.data.projects.map(p => `<option value="${p.id}" ${p.id == ONG.state.pid ? 'selected' : ''}>${ONG.escape(p.name)}</option>`).join('');
        }

        if (ONG.state.pid) {
            ONG.setVal('taskProjectSelect', ONG.state.pid);
            ONG.updateTaskModalDeps(ONG.state.pid);
        }

        ONG.openModal('modalTask');
    },

    /**
     * Édite une tâche
     */
    editTask: (id) => {
        const t = ONG.data.tasks.find(x => x.id == id);
        if (!t) return;

        const title = document.getElementById('modalTaskTitle');
        if (title) title.textContent = "Éditer Tâche";

        const pSel = ONG.el('taskProjectSelect');
        if (pSel) {
            pSel.innerHTML = ONG.data.projects.map(p => `<option value="${p.id}">${ONG.escape(p.name)}</option>`).join('');
        }

        ONG.setVal('taskId', t.id);
        ONG.setVal('taskTitle', t.title);
        ONG.setVal('taskDesc', t.desc);
        ONG.setVal('taskProjectSelect', t.project_id);

        // Définir l'ID de la tâche en cours d'édition pour les commentaires
        ONG.state.editingTaskId = t.id;

        ONG.updateTaskModalDeps(t.project_id);

        setTimeout(() => {
            ONG.setVal('taskGroupSelect', t.group_id);
            ONG.setVal('taskMilestoneSelect', t.milestone_id);
            ONG.setVal('taskOwnerSelect', t.owner_id);
            ONG.setVal('taskStatus', t.status);
            ONG.setVal('taskStartDate', t.start_date);
            ONG.setVal('taskEndDate', t.end_date);
            ONG.setVal('taskTags', t.tags);
            ONG.setVal('taskLink', t.link);

            if (t.dependencies) {
                t.dependencies.split(',').forEach(did => {
                    const cb = document.querySelector(`.dep-check[value="${did}"]`);
                    if (cb) cb.checked = true;
                });
            }
        }, 50);

        // Charger les commentaires si on édite une tâche existante
        if (t.id) {
            ONG.loadComments(t.id);
        }

        ONG.openModal('modalTask');
    },

    /**
     * Met à jour les dépendances du modal de tâche
     */
    updateTaskModalDeps: (pid) => {
        if (!pid) return;

        const groups = ONG.data.groups.filter(g => g.project_id == pid);
        const milestones = ONG.data.milestones.filter(m => m.project_id == pid);
        const taskId = ONG.el('taskId')?.value;
        const tasks = ONG.data.tasks.filter(t => t.project_id == pid && t.id != taskId);

        const groupSel = ONG.el('taskGroupSelect');
        if (groupSel) {
            groupSel.innerHTML = '<option value="">-</option>' +
                groups.map(g => `<option value="${g.id}">${ONG.escape(g.name)}</option>`).join('');
        }

        const msSel = ONG.el('taskMilestoneSelect');
        if (msSel) {
            msSel.innerHTML = '<option value="">-</option>' +
                milestones.map(m => `<option value="${m.id}">${ONG.escape(m.name)}</option>`).join('');
        }

        const depsList = ONG.el('taskDepsList');
        if (depsList) {
            depsList.innerHTML = tasks.map(t =>
                `<label class="block"><input type="checkbox" class="dep-check" value="${t.id}"> ${ONG.escape(t.title)}</label>`
            ).join('');
        }
    },

    /**
     * Ouvre le modal de projet
     */
    openModalProject: () => {
        const form = document.querySelector('#modalProject form');
        if (form) form.reset();

        ONG.setVal('projId', '');
        const title = document.getElementById('modalProjectTitle');
        if (title) title.innerText = 'Nouveau Projet';

        ONG.openModal('modalProject');
    },

    /**
     * Édite un projet
     */
    editProject: (id) => {
        const p = ONG.data.projects.find(x => x.id == id);
        if (!p) return;

        const title = document.getElementById('modalProjectTitle');
        if (title) title.innerText = 'Éditer Projet';

        ONG.setVal('projId', p.id);
        ONG.setVal('projName', p.name);
        ONG.setVal('projDesc', p.desc);
        ONG.setVal('projStart', p.start_date);
        ONG.setVal('projEnd', p.end_date);
        ONG.setVal('projOwner', p.owner_id);

        ONG.openModal('modalProject');
    },

    /**
     * Ouvre le modal de groupe
     */
    openGroupModal: () => {
        if (!ONG.state.pid) {
            alert("Sélectionnez un projet");
            return;
        }

        const form = document.querySelector('#modalGroup form');
        if (form) form.reset();

        ONG.setVal('groupProjectId', ONG.state.pid);
        ONG.openModal('modalGroup');
    },

    /**
     * Édite un groupe
     */
    editGroup: (g) => {
        ONG.setVal('groupId', g.id);
        ONG.setVal('groupProjectId', g.project_id);
        ONG.setVal('groupName', g.name);
        ONG.setVal('groupDescription', g.description || '');
        ONG.setVal('groupColor', g.color);
        ONG.setVal('groupOwner', g.owner_id);
        ONG.openModal('modalGroup');
    },

    /**
     * Ouvre le modal de jalon
     */
    openMilestoneModal: () => {
        if (!ONG.state.pid) {
            alert("Sélectionnez un projet");
            return;
        }

        const form = document.querySelector('#modalMilestone form');
        if (form) form.reset();

        ONG.setVal('milestoneProjectId', ONG.state.pid);
        ONG.openModal('modalMilestone');
    },

    /**
     * Édite un jalon
     */
    editMilestone: (m) => {
        ONG.setVal('milestoneId', m.id);
        ONG.setVal('milestoneProjectId', m.project_id);
        ONG.setVal('milestoneName', m.name);
        ONG.setVal('milestoneDate', m.date);
        ONG.setVal('milestoneStatus', m.status);
        ONG.openModal('modalMilestone');
    },

    /**
     * Remplit les selects avec les membres de l'équipe
     */
    fillTeamSelects: () => {
        const opts = '<option value="">-</option>' +
            ONG.data.members.map(m => `<option value="${m.id}">${ONG.escape(m.fname)} ${ONG.escape(m.lname)}</option>`).join('');

        document.querySelectorAll('.team-select').forEach(s => s.innerHTML = opts);

        const teamList = ONG.el('teamList');
        if (teamList) {
            teamList.innerHTML = ONG.data.members.map(m => `
                <div class="flex justify-between items-center p-2 border-b hover:bg-gray-50">
                    <span>${ONG.escape(m.fname)} ${ONG.escape(m.lname)} - ${ONG.escape(m.email)}</span>
                    <div class="flex gap-2">
                        <button class="text-blue-500 hover:text-blue-700" onclick='ONG.editMember(${JSON.stringify(m)})' title="Éditer">✏️</button>
                        <button class="text-red-500 hover:text-red-700" onclick="ONG.deleteItem('members', ${m.id})" title="Supprimer">🗑️</button>
                    </div>
                </div>
            `).join('');
        }
    },

    /**
     * Édite un membre
     */
    editMember: (member) => {
        ONG.setVal('memberId', member.id);
        ONG.setVal('memberFname', member.fname);
        ONG.setVal('memberLname', member.lname);
        ONG.setVal('memberEmail', member.email);

        // Changer le bouton en mode édition
        const btnIcon = ONG.el('memberBtnIcon');
        const btnCancel = ONG.el('btnCancelEditMember');
        const btnSave = ONG.el('btnSaveMember');

        if (btnIcon) btnIcon.textContent = '💾';
        if (btnSave) btnSave.className = 'bg-blue-600 text-white px-3 rounded';
        if (btnCancel) btnCancel.classList.remove('hidden');
    },

    /**
     * Annule l'édition d'un membre
     */
    cancelEditMember: () => {
        const form = ONG.el('formMember');
        if (form) form.reset();

        ONG.setVal('memberId', '');

        // Réinitialiser le bouton en mode ajout
        const btnIcon = ONG.el('memberBtnIcon');
        const btnCancel = ONG.el('btnCancelEditMember');
        const btnSave = ONG.el('btnSaveMember');

        if (btnIcon) btnIcon.textContent = '+';
        if (btnSave) btnSave.className = 'bg-green-600 text-white px-3 rounded';
        if (btnCancel) btnCancel.classList.add('hidden');
    },

    /**
     * Remplit les filtres
     */
    fillFilters: () => {
        // Filtres par tags
        const tagSel = ONG.el('filterTag');
        if (tagSel) {
            const tags = new Set();
            ONG.data.tasks.forEach(t => {
                if (t.tags) {
                    t.tags.split(',').forEach(x => tags.add(x.trim()));
                }
            });
            tagSel.innerHTML = '<option value="">Tags: Tous</option>' +
                Array.from(tags).map(t => `<option value="${ONG.escape(t)}">${ONG.escape(t)}</option>`).join('');
        }

        // Filtres par responsable
        const respSel = ONG.el('filterResp');
        if (respSel) {
            respSel.innerHTML = '<option value="">Resp: Tous</option>' +
                ONG.data.members.map(m => `<option value="${m.id}">${ONG.escape(m.fname)} ${ONG.escape(m.lname)}</option>`).join('');
        }
    },

    /**
     * Trie les données
     */
    sortData: (col) => {
        if (ONG.state.sort.col == col) {
            ONG.state.sort.dir = (ONG.state.sort.dir == 'asc') ? 'desc' : 'asc';
        } else {
            ONG.state.sort.col = col;
            ONG.state.sort.dir = 'asc';
        }
        ONG.renderView();
    },

    /**
     * Réinitialise les filtres
     */
    resetFilters: () => {
        document.querySelectorAll('#filtersBar select, #filtersBar input').forEach(e => e.value = '');
        ONG.renderView();
    },

    /**
     * Supprime un élément
     */
    deleteItem: async (type, id) => {
        if (!confirm("Sûr ?")) return;

        await ONG.post('delete_item', { type, id });

        if (type === 'projects' && ONG.state.pid == id) {
            ONG.state.pid = null;
        }

        ONG.loadData();
    },

    /**
     * Exporte en Excel
     */
    exportExcel: () => {
        if (!ONG.state.pid) {
            alert("Sélectionnez un projet");
            return;
        }

        const project = ONG.data.projects.find(x => x.id == ONG.state.pid);
        if (!project) return;

        const wb = XLSX.utils.book_new();
        const tasks = ONG.data.tasks.filter(t => t.project_id == project.id).map(t => ({
            ID: t.id,
            Titre: t.title,
            Debut: t.start_date,
            Fin: t.end_date,
            Statut: t.status
        }));

        XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(tasks), "Taches");
        XLSX.writeFile(wb, `Export_${project.name}.xlsx`);
    },

    /**
     * Change la langue
     */
    setLang: (lang) => {
        ONG.state.lang = lang;
        const langSelect = ONG.el('langSelect');
        if (langSelect) langSelect.value = lang;

        // Re-rendre la vue actuelle avec la nouvelle langue
        ONG.renderView();

        // Mettre à jour l'URL sans recharger la page
        const url = new URL(window.location);
        url.searchParams.set('lang', lang);
        window.history.pushState({}, '', url);
    },

    /**
     * Change le thème de couleur
     */
    setTheme: (theme) => {
        document.body.setAttribute('data-theme', theme);
        localStorage.setItem('ong_theme', theme);
    },

    /**
     * Charge le thème sauvegardé
     */
    loadTheme: () => {
        const savedTheme = localStorage.getItem('ong_theme');
        if (savedTheme) {
            document.body.setAttribute('data-theme', savedTheme);
        }
    },

    /**
     * Obtient le nom d'un membre
     */
    getMemberName: (id) => {
        if (!id) return '-';
        const m = ONG.data.members.find(x => x.id == id);
        return m ? `${m.fname} ${m.lname}` : '-';
    },

    /**
     * Récupère un élément par ID
     */
    el: (id) => document.getElementById(id),

    /**
     * Définit la valeur d'un élément
     */
    setVal: (id, val) => {
        const el = ONG.el(id);
        if (el) el.value = (val == null || val == 'null') ? '' : val;
    },

    /**
     * Attache un événement à un élément
     */
    on: (id, event, handler) => {
        const el = ONG.el(id);
        if (el) el.addEventListener(event, handler);
    },

    /**
     * Attache un handler de soumission de formulaire
     */
    onSubmit: (id, handler) => {
        const form = ONG.el(id);
        if (form) {
            form.onsubmit = async (e) => {
                e.preventDefault();
                await handler(new FormData(form));
            };
        }
    },

    /**
     * Rend la vue Dashboard avec statistiques et graphiques
     */
    renderDashboardView: (container) => {
        const allTasks = ONG.data.tasks || [];
        const allProjects = ONG.data.projects || [];
        const allMembers = ONG.data.members || [];

        // Calculer les statistiques
        const stats = {
            total: allTasks.length,
            todo: allTasks.filter(t => t.status === 'todo').length,
            wip: allTasks.filter(t => t.status === 'wip').length,
            done: allTasks.filter(t => t.status === 'done').length,
            projects: allProjects.length,
            members: allMembers.length
        };

        stats.completion = stats.total > 0 ? Math.round((stats.done / stats.total) * 100) : 0;

        // Tâches à venir cette semaine
        const today = new Date();
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);
        const upcomingTasks = allTasks.filter(t => {
            if (!t.end_date) return false;
            const endDate = new Date(t.end_date);
            return endDate >= today && endDate <= nextWeek && t.status !== 'done';
        }).slice(0, 5);

        // HTML du Dashboard
        let html = `
            <div class="p-6 space-y-6">
                <!-- Cartes de statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Tâches</p>
                                <p class="text-3xl font-bold text-gray-800">${stats.total}</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-tasks text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">En Cours</p>
                                <p class="text-3xl font-bold text-orange-600">${stats.wip}</p>
                            </div>
                            <div class="bg-orange-100 p-3 rounded-full">
                                <i class="fas fa-spinner text-orange-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Terminées</p>
                                <p class="text-3xl font-bold text-green-600">${stats.done}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Progression</p>
                                <p class="text-3xl font-bold text-purple-600">${stats.completion}%</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Graphique par Statut -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">Tâches par Statut</h3>
                        <div style="height: 250px;">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>

                    <!-- Graphique par Projet -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">Tâches par Projet</h3>
                        <div style="height: 250px;">
                            <canvas id="chartProjects"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tâches à venir & Par responsable -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Tâches à venir -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">📅 À venir cette semaine</h3>
                        ${upcomingTasks.length > 0 ? `
                            <div class="space-y-2">
                                ${upcomingTasks.map(t => {
                                    const project = allProjects.find(p => p.id == t.project_id);
                                    return `
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer" onclick="ONG.editTask(${t.id})">
                                            <div class="flex-1">
                                                <div class="font-medium">${ONG.escape(t.title)}</div>
                                                <div class="text-xs text-gray-500">${project ? ONG.escape(project.name) : ''}</div>
                                            </div>
                                            <div class="text-sm text-gray-600">${t.end_date}</div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        ` : '<p class="text-gray-400 text-center py-8">Aucune tâche à venir cette semaine</p>'}
                    </div>

                    <!-- Graphique par Responsable -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">Tâches par Responsable</h3>
                        <div style="height: 250px;">
                            <canvas id="chartMembers"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = html;

        // Créer les graphiques avec Chart.js
        setTimeout(() => {
            // Graphique par Statut (Doughnut)
            const ctxStatus = document.getElementById('chartStatus');
            if (ctxStatus) {
                new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: ['À faire', 'En cours', 'Terminé'],
                        datasets: [{
                            data: [stats.todo, stats.wip, stats.done],
                            backgroundColor: ['#FCA5A5', '#FBBF24', '#34D399'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }

            // Graphique par Projet (Bar)
            const ctxProjects = document.getElementById('chartProjects');
            if (ctxProjects) {
                const projectStats = {};
                allProjects.forEach(p => {
                    projectStats[p.name] = allTasks.filter(t => t.project_id == p.id).length;
                });

                new Chart(ctxProjects, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(projectStats),
                        datasets: [{
                            label: 'Tâches',
                            data: Object.values(projectStats),
                            backgroundColor: '#3B82F6',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }

            // Graphique par Responsable (Horizontal Bar)
            const ctxMembers = document.getElementById('chartMembers');
            if (ctxMembers) {
                const memberStats = {};
                allMembers.forEach(m => {
                    const name = `${m.fname} ${m.lname}`;
                    memberStats[name] = allTasks.filter(t => t.owner_id == m.id).length;
                });

                new Chart(ctxMembers, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(memberStats),
                        datasets: [{
                            label: 'Tâches assignées',
                            data: Object.values(memberStats),
                            backgroundColor: '#8B5CF6',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }
        }, 100);
    },

    /**
     * Échappe le HTML
     */
    escape: (str) => {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    /**
     * Détecte les conflits de dates (plusieurs tâches pour la même personne le même jour)
     */
    detectConflicts: () => {
        const conflicts = [];
        const tasks = ONG.data.tasks || [];

        // Grouper les tâches par responsable et date de fin
        const tasksByPersonAndDate = {};

        tasks.forEach(task => {
            if (!task.owner_id || !task.end_date) return;

            const key = `${task.owner_id}_${task.end_date}`;
            if (!tasksByPersonAndDate[key]) {
                tasksByPersonAndDate[key] = [];
            }
            tasksByPersonAndDate[key].push(task);
        });

        // Identifier les conflits (plus d'une tâche pour la même personne le même jour)
        for (const key in tasksByPersonAndDate) {
            const tasksGroup = tasksByPersonAndDate[key];
            if (tasksGroup.length > 1) {
                conflicts.push({
                    person_id: tasksGroup[0].owner_id,
                    person_name: ONG.getMemberName(tasksGroup[0].owner_id),
                    date: tasksGroup[0].end_date,
                    tasks: tasksGroup
                });
            }
        }

        return conflicts;
    },

    /**
     * Vérifie s'il y a des conflits et affiche une notification
     */
    checkConflicts: () => {
        const conflicts = ONG.detectConflicts();

        // Mettre à jour le badge de notification
        ONG.updateConflictBadge(conflicts.length);

        // Note: Le pop-up de conflit a été désactivé à la demande de l'utilisateur
        // Pour le réactiver, décommentez les lignes ci-dessous:
        /*
        if (conflicts.length > 0 && !ONG.state.conflictsChecked) {
            ONG.state.conflictsChecked = true;
            ONG.showConflictModal(conflicts);
        }
        */
    },

    /**
     * Met à jour le badge de notification des conflits
     */
    updateConflictBadge: (count) => {
        let badge = document.getElementById('conflictBadge');

        if (count > 0) {
            if (!badge) {
                // Créer le badge s'il n'existe pas
                const header = document.querySelector('header .flex.items-center.gap-3');
                if (header) {
                    badge = document.createElement('div');
                    badge.id = 'conflictBadge';
                    badge.className = 'bg-red-500 text-white text-xs px-2 py-1 rounded-full cursor-pointer hover:bg-red-600';
                    badge.title = 'Conflits de dates détectés - Cliquez pour voir';
                    badge.onclick = () => ONG.showConflictModal(ONG.detectConflicts());
                    header.appendChild(badge);
                }
            }
            if (badge) {
                badge.textContent = `⚠️ ${count} conflit${count > 1 ? 's' : ''}`;
            }
        } else {
            // Supprimer le badge s'il n'y a plus de conflits
            if (badge) badge.remove();
        }
    },

    /**
     * Affiche le modal des conflits
     */
    showConflictModal: (conflicts) => {
        if (conflicts.length === 0) {
            alert('Aucun conflit de dates détecté ! ✅');
            return;
        }

        let html = `
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" id="conflictModal">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full m-4 max-h-[80vh] overflow-hidden flex flex-col">
                    <div class="bg-red-500 text-white px-6 py-4 flex justify-between items-center">
                        <h3 class="text-xl font-bold">⚠️ Conflits de Dates Détectés</h3>
                        <button onclick="document.getElementById('conflictModal').remove()" class="text-white hover:text-gray-200 text-2xl">×</button>
                    </div>
                    <div class="p-6 overflow-y-auto flex-1">
                        <p class="mb-4 text-gray-700">Les personnes suivantes ont <strong>plusieurs tâches</strong> se terminant le <strong>même jour</strong> :</p>
                        <div class="space-y-4">
        `;

        conflicts.forEach(conflict => {
            html += `
                <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded">
                    <div class="font-bold text-red-800 mb-2">
                        👤 ${ONG.escape(conflict.person_name)} - 📅 ${conflict.date}
                    </div>
                    <div class="text-sm text-gray-700 mb-2">
                        ${conflict.tasks.length} tâches à terminer le même jour :
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-sm">
            `;

            conflict.tasks.forEach(task => {
                const project = ONG.data.projects.find(p => p.id == task.project_id);
                html += `
                    <li class="text-gray-600">
                        <strong>${ONG.escape(task.title)}</strong>
                        ${project ? `<span class="text-xs text-gray-500">(${ONG.escape(project.name)})</span>` : ''}
                    </li>
                `;
            });

            html += `
                    </ul>
                </div>
            `;
        });

        html += `
                        </div>
                    </div>
                    <div class="bg-gray-100 px-6 py-4 flex justify-end">
                        <button onclick="document.getElementById('conflictModal').remove()"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Compris
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Ajouter le modal au DOM
        const existingModal = document.getElementById('conflictModal');
        if (existingModal) existingModal.remove();

        document.body.insertAdjacentHTML('beforeend', html);
    },

    /**
     * Vérifie si une tâche a un conflit de date
     */
    hasConflict: (task) => {
        if (!task.owner_id || !task.end_date) return false;

        const conflicts = ONG.data.tasks.filter(t =>
            t.id !== task.id &&
            t.owner_id === task.owner_id &&
            t.end_date === task.end_date
        );

        return conflicts.length > 0;
    },

    /**
     * Ouvre le modal des templates
     */
    openTemplatesModal: async () => {
        await ONG.loadTemplates();
        ONG.fillTemplateProjectSelect();
        ONG.switchTemplateTab('list');
        ONG.openModal('modalTemplates');
    },

    /**
     * Charge la liste des templates
     */
    loadTemplates: async () => {
        const r = await ONG.post('list_templates');
        if (r.ok) {
            ONG.state.templates = r.data.templates || [];
            ONG.renderTemplatesList();
            ONG.fillTemplateSelects();
        }
    },

    /**
     * Remplit le select des projets dans le formulaire de création de template
     */
    fillTemplateProjectSelect: () => {
        const sel = ONG.el('templateProjectSelect');
        if (sel) {
            sel.innerHTML = '<option value="">-- Sélectionnez un projet --</option>' +
                ONG.data.projects.map(p => `<option value="${p.id}">${ONG.escape(p.name)}</option>`).join('');
        }
    },

    /**
     * Remplit les selects de templates
     */
    fillTemplateSelects: () => {
        const sel = ONG.el('useTemplateSelect');
        if (sel) {
            sel.innerHTML = '<option value="">-- Sélectionnez un modèle --</option>' +
                ONG.state.templates.map(t => `<option value="${t.id}">${ONG.escape(t.name)} (${t.category})</option>`).join('');
        }
    },

    /**
     * Rend la liste des templates
     */
    renderTemplatesList: () => {
        const container = ONG.el('templatesList');
        if (!container) return;

        if (ONG.state.templates.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-400 py-8">Aucun modèle disponible. Créez-en un depuis l\'onglet "Créer un modèle".</p>';
            return;
        }

        const categoryIcons = {
            custom: '📋',
            marketing: '📢',
            it: '💻',
            construction: '🏗️',
            event: '🎉',
            research: '🔬'
        };

        const categoryColors = {
            custom: 'bg-gray-100 border-gray-300',
            marketing: 'bg-blue-100 border-blue-300',
            it: 'bg-purple-100 border-purple-300',
            construction: 'bg-orange-100 border-orange-300',
            event: 'bg-pink-100 border-pink-300',
            research: 'bg-green-100 border-green-300'
        };

        container.innerHTML = ONG.state.templates.map(t => {
            const icon = categoryIcons[t.category] || '📋';
            const colorClass = categoryColors[t.category] || 'bg-gray-100 border-gray-300';

            return `
                <div class="border-2 ${colorClass} p-4 rounded-lg hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-2xl">${icon}</span>
                                <h4 class="font-bold text-lg">${ONG.escape(t.name)}</h4>
                            </div>
                            <p class="text-sm text-gray-600">${ONG.escape(t.desc || 'Aucune description')}</p>
                            <div class="text-xs text-gray-500 mt-1">
                                Catégorie: ${t.category} • Créé le ${new Date(t.created_at).toLocaleDateString()}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="ONG.useTemplate(${t.id})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700" title="Utiliser ce modèle">
                                🚀
                            </button>
                            ${!t.is_predefined ? `
                                <button onclick="ONG.deleteTemplate(${t.id})" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600" title="Supprimer">
                                    🗑️
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    },

    /**
     * Change l'onglet du modal de templates
     */
    switchTemplateTab: (tab) => {
        ONG.state.currentTemplateTab = tab;

        // Cacher tous les tabs
        document.querySelectorAll('.template-tab').forEach(t => t.classList.add('hidden'));

        // Afficher le tab sélectionné
        const tabContent = ONG.el(`templateTab${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
        if (tabContent) tabContent.classList.remove('hidden');

        // Mettre à jour les boutons
        ['list', 'create', 'use'].forEach(t => {
            const btn = ONG.el(`btnTemplateTab${t.charAt(0).toUpperCase() + t.slice(1)}`);
            if (btn) {
                if (t === tab) {
                    btn.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                } else {
                    btn.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600');
                    btn.classList.add('text-gray-500');
                }
            }
        });
    },

    /**
     * Utilise un template (pré-remplit le formulaire)
     */
    useTemplate: (templateId) => {
        ONG.switchTemplateTab('use');
        const sel = ONG.el('useTemplateSelect');
        if (sel) sel.value = templateId;
    },

    /**
     * Supprime un template
     */
    deleteTemplate: async (templateId) => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')) return;

        const r = await ONG.post('delete_template', { id: templateId });
        if (r.ok) {
            ONG.toast('Modèle supprimé avec succès', 'success');
            ONG.loadTemplates();
        }
    },

    /**
     * Charge les commentaires d'une tâche
     */
    loadComments: async (taskId) => {
        const r = await ONG.post('list_comments', { task_id: taskId });
        if (r.ok) {
            const comments = r.data.comments || [];
            const container = ONG.el('commentsList');
            const countSpan = ONG.el('commentsCount');
            const section = ONG.el('taskCommentsSection');

            if (section) section.style.display = 'block';
            if (countSpan) countSpan.textContent = `(${comments.length})`;

            if (container) {
                if (comments.length === 0) {
                    container.innerHTML = '<p class="text-gray-400 text-sm italic">Aucun commentaire pour le moment.</p>';
                } else {
                    container.innerHTML = comments.map(c => ONG.renderComment(c)).join('');
                }
            }
        }
    },

    /**
     * Génère le HTML d'un commentaire
     */
    renderComment: (comment) => {
        const authorName = `${comment.fname} ${comment.lname}`;
        const date = new Date(comment.created_at);
        const dateStr = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const isOwner = ONG.data.currentMember && ONG.data.currentMember.id === comment.member_id;

        return `
            <div id="comment-${comment.id}" class="bg-gray-50 border border-gray-200 rounded p-3 text-sm">
                <div class="flex justify-between items-start mb-2">
                    <div class="font-semibold text-gray-700">${ONG.escape(authorName)}</div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">${dateStr}</span>
                        ${isOwner ? `
                            <button onclick="ONG.editComment(${comment.id}, '${ONG.escape(comment.content).replace(/'/g, "\\'")}', this)"
                                    class="text-blue-500 hover:text-blue-700 text-xs"
                                    title="Éditer">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="ONG.deleteComment(${comment.id})"
                                    class="text-red-500 hover:text-red-700 text-xs"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
                <div class="comment-content text-gray-600">${ONG.markdownToHtml(comment.content)}</div>
            </div>
        `;
    },

    /**
     * Ajoute un commentaire
     */
    addComment: async () => {
        const textarea = ONG.el('newCommentText');
        const taskId = ONG.state.editingTaskId;

        if (!textarea || !taskId) return;

        const content = textarea.value.trim();
        if (!content) {
            ONG.toast('Le commentaire ne peut pas être vide', 'warning');
            return;
        }

        const r = await ONG.post('add_comment', {
            task_id: taskId,
            content: content
        });

        if (r.ok) {
            textarea.value = '';
            await ONG.loadComments(taskId);
        }
    },

    /**
     * Active le mode édition pour un commentaire
     */
    editComment: (commentId, currentContent, button) => {
        const commentDiv = document.getElementById(`comment-${commentId}`);
        if (!commentDiv) return;

        const contentDiv = commentDiv.querySelector('.comment-content');
        if (!contentDiv) return;

        // Sauvegarder le contenu original
        contentDiv.setAttribute('data-original', currentContent);

        // Remplacer par un textarea
        contentDiv.innerHTML = `
            <textarea class="w-full border p-2 rounded text-sm" rows="3">${ONG.escape(currentContent)}</textarea>
            <div class="flex gap-2 mt-2">
                <button onclick="ONG.saveCommentEdit(${commentId})"
                        class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                    <i class="fas fa-check"></i> Enregistrer
                </button>
                <button onclick="ONG.cancelCommentEdit(${commentId})"
                        class="px-3 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500">
                    <i class="fas fa-times"></i> Annuler
                </button>
            </div>
        `;

        // Masquer les boutons d'action pendant l'édition
        button.parentElement.style.display = 'none';
    },

    /**
     * Enregistre les modifications d'un commentaire
     */
    saveCommentEdit: async (commentId) => {
        const commentDiv = document.getElementById(`comment-${commentId}`);
        if (!commentDiv) return;

        const textarea = commentDiv.querySelector('textarea');
        if (!textarea) return;

        const newContent = textarea.value.trim();
        if (!newContent) {
            ONG.toast('Le commentaire ne peut pas être vide', 'warning');
            return;
        }

        const r = await ONG.post('update_comment', {
            id: commentId,
            content: newContent
        });

        if (r.ok) {
            ONG.toast('Commentaire modifié avec succès', 'success');
            const taskId = ONG.state.editingTaskId;
            if (taskId) {
                await ONG.loadComments(taskId);
            }
        }
    },

    /**
     * Annule l'édition d'un commentaire
     */
    cancelCommentEdit: (commentId) => {
        const commentDiv = document.getElementById(`comment-${commentId}`);
        if (!commentDiv) return;

        const contentDiv = commentDiv.querySelector('.comment-content');
        if (!contentDiv) return;

        const originalContent = contentDiv.getAttribute('data-original');
        contentDiv.innerHTML = ONG.escape(originalContent);
        contentDiv.classList.add('whitespace-pre-wrap');

        // Réafficher les boutons d'action
        const actionsDiv = commentDiv.querySelector('.flex.items-center.gap-2');
        if (actionsDiv) actionsDiv.style.display = 'flex';
    },

    /**
     * Supprime un commentaire
     */
    deleteComment: async (commentId) => {
        ONG.confirm('Supprimer ce commentaire ?', async () => {
            const r = await ONG.post('delete_comment', { id: commentId });
            if (r.ok) {
                ONG.toast('Commentaire supprimé', 'success');
                const taskId = ONG.state.editingTaskId;
                if (taskId) {
                    await ONG.loadComments(taskId);
                }
            }
        });
    },

    /**
     * Crée un backup manuel de la base de données
     */
    createBackup: async () => {
        const r = await ONG.post('create_backup', {});
        if (r.ok) {
            ONG.toast('Sauvegarde créée avec succès: ' + r.data.filename, 'success');
            ONG.loadBackupsList();
        }
    },

    /**
     * Télécharge la base de données actuelle
     */
    downloadCurrentDb: () => {
        window.location.href = '?action=download_db';
    },

    /**
     * Charge la liste des backups disponibles
     */
    loadBackupsList: async () => {
        const r = await ONG.post('list_backups', {});
        if (r.ok) {
            const backups = r.data.backups || [];
            const container = ONG.el('backupsList');

            if (container) {
                if (backups.length === 0) {
                    container.innerHTML = '<div class="italic text-gray-400">Aucune sauvegarde disponible</div>';
                } else {
                    const html = '<div class="max-h-32 overflow-y-auto">' +
                        '<div class="font-bold mb-1">Sauvegardes récentes:</div>' +
                        backups.slice(0, 5).map(b => `
                            <div class="flex justify-between items-center py-1 border-b">
                                <div>
                                    <div class="font-mono text-xs">${b.date}</div>
                                    <div class="text-gray-500" style="font-size: 10px">${(b.size / 1024).toFixed(1)} KB</div>
                                </div>
                                <button onclick="ONG.downloadBackup('${b.filename}')"
                                        class="text-blue-600 hover:text-blue-800"
                                        title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        `).join('') +
                        '</div>';
                    container.innerHTML = html;
                }
            }
        }
    },

    /**
     * Télécharge un backup spécifique
     */
    downloadBackup: async (filename) => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'download_backup';

        const filenameInput = document.createElement('input');
        filenameInput.type = 'hidden';
        filenameInput.name = 'filename';
        filenameInput.value = filename;

        form.appendChild(actionInput);
        form.appendChild(filenameInput);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    },

    /**
     * Exporte un projet en JSON
     */
    exportProject: async (projectId) => {
        if (!projectId) {
            ONG.toast('Sélectionnez un projet à exporter', 'warning');
            return;
        }

        // Créer un formulaire pour déclencher le téléchargement
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'export_project';

        const projectInput = document.createElement('input');
        projectInput.type = 'hidden';
        projectInput.name = 'project_id';
        projectInput.value = projectId;

        form.appendChild(actionInput);
        form.appendChild(projectInput);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        ONG.toast('Export du projet en cours...', 'info');
    },

    /**
     * Ouvre le modal d'import de projet
     */
    openImportModal: () => {
        ONG.openModal('modalImport');
    },

    /**
     * Importe un projet depuis un fichier JSON
     */
    importProject: async () => {
        const fileInput = document.getElementById('importFileInput');
        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            ONG.toast('Sélectionnez un fichier JSON', 'warning');
            return;
        }

        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = async (e) => {
            try {
                const jsonData = e.target.result;
                // Valider que c'est du JSON valide
                JSON.parse(jsonData);

                const r = await ONG.post('import_project', { json_data: jsonData });
                if (r.ok) {
                    ONG.toast('Projet importé avec succès !', 'success');
                    ONG.closeModal('modalImport');
                    await ONG.loadData();
                    ONG.renderView();
                }
            } catch (error) {
                ONG.toast('Fichier JSON invalide', 'error');
            }
        };

        reader.readAsText(file);
    },

    /**
     * Ouvre le modal de gestion des webhooks
     */
    openWebhooksModal: async () => {
        ONG.openModal('modalWebhooks');
        await ONG.loadWebhooks();
    },

    /**
     * Charge la liste des webhooks
     */
    loadWebhooks: async () => {
        const r = await ONG.post('list_webhooks', {});
        if (r.ok && r.data.webhooks) {
            const webhooks = r.data.webhooks;
            const container = document.getElementById('webhooksList');

            if (webhooks.length === 0) {
                container.innerHTML = '<div class="text-gray-500 italic text-sm">Aucun webhook configuré</div>';
                return;
            }

            container.innerHTML = webhooks.map(wh => `
                <div class="border rounded p-4 ${wh.is_active ? 'bg-white' : 'bg-gray-100'}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">${ONG.escape(wh.name)}</h4>
                            <div class="text-xs text-gray-600 break-all">${ONG.escape(wh.url)}</div>
                        </div>
                        <div class="flex gap-1 ml-2">
                            <button onclick="ONG.testWebhook(${wh.id})"
                                    class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600"
                                    title="Tester le webhook">
                                <i class="fas fa-flask"></i>
                            </button>
                            <button onclick="ONG.editWebhook(${wh.id})"
                                    class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600"
                                    title="Éditer">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="ONG.toggleWebhook(${wh.id}, ${wh.is_active})"
                                    class="px-2 py-1 text-xs ${wh.is_active ? 'bg-orange-500' : 'bg-green-500'} text-white rounded hover:opacity-80"
                                    title="${wh.is_active ? 'Désactiver' : 'Activer'}">
                                <i class="fas fa-${wh.is_active ? 'pause' : 'play'}"></i>
                            </button>
                            <button onclick="ONG.deleteWebhook(${wh.id})"
                                    class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1 mt-2">
                        ${(wh.events || '*').split(',').map(e =>
                            `<span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">${e}</span>`
                        ).join('')}
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        Secret: <code class="bg-gray-200 px-1 rounded">${wh.secret.substring(0, 16)}...</code>
                    </div>
                </div>
            `).join('');
        }
    },

    /**
     * Ouvre le formulaire de webhook (créer ou éditer)
     */
    openWebhookForm: (webhookId = null) => {
        if (webhookId) {
            // Mode édition
            document.getElementById('webhookFormTitle').innerHTML = '<i class="fas fa-edit"></i> Éditer le Webhook';
            // Charger les données du webhook
            const r = ONG.post('list_webhooks', {});
            r.then(response => {
                if (response.ok && response.data.webhooks) {
                    const webhook = response.data.webhooks.find(w => w.id === webhookId);
                    if (webhook) {
                        document.getElementById('webhookId').value = webhook.id;
                        document.getElementById('webhookName').value = webhook.name;
                        document.getElementById('webhookUrl').value = webhook.url;

                        // Cocher les événements
                        const events = (webhook.events || '*').split(',');
                        document.querySelectorAll('.webhook-event').forEach(cb => {
                            cb.checked = events.includes(cb.value);
                        });
                    }
                }
            });
        } else {
            // Mode création
            document.getElementById('webhookFormTitle').innerHTML = '<i class="fas fa-plus"></i> Nouveau Webhook';
            document.getElementById('webhookId').value = '';
            document.getElementById('webhookName').value = '';
            document.getElementById('webhookUrl').value = '';
            document.querySelectorAll('.webhook-event').forEach(cb => cb.checked = false);
        }

        ONG.openModal('modalWebhookForm');
    },

    /**
     * Édite un webhook
     */
    editWebhook: (webhookId) => {
        ONG.openWebhookForm(webhookId);
    },

    /**
     * Supprime un webhook
     */
    deleteWebhook: async (webhookId) => {
        if (!confirm('Supprimer ce webhook ?')) return;

        const r = await ONG.post('delete_webhook', { id: webhookId });
        if (r.ok) {
            ONG.toast('Webhook supprimé', 'success');
            await ONG.loadWebhooks();
        }
    },

    /**
     * Active/Désactive un webhook
     */
    toggleWebhook: async (webhookId, currentStatus) => {
        const r = await ONG.post('update_webhook', {
            id: webhookId,
            is_active: currentStatus ? 0 : 1
        });
        if (r.ok) {
            ONG.toast(currentStatus ? 'Webhook désactivé' : 'Webhook activé', 'success');
            await ONG.loadWebhooks();
        }
    },

    /**
     * Teste un webhook
     */
    testWebhook: async (webhookId) => {
        ONG.toast('Envoi du webhook de test...', 'info');
        const r = await ONG.post('test_webhook', { id: webhookId });
        if (r.ok) {
            ONG.toast(`Test envoyé (HTTP ${r.data.http_code})`, 'success');
        }
    }
};

// Initialiser l'application au chargement du DOM
document.addEventListener("DOMContentLoaded", ONG.init);

// Gérer la soumission du formulaire webhook
document.addEventListener("DOMContentLoaded", () => {
    const formWebhook = document.getElementById('formWebhook');
    if (formWebhook) {
        formWebhook.addEventListener('submit', async (e) => {
            e.preventDefault();

            const webhookId = document.getElementById('webhookId').value;
            const name = document.getElementById('webhookName').value.trim();
            const url = document.getElementById('webhookUrl').value.trim();

            // Collecter les événements sélectionnés
            const events = [];
            document.querySelectorAll('.webhook-event:checked').forEach(cb => {
                events.push(cb.value);
            });

            if (events.length === 0) {
                ONG.toast('Sélectionnez au moins un événement', 'warning');
                return;
            }

            const data = { name, url, events: events.join(',') };
            if (webhookId) data.id = webhookId;

            const action = webhookId ? 'update_webhook' : 'create_webhook';
            const r = await ONG.post(action, data);

            if (r.ok) {
                ONG.toast(webhookId ? 'Webhook mis à jour' : 'Webhook créé', 'success');
                ONG.closeModal('modalWebhookForm');
                await ONG.loadWebhooks();
            }
        });
    }

    // Gérer la sélection exclusive de "Tous les événements"
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('webhook-event')) {
            if (e.target.value === '*' && e.target.checked) {
                // Si "Tous les événements" est coché, décocher les autres
                document.querySelectorAll('.webhook-event:not([value="*"])').forEach(cb => cb.checked = false);
            } else if (e.target.value !== '*' && e.target.checked) {
                // Si un événement spécifique est coché, décocher "Tous les événements"
                document.querySelector('.webhook-event[value="*"]').checked = false;
            }
        }
    });
});
