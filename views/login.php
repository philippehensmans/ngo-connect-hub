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

<div class="flex-1 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center"><?= $t->translate('app') ?></h1>
        <form id="loginForm" class="space-y-4">
            <input type="text" name="name" placeholder="ONG Démo" class="w-full border p-2 rounded" required>
            <input type="password" name="password" placeholder="<?= $t->translate('pass') ?>" class="w-full border p-2 rounded" required>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700">
                <?= $t->translate('login') ?>
            </button>
        </form>
        <div class="flex gap-2 justify-center mt-6">
            <a href="?lang=fr">🇫🇷</a>
            <a href="?lang=en">🇬🇧</a>
            <a href="?lang=es">🇪🇸</a>
            <a href="?lang=sl">🇸🇮</a>
        </div>
    </div>
</div>

<script src="public/js/app.js"></script>
</body>
</html>
