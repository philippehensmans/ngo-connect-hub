<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t->translate('app') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="h-screen flex flex-col">

<div id="errorToast" class="fixed top-4 right-4 bg-red-600 text-white p-4 rounded shadow-lg hidden z-50"></div>
<div id="successToast" class="fixed top-4 right-4 bg-green-600 text-white p-4 rounded shadow-lg hidden z-50"></div>

<div class="flex-1 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center"><?= $t->translate('app') ?></h1>

        <!-- Formulaire de connexion -->
        <div id="loginContainer">
            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('email') ?></label>
                    <input type="email" name="email" placeholder="votre@email.org" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('pass') ?></label>
                    <input type="password" name="password" placeholder="<?= $t->translate('pass') ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i><?= $t->translate('login') ?>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-center text-gray-600 text-sm mb-3">
                    <?= $t->translate('no_account') ?>
                </p>
                <button onclick="showRegisterForm()" class="w-full bg-green-600 text-white p-2 rounded font-bold hover:bg-green-700 transition mb-2">
                    <i class="fas fa-user-plus mr-2"></i><?= $t->translate('register') ?? 'Créer mon organisation' ?>
                </button>
                <button onclick="showRequestForm()" class="w-full bg-gray-100 text-gray-700 p-2 rounded font-bold hover:bg-gray-200 transition text-sm">
                    <i class="fas fa-envelope mr-2"></i><?= $t->translate('request_access') ?>
                </button>
            </div>
        </div>

        <!-- Formulaire d'inscription -->
        <div id="registerContainer" class="hidden">
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-800">
                <i class="fas fa-info-circle mr-1"></i>
                <?= $t->translate('register_info') ?? 'Créez votre organisation et votre compte administrateur. Vous pourrez ensuite inviter vos collaborateurs.' ?>
            </div>
            <form id="registerForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('org_name') ?></label>
                    <input type="text" name="org_name" placeholder="<?= $t->translate('org_name_placeholder') ?? 'Mon Association' ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('firstname') ?></label>
                        <input type="text" name="fname" placeholder="Marie" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('lastname') ?></label>
                        <input type="text" name="lname" placeholder="Dupont" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('email') ?></label>
                    <input type="email" name="email" placeholder="contact@association.org" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('pass') ?></label>
                    <input type="password" name="password" placeholder="Min. 6 caractères" minlength="6" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white p-2 rounded font-bold hover:bg-green-700 transition">
                    <i class="fas fa-rocket mr-2"></i><?= $t->translate('create_org') ?? 'Créer mon organisation' ?>
                </button>
            </form>
            <div class="mt-4 text-center">
                <p class="text-gray-600 text-sm">
                    <?= $t->translate('already_account') ?>
                    <a href="#" onclick="showLogin()" class="text-blue-600 hover:underline font-medium">
                        <?= $t->translate('login') ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- Formulaire de demande d'accès -->
        <div id="requestContainer" class="hidden">
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
                <i class="fas fa-info-circle mr-1"></i>
                <?= $t->translate('request_access_info') ?>
            </div>
            <form id="requestForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('org_name') ?></label>
                    <input type="text" name="org_name" placeholder="<?= $t->translate('org_name_placeholder') ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('firstname') ?></label>
                        <input type="text" name="fname" placeholder="Marie" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('lastname') ?></label>
                        <input type="text" name="lname" placeholder="Dupont" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('email') ?></label>
                    <input type="email" name="email" placeholder="contact@association.org" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $t->translate('request_message') ?></label>
                    <textarea name="message" rows="3" placeholder="<?= $t->translate('request_message_placeholder') ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white p-2 rounded font-bold hover:bg-green-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i><?= $t->translate('send_request') ?>
                </button>
            </form>
            <div class="mt-4 text-center">
                <p class="text-gray-600 text-sm">
                    <?= $t->translate('already_account') ?>
                    <a href="#" onclick="showLogin()" class="text-blue-600 hover:underline font-medium">
                        <?= $t->translate('login') ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- Choix de l'organisation -->
        <div id="orgChoiceContainer" class="hidden">
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
                <i class="fas fa-building mr-1"></i>
                Vous avez des comptes dans plusieurs organisations. Choisissez celle à laquelle vous souhaitez vous connecter.
            </div>
            <div id="orgList" class="space-y-2 mb-4"></div>
            <div class="mt-4 text-center">
                <a href="#" onclick="showLogin()" class="text-gray-500 hover:underline text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Retour
                </a>
            </div>
        </div>

        <!-- Message de confirmation -->
        <div id="confirmationContainer" class="hidden text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle text-green-500 text-5xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2"><?= $t->translate('request_sent') ?></h2>
            <p class="text-gray-600 mb-4"><?= $t->translate('request_sent_info') ?></p>
            <button onclick="showLogin()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                <i class="fas fa-arrow-left mr-2"></i><?= $t->translate('back_to_login') ?>
            </button>
        </div>

        <div class="flex gap-2 justify-center mt-6">
            <a href="?lang=fr">FR</a>
            <a href="?lang=en">EN</a>
            <a href="?lang=es">ES</a>
            <a href="?lang=sl">SL</a>
        </div>
    </div>
</div>

<script>
function showRegisterForm() {
    document.getElementById('loginContainer').classList.add('hidden');
    document.getElementById('confirmationContainer').classList.add('hidden');
    document.getElementById('requestContainer').classList.add('hidden');
    document.getElementById('registerContainer').classList.remove('hidden');
    history.pushState({}, '', '?register=1');
}

function showRequestForm() {
    document.getElementById('loginContainer').classList.add('hidden');
    document.getElementById('confirmationContainer').classList.add('hidden');
    document.getElementById('registerContainer').classList.add('hidden');
    document.getElementById('requestContainer').classList.remove('hidden');
    history.pushState({}, '', '?request=1');
}

function showLogin() {
    document.getElementById('requestContainer').classList.add('hidden');
    document.getElementById('confirmationContainer').classList.add('hidden');
    document.getElementById('registerContainer').classList.add('hidden');
    document.getElementById('orgChoiceContainer').classList.add('hidden');
    document.getElementById('loginContainer').classList.remove('hidden');
    pendingCredentials = null;
    history.pushState({}, '', window.location.pathname);
}

function showConfirmation() {
    document.getElementById('loginContainer').classList.add('hidden');
    document.getElementById('requestContainer').classList.add('hidden');
    document.getElementById('registerContainer').classList.add('hidden');
    document.getElementById('confirmationContainer').classList.remove('hidden');
}

// Afficher le bon formulaire selon l'URL
if (window.location.search.includes('register=1')) {
    showRegisterForm();
} else if (window.location.search.includes('request=1')) {
    showRequestForm();
}

function showError(msg) {
    const toast = document.getElementById('errorToast');
    toast.textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}

function showSuccess(msg) {
    const toast = document.getElementById('successToast');
    toast.textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}

// Variables pour le choix d'organisation
let pendingCredentials = null;

function showOrgChoice(organizations, email) {
    document.getElementById('loginContainer').classList.add('hidden');
    document.getElementById('registerContainer').classList.add('hidden');
    document.getElementById('requestContainer').classList.add('hidden');
    document.getElementById('confirmationContainer').classList.add('hidden');
    document.getElementById('orgChoiceContainer').classList.remove('hidden');

    const orgList = document.getElementById('orgList');
    orgList.innerHTML = '';
    organizations.forEach(org => {
        const roleLabel = org.role === 'super_admin' ? 'Super Admin' :
                          org.role === 'org_admin' ? 'Admin' : 'Membre';
        const btn = document.createElement('button');
        btn.className = 'w-full text-left p-3 border rounded hover:bg-blue-50 hover:border-blue-300 transition flex items-center justify-between';
        btn.innerHTML = `
            <div>
                <div class="font-medium text-gray-800"><i class="fas fa-building mr-2 text-blue-500"></i>${org.name}</div>
                <div class="text-xs text-gray-500 ml-6">${roleLabel}</div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        `;
        btn.onclick = () => loginWithOrg(org.id);
        orgList.appendChild(btn);
    });
}

async function loginWithOrg(orgId) {
    if (!pendingCredentials) return;
    try {
        const response = await fetch('?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: pendingCredentials.email,
                password: pendingCredentials.password,
                org_id: orgId
            })
        });
        const data = await response.json();
        if (data.ok) {
            pendingCredentials = null;
            window.location.reload();
        } else {
            showError(data.msg || 'Erreur de connexion');
        }
    } catch (err) {
        showError('Erreur de connexion au serveur');
    }
}

// Connexion
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const email = formData.get('email');
    const password = formData.get('password');

    try {
        const response = await fetch('?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await response.json();

        if (data.ok) {
            if (data.data && data.data.choose_org) {
                // Stocker les credentials et afficher le choix
                pendingCredentials = { email, password };
                showOrgChoice(data.data.organizations, email);
            } else {
                window.location.reload();
            }
        } else {
            showError(data.msg || 'Erreur de connexion');
        }
    } catch (err) {
        showError('Erreur de connexion au serveur');
    }
});

// Inscription (création d'organisation)
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
        const response = await fetch('?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                org_name: formData.get('org_name'),
                fname: formData.get('fname'),
                lname: formData.get('lname'),
                email: formData.get('email'),
                password: formData.get('password')
            })
        });
        const data = await response.json();

        if (data.ok) {
            showConfirmation();
        } else {
            showError(data.msg || "Erreur lors de la création");
        }
    } catch (err) {
        showError('Erreur de connexion au serveur');
    }
});

// Demande d'accès
document.getElementById('requestForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
        const response = await fetch('?action=request_access', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                org_name: formData.get('org_name'),
                fname: formData.get('fname'),
                lname: formData.get('lname'),
                email: formData.get('email'),
                message: formData.get('message')
            })
        });
        const data = await response.json();

        if (data.ok) {
            showConfirmation();
        } else {
            showError(data.msg || "Erreur lors de l'envoi de la demande");
        }
    } catch (err) {
        showError('Erreur de connexion au serveur');
    }
});
</script>

</body>
</html>
