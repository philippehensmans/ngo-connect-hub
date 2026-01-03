# 🖥️ Installer ONG Manager sur votre PC Windows

## Guide pour débutants - Aucune connaissance technique requise !

Ce guide vous accompagne pas à pas pour installer l'application ONG Manager sur votre ordinateur Windows.

**Temps estimé : 10-15 minutes**

---

## 📋 Ce dont vous avez besoin

- Un PC sous Windows 10 ou 11
- Une connexion Internet (pour le téléchargement)
- Environ 500 Mo d'espace disque

---

## Étape 1 : Télécharger XAMPP

XAMPP est un logiciel gratuit qui permet de faire fonctionner des applications web sur votre PC.

### 1.1 Aller sur le site de XAMPP

👉 Ouvrez votre navigateur et allez sur : **https://www.apachefriends.org/download.html**

### 1.2 Télécharger la version Windows

- Cliquez sur le bouton **"Download"** à côté de "XAMPP for Windows"
- Choisissez la version **8.2.x** (ou la plus récente)
- Le téléchargement commence automatiquement
- Attendez la fin du téléchargement (environ 150 Mo)

---

## Étape 2 : Installer XAMPP

### 2.1 Lancer l'installation

- Allez dans votre dossier **Téléchargements**
- Double-cliquez sur le fichier **xampp-windows-x64-8.x.x-installer.exe**

### 2.2 Suivre l'assistant d'installation

1. **Si Windows demande une autorisation** : Cliquez sur **"Oui"**

2. **Écran "Setup"** : Cliquez sur **"Next"**

3. **Écran "Select Components"** :
   - Laissez tout coché par défaut
   - Cliquez sur **"Next"**

4. **Écran "Installation folder"** :
   - Laissez le chemin par défaut : `C:\xampp`
   - Cliquez sur **"Next"**

5. **Écran "Bitnami for XAMPP"** :
   - Décochez la case "Learn more about Bitnami..."
   - Cliquez sur **"Next"**

6. **Écran "Ready to Install"** : Cliquez sur **"Next"**

7. **Attendez** que l'installation se termine (1-2 minutes)

8. **Écran "Completing"** :
   - Laissez coché "Do you want to start the Control Panel now?"
   - Cliquez sur **"Finish"**

---

## Étape 3 : Démarrer le serveur web

### 3.1 Le panneau de contrôle XAMPP s'ouvre

Vous voyez une fenêtre avec plusieurs lignes : Apache, MySQL, FileZilla, etc.

### 3.2 Démarrer Apache

- Sur la ligne **"Apache"**, cliquez sur le bouton **"Start"**
- Le texte "Apache" devient **vert** = ✅ C'est bon !

> ⚠️ **Si ça ne fonctionne pas :**
> - Un autre programme utilise peut-être le port 80 (Skype, IIS...)
> - Fermez ces programmes et réessayez

---

## Étape 4 : Télécharger ONG Manager

### 4.1 Télécharger l'application

👉 Cliquez sur ce lien : **https://github.com/philippehensmans/ngo-connect-hub/raw/main/ong-manager-v10.zip**

Le fichier ZIP se télécharge automatiquement.

### 4.2 Extraire le fichier ZIP

1. Allez dans votre dossier **Téléchargements**
2. Faites un **clic droit** sur le fichier `ong-manager-v10.zip`
3. Cliquez sur **"Extraire tout..."**
4. Cliquez sur **"Extraire"**

Un nouveau dossier `ong-manager-v10` apparaît.

---

## Étape 5 : Copier l'application dans XAMPP

### 5.1 Ouvrir le dossier extrait

- Double-cliquez sur le dossier `ong-manager-v10`
- Vous voyez un dossier `ngo-connect-hub`

### 5.2 Copier le dossier

1. Faites un **clic droit** sur le dossier `ngo-connect-hub`
2. Cliquez sur **"Copier"**

### 5.3 Coller dans XAMPP

1. Ouvrez l'**Explorateur de fichiers** (icône dossier jaune dans la barre des tâches)
2. Dans la barre d'adresse en haut, tapez : `C:\xampp\htdocs`
3. Appuyez sur **Entrée**
4. Faites un **clic droit** dans la fenêtre
5. Cliquez sur **"Coller"**

Le dossier `ngo-connect-hub` est maintenant dans `C:\xampp\htdocs\`

---

## Étape 6 : Ouvrir l'application 🎉

### 6.1 Ouvrir votre navigateur

Ouvrez **Chrome**, **Firefox**, ou **Edge**.

### 6.2 Accéder à l'application

Dans la barre d'adresse, tapez :

```
http://localhost/ngo-connect-hub/
```

Appuyez sur **Entrée**.

### 6.3 Première connexion

1. **Nom de l'équipe** : Entrez un nom (ex: "Mon Association")
2. **Mot de passe** : Choisissez un mot de passe
3. Cliquez sur **"Connexion"**

---

## ✅ C'est terminé !

Félicitations ! ONG Manager fonctionne sur votre PC.

**Pour les prochaines fois :**

1. Lancez **XAMPP Control Panel** (icône orange dans le menu Démarrer)
2. Cliquez sur **"Start"** à côté de Apache
3. Ouvrez votre navigateur à l'adresse : `http://localhost/ngo-connect-hub/`

---

## 🆘 Problèmes fréquents

### "La page ne s'affiche pas"

- Vérifiez que Apache est bien démarré (vert dans XAMPP)
- Vérifiez l'adresse : `http://localhost/ngo-connect-hub/`

### "Apache ne démarre pas"

- Un autre programme utilise le port 80
- Solution : Dans XAMPP, cliquez sur **"Config"** puis **"Apache (httpd.conf)"**
- Cherchez `Listen 80` et remplacez par `Listen 8080`
- Sauvegardez et redémarrez Apache
- Utilisez alors : `http://localhost:8080/ngo-connect-hub/`

### "J'ai oublié mon mot de passe"

- Supprimez le fichier `C:\xampp\htdocs\ngo-connect-hub\data\ong_manager.db`
- Rechargez la page pour créer une nouvelle équipe

---

## 💡 Conseils

- **Sauvegardez vos données** : Copiez régulièrement le dossier `data` ailleurs
- **Mises à jour** : Retéléchargez le ZIP et remplacez les fichiers (sauf le dossier `data`)

---

## 📞 Besoin d'aide ?

- Consultez le manuel intégré : cliquez sur **?** dans l'application
- GitHub : https://github.com/philippehensmans/ngo-connect-hub

---

*Guide créé pour ONG Manager v10.0*
*Dernière mise à jour : Janvier 2025*
