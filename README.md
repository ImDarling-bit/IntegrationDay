# 🎪 Maria Curia - Version WAMP/MySQL

Version optimisée pour tests locaux avec WAMP Server et MySQL.

## 🚀 Installation WAMP

### 1. Prérequis
- **WAMP Server** installé et démarré (icône verte)
- **MySQL** actif dans WAMP
- **Apache** démarré

### 2. Installation
1. **Copier les fichiers** dans `C:\wamp64\www\maria-curia-wamp\`

2. **Créer la base de données** :
   - Ouvrir **phpMyAdmin** : http://localhost/phpmyadmin
   - Créer une nouvelle base : `maria_curia`
   - Importer le fichier `database.sql`

3. **Tester la connexion** : http://localhost/maria-curia-wamp/

## 👥 Équipes de Test

- **Equipe1** / mot de passe : `pass1`
- **Equipe2** / mot de passe : `pass2`
- **Equipe3** / mot de passe : `pass3`
- **Test Team** / mot de passe : `test123`
- **Hackers** / mot de passe : `hack`

## 🔍 Codes QR Disponibles

- `MARIE_CURIE_001` - Marie Curie (100 pts)
- `EINSTEIN_002` - Albert Einstein (100 pts)
- `NEWTON_003` - Isaac Newton (100 pts)
- `GALILEO_004` - Galilée (100 pts)
- `DARWIN_005` - Charles Darwin (100 pts)
- `PASTEUR_006` - Louis Pasteur (100 pts)

## 📁 Structure

```
maria-curia-wamp/
├── index.php          # Redirection vers login
├── login.php          # Connexion équipes
├── dashboard.php      # Tableau de bord
├── scanner.php        # Scanner QR codes
├── leaderboard.php    # Classement temps réel
├── stats.php          # Statistiques avancées
├── logout.php         # Déconnexion
├── config.php         # Configuration MySQL
├── style.css          # CSS amélioré
├── database.sql       # Structure MySQL
└── README.md          # Documentation
```

## 🎮 Fonctionnalités

### ✅ **Interface Équipes**
- **Connexion** avec nom d'équipe + mot de passe
- **Dashboard** avec progression et statistiques
- **Scanner** avec aide visuelle et codes
- **Classement** temps réel avec auto-refresh
- **Statistiques** avancées et graphiques

### ✅ **Base MySQL**
- **Tables optimisées** avec relations et index
- **Transactions** pour garantir l'intégrité
- **Requêtes préparées** pour la sécurité
- **Auto-refresh** des données

### ✅ **Interface Améliorée**
- **Design responsive** et moderne
- **Animations CSS** fluides
- **Auto-refresh** automatique
- **Notifications** de découvertes
- **Graphiques** et visualisations

## 🔧 Configuration

### Paramètres MySQL (config.php)
```php
$host = 'localhost';        // Serveur MySQL
$dbname = 'maria_curia';   // Nom de la base
$username = 'root';        // Utilisateur (défaut WAMP)
$password = '';            // Mot de passe (vide par défaut)
```

### Auto-refresh
- **Dashboard** : 30 secondes
- **Classement** : 15 secondes
- **Statistiques** : 30 secondes

## 🔍 Dépannage

### ❌ Erreur de connexion MySQL
1. Vérifiez que **WAMP** est démarré (icône verte)
2. Vérifiez que **MySQL** fonctionne
3. Créez la base `maria_curia` dans phpMyAdmin
4. Importez `database.sql`

### ❌ Page blanche
1. Vérifiez les **erreurs PHP** dans les logs WAMP
2. Vérifiez que le **chemin** est correct
3. Testez la **connexion MySQL**

### ❌ QR codes ne fonctionnent pas
1. Vérifiez que les **personnages** sont en base
2. Testez avec les **codes fournis**
3. Vérifiez la **casse** (majuscules)

## 📊 Monitoring

### Logs MySQL
- **Connexions** : Tracking des logins
- **Scans** : Historique complet des découvertes
- **Performance** : Requêtes optimisées

### Statistiques Temps Réel
- **Activité par équipe** en live
- **Découvertes populaires**
- **Progression globale**
- **Timeline des événements**

## 🎯 Avantages MySQL vs SQLite

- ✅ **Performance** : Plus rapide pour les accès concurrents
- ✅ **Concurrence** : Gestion optimale des accès simultanés
- ✅ **Intégrité** : Transactions ACID complètes
- ✅ **Monitoring** : Outils phpMyAdmin intégrés
- ✅ **Scalabilité** : Support de plus d'équipes simultanées

## 🚀 URLs d'Accès

- **Jeu** : http://localhost/maria-curia-wamp/
- **phpMyAdmin** : http://localhost/phpmyadmin
- **WAMP** : http://localhost/

Parfait pour des tests avec de nombreuses équipes simultanées ! 🎪🔬