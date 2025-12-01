# Scripts ONG Manager

Ce dossier contient des scripts utiles pour gérer l'application ONG Manager.

## 📋 add_demo_project.php

Script pour ajouter un projet de démonstration complet dans votre base de données.

### Projet créé : Campagne de sensibilisation sur la situation des Roms en Belgique

Ce projet d'exemple inclut :
- **1 projet principal** avec dates de début et fin
- **6 groupes** (phases du projet) :
  - 🔍 Phase de recherche
  - ⚖️ Analyse juridique
  - 📝 Création de contenu
  - 🎨 Matériel de campagne
  - 💻 Présence digitale
  - 🚀 Lancement et actions

- **6 jalons** marquant les étapes clés
- **~30 tâches détaillées** couvrant :
  - Études de terrain et interviews
  - Analyse de la législation
  - Consultations avec ONG
  - Rédaction de rapports
  - Création de matériel (affiches, vidéos, dépliants)
  - Site web et réseaux sociaux
  - Événements et actions de visibilité
  - Rencontres avec responsables politiques

### Utilisation

```bash
php scripts/add_demo_project.php
```

**Note :** Vous devez avoir au moins une équipe (team) dans votre base de données avant d'exécuter ce script.

### Après l'exécution

1. Connectez-vous à l'application
2. Le nouveau projet apparaîtra dans la liste des projets
3. Explorez les différentes vues (Liste, Kanban, Arbo, Jalons, etc.)
4. Toutes les tâches sont organisées par groupes et jalons
5. Vous pouvez modifier, supprimer ou ajouter des éléments selon vos besoins

### Exemple de structure visible

```
📍 Jalon: Recherche terminée (dans 1 mois)
  └─ 🔍 Phase de recherche
     • Étude de terrain - Visites communautaires
     • Interviews avec les familles Roms
     • Collecte de données statistiques
     • Analyse des besoins

📍 Jalon: Contenu et rapports prêts (dans 2 mois)
  └─ ⚖️ Analyse juridique
     • Examen de la législation belge
     • Consultation avec autres ONG
     • Rédaction des recommandations
  └─ 📝 Création de contenu
     • Rédaction du rapport principal
     • Synthèse executive
     • Fiches thématiques

📍 Jalon: Lancement officiel (dans 4 mois)
  └─ 🚀 Lancement et actions
     • Conférence de presse
     • Événement de lancement public
     • Actions de rue
     • Projection-débats
```

### Personnalisation

Vous pouvez facilement modifier le script pour :
- Changer les dates
- Ajouter/supprimer des tâches
- Modifier les descriptions
- Assigner des responsables (membres d'équipe)
- Adapter le projet à votre propre contexte

---

💡 **Astuce :** Ce projet sert d'exemple pour comprendre comment structurer un projet complexe avec l'application ONG Manager.
