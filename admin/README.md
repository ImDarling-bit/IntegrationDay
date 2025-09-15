# 🔓 Admin Panel - Maria Curia WAMP

Panel d'administration pour organisateurs et "hackers" du jeu Maria Curia.

## 🚀 Installation

### 1. Base de Données Admin
```sql
-- Créer la base admin séparée
CREATE DATABASE maria_curia_admin;

-- Importer le schéma
-- Dans phpMyAdmin : importer admin_database.sql
```

### 2. Configuration
- **Base Admin** : `maria_curia_admin`
- **Base Jeu** : `maria_curia`
- **Accès** : `localhost/maria-curia-wamp/admin/`

## 👤 Comptes par Défaut

| Utilisateur | Mot de Passe | Rôle |
|-------------|--------------|------|
| admin | admin123 | admin |
| hacker1 | hack123 | hacker |
| hacker2 | hack456 | hacker |
| organizer | org123 | organizer |

## 🎮 Fonctionnalités

### ✅ **Dashboard**
- Statistiques temps réel
- Actions rapides (reset, export)
- Monitoring des équipes
- Logs d'activité

### ✅ **Gestion Équipes**
- Ajouter/modifier/supprimer équipes
- Reset progression individuelle
- Attribution de points
- Export données

### ✅ **Monitoring**
- Graphiques d'activité
- Stream live des découvertes
- Popularité des personnages
- Santé système

### ✅ **Logs Admin**
- Journal complet des actions
- Filtres par admin/action
- Export rapports
- Statistiques d'usage

## 🎨 Interface

- **Thème** : Hacker/Matrix noir et vert
- **Responsive** : Mobile et desktop
- **Auto-refresh** : Données temps réel
- **Animations** : Effets visuels immersifs

## ⚡ Actions Disponibles

### 🔧 **Manipulation Jeu**
- Reset progression globale
- Reset équipe spécifique
- Ajout points bonus
- Gestion QR codes

### 📊 **Monitoring**
- Surveillance temps réel
- Alertes système
- Export instantané
- Graphiques live

### 👥 **Gestion Équipes**
- CRUD complet équipes
- Modification mots de passe
- Statistiques équipes
- Export CSV

## 🔒 Sécurité

- **Sessions** : Gestion sécurisée
- **Logs** : Traçabilité complète
- **Accès** : Contrôle par rôles
- **IP** : Tracking des connexions

## 📁 Structure

```
admin/
├── index.php          # Redirection login
├── login.php           # Authentification
├── dashboard.php       # Tableau de bord
├── teams.php           # Gestion équipes
├── monitoring.php      # Monitoring live
├── logs.php           # Journaux admin
├── logout.php         # Déconnexion
├── config.php         # Configuration
├── style.css          # CSS hacker
├── admin_database.sql # Schéma MySQL
└── README.md          # Documentation
```

## 🎯 Utilisation

1. **Connexion** : `localhost/maria-curia-wamp/admin/`
2. **Login** : Utiliser un compte admin
3. **Dashboard** : Vue d'ensemble du jeu
4. **Manipulation** : Contrôler le déroulement
5. **Monitoring** : Surveiller l'activité

## 🚨 Actions Critiques

- ⚠️ **Reset Général** : Efface toutes les progressions
- 🗑️ **Suppression Équipe** : Action irréversible
- 💥 **Modifications Points** : Impact classement

## 🔍 Dépannage

### ❌ Erreur Base Admin
1. Vérifier que `maria_curia_admin` existe
2. Importer `admin_database.sql`
3. Vérifier connexion MySQL

### ❌ Accès Refusé
1. Vérifier identifiants admin
2. Contrôler permissions fichiers
3. Vérifier session PHP

## 📊 Monitoring

- **Auto-refresh** : 15-30 secondes
- **Alertes** : Équipes inactives
- **Métriques** : Temps réel
- **Export** : JSON/CSV

Perfect pour contrôler le jeu pendant l'événement ! 🎪🔓