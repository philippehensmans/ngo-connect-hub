# Guide d'Installation - ONG Manager v10.0

**Application de gestion de projets pour ONG**
Architecture : PHP + SQLite
Version : 10.0.1

---

## 1. Prérequis

### Configuration serveur minimale

| Composant | Version minimale | Notes |
|-----------|------------------|-------|
| PHP | 7.4+ | PHP 8.x recommandé |
| Serveur Web | Apache 2.4+ | Avec mod_rewrite activé |
| SQLite | 3.x | Intégré à PHP |

### Extensions PHP requises

- `pdo_sqlite` - Pour la base de données
- `mbstring` - Pour le support Unicode
- `json` - Pour les API

> **Vérification des extensions :**
> Créez un fichier `phpinfo.php` avec `<?php phpinfo(); ?>` pour vérifier les extensions installées.

---

## 2. Téléchargement

Téléchargez le fichier ZIP depuis :

👉 **https://github.com/philippehensmans/ngo-connect-hub/raw/main/ong-manager-v10.zip**

---

## 3. Installation

### Étape 1 : Extraction des fichiers

Décompressez l'archive dans le dossier de votre serveur web :

```bash
# Linux/Mac
unzip ong-manager-v10.zip -d /var/www/html/

# Ou via FTP/SFTP
# Uploadez et décompressez via le gestionnaire de fichiers
```

### Étape 2 : Renommer le dossier (optionnel)

```bash
mv /var/www/html/ngo-connect-hub /var/www/html/ong-manager
```

### Étape 3 : Permissions

> ⚠️ **Important :** Le dossier `data/` doit être accessible en écriture pour stocker la base de données SQLite.

```bash
# Créer le dossier data
mkdir -p /var/www/html/ong-manager/data

# Donner les permissions d'écriture
chmod 755 /var/www/html/ong-manager
chmod 777 /var/www/html/ong-manager/data
```

### Étape 4 : Configuration Apache

Assurez-vous que `mod_rewrite` est activé :

```bash
# Activer mod_rewrite
sudo a2enmod rewrite

# Redémarrer Apache
sudo systemctl restart apache2
```

Vérifiez que votre configuration Apache permet les fichiers `.htaccess` :

```apache
<Directory /var/www/html>
    AllowOverride All
</Directory>
```

---

## 4. Premier démarrage

### Accès à l'application

Ouvrez votre navigateur et accédez à :

```
http://votre-serveur/ong-manager/
```

### Création de l'équipe

1. Entrez un **nom d'équipe** (ex: "Mon ONG")
2. Choisissez un **mot de passe**
3. Cliquez sur **Connexion**

> ✅ **Félicitations !** L'application est installée et prête à l'emploi.

---

## 5. Structure des fichiers

| Dossier/Fichier | Description |
|-----------------|-------------|
| `index.php` | Point d'entrée principal |
| `config/` | Configuration de l'application |
| `src/` | Code source (Controllers, Models, Services) |
| `views/` | Templates PHP (interface utilisateur) |
| `public/` | Fichiers statiques (JS, images) |
| `data/` | Base de données SQLite (créé automatiquement) |

---

## 6. Configuration avancée

### Modifier le chemin de la base de données

Éditez `config/config.php` :

```php
return [
    'database' => [
        'path' => __DIR__ . '/../data/ong_manager.db'
    ],
    // ...
];
```

### Activer le mode debug

Dans `config/config.php`, mettez :

```php
'app' => [
    'debug' => true,
    // ...
]
```

---

## 7. Sauvegarde

### Sauvegarde manuelle

Copiez simplement le fichier de base de données :

```bash
cp /var/www/html/ong-manager/data/ong_manager.db /chemin/backup/
```

### Sauvegarde automatique

L'application crée automatiquement des backups quotidiens dans `data/backups/`.

Un bouton **"Backup"** est également disponible dans l'interface.

---

## 8. Dépannage

| Problème | Solution |
|----------|----------|
| Page blanche | Vérifiez les logs PHP : `tail -f /var/log/apache2/error.log` |
| Erreur 500 | Vérifiez les permissions et mod_rewrite |
| Base de données non créée | Vérifiez que `data/` est en chmod 777 |
| Erreur SQLite | Vérifiez que l'extension `pdo_sqlite` est installée |

---

## 9. Support

Pour toute question ou problème :

- 📖 Documentation : `MANUEL.md` inclus dans l'application
- ❓ Aide en ligne : Cliquez sur l'icône **?** dans l'application
- 🐙 GitHub : https://github.com/philippehensmans/ngo-connect-hub

---

*ONG Manager v10.0 - Guide d'installation*
*© 2024 Philippe Hensmans*
