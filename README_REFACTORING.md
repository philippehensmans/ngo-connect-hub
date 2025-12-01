# ONG Manager v10.0 - Architecture Refactorisée

## 🎯 Vue d'ensemble

Cette version représente une refonte complète de l'application ONG Manager avec une architecture MVC moderne et maintenable.

## 📁 Structure du Projet

```
ngo-connect-hub/
├── config/
│   └── config.php                 # Configuration de l'application
├── src/
│   ├── Controllers/               # Contrôleurs de l'application
│   │   ├── Controller.php         # Contrôleur de base
│   │   ├── AuthController.php     # Authentification
│   │   ├── DataController.php     # Chargement des données
│   │   ├── ProjectController.php  # Gestion des projets
│   │   ├── TaskController.php     # Gestion des tâches
│   │   ├── MemberController.php   # Gestion des membres
│   │   ├── GroupController.php    # Gestion des groupes
│   │   ├── MilestoneController.php # Gestion des jalons
│   │   └── DeleteController.php   # Suppression d'éléments
│   ├── Models/                    # Modèles de données
│   │   ├── Model.php              # Modèle de base
│   │   ├── Team.php               # Modèle Équipe
│   │   ├── Member.php             # Modèle Membre
│   │   ├── Project.php            # Modèle Projet
│   │   ├── Task.php               # Modèle Tâche
│   │   ├── Group.php              # Modèle Groupe
│   │   └── Milestone.php          # Modèle Jalon
│   ├── Services/                  # Services applicatifs
│   │   ├── Database.php           # Service de base de données
│   │   ├── Auth.php               # Service d'authentification
│   │   └── Translation.php        # Service de traduction
│   └── Router.php                 # Routeur de l'application
├── views/                         # Vues/Templates
│   ├── login.php                  # Page de connexion
│   ├── app.php                    # Application principale
│   └── modals.php                 # Modaux (dialogs)
├── public/                        # Fichiers publics
│   ├── js/
│   │   └── app.js                 # JavaScript frontend
│   └── css/                       # (À ajouter si nécessaire)
├── data/                          # Données (créé automatiquement)
│   └── ong_manager.db             # Base de données SQLite
└── index.php                      # Point d'entrée principal
```

## 🚀 Améliorations Principales

### 1. **Architecture MVC**
- Séparation claire des responsabilités
- Modèles pour la logique de données
- Contrôleurs pour la logique métier
- Vues pour le rendu HTML

### 2. **Sécurité Renforcée**
- Utilisation systématique de prepared statements
- Validation des données d'entrée
- Échappement des sorties HTML
- Protection contre les injections SQL

### 3. **Maintenabilité**
- Code organisé et modulaire
- Classes réutilisables
- Documentation claire
- Nommage cohérent

### 4. **Fonctionnalités**
- Autoloader pour les classes
- Routeur centralisé pour les API
- Service de traduction multilingue
- Gestion des sessions sécurisée

## 💻 Installation

1. **Prérequis**
   - PHP 7.4 ou supérieur
   - Extension PDO SQLite
   - Serveur web (Apache, Nginx, ou serveur PHP intégré)

2. **Configuration**
   ```bash
   # Cloner ou placer les fichiers dans votre répertoire web
   cd /path/to/ngo-connect-hub

   # Vérifier les permissions (le dossier data doit être accessible en écriture)
   chmod 755 .
   mkdir -p data
   chmod 777 data
   ```

3. **Lancement**
   ```bash
   # Avec le serveur PHP intégré
   php -S localhost:8000

   # Ouvrir dans le navigateur
   # http://localhost:8000
   ```

## 🔑 Connexion par Défaut

- **Nom d'équipe**: ONG Démo
- **Mot de passe**: demo

## 📚 Guide du Développeur

### Ajouter une Nouvelle Fonctionnalité

1. **Créer un Contrôleur**
   ```php
   <?php
   namespace App\Controllers;

   class MonController extends Controller {
       public function maMethode(array $data): void {
           // Votre logique ici
           $this->success($data, 'Success!');
       }
   }
   ```

2. **Enregistrer la Route**
   ```php
   // Dans src/Router.php
   $this->routes['mon_action'] = [MonController::class, 'maMethode'];
   ```

3. **Utiliser depuis le Frontend**
   ```javascript
   await ONG.post('mon_action', { param: 'value' });
   ```

### Ajouter un Nouveau Modèle

```php
<?php
namespace App\Models;

class MonModele extends Model {
    protected string $table = 'ma_table';
    protected array $fillable = ['champ1', 'champ2'];
}
```

### Modifier la Configuration

Éditez `config/config.php` pour ajuster :
- Paramètres de la base de données
- Configuration de l'application
- Langues supportées
- Options de sécurité

## 🌐 Support Multilingue

L'application supporte actuellement :
- Français (FR)
- Anglais (EN)
- Espagnol (ES)
- Slovène (SL)

Pour ajouter une langue, éditez `src/Services/Translation.php`.

## 🛠️ API REST

### Endpoints Disponibles

#### Authentification
- `POST /?action=login` - Connexion
- `POST /?action=logout` - Déconnexion
- `POST /?action=update_settings` - Mise à jour des paramètres

#### Données
- `POST /?action=load_all` - Charger toutes les données

#### Projets
- `POST /?action=save_project` - Créer/Modifier un projet

#### Tâches
- `POST /?action=save_task` - Créer/Modifier une tâche

#### Membres
- `POST /?action=save_member` - Ajouter un membre

#### Groupes
- `POST /?action=save_group` - Créer/Modifier un groupe

#### Jalons
- `POST /?action=save_milestone` - Créer/Modifier un jalon

#### Suppression
- `POST /?action=delete_item` - Supprimer un élément

#### Backup
- `GET /?action=download_db` - Télécharger la base de données

## 🔄 Migration depuis l'Ancienne Version

Si vous aviez l'ancienne version (v9.6), vous pouvez :

1. **Migrer la Base de Données**
   ```bash
   # Copier l'ancienne base de données
   cp ong_v96_rescue.db data/ong_manager.db
   ```

2. **Ou Réinitialiser**
   - Accédez à la page de login
   - Cliquez sur le lien "Réinitialiser l'application"

## 🐛 Débogage

En mode développement (dans `config/config.php`) :
```php
'debug' => true,  // Active l'affichage des erreurs
```

## 📊 Base de Données

### Structure

- **teams** - Équipes/Organisations
- **members** - Membres de l'équipe
- **projects** - Projets
- **groups** - Groupes de tâches
- **milestones** - Jalons/Étapes importantes
- **tasks** - Tâches

### Relations

- Cascade delete sur la suppression de projets
- Clés étrangères avec contraintes
- Support des valeurs NULL pour les champs optionnels

## 🎨 Personnalisation

### Modifier les Styles

Les styles utilisent Tailwind CSS via CDN. Pour personnaliser :
- Éditez la balise `<style>` dans `views/app.php`
- Ou ajoutez un fichier CSS dans `public/css/`

### Modifier le JavaScript

Le fichier `public/js/app.js` contient toute la logique frontend.
Il est organisé en modules :
- Initialisation
- Gestion des événements
- Rendu des vues
- Communication API

## 🔐 Sécurité

### Bonnes Pratiques Implémentées

- ✅ Prepared statements pour toutes les requêtes SQL
- ✅ Hachage des mots de passe avec bcrypt
- ✅ Validation des données d'entrée
- ✅ Échappement des sorties HTML
- ✅ Protection CSRF (à améliorer)
- ✅ Sessions sécurisées

### Recommandations de Production

1. Activer HTTPS
2. Configurer `debug => false`
3. Restreindre les permissions des fichiers
4. Sauvegardes régulières de la base de données
5. Implémenter un système de logs

## 📝 Licence

Ce projet est fourni "tel quel" sans garantie.

## 👥 Contributions

Pour contribuer :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## 📞 Support

Pour toute question ou problème, veuillez ouvrir une issue sur le dépôt du projet.

---

**Version**: 10.0
**Date**: 2025
**Auteur**: Refactoring MVC
