# Manuel d'Utilisation - ONG Manager

## Table des matières

1. [Introduction](#introduction)
2. [Premier démarrage](#premier-démarrage)
3. [Interface générale](#interface-générale)
4. [Gestion des projets](#gestion-des-projets)
5. [Gestion des groupes](#gestion-des-groupes)
6. [Gestion des jalons](#gestion-des-jalons)
7. [Gestion des tâches](#gestion-des-tâches)
8. [Les différentes vues](#les-différentes-vues)
9. [Gestion de l'équipe](#gestion-de-léquipe)
10. [Paramètres et langue](#paramètres-et-langue)
11. [Export de données](#export-de-données)

---

## Introduction

**ONG Manager** est une application de gestion de projets conçue pour les organisations à but non lucratif. Elle permet de planifier, organiser et suivre vos projets, tâches et équipes de manière simple et efficace.

### Fonctionnalités principales

- ✅ Gestion multi-projets
- ✅ Organisation en groupes avec affectation de membres
- ✅ Planification par jalons (milestones)
- ✅ Multiples vues : Dashboard, Liste, Kanban, Gantt, Calendrier
- ✅ Gestion d'équipe avec rôles Admin/Utilisateur
- ✅ Interface multilingue (Français, Anglais, Espagnol, Slovène)
- ✅ Export Excel des données
- ✅ Graphiques et statistiques

---

## Premier démarrage

### Connexion

1. Ouvrez l'application dans votre navigateur
2. Entrez le **nom de votre équipe**
3. Entrez le **mot de passe** de l'équipe
4. Cliquez sur **Connexion**

> **Note** : La première fois, vous devrez créer une équipe en choisissant un nom et un mot de passe.

### Langue

Vous pouvez changer la langue de l'interface à tout moment via le sélecteur en haut à droite :
- 🇫🇷 Français
- 🇬🇧 English
- 🇪🇸 Español
- 🇸🇮 Slovenščina

---

## Interface générale

### Barre supérieure

```
[👥 Équipe] [⚙️ Paramètres] [🌍 Langue]
```

- **👥 Équipe** : Gérer les membres de votre organisation
- **⚙️ Paramètres** : Modifier le nom de l'équipe et le mot de passe (Admin uniquement)
- **🌍 Langue** : Changer la langue de l'interface

### Barre latérale gauche

Liste de tous vos **projets**. Cliquez sur un projet pour le sélectionner et voir ses tâches.

**Actions disponibles :**
- ➕ **Nouveau Projet** : Créer un nouveau projet
- ✏️ **Éditer** : Modifier un projet existant
- 🗑️ **Supprimer** : Supprimer un projet

### Onglets de navigation

Une fois un projet sélectionné, vous avez accès à plusieurs vues :

- **Tableau de Bord** : Vue d'ensemble avec statistiques et graphiques
- **Vue Globale** : Vue d'ensemble de toutes les tâches du projet
- **Liste** : Liste détaillée des tâches
- **Kanban** : Tableau visuel (À faire / En cours / Terminé)
- **Groupes** : Organisation par groupes de travail
- **Gantt** : Diagramme de Gantt pour la planification temporelle
- **Calendrier** : Vue calendrier des tâches
- **Jalons** : Gestion des jalons (milestones) du projet

---

## Gestion des projets

### Créer un projet

1. Cliquez sur **+ Nouveau Projet** dans la barre latérale
2. Remplissez les informations :
   - **Nom** : Nom du projet (requis)
   - **Description** : Description détaillée
   - **Responsable** : Personne en charge du projet
   - **Date de début** : Date de démarrage
   - **Date de fin** : Date de fin prévue
3. Cliquez sur **Enregistrer**

### Éditer un projet

1. Cliquez sur l'icône ✏️ à côté du nom du projet
2. Modifiez les informations
3. Cliquez sur **Enregistrer**

### Supprimer un projet

1. Cliquez sur l'icône 🗑️ à côté du nom du projet
2. Confirmez la suppression

> **⚠️ Attention** : La suppression d'un projet supprime aussi toutes ses tâches, groupes et jalons !

---

## Gestion des groupes

Les groupes permettent d'organiser les tâches par thématique ou équipe de travail et d'assigner des membres spécifiques à chaque groupe.

### Créer un groupe

1. Sélectionnez un projet
2. Allez dans l'onglet **Groupes**
3. Cliquez sur **+ Nouveau Groupe**
4. Remplissez les informations :
   - **Titre** : Nom du groupe (requis)
   - **Description** : Objectifs et rôle de ce groupe
   - **Responsable** : Chef de groupe
   - **Membres du groupe** : Cochez les membres qui font partie de ce groupe
   - **Couleur** : Couleur d'identification du groupe
5. Cliquez sur **Enregistrer**

### Affecter des membres à un groupe

Lors de la création ou modification d'un groupe :
1. Dans la section **Membres du groupe**, cochez les membres à assigner
2. Vous pouvez sélectionner plusieurs membres
3. Les membres apparaîtront dans la carte du groupe avec l'icône 👥

### Visualiser les groupes

Dans l'onglet **Groupes**, chaque groupe est affiché dans une carte montrant :
- Le nom du groupe et sa description
- Le responsable
- **👥 Membres :** Liste des membres assignés
- La progression (tâches terminées / total)
- La liste des tâches du groupe

### Éditer un groupe

1. Cliquez sur ✏️ dans la carte du groupe
2. Modifiez les informations (y compris les membres)
3. Cliquez sur **Enregistrer**

---

## Gestion des jalons

Les jalons (milestones) sont des étapes clés dans votre projet.

### Créer un jalon

1. Allez dans l'onglet **Jalons**
2. Cliquez sur **+ Nouveau Jalon**
3. Remplissez :
   - **Titre** : Nom du jalon (requis)
   - **Date** : Date cible du jalon (requis)
   - **Statut** : En cours / Terminé
4. Cliquez sur **Enregistrer**

### Associer des tâches à un jalon

Lors de la création d'une tâche, sélectionnez le jalon dans le champ **Jalon**.

---

## Gestion des tâches

### Créer une tâche

1. Cliquez sur le bouton **+** en haut à droite
2. Remplissez les informations :
   - **Titre** : Nom de la tâche (requis)
   - **Description** : Détails de la tâche
   - **Groupe** : À quel groupe appartient la tâche
   - **Jalon** : À quel jalon est rattachée la tâche
   - **Responsable** : Personne en charge
   - **Statut** : À faire / En cours / Terminé
   - **Priorité** : Basse / Moyenne / Haute
   - **Date de début** / **Date de fin**
   - **Lien** : URL liée à la tâche
   - **Tags** : Mots-clés séparés par des virgules
   - **Dépendances** : Tâches qui doivent être terminées avant
3. Cliquez sur **Enregistrer**

### Modifier une tâche

1. Cliquez sur ✏️ à côté de la tâche
2. Modifiez les informations
3. Cliquez sur **Enregistrer**

### Supprimer une tâche

1. Cliquez sur 🗑️ à côté de la tâche
2. Confirmez la suppression

### Filtres

Utilisez les filtres en haut pour affiner l'affichage :
- **Recherche** : Rechercher dans les titres, descriptions, tags
- **Responsable** : Filtrer par responsable
- **Statut** : Filtrer par statut (À faire, En cours, Terminé)
- **Tags** : Filtrer par tag

Cliquez sur **Reset** pour réinitialiser tous les filtres.

---

## Les différentes vues

### 📊 Tableau de Bord

Vue d'ensemble du projet avec :
- **Statistiques** : Total tâches, en cours, terminées, progression
- **Graphiques** :
  - Tâches par statut (camembert)
  - Tâches par projet (barres)
  - Tâches par responsable (barres)
- **À venir cette semaine** : Tâches avec échéance dans les 7 prochains jours

### 📋 Vue Globale

Affichage en colonnes par statut :
- **À faire** (rouge)
- **En cours** (jaune)
- **Terminé** (vert)

Chaque carte de tâche affiche :
- Titre et description
- Responsable et dates
- Groupe et jalon
- Priorité et tags

### 📝 Liste

Liste détaillée de toutes les tâches du projet avec :
- Statut visuel (⭕ À faire, 🔄 En cours, ✅ Terminé)
- Toutes les informations de la tâche
- Actions rapides (éditer, supprimer)

### 🎯 Kanban

Tableau Kanban classique avec 3 colonnes :
- **À faire**
- **En cours**
- **Terminé**

Glissez-déposez les cartes pour changer le statut des tâches.

### 👥 Groupes

Organisation des tâches par groupes de travail.

Chaque carte de groupe affiche :
- Nom et description
- Responsable
- **👥 Membres assignés** (nouveauté)
- Barre de progression
- Liste des tâches du groupe

### 📅 Gantt

Diagramme de Gantt pour visualiser la planification temporelle :
- Barre temporelle configurable (Jour, Semaine, Mois)
- Barres colorées par groupe
- Dépendances entre tâches
- Jalons affichés

### 🗓️ Calendrier

Vue calendrier mensuel des tâches :
- Événements cliquables
- Couleurs par statut
- Navigation mois par mois

### 🎯 Jalons

Liste des jalons du projet avec :
- Date et statut
- Tâches associées à chaque jalon
- Progression par jalon

---

## Gestion de l'équipe

### Accéder à l'équipe

Cliquez sur **👥 Équipe** en haut à gauche.

### Ajouter un membre

1. Cliquez sur **+ Nouveau Membre**
2. Remplissez :
   - **Prénom**
   - **Nom**
   - **Email**
3. Cliquez sur **Enregistrer**

### Gérer les rôles (Admin uniquement)

Dans les paramètres, les administrateurs peuvent :
- Voir la liste de tous les membres
- Changer le rôle de chaque membre :
  - **👤 User** : Utilisateur standard
  - **👑 Admin** : Administrateur avec accès aux paramètres

**Basculer entre les rôles :**
Cliquez sur le toggle à côté du nom du membre pour changer son rôle.

### Modifier un membre

1. Cliquez sur ✏️ à côté du membre
2. Modifiez les informations
3. Cliquez sur **Enregistrer**

### Supprimer un membre

1. Cliquez sur 🗑️ à côté du membre
2. Confirmez la suppression

---

## Paramètres et langue

### Paramètres (Admin uniquement)

Seuls les administrateurs peuvent accéder aux paramètres via ⚙️.

**Options disponibles :**
- **Nom de l'organisation** : Modifier le nom de l'équipe
- **Mot de passe actuel** : Pour valider les changements
- **Nouveau mot de passe** : Changer le mot de passe de l'équipe
- **Gestion des membres** : Voir et modifier les rôles des membres

### Changer la langue

Cliquez sur le sélecteur de langue en haut à droite et choisissez :
- 🇫🇷 **Français**
- 🇬🇧 **English**
- 🇪🇸 **Español**
- 🇸🇮 **Slovenščina**

L'interface change immédiatement, y compris :
- Tous les labels et boutons
- Le tableau de bord
- Les graphiques
- Les messages

---

## Export de données

### Exporter en Excel

1. Sélectionnez un projet
2. Cliquez sur le bouton **Excel** (📊) en haut à droite
3. Un fichier `.xlsx` sera téléchargé avec :
   - Feuille **Tâches** : Toutes les tâches du projet
   - Feuille **Jalons** : Tous les jalons
   - Feuille **Groupes** : Tous les groupes

### Contenu de l'export

**Tâches :**
- Titre, Description
- Statut, Priorité
- Responsable
- Groupe, Jalon
- Dates de début et fin
- Tags, Lien
- Dépendances

**Jalons :**
- Nom
- Date
- Statut

**Groupes :**
- Nom
- Description
- Responsable
- Membres assignés

---

## Conseils et bonnes pratiques

### Organisation par groupes

✅ **Créez des groupes thématiques** :
- Par domaine d'activité (Communication, Logistique, Finance)
- Par équipe opérationnelle
- Par zone géographique

✅ **Assignez des membres à chaque groupe** pour une meilleure visibilité de qui travaille sur quoi

### Planification avec jalons

✅ **Utilisez les jalons pour les étapes importantes** :
- Lancement du projet
- Livrables majeurs
- Réunions de suivi
- Date de clôture

✅ **Associez les tâches aux jalons** pour une vision claire de ce qui doit être fait pour chaque étape

### Gestion des tâches

✅ **Soyez spécifique dans les titres** : "Rédiger rapport trimestriel" plutôt que "Rapport"

✅ **Utilisez les descriptions** pour ajouter des détails importants

✅ **Définissez des responsables** pour chaque tâche

✅ **Utilisez les tags** pour catégoriser : "urgent", "externe", "budget", etc.

✅ **Mettez à jour régulièrement les statuts** pour que le tableau de bord reflète la réalité

### Travail d'équipe

✅ **Utilisez les rôles Admin/User** pour contrôler qui peut modifier les paramètres

✅ **Organisez des points réguliers** en utilisant la vue Kanban ou le tableau de bord

✅ **Exportez régulièrement les données** pour garder une trace de l'avancement

---

## Support et questions

Pour toute question ou suggestion d'amélioration, contactez votre administrateur système.

**Version** : 10.0
**Date** : Décembre 2024

---

*Ce manuel est mis à jour régulièrement pour refléter les nouvelles fonctionnalités de l'application.*
