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

    // Statut administrateur
    isAdmin: false,

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
            mindmap: 'Carte Mentale',
            global: 'Vue Globale',
            total_tasks: 'Total Tâches',
            completed: 'Terminées',
            progress: 'Progression',
            tasks_by_status: 'Tâches par Statut',
            tasks_by_project: 'Tâches par Projet',
            upcoming_week: 'À venir cette semaine',
            no_upcoming: 'Aucune tâche à venir cette semaine',
            tasks_by_assignee: 'Tâches par Responsable',
            tasks_label: 'Tâches',
            assistant: 'Assistant',
            ai_assistant: 'Assistant IA',
            start_conversation: 'Nouvelle conversation',
            send_message: 'Envoyer',
            generate_structure: 'Générer la structure',
            typing: 'L\'assistant écrit...',
            new_conversation: 'Nouvelle conversation',
            assistant_placeholder: 'Écrivez votre message...',
            assistant_welcome: 'Bienvenue sur l\'assistant de planification',
            generating: 'Génération en cours...',
            structure_generated: 'Structure générée avec succès',
            select_project: 'Sélectionnez un projet',
            export_calendar: 'Exporter calendrier',
            export_project_calendar: 'Exporter ce projet',
            export_team_calendar: 'Exporter tous les projets',
            download_ics: 'Télécharger .ics',
            sort: 'Trier par',
            name: 'Nom',
            date: 'Date',
            assistant_api_mode: 'Mode API',
            assistant_free_mode: 'Mode Gratuit (Règles)',
            assistant_start_conversation: 'Démarrez d\'abord une conversation'
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
            mindmap: 'Mind Map',
            global: 'Global View',
            total_tasks: 'Total Tasks',
            completed: 'Completed',
            progress: 'Progress',
            tasks_by_status: 'Tasks by Status',
            tasks_by_project: 'Tasks by Project',
            upcoming_week: 'Upcoming this week',
            no_upcoming: 'No upcoming tasks this week',
            tasks_by_assignee: 'Tasks by Assignee',
            tasks_label: 'Tasks',
            assistant: 'Assistant',
            ai_assistant: 'AI Assistant',
            start_conversation: 'New conversation',
            send_message: 'Send',
            generate_structure: 'Generate structure',
            typing: 'Assistant is typing...',
            new_conversation: 'New conversation',
            assistant_placeholder: 'Type your message...',
            assistant_welcome: 'Welcome to the planning assistant',
            generating: 'Generating...',
            structure_generated: 'Structure generated successfully',
            select_project: 'Select a project',
            export_calendar: 'Export calendar',
            export_project_calendar: 'Export this project',
            export_team_calendar: 'Export all projects',
            download_ics: 'Download .ics',
            sort: 'Sort by',
            name: 'Name',
            date: 'Date',
            assistant_api_mode: 'API Mode',
            assistant_free_mode: 'Free Mode (Rules)',
            assistant_start_conversation: 'Please start a conversation first'
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
            mindmap: 'Mapa Mental',
            global: 'Global',
            total_tasks: 'Total Tareas',
            completed: 'Completadas',
            progress: 'Progreso',
            tasks_by_status: 'Tareas por Estado',
            tasks_by_project: 'Tareas por Proyecto',
            upcoming_week: 'Próximas esta semana',
            no_upcoming: 'No hay tareas próximas esta semana',
            tasks_by_assignee: 'Tareas por Responsable',
            tasks_label: 'Tareas',
            assistant: 'Asistente',
            ai_assistant: 'Asistente IA',
            start_conversation: 'Nueva conversación',
            send_message: 'Enviar',
            generate_structure: 'Generar estructura',
            typing: 'El asistente está escribiendo...',
            new_conversation: 'Nueva conversación',
            assistant_placeholder: 'Escribe tu mensaje...',
            assistant_welcome: 'Bienvenido al asistente de planificación',
            generating: 'Generando...',
            structure_generated: 'Estructura generada con éxito',
            select_project: 'Seleccionar un proyecto',
            export_calendar: 'Exportar calendario',
            export_project_calendar: 'Exportar este proyecto',
            export_team_calendar: 'Exportar todos los proyectos',
            download_ics: 'Descargar .ics',
            sort: 'Ordenar por',
            name: 'Nombre',
            date: 'Fecha',
            assistant_api_mode: 'Modo API',
            assistant_free_mode: 'Modo Gratuito (Reglas)',
            assistant_start_conversation: 'Primero inicie una conversación'
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
            mindmap: 'Miselni Zemljevid',
            global: 'Globalno',
            total_tasks: 'Skupaj Nalog',
            completed: 'Končano',
            progress: 'Napredek',
            tasks_by_status: 'Naloge po Statusu',
            tasks_by_project: 'Naloge po Projektu',
            upcoming_week: 'Prihajajoče ta teden',
            no_upcoming: 'Ni prihodnjih nalog ta teden',
            tasks_by_assignee: 'Naloge po Odgovornem',
            tasks_label: 'Naloge',
            assistant: 'Asistent',
            ai_assistant: 'AI Asistent',
            start_conversation: 'Nov pogovor',
            send_message: 'Pošlji',
            generate_structure: 'Generiraj strukturo',
            typing: 'Asistent piše...',
            new_conversation: 'Nov pogovor',
            assistant_placeholder: 'Vpiši svoje sporočilo...',
            assistant_welcome: 'Dobrodošli v načrtovalnem asistentu',
            generating: 'Generiranje...',
            structure_generated: 'Struktura uspešno generirana',
            select_project: 'Izberi projekt',
            export_calendar: 'Izvozi koledar',
            export_project_calendar: 'Izvozi ta projekt',
            export_team_calendar: 'Izvozi vse projekte',
            download_ics: 'Prenesi .ics',
            sort: 'Razvrsti po',
            name: 'Ime',
            date: 'Datum',
            assistant_api_mode: 'API način',
            assistant_free_mode: 'Brezplačni način (Pravila)',
            assistant_start_conversation: 'Najprej začnite pogovor'
        }
    },

    /**
     * Fonction helper pour traduire une clé
     * @param {string} key - Clé de traduction
     * @return {string} Traduction ou la clé si non trouvée
     */
    t: (key) => {
        return ONG.dict[ONG.state.lang]?.[key] || key;
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
     * Échappe les caractères HTML pour éviter les injections XSS
     * @param {string} text - Le texte à échapper
     * @return {string} Texte échappé
     */
    escapeHtml: (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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

        // Initialiser la langue AVANT de charger les données
        const langSelect = ONG.el('langSelect');
        if (langSelect) {
            const urlParams = new URLSearchParams(window.location.search);
            ONG.state.lang = urlParams.get('lang') || 'fr';
            langSelect.value = ONG.state.lang;
        }

        // Charger les données (qui appelle renderView)
        await ONG.loadData();

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
        ONG.on('btnExportCalendar', 'click', () => {
            if (ONG.state.pid) {
                ONG.exportProjectCalendar(ONG.state.pid);
            } else {
                ONG.toast('Sélectionnez un projet ou exportez tous les projets depuis les paramètres', 'warning');
            }
        });
        ONG.on('btnSettings', 'click', () => {
            ONG.openModal('modalSettings');
            ONG.loadBackupsList();
            ONG.loadMembersList();
            ONG.loadAIConfig();
        });
        ONG.on('btnAddProject', 'click', () => ONG.openModalProject());
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
            // Collecter les IDs des membres sélectionnés
            const selectedMembers = Array.from(document.querySelectorAll('#groupMembersList input[type="checkbox"]:checked'))
                .map(cb => parseInt(cb.value));
            fd.append('member_ids', JSON.stringify(selectedMembers));

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
            // Extraire isAdmin avant de stocker les données
            ONG.isAdmin = r.data.isAdmin || false;
            ONG.data = r.data;
            ONG.renderSidebar();
            ONG.fillFilters();
            ONG.renderView();
            ONG.fillTeamSelects();
            ONG.checkConflicts();
            ONG.updateAdminUI();
        }
    },

    /**
     * Met à jour l'interface en fonction du statut admin
     */
    updateAdminUI: () => {
        const btnSettings = document.getElementById('btnSettings');
        if (btnSettings) {
            btnSettings.style.display = ONG.isAdmin ? '' : 'none';
        }

        // Afficher/masquer la section de gestion des membres dans les paramètres
        const memberManagementSection = document.getElementById('memberManagementSection');
        if (memberManagementSection) {
            memberManagementSection.style.display = ONG.isAdmin ? 'block' : 'none';
        }
    },

    /**
     * Charge la liste des membres (admin uniquement)
     */
    loadMembersList: async () => {
        if (!ONG.isAdmin) {
            console.log('❌ User is not admin, skipping members list');
            return;
        }

        console.log('📋 Loading members list...');
        const r = await ONG.post('list_members');
        console.log('📋 Members response:', r);

        if (r.ok && r.data.members) {
            const container = document.getElementById('membersList');
            if (!container) {
                console.log('❌ membersList container not found in DOM');
                return;
            }

            console.log('✅ Members data:', r.data.members, 'Count:', r.data.members.length);

            container.innerHTML = r.data.members.map(member => {
                const isAdmin = member.is_admin == 1;
                const fullName = `${member.fname} ${member.lname}`;
                return `
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-sm">
                        <div class="flex-1">
                            <span class="font-semibold">${ONG.escape(fullName)}</span>
                            <div class="text-xs text-gray-500">${ONG.escape(member.email)}</div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <span class="text-xs ${isAdmin ? 'text-green-600' : 'text-gray-500'}">
                                ${isAdmin ? '👑 Admin' : '👤 User'}
                            </span>
                            <input type="checkbox"
                                   ${isAdmin ? 'checked' : ''}
                                   onchange="ONG.toggleMemberRole(${member.id}, this.checked)"
                                   class="toggle-checkbox">
                        </label>
                    </div>
                `;
            }).join('');
        }
    },

    /**
     * Bascule le rôle admin d'un membre
     */
    toggleMemberRole: async (memberId, isAdmin) => {
        if (!confirm(`Êtes-vous sûr de vouloir ${isAdmin ? 'promouvoir' : 'rétrograder'} ce membre ?`)) {
            // Recharger la liste pour réinitialiser la checkbox
            ONG.loadMembersList();
            return;
        }

        const r = await ONG.post('update_member_role', {
            member_id: memberId,
            is_admin: isAdmin ? 1 : 0
        });

        if (r.ok) {
            ONG.toast(isAdmin ? 'Membre promu administrateur' : 'Droits admin retirés', 'success');
            ONG.loadMembersList();
        } else {
            ONG.toast(r.msg || 'Erreur lors de la mise à jour', 'error');
            ONG.loadMembersList();
        }
    },

    /**
     * Charge la configuration de l'API de l'assistant IA
     */
    loadAIConfig: async () => {
        if (!ONG.isAdmin) {
            return;
        }

        // Afficher la section AI config pour les admins
        const aiSection = document.getElementById('aiConfigSection');
        if (aiSection) {
            aiSection.style.display = 'block';
        }

        // Charger la configuration actuelle depuis les données de l'équipe
        const r = await ONG.post('load_all');
        if (r.ok && r.data.team) {
            const team = r.data.team;

            // Remplir le formulaire
            const useApiCheckbox = document.getElementById('aiUseApi');
            const providerSelect = document.getElementById('aiProvider');
            const apiKeyInput = document.getElementById('aiApiKey');
            const modelInput = document.getElementById('aiModel');
            const apiFields = document.getElementById('aiApiFields');

            if (useApiCheckbox) {
                useApiCheckbox.checked = team.ai_use_api == 1;
                // Afficher/masquer les champs API
                if (apiFields) {
                    apiFields.style.display = team.ai_use_api == 1 ? 'block' : 'none';
                }
            }

            if (providerSelect && team.ai_api_provider) {
                providerSelect.value = team.ai_api_provider;
            }

            if (apiKeyInput && team.ai_api_key) {
                apiKeyInput.value = team.ai_api_key;
            }

            if (modelInput && team.ai_api_model) {
                modelInput.value = team.ai_api_model;
            }
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
        const tabs = ['dashboard', 'global', 'list', 'kanban', 'groups', 'gantt', 'calendar', 'milestones', 'assistant'];

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
            case 'assistant':
                ONG.renderAssistantView(container);
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
            const doneCount = milestoneTasks.filter(t => t.status === 'done').length;
            const progress = milestoneTasks.length > 0 ? Math.round((doneCount / milestoneTasks.length) * 100) : 0;

            // Trouver le milestone parent si dépendance
            const dependsOnMilestone = milestone.depends_on ? milestones.find(parent => parent.id == milestone.depends_on) : null;
            const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;

            html += `
                <tr class="bg-indigo-50 border-t-2 border-indigo-300">
                    <td colspan="7" class="px-3 py-2 font-bold">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-indigo-600">📍</span>
                                <span>${ONG.escape(milestone.name)}</span>
                                <span class="text-xs font-normal text-gray-600">(${milestone.date})</span>
                                ${dependsOnMilestone ? `<span class="text-xs font-normal text-gray-600">🔗 ${dict.depends_on || 'Dépend de'}: ${ONG.escape(dependsOnMilestone.name)}</span>` : ''}
                                <span class="text-xs font-normal text-gray-500">${milestoneTasks.length} tâche${milestoneTasks.length > 1 ? 's' : ''}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: ${progress}%"></div>
                                </div>
                                <span class="text-xs text-gray-600">${progress}%</span>
                                <div class="flex gap-1">
                                    <button onclick='ONG.editMilestone(${JSON.stringify(milestone)})' class="text-blue-600 hover:text-blue-800" title="Éditer">✏️</button>
                                    <button onclick="ONG.deleteItem('milestones', ${milestone.id})" class="text-red-600 hover:text-red-800" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            milestoneTasks.forEach(t => {
                html += renderTaskRow(t);
            });
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

        // Fonction helper pour obtenir les noms des membres d'un groupe
        const getGroupMembersHtml = (group) => {
            let memberIds = [];
            if (group.member_ids) {
                try {
                    memberIds = JSON.parse(group.member_ids);
                } catch (e) {
                    memberIds = [];
                }
            }
            if (memberIds.length > 0 && ONG.data.members) {
                const memberNames = memberIds
                    .map(id => {
                        const member = ONG.data.members.find(m => m.id == id);
                        return member ? `${member.fname} ${member.lname}` : null;
                    })
                    .filter(name => name !== null);
                if (memberNames.length > 0) {
                    return `<div class="text-xs text-gray-500 mb-2">
                                <span class="font-semibold">👥 Membres: </span>
                                <span>${memberNames.join(', ')}</span>
                            </div>`;
                }
            }
            return '';
        };

        let html = `
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
                            <div class="text-xs text-gray-500 mb-1">Responsable: ${ONG.getMemberName(g.owner_id)}</div>
                            ${getGroupMembersHtml(g)}
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

        // Initialiser le tri des jalons si pas défini
        if (!ONG.state.milestoneSort) {
            ONG.state.milestoneSort = { field: 'date', dir: 'asc' };
        }

        const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
        let milestones = ONG.data.milestones.filter(m => m.project_id == ONG.state.pid);

        // Trier les milestones
        milestones.sort((a, b) => {
            const field = ONG.state.milestoneSort.field;
            const dir = ONG.state.milestoneSort.dir === 'asc' ? 1 : -1;

            if (field === 'name') {
                return a.name.localeCompare(b.name) * dir;
            } else if (field === 'date') {
                return ((a.date || '').localeCompare(b.date || '')) * dir;
            }
            return 0;
        });

        let html = `
            <div class="mb-4 flex gap-2 items-center">
                <span class="text-sm font-medium text-gray-700">${dict.sort || 'Trier par'} :</span>
                <button onclick="ONG.toggleMilestoneSort('name')"
                        class="px-3 py-1 text-sm border rounded ${ONG.state.milestoneSort.field === 'name' ? 'bg-blue-100 border-blue-500 text-blue-700' : 'bg-white hover:bg-gray-50'}">
                    📝 ${dict.name || 'Nom'} ${ONG.state.milestoneSort.field === 'name' ? (ONG.state.milestoneSort.dir === 'asc' ? '↑' : '↓') : ''}
                </button>
                <button onclick="ONG.toggleMilestoneSort('date')"
                        class="px-3 py-1 text-sm border rounded ${ONG.state.milestoneSort.field === 'date' ? 'bg-blue-100 border-blue-500 text-blue-700' : 'bg-white hover:bg-gray-50'}">
                    📅 ${dict.date || 'Date'} ${ONG.state.milestoneSort.field === 'date' ? (ONG.state.milestoneSort.dir === 'asc' ? '↑' : '↓') : ''}
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

                    // Trouver le milestone parent si dépendance
                    const dependsOnMilestone = m.depends_on ? milestones.find(parent => parent.id == m.depends_on) : null;
                    const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;

                    return `
                        <div class="bg-white p-4 rounded shadow">
                            <div class="flex justify-between items-center mb-2">
                                <div>
                                    <h3 class="font-bold text-lg">
                                        ${ONG.escape(m.name)}
                                        <span class="text-xs font-normal text-gray-500">(${m.date})</span>
                                    </h3>
                                    ${dependsOnMilestone ? `
                                        <div class="text-xs text-gray-600 mt-1 flex items-center gap-1">
                                            <i class="fas fa-link"></i>
                                            <span>${dict.depends_on || 'Dépend de'}: <strong>${ONG.escape(dependsOnMilestone.name)}</strong></span>
                                        </div>
                                    ` : ''}
                                </div>
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
                // Ajouter la dépendance si elle existe
                const dependencies = m.depends_on ? 'm_' + m.depends_on : '';

                ganttTasks.push({
                    id: 'm_' + m.id,
                    name: '◆ ' + m.name,
                    start: m.date,
                    end: m.date,
                    progress: 100,
                    dependencies: dependencies,
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
                // Trouver le milestone parent si dépendance
                const dependsOnMilestone = milestone.depends_on ? milestones.find(parent => parent.id == milestone.depends_on) : null;
                const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
                const titleSuffix = dependsOnMilestone ? ` (${dict.depends_on || 'Dépend de'}: ${dependsOnMilestone.name})` : '';

                events.push({
                    id: 'm_' + milestone.id,
                    title: '◆ ' + milestone.name + titleSuffix,
                    start: milestone.date,
                    allDay: true,
                    backgroundColor: '#10B981',
                    borderColor: '#10B981',
                    classNames: ['fc-event-milestone'],
                    extendedProps: {
                        type: 'milestone',
                        milestoneData: milestone,
                        dependsOn: dependsOnMilestone ? dependsOnMilestone.name : null
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
     * Rend la vue Carte Mentale (Mind Map)
     */
    renderMindMapView: (container, tasks) => {
        container.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">🧠 Carte Mentale du Projet</h2>
                    <div class="flex gap-2">
                        <button onclick="ONG.exportMindMap()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                            <i class="fas fa-download mr-2"></i>Exporter PNG
                        </button>
                        <button onclick="ONG.expandAllNodes()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">
                            <i class="fas fa-expand-alt mr-2"></i>Tout Développer
                        </button>
                        <button onclick="ONG.collapseAllNodes()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">
                            <i class="fas fa-compress-alt mr-2"></i>Tout Réduire
                        </button>
                    </div>
                </div>
                <div id="mindMapContainer" style="height: 600px; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb;"></div>
            </div>
        `;

        // Attendre que le DOM soit prêt
        setTimeout(() => {
            // Vérifier que MindElixir est chargé
            if (typeof MindElixir === 'undefined') {
                document.getElementById('mindMapContainer').innerHTML = "<p class='text-center text-red-500 p-8'>❌ Erreur: Bibliothèque MindElixir non chargée</p>";
                console.error('MindElixir non chargé !');
                return;
            }

            // Récupérer le projet actuel
            const project = ONG.data.projects.find(p => p.id == ONG.state.pid);
            if (!project) {
                document.getElementById('mindMapContainer').innerHTML = "<p class='text-center text-gray-500 p-8'>Aucun projet sélectionné</p>";
                return;
            }

            console.log('Projet sélectionné:', project.name);
            console.log('Nombre de tâches:', tasks.length);

            // Générer la structure de données pour MindElixir
            const mindMapData = ONG.generateMindMapData(project, tasks);

            console.log('Données Mind Map générées:', mindMapData);

            try {
                // Initialiser MindElixir
                const mind = new MindElixir({
                    el: '#mindMapContainer',
                    direction: MindElixir.SIDE,
                    draggable: true,
                    contextMenu: true,
                    toolBar: true,
                    nodeMenu: true,
                    keypress: true,
                    locale: ONG.state.lang === 'fr' ? 'fr' : 'en'
                });

                console.log('Initialisation avec nos données personnalisées (incluant linkData)');

                // Utiliser nos données générées (qui incluent maintenant linkData)
                mind.init(mindMapData);

                window.mindElixirInstance = mind;
                console.log('Mind Map initialisée avec succès - Structure complète chargée');

                // Vérifier le rendu après un court délai
                setTimeout(() => {
                    const svg = document.querySelector('#mindMapContainer svg');
                    if (svg) {
                        console.log('✅ SVG de la carte mentale trouvé dans le DOM');
                    } else {
                        console.error('❌ SVG de la carte mentale non trouvé');
                    }
                }, 300);

            } catch (err) {
                console.error('Erreur lors de l\'initialisation de Mind Map:', err);
                console.error('Stack:', err.stack);
                document.getElementById('mindMapContainer').innerHTML = `<p class='text-center text-red-500 p-8'>❌ Erreur: ${err.message}</p>`;
            }
        }, 100);
    },

    /**
     * Génère la structure de données pour la carte mentale
     */
    generateMindMapData: (project, tasks) => {
        const milestones = ONG.data.milestones.filter(m => m.project_id == project.id);
        const groups = ONG.data.groups.filter(g => g.project_id == project.id);

        console.log('Jalons trouvés:', milestones.length);
        console.log('Groupes trouvés:', groups.length);

        const getTasksFor = (milestoneId = null, groupId = null) => {
            return tasks.filter(t => {
                if (milestoneId !== null) return t.milestone_id == milestoneId;
                if (groupId !== null) return t.group_id == groupId;
                return false;
            });
        };

        const createTaskNode = (task) => {
            const member = ONG.data.members.find(m => m.id == task.owner_id);

            const node = {
                topic: task.title,
                id: `task-${task.id}`,
                expanded: true
            };

            // Ajouter le membre comme enfant si présent
            if (member) {
                node.children = [{
                    topic: `${member.fname} ${member.lname}`,
                    id: `member-${task.id}`,
                    expanded: true
                }];
            }

            return node;
        };

        let childrenNodes = [];

        // Ajouter les jalons avec leurs tâches
        if (milestones.length > 0) {
            const milestoneNodes = milestones.map(milestone => {
                const milestoneTasks = getTasksFor(milestone.id, null);
                return {
                    topic: `Milestone: ${milestone.name}`,
                    id: `milestone-${milestone.id}`,
                    expanded: true,
                    children: milestoneTasks.map(createTaskNode)
                };
            });
            childrenNodes = childrenNodes.concat(milestoneNodes);
        }

        // Ajouter les groupes avec leurs tâches
        if (groups.length > 0) {
            const groupNodes = groups.map(group => {
                const groupTasks = getTasksFor(null, group.id);
                return {
                    topic: `Group: ${group.name}`,
                    id: `group-${group.id}`,
                    expanded: true,
                    children: groupTasks.map(createTaskNode)
                };
            });
            childrenNodes = childrenNodes.concat(groupNodes);
        }

        // Ajouter les tâches orphelines
        const orphanTasks = tasks.filter(t => !t.milestone_id && !t.group_id);
        if (orphanTasks.length > 0) {
            childrenNodes.push({
                topic: 'Unclassified Tasks',
                id: 'orphans',
                expanded: true,
                children: orphanTasks.map(createTaskNode)
            });
        }

        // Si aucune organisation (pas de jalons ni groupes), afficher toutes les tâches directement
        if (childrenNodes.length === 0 && tasks.length > 0) {
            console.log('Aucun jalon/groupe trouvé, affichage de toutes les tâches directement');
            childrenNodes = tasks.map(createTaskNode);
        }

        // Si toujours rien, ajouter un nœud par défaut
        if (childrenNodes.length === 0) {
            childrenNodes = [{
                topic: 'Aucune tâche pour le moment',
                id: 'empty',
                style: {
                    background: '#9CA3AF',
                    color: '#fff',
                    fontSize: '14px',
                    padding: '10px 15px',
                    borderRadius: '8px'
                }
            }];
        }

        console.log('Nœuds enfants générés:', childrenNodes.length);

        // Fonction pour ajouter expanded: true récursivement
        const expandNode = (node) => {
            node.expanded = true;
            if (node.children && node.children.length > 0) {
                node.children.forEach(child => expandNode(child));
            }
            return node;
        };

        // Expandre tous les nœuds
        childrenNodes = childrenNodes.map(expandNode);

        const mindMapData = {
            nodeData: {
                id: 'root',
                topic: project.name,
                root: true,
                expanded: true,
                children: childrenNodes
            },
            linkData: {}  // Propriété requise par MindElixir
        };

        console.log('Structure finale (simplifiée sans styles):', mindMapData);

        return mindMapData;
    },

    /**
     * Exporte la carte mentale en PNG
     */
    exportMindMap: () => {
        if (!window.mindElixirInstance) {
            ONG.showToast('Carte mentale non disponible', 'error');
            return;
        }
        try {
            const project = ONG.data.projects.find(p => p.id == ONG.state.pid);
            const filename = project ? `mindmap-${project.name.toLowerCase().replace(/\s+/g, '-')}.png` : 'mindmap.png';
            MindElixir.exportPng(window.mindElixirInstance, filename);
            ONG.showToast('Carte mentale exportée !', 'success');
        } catch (err) {
            console.error('Erreur export mind map:', err);
            ONG.showToast('Erreur lors de l\'export', 'error');
        }
    },

    /**
     * Développe tous les noeuds de la carte mentale
     */
    expandAllNodes: () => {
        if (window.mindElixirInstance) {
            window.mindElixirInstance.expandNode();
        }
    },

    /**
     * Réduit tous les noeuds de la carte mentale
     */
    collapseAllNodes: () => {
        if (window.mindElixirInstance) {
            const allNodes = document.querySelectorAll('.mind-elixir-node');
            allNodes.forEach((node, index) => {
                if (index > 0) {
                    window.mindElixirInstance.selectNode(node);
                    window.mindElixirInstance.collapse();
                }
            });
        }
    },

    /**
     * Rend la vue Assistant IA
     */
    renderAssistantView: (container) => {
        const t = ONG.dict[ONG.state.lang];

        if (!ONG.state.pid) {
            container.innerHTML = "<p class='text-center text-gray-400'>" + t.select_project + "</p>";
            return;
        }

        // Déterminer le mode actif
        const team = ONG.data.team || {};
        const useApi = team.ai_use_api == 1;
        const provider = team.ai_api_provider || 'rules';
        const model = team.ai_api_model || '';

        let modeBadge = '';
        if (useApi && provider !== 'rules') {
            const providerNames = {
                'claude': 'Claude',
                'openai': 'OpenAI',
                'azure': 'Azure'
            };
            const providerName = providerNames[provider] || provider;
            const modelText = model ? ` (${model})` : '';
            modeBadge = `<span class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-full font-semibold">
                🤖 ${t.assistant_api_mode || 'Mode API'}: ${providerName}${modelText}
            </span>`;
        } else {
            modeBadge = `<span class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">
                💡 ${t.assistant_free_mode || 'Mode Gratuit (Règles)'}
            </span>`;
        }

        container.innerHTML = `
            <div class="h-full flex flex-col bg-white rounded-lg shadow">
                <div class="p-4 border-b flex justify-between items-center bg-gradient-to-r from-blue-50 to-purple-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-white text-lg"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-bold text-gray-800">${t.ai_assistant}</h2>
                                ${modeBadge}
                            </div>
                            <p class="text-sm text-gray-600">${t.assistant_welcome}</p>
                        </div>
                    </div>
                    <button id="btnNewConversation" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>${t.new_conversation}</span>
                    </button>
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">
                    <!-- Zone de chat -->
                    <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Messages apparaîtront ici -->
                    </div>

                    <!-- Zone de saisie -->
                    <div class="p-4 border-t bg-gray-50">
                        <div id="suggestionButtons" class="mb-3 flex flex-wrap gap-2">
                            <!-- Boutons de suggestion apparaîtront ici -->
                        </div>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                id="chatInput"
                                placeholder="${t.assistant_placeholder}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <button id="btnSendMessage" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>${t.send_message}</span>
                            </button>
                        </div>
                        <div id="generateButton" class="mt-3" style="display: none;">
                            <button id="btnGenerateStructure" class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition flex items-center justify-center gap-2">
                                <i class="fas fa-magic"></i>
                                <span>${t.generate_structure}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Initialiser la conversation
        ONG.initAssistant();
    },

    /**
     * Initialise l'assistant IA
     */
    initAssistant: async () => {
        // Vérifier s'il y a une conversation active
        ONG.assistant = ONG.assistant || {
            conversationId: null,
            messages: []
        };

        const btnNewConversation = document.getElementById('btnNewConversation');
        const btnSendMessage = document.getElementById('btnSendMessage');
        const btnGenerateStructure = document.getElementById('btnGenerateStructure');
        const chatInput = document.getElementById('chatInput');

        if (!btnNewConversation || !btnSendMessage || !chatInput) {
            console.error('Assistant elements not found');
            return;
        }

        // Démarrer une nouvelle conversation
        const startNewConversation = async () => {
            const response = await ONG.post('start_conversation', { project_id: ONG.state.pid });
            if (response.ok) {
                ONG.assistant.conversationId = response.data.conversation_id;
                ONG.assistant.messages = [];
                // Effacer le chat
                const chatMessages = document.getElementById('chatMessages');
                if (chatMessages) chatMessages.innerHTML = '';
                // Ajouter le message initial avec suggestions
                ONG.addAssistantMessage(response.data.message, response.data.suggestions);
            }
        };

        btnNewConversation.onclick = startNewConversation;

        // Envoyer un message
        const sendMessage = async () => {
            const message = chatInput.value.trim();
            if (!message) return;

            if (!ONG.assistant.conversationId) {
                const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
                ONG.toast(dict.assistant_start_conversation || 'Démarrez d\'abord une conversation', 'warning');
                return;
            }

            // Ajouter le message de l'utilisateur
            ONG.addUserMessage(message);
            chatInput.value = '';

            // Afficher l'indicateur de saisie
            ONG.showTypingIndicator();

            try {
                // Envoyer le message à l'API
                const response = await ONG.post('send_message', {
                    conversation_id: ONG.assistant.conversationId,
                    message: message
                });

                ONG.hideTypingIndicator();

                if (response.ok) {
                    ONG.addAssistantMessage(response.data.message, response.data.suggestions);

                    // Afficher le bouton de génération si la conversation est terminée
                    if (response.data.completed) {
                        const generateBtn = document.getElementById('generateButton');
                        if (generateBtn) generateBtn.style.display = 'block';
                    }
                } else {
                    console.error('API Error:', response);
                    ONG.toast('Erreur lors de l\'envoi du message', 'error');
                }
            } catch (error) {
                console.error('Send message error:', error);
                ONG.hideTypingIndicator();
                ONG.toast('Erreur lors de l\'envoi du message', 'error');
            }
        };

        btnSendMessage.onclick = sendMessage;
        chatInput.onkeypress = (e) => {
            if (e.key === 'Enter') sendMessage();
        };

        // Générer la structure
        if (btnGenerateStructure) {
            btnGenerateStructure.onclick = async () => {
                const t = ONG.dict[ONG.state.lang];
                btnGenerateStructure.disabled = true;
                btnGenerateStructure.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${t.generating}`;

                const response = await ONG.post('generate_structure', {
                    conversation_id: ONG.assistant.conversationId,
                    project_id: ONG.state.pid
                });

                if (response.ok) {
                    ONG.toast(t.structure_generated, 'success');

                    // Recharger les données du projet
                    await ONG.loadData();

                    // Basculer vers la vue des groupes pour voir le résultat
                    ONG.switchView('groups');
                } else {
                    btnGenerateStructure.disabled = false;
                    btnGenerateStructure.innerHTML = `<i class="fas fa-magic"></i> ${t.generate_structure}`;
                }
            };
        }

        // Démarrer automatiquement une conversation si aucune n'existe
        if (!ONG.assistant.conversationId) {
            await startNewConversation();
        }
    },

    /**
     * Ajoute un message utilisateur au chat
     */
    addUserMessage: (message) => {
        const chatMessages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex justify-end';
        messageDiv.innerHTML = `
            <div class="max-w-[70%] bg-blue-600 text-white rounded-lg px-4 py-3 shadow">
                <p class="text-sm whitespace-pre-wrap">${ONG.escapeHtml(message)}</p>
            </div>
        `;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        ONG.assistant.messages.push({ role: 'user', content: message });
    },

    /**
     * Ajoute un message de l'assistant au chat
     */
    addAssistantMessage: (message, suggestions = null) => {
        const chatMessages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex justify-start';

        // Convertir les sauts de ligne en <br> et supporter le markdown basique
        const formattedMessage = ONG.formatAssistantMessage(message);

        messageDiv.innerHTML = `
            <div class="max-w-[70%] bg-gray-100 text-gray-800 rounded-lg px-4 py-3 shadow">
                <div class="flex items-start gap-2">
                    <i class="fas fa-robot text-blue-600 mt-1"></i>
                    <div class="text-sm whitespace-pre-wrap">${formattedMessage}</div>
                </div>
            </div>
        `;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        ONG.assistant.messages.push({ role: 'assistant', content: message });

        // Afficher les suggestions si présentes
        if (suggestions && suggestions.length > 0) {
            ONG.showSuggestions(suggestions);
        } else {
            document.getElementById('suggestionButtons').innerHTML = '';
        }
    },

    /**
     * Formate le message de l'assistant (supporte le markdown basique)
     */
    formatAssistantMessage: (message) => {
        return message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Gras
            .replace(/\n/g, '<br>'); // Sauts de ligne
    },

    /**
     * Affiche les boutons de suggestion
     */
    showSuggestions: (suggestions) => {
        const container = document.getElementById('suggestionButtons');
        container.innerHTML = suggestions.map(suggestion => `
            <button class="suggestion-btn px-4 py-2 bg-white border-2 border-blue-400 text-blue-700 rounded-lg hover:bg-blue-50 transition text-sm">
                ${ONG.escapeHtml(suggestion)}
            </button>
        `).join('');

        // Gérer les clics sur les suggestions
        container.querySelectorAll('.suggestion-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('chatInput').value = btn.textContent.trim();
                document.getElementById('btnSendMessage').click();
            });
        });
    },

    /**
     * Affiche l'indicateur de saisie
     */
    showTypingIndicator: () => {
        const t = ONG.dict[ONG.state.lang];
        const chatMessages = document.getElementById('chatMessages');
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'flex justify-start';
        typingDiv.innerHTML = `
            <div class="max-w-[70%] bg-gray-100 text-gray-600 rounded-lg px-4 py-3 shadow">
                <div class="flex items-center gap-2">
                    <i class="fas fa-robot text-blue-600"></i>
                    <span class="text-sm italic">${t.typing}</span>
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                    </div>
                </div>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    },

    /**
     * Masque l'indicateur de saisie
     */
    hideTypingIndicator: () => {
        const typingDiv = document.getElementById('typingIndicator');
        if (typingDiv) typingDiv.remove();
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

        const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
        const title = document.getElementById('modalTaskTitle');
        if (title) title.textContent = dict.edit_task_title || "Éditer Tâche";

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

        const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
        ONG.setVal('projId', '');
        const title = document.getElementById('modalProjectTitle');
        if (title) title.innerText = dict.new_proj || 'Nouveau Projet';

        ONG.openModal('modalProject');
    },

    /**
     * Édite un projet
     */
    editProject: (id) => {
        const p = ONG.data.projects.find(x => x.id == id);
        if (!p) return;

        const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
        const title = document.getElementById('modalProjectTitle');
        if (title) title.innerText = dict.edit_project_title || 'Éditer Projet';

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
            const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
            alert(dict.select_project || "Sélectionnez un projet");
            return;
        }

        const form = document.querySelector('#modalGroup form');
        if (form) form.reset();

        ONG.setVal('groupProjectId', ONG.state.pid);
        ONG.fillGroupMembersList([]);
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

        // Parser les member_ids (stockés en JSON)
        let selectedMembers = [];
        if (g.member_ids) {
            try {
                selectedMembers = JSON.parse(g.member_ids);
            } catch (e) {
                selectedMembers = [];
            }
        }
        ONG.fillGroupMembersList(selectedMembers);

        ONG.openModal('modalGroup');
    },

    /**
     * Ouvre le modal de jalon
     */
    openMilestoneModal: () => {
        if (!ONG.state.pid) {
            const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
            alert(dict.select_project || "Sélectionnez un projet");
            return;
        }

        const form = document.querySelector('#modalMilestone form');
        if (form) form.reset();

        ONG.setVal('milestoneProjectId', ONG.state.pid);
        ONG.fillMilestoneDependencies();
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
        ONG.fillMilestoneDependencies(m.id);
        ONG.setVal('milestoneDependsOn', m.depends_on || '');
        ONG.openModal('modalMilestone');
    },

    /**
     * Remplit le select des dépendances de milestone
     */
    fillMilestoneDependencies: (excludeId = null) => {
        const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;
        const milestones = (ONG.data.milestones || []).filter(m => m.project_id == ONG.state.pid && m.id != excludeId);

        const opts = `<option value="">${dict.no_dependency || 'Aucune dépendance'}</option>` +
            milestones.map(m => `<option value="${m.id}">${ONG.escape(m.name)} (${m.date})</option>`).join('');

        const select = ONG.el('milestoneDependsOn');
        if (select) select.innerHTML = opts;
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
     * Remplit la liste des membres pour le modal de groupe
     */
    fillGroupMembersList: (selectedMemberIds = []) => {
        const container = document.getElementById('groupMembersList');
        if (!container) return;

        if (ONG.data.members.length === 0) {
            container.innerHTML = '<p class="text-gray-400 text-sm">Aucun membre disponible</p>';
            return;
        }

        container.innerHTML = ONG.data.members.map(m => {
            const isChecked = selectedMemberIds.includes(m.id);
            return `
                <label class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded cursor-pointer">
                    <input type="checkbox" value="${m.id}" ${isChecked ? 'checked' : ''}
                           class="rounded border-gray-300">
                    <span class="text-sm">${ONG.escape(m.fname)} ${ONG.escape(m.lname)}</span>
                </label>
            `;
        }).join('');
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
     * Toggle le tri des jalons
     */
    toggleMilestoneSort: (field) => {
        if (!ONG.state.milestoneSort) {
            ONG.state.milestoneSort = { field: 'date', dir: 'asc' };
        }

        if (ONG.state.milestoneSort.field === field) {
            // Inverser la direction si même champ
            ONG.state.milestoneSort.dir = (ONG.state.milestoneSort.dir === 'asc') ? 'desc' : 'asc';
        } else {
            // Nouveau champ, commencer par ordre croissant
            ONG.state.milestoneSort.field = field;
            ONG.state.milestoneSort.dir = 'asc';
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
        // Construire l'URL avec la nouvelle langue
        const url = new URL(window.location);
        url.searchParams.set('lang', lang);

        // Recharger la page avec la nouvelle langue
        window.location.href = url.toString();
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

        // Récupérer les traductions
        const dict = ONG.dict[ONG.state.lang] || ONG.dict.fr;

        // HTML du Dashboard
        let html = `
            <div class="p-6 space-y-6">
                <!-- Cartes de statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">${dict.total_tasks || 'Total Tâches'}</p>
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
                                <p class="text-gray-500 text-sm">${dict.wip || 'En cours'}</p>
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
                                <p class="text-gray-500 text-sm">${dict.completed || 'Terminées'}</p>
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
                                <p class="text-gray-500 text-sm">${dict.progress || 'Progression'}</p>
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
                        <h3 class="text-lg font-bold mb-4">${dict.tasks_by_status || 'Tâches par Statut'}</h3>
                        <div style="height: 250px;">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>

                    <!-- Graphique par Projet -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">${dict.tasks_by_project || 'Tâches par Projet'}</h3>
                        <div style="height: 250px;">
                            <canvas id="chartProjects"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tâches à venir & Par responsable -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Tâches à venir -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">📅 ${dict.upcoming_week || 'À venir cette semaine'}</h3>
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
                        ` : `<p class="text-gray-400 text-center py-8">${dict.no_upcoming || 'Aucune tâche à venir cette semaine'}</p>`}
                    </div>

                    <!-- Graphique par Responsable -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">${dict.tasks_by_assignee || 'Tâches par Responsable'}</h3>
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
                        labels: [dict.todo || 'À faire', dict.wip || 'En cours', dict.done || 'Terminé'],
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
                            label: dict.tasks_label || 'Tâches',
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
     * Exporte le calendrier du projet actuel au format .ics
     */
    exportProjectCalendar: (projectId) => {
        if (!projectId) {
            ONG.toast('Sélectionnez un projet', 'warning');
            return;
        }

        // Créer un formulaire pour déclencher le téléchargement
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'export_project_calendar';

        const projectInput = document.createElement('input');
        projectInput.type = 'hidden';
        projectInput.name = 'project_id';
        projectInput.value = projectId;

        form.appendChild(actionInput);
        form.appendChild(projectInput);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        ONG.toast('Export du calendrier en cours...', 'info');
    },

    /**
     * Exporte le calendrier de tous les projets de l'équipe au format .ics
     */
    exportTeamCalendar: () => {
        // Créer un formulaire pour déclencher le téléchargement
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'export_team_calendar';

        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        ONG.toast('Export du calendrier de l\'équipe en cours...', 'info');
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

    // Gérer l'affichage/masquage des champs API de l'assistant IA
    const aiUseApiCheckbox = document.getElementById('aiUseApi');
    if (aiUseApiCheckbox) {
        aiUseApiCheckbox.addEventListener('change', (e) => {
            const apiFields = document.getElementById('aiApiFields');
            if (apiFields) {
                apiFields.style.display = e.target.checked ? 'block' : 'none';
            }
        });
    }

    // Gérer la soumission du formulaire de configuration IA
    const formAIConfig = document.getElementById('formAIConfig');
    if (formAIConfig) {
        formAIConfig.addEventListener('submit', async (e) => {
            e.preventDefault();

            const useApi = document.getElementById('aiUseApi').checked;
            const provider = document.getElementById('aiProvider').value;
            const apiKey = document.getElementById('aiApiKey').value.trim();
            const model = document.getElementById('aiModel').value.trim();

            const data = {
                ai_use_api: useApi ? 1 : 0,
                ai_api_provider: provider,
                ai_api_key: apiKey,
                ai_api_model: model
            };

            const r = await ONG.post('update_ai_config', data);

            if (r.ok) {
                ONG.toast('Configuration IA mise à jour avec succès', 'success');
            } else {
                ONG.toast(r.msg || 'Erreur lors de la mise à jour', 'error');
            }
        });
    }
});
// Cache buster - version 1764791874
