<!-- Modal Settings -->
<div id="modalSettings" class="modal">
    <div class="bg-white rounded w-96 max-h-[90vh] flex flex-col">
        <div class="p-6 pb-3">
            <h3 class="font-bold"><?= $t->translate('settings') ?></h3>
        </div>
        <div class="flex-1 overflow-y-auto px-6">
            <form id="formSettings">
            <label class="block text-xs font-bold text-gray-500 mb-1"><?= $t->translate('org_name') ?></label>
            <input name="org_name" class="w-full border p-2 mb-3 rounded" value="<?= htmlspecialchars($teamName) ?>" required>

            <label class="block text-xs font-bold text-gray-500 mb-1">🎨 Thème de couleur</label>
            <div class="grid grid-cols-5 gap-2 mb-3">
                <button type="button" onclick="ONG.setTheme('blue')" class="h-10 rounded border-2 hover:scale-110 transition" style="background: linear-gradient(135deg, #2563EB 0%, #DBEAFE 100%)" title="Bleu"></button>
                <button type="button" onclick="ONG.setTheme('green')" class="h-10 rounded border-2 hover:scale-110 transition" style="background: linear-gradient(135deg, #10B981 0%, #D1FAE5 100%)" title="Vert"></button>
                <button type="button" onclick="ONG.setTheme('purple')" class="h-10 rounded border-2 hover:scale-110 transition" style="background: linear-gradient(135deg, #8B5CF6 0%, #EDE9FE 100%)" title="Violet"></button>
                <button type="button" onclick="ONG.setTheme('orange')" class="h-10 rounded border-2 hover:scale-110 transition" style="background: linear-gradient(135deg, #F97316 0%, #FFEDD5 100%)" title="Orange"></button>
                <button type="button" onclick="ONG.setTheme('red')" class="h-10 rounded border-2 hover:scale-110 transition" style="background: linear-gradient(135deg, #EF4444 0%, #FEE2E2 100%)" title="Rouge"></button>
            </div>

            <label class="block text-xs font-bold text-gray-500 mb-1"><?= $t->translate('new_pass') ?></label>
            <input type="password" name="new_password" class="w-full border p-2 mb-3 rounded">

            <label class="block text-xs font-bold text-gray-500 mb-1"><?= $t->translate('current_pass') ?></label>
            <input type="password" name="current_password" class="w-full border p-2 mb-4 rounded border-red-200 bg-red-50" required>

            <button class="w-full bg-gray-800 text-white p-2 rounded"><?= $t->translate('save_settings') ?></button>
        </form>

        <!-- Section Backups -->
        <div class="mt-6 pt-6 border-t">
            <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
                <i class="fas fa-database"></i>
                💾 Sauvegarde des Données
            </h4>

            <button onclick="ONG.createBackup()" class="w-full bg-blue-600 text-white p-2 rounded mb-2 hover:bg-blue-700">
                <i class="fas fa-plus"></i> Créer une Sauvegarde
            </button>

            <button onclick="ONG.downloadCurrentDb()" class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700">
                <i class="fas fa-download"></i> Télécharger Base Actuelle
            </button>

            <!-- Liste des backups récents -->
            <div id="backupsList" class="mt-3 text-xs text-gray-600">
                <div class="italic">Chargement des sauvegardes...</div>
            </div>
        </div>

        <!-- Section Webhooks -->
        <div class="mt-6 pt-6 border-t">
            <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
                <i class="fas fa-link"></i>
                🔗 Webhooks & Intégrations
            </h4>

            <button onclick="ONG.openWebhooksModal()" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700">
                <i class="fas fa-cog"></i> Gérer les Webhooks
            </button>
        </div>

        <!-- Section Gestion des Équipes (Admin uniquement) -->
        <div id="teamManagementSection" class="mt-6 pt-6 border-t" style="display: none;">
            <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
                <i class="fas fa-users-cog"></i>
                👥 Gestion des Équipes
            </h4>

            <div id="teamsList" class="space-y-2 max-h-48 overflow-y-auto">
                <div class="text-xs text-gray-500 italic">Chargement des équipes...</div>
            </div>
        </div>
        </div>

        <div class="p-6 pt-3 border-t">
            <button type="button" class="w-full text-gray-500 btn-close"><?= $t->translate('cancel') ?></button>
        </div>
    </div>
</div>

<!-- Modal Project -->
<div id="modalProject" class="modal">
    <div class="bg-white p-6 rounded w-96">
        <h3 class="font-bold mb-4" id="modalProjectTitle"><?= $t->translate('new_proj') ?></h3>
        <form id="formProject">
            <input type="hidden" name="id" id="projId">
            <input name="name" id="projName" class="w-full border p-2 mb-2 rounded" placeholder="<?= $t->translate('title') ?>" required>
            <input name="desc" id="projDesc" class="w-full border p-2 mb-2 rounded" placeholder="<?= $t->translate('desc') ?>">
            <div class="flex gap-2 mb-2">
                <input type="date" name="start" id="projStart" class="w-1/2 border p-2 rounded">
                <input type="date" name="end" id="projEnd" class="w-1/2 border p-2 rounded">
            </div>
            <select name="owner_id" id="projOwner" class="w-full border p-2 mb-4 rounded team-select">
                <option value=""><?= $t->translate('resp') ?>...</option>
            </select>
            <button class="w-full bg-blue-600 text-white p-2 rounded"><?= $t->translate('create') ?></button>
            <button type="button" class="w-full mt-2 text-gray-500 btn-close"><?= $t->translate('cancel') ?></button>
        </form>
    </div>
</div>

<!-- Modal Task -->
<div id="modalTask" class="modal">
    <div class="bg-white p-6 rounded w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-lg mb-4" id="modalTaskTitle"><?= $t->translate('new_task') ?></h3>
        <form id="formTask">
            <input type="hidden" name="id" id="taskId">
            <input type="hidden" name="dependencies" id="taskDependencies">

            <input name="title" id="taskTitle" class="w-full border p-2 mb-2 rounded font-bold"
                   placeholder="<?= $t->translate('title') ?>" required>

            <textarea name="desc" id="taskDesc" class="w-full border p-2 mb-2 rounded"
                      placeholder="<?= $t->translate('desc') ?>"></textarea>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="text-xs text-gray-500"><?= $t->translate('proj') ?></label>
                    <select name="project_id" id="taskProjectSelect" class="w-full border p-2 rounded" required></select>
                </div>
                <div>
                    <label class="text-xs text-gray-500"><?= $t->translate('groups') ?></label>
                    <select name="group_id" id="taskGroupSelect" class="w-full border p-2 rounded">
                        <option value="">-</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500"><?= $t->translate('resp') ?></label>
                    <select name="owner_id" id="taskOwnerSelect" class="w-full border p-2 rounded team-select">
                        <option value="">-</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500"><?= $t->translate('status') ?></label>
                    <select name="status" id="taskStatus" class="w-full border p-2 rounded">
                        <option value="todo"><?= $t->translate('todo') ?></option>
                        <option value="wip"><?= $t->translate('wip') ?></option>
                        <option value="done"><?= $t->translate('done') ?></option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500"><?= $t->translate('start') ?></label>
                    <input type="date" name="start_date" id="taskStartDate" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="text-xs text-gray-500"><?= $t->translate('end') ?></label>
                    <input type="date" name="end_date" id="taskEndDate" class="w-full border p-2 rounded">
                </div>
            </div>

            <div class="mb-3">
                <input name="tags" id="taskTags" class="w-full border p-2 rounded text-sm"
                       placeholder="<?= $t->translate('tags') ?>">
            </div>

            <div class="mb-3">
                <input name="link" id="taskLink" class="w-full border p-2 rounded text-sm"
                       placeholder="<?= $t->translate('link') ?>">
            </div>

            <div class="bg-gray-50 p-3 rounded border">
                <label class="text-xs font-bold block mb-1"><?= $t->translate('deps') ?></label>
                <select name="milestone_id" id="taskMilestoneSelect" class="w-full border p-1 mb-2 text-sm">
                    <option value="">-</option>
                </select>
                <div class="text-xs text-gray-500 mb-1"><?= $t->translate('t_dep') ?>:</div>
                <div id="taskDepsList" class="max-h-24 overflow-y-auto border bg-white p-1"></div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" class="px-4 py-2 text-gray-600 btn-close"><?= $t->translate('cancel') ?></button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded"><?= $t->translate('save') ?></button>
            </div>
        </form>

        <!-- Section Commentaires -->
        <div id="taskCommentsSection" class="mt-6 border-t pt-4" style="display: none;">
            <h4 class="font-bold text-md mb-3 flex items-center gap-2">
                <i class="fas fa-comments"></i>
                💬 Commentaires
                <span id="commentsCount" class="text-sm text-gray-500"></span>
            </h4>

            <!-- Liste des commentaires -->
            <div id="commentsList" class="space-y-3 mb-4 max-h-64 overflow-y-auto"></div>

            <!-- Formulaire d'ajout de commentaire -->
            <div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <textarea id="newCommentText" class="w-full border p-2 rounded text-sm" rows="2"
                                  placeholder="Ajouter un commentaire..." maxlength="1000"
                                  oninput="ONG.updateCharCount(this, 'commentCharCount')"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <div class="text-xs text-gray-500">
                                <span class="font-semibold">Markdown:</span>
                                <code class="bg-gray-100 px-1">**gras**</code>
                                <code class="bg-gray-100 px-1">*italique*</code>
                                <code class="bg-gray-100 px-1">`code`</code>
                                <code class="bg-gray-100 px-1">- liste</code>
                            </div>
                            <span id="commentCharCount" class="text-xs text-gray-400">0/1000</span>
                        </div>
                    </div>
                    <button id="btnAddComment" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 self-start">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Group -->
<div id="modalGroup" class="modal">
    <div class="bg-white p-6 rounded w-96">
        <h3 class="font-bold mb-4" id="modalGroupTitle"><?= $t->translate('groups') ?></h3>
        <form id="formGroup">
            <input type="hidden" name="id" id="groupId">
            <input type="hidden" name="project_id" id="groupProjectId">

            <input name="name" id="groupName" class="w-full border p-2 mb-2 rounded"
                   placeholder="<?= $t->translate('title') ?>" required>

            <textarea name="description" id="groupDescription" rows="3"
                      class="w-full border p-2 mb-2 rounded"
                      placeholder="Description: Que font les membres de ce groupe?"></textarea>

            <select name="owner_id" id="groupOwner" class="w-full border p-2 mb-2 rounded team-select">
                <option value=""><?= $t->translate('resp') ?>...</option>
            </select>

            <input type="color" name="color" id="groupColor" value="#E5E7EB"
                   class="w-full h-10 p-1 rounded cursor-pointer">

            <button class="w-full bg-blue-600 text-white p-2 rounded mt-2"><?= $t->translate('save') ?></button>
            <button type="button" class="w-full mt-2 text-gray-500 btn-close"><?= $t->translate('cancel') ?></button>
        </form>
    </div>
</div>

<!-- Modal Milestone -->
<div id="modalMilestone" class="modal">
    <div class="bg-white p-6 rounded w-96">
        <h3 class="font-bold text-lg mb-4" id="modalMilestoneTitle"><?= $t->translate('milestones') ?></h3>
        <form id="formMilestone">
            <input type="hidden" name="id" id="milestoneId">
            <input type="hidden" name="project_id" id="milestoneProjectId">

            <input name="name" id="milestoneName" class="w-full border p-2 mb-2 rounded"
                   placeholder="<?= $t->translate('title') ?>" required>

            <input type="date" name="date" id="milestoneDate" class="w-full border p-2 mb-2 rounded" required>

            <select name="status" id="milestoneStatus" class="w-full border p-2 mb-4 rounded">
                <option value="active"><?= $t->translate('wip') ?></option>
                <option value="done"><?= $t->translate('done') ?></option>
            </select>

            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 border rounded btn-close"><?= $t->translate('cancel') ?></button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded"><?= $t->translate('save') ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Project -->
<div id="modalImport" class="modal">
    <div class="bg-white p-6 rounded w-full max-w-md">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <i class="fas fa-file-import"></i>
            📥 Importer un Projet
        </h3>

        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded text-sm">
            <p class="text-blue-800 mb-2"><strong>Format attendu :</strong> Fichier JSON exporté depuis ONG Manager</p>
            <p class="text-blue-600 text-xs">Le projet sera créé avec toutes ses tâches, groupes et jalons.</p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">
                Sélectionner un fichier JSON
            </label>
            <input type="file"
                   id="importFileInput"
                   accept=".json"
                   class="w-full border p-2 rounded">
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 text-gray-700 btn-close">
                Annuler
            </button>
            <button onclick="ONG.importProject()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fas fa-upload"></i> Importer
            </button>
        </div>
    </div>
</div>

<!-- Modal Team -->
<div id="modalTeam" class="modal">
    <div class="bg-white p-6 rounded w-full max-w-2xl">
        <h3 class="font-bold mb-4"><?= $t->translate('team') ?></h3>
        <form id="formMember" class="flex gap-2 mb-4 bg-gray-100 p-2 rounded">
            <input type="hidden" name="id" id="memberId">
            <input name="fname" id="memberFname" placeholder="Prénom" class="border p-1 rounded flex-1" required>
            <input name="lname" id="memberLname" placeholder="Nom" class="border p-1 rounded flex-1" required>
            <input name="email" id="memberEmail" placeholder="Email" class="border p-1 rounded flex-1" required>
            <button type="submit" class="bg-green-600 text-white px-3 rounded" id="btnSaveMember">
                <span id="memberBtnIcon">+</span>
            </button>
            <button type="button" class="bg-gray-400 text-white px-3 rounded hidden" id="btnCancelEditMember" onclick="ONG.cancelEditMember()">
                ✕
            </button>
        </form>
        <div id="teamList" class="space-y-1 max-h-60 overflow-y-auto"></div>
        <button class="mt-4 w-full border p-2 rounded text-gray-600 btn-close">Fermer</button>
    </div>
</div>

<!-- Modal Templates -->
<div id="modalTemplates" class="modal">
    <div class="bg-white p-6 rounded w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-xl mb-4">📋 Modèles de Projets</h3>

        <!-- Tabs -->
        <div class="flex gap-4 border-b mb-4">
            <button onclick="ONG.switchTemplateTab('list')" class="px-4 py-2 font-medium" id="btnTemplateTabList">
                Parcourir les modèles
            </button>
            <button onclick="ONG.switchTemplateTab('create')" class="px-4 py-2 font-medium" id="btnTemplateTabCreate">
                Créer un modèle
            </button>
            <button onclick="ONG.switchTemplateTab('use')" class="px-4 py-2 font-medium" id="btnTemplateTabUse">
                Utiliser un modèle
            </button>
        </div>

        <!-- Tab: Liste des templates -->
        <div id="templateTabList" class="template-tab">
            <div class="mb-3 text-sm text-gray-600">
                Utilisez les modèles ci-dessous pour créer rapidement de nouveaux projets avec une structure prédéfinie.
            </div>
            <div id="templatesList" class="space-y-3">
                <!-- Will be filled by JavaScript -->
            </div>
        </div>

        <!-- Tab: Créer un modèle -->
        <div id="templateTabCreate" class="template-tab hidden">
            <form id="formCreateTemplate">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sauvegarder le projet actuel comme modèle</label>
                    <select name="project_id" id="templateProjectSelect" class="w-full border p-2 rounded mb-3" required>
                        <option value="">-- Sélectionnez un projet --</option>
                    </select>

                    <input name="template_name" id="templateName" class="w-full border p-2 mb-2 rounded"
                           placeholder="Nom du modèle (ex: Campagne Marketing Type)" required>

                    <textarea name="template_desc" id="templateDesc" class="w-full border p-2 mb-2 rounded"
                              placeholder="Description du modèle (optionnel)"></textarea>

                    <select name="category" id="templateCategory" class="w-full border p-2 rounded">
                        <option value="custom">Personnalisé</option>
                        <option value="marketing">Marketing</option>
                        <option value="it">Informatique</option>
                        <option value="construction">Construction</option>
                        <option value="event">Événement</option>
                        <option value="research">Recherche</option>
                    </select>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4 text-sm">
                    <strong>Note:</strong> Le modèle contiendra la structure du projet (groupes, jalons, tâches) mais pas les dates ni les responsables spécifiques.
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-medium">
                    💾 Créer le modèle
                </button>
            </form>
        </div>

        <!-- Tab: Utiliser un modèle -->
        <div id="templateTabUse" class="template-tab hidden">
            <form id="formUseTemplate">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Créer un nouveau projet à partir d'un modèle</label>

                    <select name="template_id" id="useTemplateSelect" class="w-full border p-2 rounded mb-3" required>
                        <option value="">-- Sélectionnez un modèle --</option>
                    </select>

                    <input name="project_name" id="useTemplateName" class="w-full border p-2 mb-2 rounded"
                           placeholder="Nom du nouveau projet" required>

                    <textarea name="project_desc" id="useTemplateDesc" class="w-full border p-2 mb-3 rounded"
                              placeholder="Description du projet (optionnel)"></textarea>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-xs text-gray-500">Date de début</label>
                            <input type="date" name="start_date" id="useTemplateStartDate" class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Date de fin</label>
                            <input type="date" name="end_date" id="useTemplateEndDate" class="w-full border p-2 rounded">
                        </div>
                    </div>

                    <select name="owner_id" id="useTemplateOwner" class="w-full border p-2 rounded team-select">
                        <option value="">Responsable du projet...</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white p-2 rounded font-medium">
                    🚀 Créer le projet depuis ce modèle
                </button>
            </form>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <button class="px-4 py-2 border rounded text-gray-600 btn-close">Fermer</button>
        </div>
    </div>
</div>

<!-- Modal Webhooks -->
<div id="modalWebhooks" class="modal">
    <div class="bg-white p-6 rounded w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
            <i class="fas fa-link"></i>
            🔗 Gestion des Webhooks
        </h3>

        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded text-sm">
            <p class="text-blue-800 mb-1"><strong>Qu'est-ce qu'un webhook ?</strong></p>
            <p class="text-blue-600 text-xs">Les webhooks permettent d'envoyer automatiquement des notifications HTTP vers vos outils externes (Slack, Discord, etc.) quand des événements se produisent dans ONG Manager.</p>
        </div>

        <button onclick="ONG.openWebhookForm()" class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            <i class="fas fa-plus"></i> Nouveau Webhook
        </button>

        <div id="webhooksList" class="space-y-3">
            <div class="text-gray-500 italic text-sm">Chargement des webhooks...</div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <button class="px-4 py-2 border rounded text-gray-600 btn-close">Fermer</button>
        </div>
    </div>
</div>

<!-- Modal Webhook Form (Créer/Éditer) -->
<div id="modalWebhookForm" class="modal">
    <div class="bg-white p-6 rounded w-full max-w-xl">
        <h3 class="font-bold text-lg mb-4" id="webhookFormTitle">
            <i class="fas fa-plus"></i> Nouveau Webhook
        </h3>

        <form id="formWebhook" class="space-y-3">
            <input type="hidden" id="webhookId">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                <input type="text" id="webhookName" class="w-full border p-2 rounded" placeholder="Ex: Notifications Slack" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL du Webhook</label>
                <input type="url" id="webhookUrl" class="w-full border p-2 rounded" placeholder="https://hooks.slack.com/..." required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Événements à surveiller</label>
                <div class="space-y-2 max-h-48 overflow-y-auto border p-3 rounded bg-gray-50">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="*" class="webhook-event" data-exclusive="true">
                        <span class="text-sm">🌟 Tous les événements</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="task.created" class="webhook-event">
                        <span class="text-sm">✅ Nouvelle tâche créée</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="task.updated" class="webhook-event">
                        <span class="text-sm">✏️ Tâche mise à jour</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="task.deleted" class="webhook-event">
                        <span class="text-sm">🗑️ Tâche supprimée</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="task.status_changed" class="webhook-event">
                        <span class="text-sm">🔄 Statut de tâche modifié</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="project.created" class="webhook-event">
                        <span class="text-sm">📁 Nouveau projet créé</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="project.updated" class="webhook-event">
                        <span class="text-sm">📝 Projet mis à jour</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="comment.created" class="webhook-event">
                        <span class="text-sm">💬 Nouveau commentaire</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" value="milestone.completed" class="webhook-event">
                        <span class="text-sm">🎯 Jalon complété</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit" class="flex-1 bg-green-600 text-white p-2 rounded hover:bg-green-700">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <button type="button" class="flex-1 border p-2 rounded text-gray-600 hover:bg-gray-50 btn-close">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>
