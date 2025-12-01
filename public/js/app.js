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
            milestones: 'Jalons',
            global: 'Vue Globale',
            tree: 'Arbo'
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
            milestones: 'Milestones',
            global: 'Global View',
            tree: 'Tree'
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
            milestones: 'Hitos',
            global: 'Global',
            tree: 'Árbol'
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
            milestones: 'Mejniki',
            global: 'Globalno',
            tree: 'Drevo'
        }
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
                    alert(data.msg);
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
        ONG.on('btnSettings', 'click', () => ONG.openModal('modalSettings'));
        ONG.on('btnAddProject', 'click', () => ONG.openModalProject());
        ONG.on('btnAddTask', 'click', () => ONG.openTaskModal());
        ONG.on('btnExport', 'click', () => ONG.exportExcel());
        ONG.on('btnResetFilters', 'click', () => ONG.resetFilters());

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
        const tabs = ['dashboard', 'global', 'list', 'kanban', 'groups', 'gantt', 'milestones', 'tree'];

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
                ONG.renderGroupsView(container);
                break;
            case 'milestones':
                ONG.renderMilestonesView(container, tasks);
                break;
            case 'gantt':
                ONG.renderGanttView(container, tasks);
                break;
            case 'tree':
                ONG.renderTreeView(container, tasks);
                break;
        }
    },

    /**
     * Rend la vue en liste
     */
    renderListView: (container, tasks) => {
        let html = `
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left bg-white shadow rounded">
                    <thead class="bg-gray-100 border-b cursor-pointer select-none">
                        <tr>
                            <th class="px-3 py-2" onclick="ONG.sortData('title')">Titre</th>
                            <th class="px-3 py-2" onclick="ONG.sortData('owner_id')">Responsable</th>
                            <th class="px-3 py-2" onclick="ONG.sortData('end_date')">Fin</th>
                            <th class="px-3 py-2" onclick="ONG.sortData('status')">Statut</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        tasks.forEach(t => {
            const hasConflict = ONG.hasConflict(t);
            const rowClass = hasConflict ? 'bg-red-100 border-l-4 border-red-500' : 'hover:bg-gray-50';
            const conflictIcon = hasConflict ? '<span title="Conflit de date détecté">⚠️</span> ' : '';

            html += `
                <tr class="border-b ${rowClass}">
                    <td class="compact-td font-medium">${conflictIcon}${ONG.escape(t.title)}</td>
                    <td class="compact-td text-gray-600">${ONG.getMemberName(t.owner_id)}</td>
                    <td class="compact-td text-gray-500">${t.end_date || ''}</td>
                    <td class="compact-td">
                        <span class="px-2 rounded text-xs bg-gray-200">
                            ${ONG.dict[ONG.state.lang][t.status] || t.status}
                        </span>
                    </td>
                    <td class="compact-td text-right">
                        ${t.link ? `<a href="${ONG.escape(t.link)}" target="_blank" class="text-blue-500 mr-2">🔗</a>` : ''}
                        <button onclick="ONG.editTask(${t.id})" class="text-blue-600 mr-2">✏️</button>
                        <button onclick="ONG.deleteItem('tasks', ${t.id})" class="text-red-500">🗑️</button>
                    </td>
                </tr>
            `;
        });

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

        let html = '';
        for (let status in cols) {
            const colTasks = tasks.filter(t => t.status === status);
            html += `
                <div class="w-80 bg-gray-100 rounded-lg p-3 flex-shrink-0">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">
                        ${cols[status]} (${colTasks.length})
                    </h3>
                    <div class="space-y-3">
                        ${colTasks.map(t => {
                            const hasConflict = ONG.hasConflict(t);
                            const borderColor = hasConflict ? 'border-red-500' : 'border-blue-500';
                            const bgColor = hasConflict ? 'bg-red-50' : 'bg-white';
                            const conflictIcon = hasConflict ? '<span title="Conflit de date">⚠️</span> ' : '';
                            return `
                            <div class="${bgColor} p-3 rounded shadow cursor-pointer hover:shadow-md border-l-4 ${borderColor}"
                                 onclick="ONG.editTask(${t.id})">
                                <div class="text-sm font-medium mb-1">${conflictIcon}${ONG.escape(t.title)}</div>
                                <div class="text-xs text-gray-500 flex justify-between">
                                    <span>${t.end_date || ''}</span>
                                    ${t.link ? '🔗' : ''}
                                </div>
                            </div>
                        `;
                        }).join('')}
                    </div>
                </div>
            `;
        }

        container.innerHTML = html;
    },

    /**
     * Rend la vue des groupes
     */
    renderGroupsView: (container) => {
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                ${groups.map(g => `
                    <div class="bg-white p-4 rounded shadow border-l-4" style="border-color:${g.color}">
                        <div class="flex justify-between mb-2">
                            <h3 class="font-bold">${ONG.escape(g.name)}</h3>
                            <div>
                                <button onclick='ONG.editGroup(${JSON.stringify(g)})' class="text-blue-500 mr-1">✏️</button>
                                <button onclick="ONG.deleteItem('groups', ${g.id})" class="text-red-500">🗑️</button>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">Responsable: ${ONG.getMemberName(g.owner_id)}</div>
                    </div>
                `).join('')}
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
                            <div class="text-xs text-gray-500">${done}/${mTasks.length} tâches</div>
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
        if (!ONG.state.pid) {
            container.innerHTML = "<p class='text-center text-gray-400'>Sélectionnez un projet</p>";
            return;
        }

        const valid = tasks.filter(t => t.start_date && t.end_date)
            .sort((a, b) => a.start_date.localeCompare(b.start_date));

        if (!valid.length) {
            container.innerHTML = "<p class='text-center text-gray-400'>Pas de dates définies</p>";
            return;
        }

        let min = new Date(valid[0].start_date);
        let max = new Date(valid[0].end_date);

        valid.forEach(t => {
            const s = new Date(t.start_date);
            const e = new Date(t.end_date);
            if (s < min) min = s;
            if (e > max) max = e;
        });

        const totalDuration = (max - min) / (1000 * 60 * 60 * 24) + 5;

        let html = '<div class="overflow-x-auto"><div class="min-w-[800px] bg-white p-4 rounded shadow">';

        valid.forEach(t => {
            const start = new Date(t.start_date);
            const end = new Date(t.end_date);
            const duration = Math.max(1, (end - start) / (1000 * 60 * 60 * 24));
            const offset = (start - min) / (1000 * 60 * 60 * 24);
            const width = (duration / totalDuration) * 100;
            const left = (offset / totalDuration) * 100;

            html += `
                <div class="flex items-center mb-2 h-8 group hover:bg-gray-50">
                    <div class="w-48 text-sm truncate pr-2" title="${ONG.escape(t.title)}">
                        ${ONG.escape(t.title)}
                    </div>
                    <div class="flex-1 relative h-6 bg-gray-100 rounded">
                        <div class="absolute h-full bg-blue-500 rounded opacity-80 text-white text-xs flex items-center pl-1 overflow-hidden cursor-pointer"
                             style="left:${left}%; width:${width}%;"
                             onclick="ONG.editTask(${t.id})">
                            ${width > 5 ? ONG.getMemberName(t.owner_id) : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div></div>';
        container.innerHTML = html;
    },

    /**
     * Rend la vue arborescente
     */
    renderTreeView: (container, tasks) => {
        if (!ONG.state.pid) {
            container.innerHTML = "<p class='text-center text-gray-400'>Sélectionnez un projet</p>";
            return;
        }

        const groups = ONG.data.groups.filter(g => g.project_id == ONG.state.pid);
        const orphans = tasks.filter(t => !t.group_id);

        let html = '<div class="bg-white p-6 rounded shadow space-y-4">';

        const renderTask = (t) => `
            <div class="pl-6 py-1 border-l-2 hover:bg-gray-50 flex justify-between group text-sm">
                <span class="${t.status == 'done' ? 'line-through text-gray-400' : ''}">
                    ${ONG.escape(t.title)}
                </span>
                <button onclick="ONG.editTask(${t.id})" class="opacity-0 group-hover:opacity-100 text-blue-500">✏️</button>
            </div>
        `;

        groups.forEach(g => {
            const gTasks = tasks.filter(t => t.group_id == g.id);
            html += `
                <details open>
                    <summary class="font-bold cursor-pointer p-2 bg-gray-50 rounded mb-1 select-none flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background:${g.color}"></span>
                        ${ONG.escape(g.name)}
                    </summary>
                    <div class="pl-2">
                        ${gTasks.map(renderTask).join('')}
                    </div>
                </details>
            `;
        });

        if (orphans.length) {
            html += `
                <details open>
                    <summary class="font-bold cursor-pointer p-2 text-gray-500">Aucun Groupe</summary>
                    <div class="pl-2">
                        ${orphans.map(renderTask).join('')}
                    </div>
                </details>
            `;
        }

        html += '</div>';
        container.innerHTML = html;
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
                if (search && !t.title.toLowerCase().includes(search)) return false;
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
        location.href = "?lang=" + lang;
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
                        <canvas id="chartStatus" height="250"></canvas>
                    </div>

                    <!-- Graphique par Projet -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">Tâches par Projet</h3>
                        <canvas id="chartProjects" height="250"></canvas>
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
                        <canvas id="chartMembers" height="250"></canvas>
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
                        maintainAspectRatio: false,
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
                        maintainAspectRatio: false,
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
                        maintainAspectRatio: false,
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

        // Afficher le pop-up seulement s'il y a des conflits et que c'est le premier chargement
        if (conflicts.length > 0 && !ONG.state.conflictsChecked) {
            ONG.state.conflictsChecked = true;
            ONG.showConflictModal(conflicts);
        }
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
            alert('Modèle supprimé avec succès');
            ONG.loadTemplates();
        }
    }
};

// Initialiser l'application au chargement du DOM
document.addEventListener("DOMContentLoaded", ONG.init);
