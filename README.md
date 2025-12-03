# ONG Manager - Application de Gestion de Projets

**Version**: 10.0
**Architecture**: MVC PHP + SQLite
**Auteur**: Philippe Hensmans

## 📋 Description

ONG Manager est une application web complète pour la gestion de projets et tâches pour les organisations non gouvernementales (ONG). Elle permet de gérer des projets, tâches, membres, groupes, jalons, avec des vues multiples (Dashboard, Liste, Kanban, Gantt, Calendrier) et des fonctionnalités avancées d'export/import et de webhooks.

## ✨ Fonctionnalités

### Gestion de Projets
- Création et modification de projets
- Suivi de l'avancement
- Export/Import de projets (JSON, Excel)
- Templates de projets réutilisables

### Gestion de Tâches
- Création et modification de tâches
- Statuts: À faire, En cours, Terminé
- Priorités et dates de début/fin
- Système de commentaires avec support Markdown
- Assignation de membres

### Vues Multiples
- **Dashboard**: Vue d'ensemble avec statistiques
- **Liste**: Liste complète des tâches avec tri et filtre
- **Kanban**: Organisation visuelle par statut
- **Groupes**: Regroupement par groupes de tâches
- **Gantt**: Diagramme de Gantt interactif
- **Jalons**: Vue par jalons/étapes importantes
- **Calendrier**: Vue calendrier avec FullCalendar

### Fonctionnalités Avancées
- Système de commentaires avec Markdown
- Export/Import de projets
- Webhooks pour intégrations externes
- Recherche avancée
- Backups automatiques
- Support multilingue (FR, EN, ES, SL)
- Thèmes personnalisables

## 🚀 Installation

### Prérequis

- PHP 7.4 ou supérieur
- Extension PDO SQLite
- Serveur web (Apache, Nginx, ou serveur PHP intégré)

### Installation Rapide

1. **Cloner ou télécharger le projet**
   ```bash
   cd /path/to/your/webserver
   git clone https://github.com/philippehensmans/ngo-connect-hub.git
   cd ngo-connect-hub
   ```

2. **Configurer les permissions**
   ```bash
   chmod 755 .
   mkdir -p data
   chmod 777 data
   ```

3. **Lancer l'application**

   Avec le serveur PHP intégré:
   ```bash
   php -S localhost:8000
   ```

   Ou configurez votre serveur web (Apache/Nginx) pour pointer vers le répertoire du projet.

4. **Accéder à l'application**
   ```
   http://localhost:8000
   ```

### Connexion par Défaut

- **Nom d'équipe**: ONG Démo
- **Mot de passe**: demo

## 📁 Structure du Projet

```
ngo-connect-hub/
├── config/
│   └── config.php                 # Configuration de l'application
├── src/
│   ├── Controllers/               # Contrôleurs MVC
│   │   ├── Controller.php         # Contrôleur de base
│   │   ├── AuthController.php     # Authentification
│   │   ├── DataController.php     # Chargement des données
│   │   ├── ProjectController.php  # Gestion des projets
│   │   ├── TaskController.php     # Gestion des tâches
│   │   ├── MemberController.php   # Gestion des membres
│   │   ├── GroupController.php    # Gestion des groupes
│   │   ├── MilestoneController.php # Gestion des jalons
│   │   ├── CommentController.php  # Système de commentaires
│   │   ├── ExportController.php   # Export/Import
│   │   ├── WebhookController.php  # Webhooks
│   │   ├── BackupController.php   # Backups
│   │   ├── TemplateController.php # Templates
│   │   └── DeleteController.php   # Suppression d'éléments
│   ├── Models/                    # Modèles de données
│   │   ├── Model.php              # Modèle de base
│   │   ├── Team.php               # Équipe
│   │   ├── Member.php             # Membre
│   │   ├── Project.php            # Projet
│   │   ├── Task.php               # Tâche
│   │   ├── Group.php              # Groupe
│   │   ├── Milestone.php          # Jalon
│   │   ├── Comment.php            # Commentaire
│   │   └── Webhook.php            # Webhook
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
│   └── favicon.ico
├── data/                          # Données (créé automatiquement)
│   ├── ong_manager.db             # Base de données SQLite
│   └── backups/                   # Backups automatiques
├── index.php                      # Point d'entrée principal
├── composer.json                  # Dépendances PHP (optionnel)
├── README_REFACTORING.md          # Documentation technique
└── CHANGES.md                     # Historique des changements
```

## 🔧 Configuration

Éditez `config/config.php` pour personnaliser:

- Paramètres de la base de données
- Configuration de l'application (debug, timezone)
- Langues supportées
- Options de sécurité
- Thèmes disponibles

## 📊 Base de Données

### Tables Principales

- **teams**: Équipes/Organisations
- **members**: Membres de l'équipe
- **projects**: Projets
- **groups**: Groupes de tâches
- **milestones**: Jalons/Étapes importantes
- **tasks**: Tâches
- **comments**: Commentaires sur les tâches
- **webhooks**: Webhooks pour intégrations

### Backups Automatiques

L'application crée automatiquement des backups quotidiens de la base de données dans `data/backups/`.

## 🌐 API REST

### Authentification
- `POST /?action=login` - Connexion
- `POST /?action=logout` - Déconnexion
- `POST /?action=update_settings` - Mise à jour des paramètres

### Données
- `POST /?action=load_all` - Charger toutes les données

### Projets
- `POST /?action=save_project` - Créer/Modifier un projet
- `POST /?action=export_project` - Exporter un projet
- `POST /?action=import_project` - Importer un projet

### Tâches
- `POST /?action=save_task` - Créer/Modifier une tâche

### Commentaires
- `POST /?action=save_comment` - Ajouter un commentaire
- `POST /?action=edit_comment` - Modifier un commentaire
- `POST /?action=delete_comment` - Supprimer un commentaire

### Webhooks
- `POST /?action=save_webhook` - Créer/Modifier un webhook
- `POST /?action=test_webhook` - Tester un webhook

### Suppression
- `POST /?action=delete_item` - Supprimer un élément

### Backup
- `GET /?action=download_db` - Télécharger la base de données

## 🎨 Personnalisation

### Thèmes

5 thèmes de couleurs disponibles:
- Bleu (par défaut)
- Vert
- Violet
- Orange
- Rouge

Changez le thème depuis les paramètres de l'application.

### Langues

4 langues supportées:
- Français (FR)
- Anglais (EN)
- Espagnol (ES)
- Slovène (SL)

## 🔐 Sécurité

### Bonnes Pratiques Implémentées

- ✅ Prepared statements pour toutes les requêtes SQL
- ✅ Hachage des mots de passe avec bcrypt
- ✅ Validation des données d'entrée
- ✅ Échappement des sorties HTML
- ✅ Sessions sécurisées
- ✅ Protection contre les injections SQL

### Recommandations de Production

1. Activer HTTPS
2. Configurer `debug => false` dans `config/config.php`
3. Restreindre les permissions des fichiers
4. Sauvegardes régulières de la base de données
5. Implémenter un système de logs
6. Utiliser des mots de passe forts

## 🛠️ Développement

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

## 📝 Changelog

Voir [CHANGES.md](CHANGES.md) pour l'historique détaillé des modifications.

## 🐛 Débogage

En mode développement (dans `config/config.php`):
```php
'debug' => true,  // Active l'affichage des erreurs
```

## 📞 Support

Pour toute question ou problème:
- Ouvrir une issue sur GitHub: https://github.com/philippehensmans/ngo-connect-hub/issues

## 📄 Licence

Ce projet est fourni "tel quel" sans garantie.

## 🙏 Remerciements

Merci aux contributeurs et à la communauté open source pour les bibliothèques utilisées:
- Tailwind CSS
- Chart.js
- FullCalendar
- Frappe Gantt
- Font Awesome

---

**Version**: 10.0
**Date**: Décembre 2025
**Architecture**: MVC PHP moderne
