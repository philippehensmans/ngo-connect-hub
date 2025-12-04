# Déploiement FTP Automatique

Ce projet utilise GitHub Actions pour déployer automatiquement le code sur votre serveur web via FTP.

## Configuration des Secrets

Vous devez configurer les secrets suivants dans votre dépôt GitHub :

**Settings** → **Secrets and variables** → **Actions** → **New repository secret**

### Secrets Requis

| Secret | Description | Exemple |
|--------|-------------|---------|
| `FTP_SERVER` | Adresse du serveur FTP | `ftp.example.com` ou `192.168.1.1` |
| `FTP_USERNAME` | Nom d'utilisateur FTP | `username@example.com` |
| `FTP_PASSWORD` | Mot de passe FTP | `votre_mot_de_passe_securise` |
| `FTP_SERVER_DIR` | Répertoire cible sur le serveur | `/public_html/` ou `/www/` |

## Déclenchement du Déploiement

Le déploiement se déclenche automatiquement lors d'un **push** sur les branches suivantes :
- `main`
- `master`  
- `claude/ai-assistant-*` (toutes les branches d'assistant)

## Fichiers Exclus du Déploiement

Les fichiers suivants ne sont **PAS** déployés :
- `.git*` et fichiers Git
- `node_modules/`
- `tests/`
- `docs/`
- `.env.example`
- `.htaccess.bak`
- `CHANGES.md`
- `MANUEL.md`
- `.github/` (workflows)

## Vérifier le Déploiement

1. Allez dans **Actions** sur GitHub
2. Cliquez sur le dernier workflow **"Deploy to FTP"**
3. Vérifiez que toutes les étapes sont ✅ vertes

## Logs

Les logs de déploiement sont disponibles dans l'onglet **Actions** de votre dépôt GitHub.

## Test en Mode Dry-Run

Pour tester sans déployer réellement, modifiez dans `.github/workflows/deploy.yml` :
```yaml
dry-run: true  # Change false → true
```

Puis revenez à `false` pour déployer réellement.

## Dépannage

### Erreur : "Could not connect to FTP server"
- Vérifiez `FTP_SERVER` (sans `ftp://` ni `/` à la fin)
- Vérifiez que le port 21 est ouvert

### Erreur : "Authentication failed"
- Vérifiez `FTP_USERNAME` et `FTP_PASSWORD`
- Testez manuellement avec un client FTP (FileZilla)

### Erreur : "Permission denied"
- Vérifiez `FTP_SERVER_DIR`
- Assurez-vous que l'utilisateur a les droits d'écriture

## Déploiement Manuel

Si vous souhaitez déclencher un déploiement manuellement :

1. Allez dans **Actions**
2. Sélectionnez **Deploy to FTP**
3. Cliquez sur **Run workflow**
4. Choisissez la branche et cliquez **Run workflow**

## Sécurité

⚠️ **Important** :
- Ne commitez JAMAIS vos identifiants FTP dans le code
- Utilisez toujours les **Secrets** GitHub
- Les secrets sont masqués dans les logs GitHub Actions
