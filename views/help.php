<!DOCTYPE html>
<html lang="<?= $lang ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t->translate('help') ?> - ONG Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Marked.js pour parser le Markdown -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .markdown-body {
            font-family: 'Segoe UI', sans-serif;
            line-height: 1.8;
            color: #333;
        }
        .markdown-body h1 {
            font-size: 2rem;
            font-weight: 700;
            border-bottom: 2px solid #2563EB;
            padding-bottom: 0.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1e40af;
        }
        .markdown-body h2 {
            font-size: 1.5rem;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #2563EB;
        }
        .markdown-body h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #374151;
        }
        .markdown-body p {
            margin-bottom: 1rem;
        }
        .markdown-body ul, .markdown-body ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }
        .markdown-body li {
            margin-bottom: 0.5rem;
        }
        .markdown-body ul li {
            list-style-type: disc;
        }
        .markdown-body ol li {
            list-style-type: decimal;
        }
        .markdown-body code {
            background-color: #f3f4f6;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: monospace;
            font-size: 0.9em;
        }
        .markdown-body pre {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-bottom: 1rem;
        }
        .markdown-body pre code {
            background-color: transparent;
            padding: 0;
            color: inherit;
        }
        .markdown-body blockquote {
            border-left: 4px solid #2563EB;
            padding-left: 1rem;
            margin: 1rem 0;
            background-color: #eff6ff;
            padding: 1rem;
            border-radius: 0 0.5rem 0.5rem 0;
            color: #1e40af;
        }
        .markdown-body hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 2rem 0;
        }
        .markdown-body table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .markdown-body th, .markdown-body td {
            border: 1px solid #e5e7eb;
            padding: 0.75rem;
            text-align: left;
        }
        .markdown-body th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        .markdown-body a {
            color: #2563EB;
            text-decoration: underline;
        }
        .markdown-body a:hover {
            color: #1e40af;
        }
        .markdown-body strong {
            font-weight: 600;
        }
        /* Table of contents styling */
        .markdown-body > ul:first-of-type {
            background-color: #f9fafb;
            padding: 1.5rem 2rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="/" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-question-circle text-blue-600"></i>
                    <?= $t->translate('help') ?> - ONG Manager
                </h1>
            </div>
            <div class="flex items-center gap-4">
                <select id="langSelect" onchange="changeLanguage(this.value)" class="border rounded px-2 py-1 text-sm">
                    <option value="fr" <?= ($lang ?? 'fr') === 'fr' ? 'selected' : '' ?>>Fran&ccedil;ais</option>
                    <option value="en" <?= ($lang ?? 'fr') === 'en' ? 'selected' : '' ?>>English</option>
                    <option value="es" <?= ($lang ?? 'fr') === 'es' ? 'selected' : '' ?>>Espa&ntilde;ol</option>
                    <option value="sl" <?= ($lang ?? 'fr') === 'sl' ? 'selected' : '' ?>>Sloven&scaron;&ccaron;ina</option>
                </select>
                <button onclick="window.print()" class="text-gray-600 hover:text-gray-800" title="Imprimer">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div id="markdown-content" class="markdown-body">
                <p class="text-gray-500"><i class="fas fa-spinner fa-spin"></i> Chargement...</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-6 text-gray-500 text-sm">
        ONG Manager v10.0 - Manuel d'utilisation
    </footer>

    <script>
        // Determine which manual to load based on language
        const lang = '<?= $lang ?? 'fr' ?>';
        const manualFiles = {
            'fr': 'MANUEL.md',
            'en': 'MANUEL.md',  // Fallback to French if no English version
            'es': 'MANUEL.md',  // Fallback to French if no Spanish version
            'sl': 'MANUAL_SL.md'
        };

        const manualFile = manualFiles[lang] || 'MANUEL.md';

        // Load and render markdown
        fetch(manualFile)
            .then(response => {
                if (!response.ok) throw new Error('File not found');
                return response.text();
            })
            .then(markdown => {
                document.getElementById('markdown-content').innerHTML = marked.parse(markdown);
            })
            .catch(error => {
                document.getElementById('markdown-content').innerHTML =
                    '<p class="text-red-500"><i class="fas fa-exclamation-triangle"></i> Erreur de chargement du manuel.</p>';
                console.error('Error loading manual:', error);
            });

        function changeLanguage(newLang) {
            window.location.href = '?action=help&lang=' + newLang;
        }
    </script>
</body>
</html>
