# ParkMe In - Système de Gestion de Parking Intelligent

## 🚗 Présentation
**ParkMe In** est une application web complète de gestion de parking développée avec une architecture MVC moderne. Elle offre une solution intelligente pour la gestion des places de stationnement, des réservations, des utilisateurs et des abonnements, avec une interface d'administration avancée et une expérience utilisateur optimisée.

## ✨ Fonctionnalités Principales

### 🏠 **Interface Publique**
- **Page d'accueil** avec présentation du service et statistiques en temps réel
- **Catalogue des places** avec filtrage par type (standard, électrique, PMR, moto/scooter, vélo)
- **Système de réservation** avec mode invité et utilisateur connecté
- **Réservations immédiates** pour un stationnement instantané
- **Suivi des réservations** en temps réel avec codes de tracking
- **Système d'abonnements** avec réductions et avantages
- **Pages informatives** (À propos, Contact, FAQ, Carrières, Conditions d'utilisation)

### 👤 **Gestion des Utilisateurs**
- **Inscription/Connexion** avec validation sécurisée
- **Profil utilisateur** avec gestion des informations personnelles
- **Historique des réservations** avec statuts détaillés
- **Conversion automatique** des réservations invité vers compte utilisateur
- **Système de notifications** personnalisées
- **Gestion des abonnements** utilisateur

### 🅿️ **Gestion des Places de Parking**
- **Types de places multiples** : Standard, Électrique, PMR (handicapé), Moto/Scooter, Vélo
- **Statuts dynamiques** : Libre, Occupé, Maintenance
- **Images personnalisées** par type de place
- **Numérotation flexible** et gestion des emplacements
- **Disponibilité en temps réel** avec vérification des conflits

### 📅 **Système de Réservations**
- **Réservations planifiées** avec sélection date/heure
- **Réservations immédiates** pour stationnement instantané
- **Mode invité** sans inscription obligatoire
- **Vérification de disponibilité** en temps réel
- **Gestion des conflits** et suggestions d'alternatives
- **Statuts multiples** : Confirmée, En cours, Terminée, Annulée
- **Système de paiement** intégré avec génération de factures PDF
- **Notifications automatiques** de rappel et confirmation

### 💳 **Système d'Abonnements**
- **Abonnements multiples** : Hebdomadaire, Mensuel, Annuel
- **Réductions progressives** : 5%, 15%, 30%
- **Minutes gratuites** incluses dans chaque abonnement
- **Gestion automatique** des avantages et réductions
- **Facturation automatisée** avec génération de factures

### 💰 **Gestion des Tarifs**
- **Tarification par type** de place
- **Tarifs horaires** personnalisables
- **Historique des modifications** de tarifs
- **Application automatique** des réductions d'abonnement
- **Calcul dynamique** des coûts de réservation

### 🔔 **Système de Notifications**
- **Notifications en temps réel** pour les utilisateurs
- **Alertes de disponibilité** pour places demandées
- **Rappels de réservation** automatiques
- **Notifications administratives** pour les gestionnaires
- **Système de badges** avec compteurs visuels

### 🛠️ **Interface d'Administration**
- **Dashboard complet** avec statistiques et graphiques en temps réel
- **Gestion des utilisateurs** : création, modification, activation/désactivation, suppression
- **Gestion des places** : ajout, modification, suppression avec gestion des types
- **Gestion des réservations** : visualisation, modification, annulation, suivi
- **Gestion des abonnements** : création, modification, affectation aux utilisateurs
- **Gestion des tarifs** : configuration par type de place, historique des modifications
- **Système de logs** complet pour traçabilité des actions
- **Interface responsive** optimisée pour desktop, tablette et mobile
- **Modales avancées** pour édition rapide sans rechargement de page

## 🏗️ Architecture et Technologies

### **Architecture MVC (Modèle-Vue-Contrôleur)**
L'application suit une architecture MVC stricte pour une séparation claire des responsabilités :

### 1. **PHP 8+**
- **Rôle :** Langage principal côté serveur (backend).
- **Fonctionnement :**
  - Structure MVC (Modèle-Vue-Contrôleur) :
    - **Controllers** (backend/controllers/) : gèrent la logique métier, reçoivent les requêtes, appellent les modèles et renvoient les vues.
    - **Models** (backend/models/) : accès et manipulation des données (MySQL).
    - **Views** (frontend/views/) : affichage HTML, variables injectées par les contrôleurs.
  - Routage personnalisé via `backend/controllers/Router.php` et `.htaccess`.

### 2. **MySQL/MariaDB**
- **Rôle :** Base de données relationnelle.
- **Fonctionnement :**
  - Stocke les utilisateurs, réservations, abonnements, tarifs, logs, etc.
  - Accès via PDO dans les modèles PHP.
  - Script d’installation : `parking_db.sql`.

### 3. **HTML5**
- **Rôle :** Structure des pages web.
- **Fonctionnement :**
  - Utilisé dans toutes les vues (frontend/views/).
  - Sémantique moderne pour l’accessibilité et le SEO.

### 4. **CSS3 (Flexbox, Grid, Variables CSS)**
- **Rôle :** Mise en forme et design responsive.
- **Fonctionnement :**
  - Fichiers CSS modulaires dans `frontend/assets/css/` :
    - `variables.css` : variables globales (couleurs, espacements, etc.)
    - `app.css` : point d’entrée qui importe tous les autres CSS
    - `components.css`, `pages.css`, `admin.css`, etc. : styles spécifiques
  - Utilisation de Flexbox et Grid pour la disposition responsive.
  - Variables CSS pour la personnalisation rapide du thème.

### 5. **JavaScript (ES6+, modules, gestionnaires unifiés)**
- **Rôle :** Dynamisme, interactivité, logique côté client.
- **Fonctionnement :**
  - Scripts dans `frontend/assets/js/` :
    - `core/app.js` : initialisation globale, chargement dynamique des modules JS selon la page
    - `components/` : gestionnaires unifiés (UnifiedAdminManager, UnifiedUIManager, etc.)
    - `services/` : services spécialisés (CoreAdminService, CoreDataService, etc.)
  - Utilisation de classes ES6, modules, IIFE pour éviter les conflits globaux.
  - Gestion dynamique des dépendances JS selon la page affichée.

### 6. **Bootstrap 5**
- **Rôle :** Framework CSS pour la mise en page responsive et les composants UI.
- **Fonctionnement :**
  - Chargé via CDN dans le header.
  - Utilisé pour la grille, les boutons, les modales, les alertes, etc.
  - Complété par des styles personnalisés dans les fichiers CSS du projet.

### 7. **Font Awesome**
- **Rôle :** Icônes vectorielles.
- **Fonctionnement :**
  - Chargé via CDN dans le header.
  - Utilisé dans les boutons, menus, alertes, etc. via des classes CSS (`<i class="fas fa-...">`).

### 8. **Chart.js**
- **Rôle :** Graphiques et statistiques dynamiques.
- **Fonctionnement :**
  - Chargé via CDN dans le header.
  - Utilisé dans le dashboard admin pour afficher des statistiques (places, réservations, abonnements).
  - Données injectées depuis PHP dans le HTML, puis exploitées par JS.

### 9. **.htaccess (Apache)**
- **Rôle :** Réécriture d’URL pour le routage MVC.
- **Fonctionnement :**
  - Redirige toutes les requêtes vers `index.php` sauf les fichiers/dossiers existants.
  - Permet des URLs propres et le fonctionnement du routeur PHP.

## Structure du projet
```
parking_d/
├── backend/
│   ├── config/           # Fichiers de configuration PHP
│   ├── controllers/      # Contrôleurs MVC
│   ├── helpers/          # Fonctions utilitaires
│   ├── models/           # Modèles de données
│   └── services/         # Services backend
├── frontend/
│   ├── assets/
│   │   ├── css/          # Feuilles de style CSS
│   │   ├── js/           # Scripts JavaScript
│   │   └── img/          # Images
│   └── views/
│       ├── admin/        # Vues d'administration (places, users, tarifs, etc.)
│       └── ...           # Autres vues
├── index.php             # Point d'entrée principal
├── .htaccess             # Réécriture d'URL pour Apache
└── parking_db.sql        # Script SQL de la base de données
```

## 🚀 Installation et Configuration

### **Prérequis**
- **Serveur web** : Apache 2.4+ avec mod_rewrite activé
- **PHP** : Version 8.0 ou supérieure
- **Base de données** : MySQL 5.7+ ou MariaDB 10.3+
- **Extensions PHP requises** : PDO, PDO_MySQL, mbstring, openssl

### **Installation étape par étape**

#### **1. Cloner le projet**
```bash
git clone [URL_DU_DEPOT]
cd parking_d
```

#### **2. Configuration de l'environnement**

##### **A. Serveur local (XAMPP/WAMP)**
```bash
# Copier le projet dans le répertoire web
cp -r parking_d/ /path/to/xampp/htdocs/projet/
```

##### **B. Configuration de la base de données**
1. **Créer la base de données** :
   ```sql
   CREATE DATABASE parking_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Importer le schéma** :
   ```bash
   mysql -u root -p parking_db < parking_db.sql
   ```

3. **Configurer les accès** dans `backend/config/config.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'parking_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_CHARSET', 'utf8mb4');
   ```

#### **3. Configuration du serveur web**

##### **Apache (.htaccess)**
Le fichier `.htaccess` est déjà configuré pour :
- Réécriture d'URL pour le routage MVC
- Redirection des erreurs 404
- Optimisation des performances

##### **Permissions (Linux/Mac)**
```bash
chmod 755 parking_d/
chmod 644 parking_d/.htaccess
chmod -R 755 parking_d/frontend/assets/
```

#### **4. Vérification de l'installation**
1. **Démarrer les services** (Apache + MySQL)
2. **Accéder à l'application** : `http://localhost/projet/parking_d/`
3. **Vérifier la page d'accueil** et les fonctionnalités de base

### **Configuration avancée**

#### **Variables d'environnement**
Modifier `backend/config/config.php` selon vos besoins :
```php
// Configuration de l'application
define('APP_NAME', 'ParkMe In');
define('BASE_URL', 'http://localhost/projet/parking_d/');
define('ADMIN_EMAIL', 'admin@parkmein.com');

// Configuration de sécurité
define('SESSION_LIFETIME', 3600); // 1 heure
define('BCRYPT_COST', 12);

// Configuration des uploads
define('MAX_FILE_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', 'frontend/assets/img/uploads/');
```

#### **Configuration des emails (optionnel)**
Pour activer les notifications par email :
```php
// Configuration SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@gmail.com');
define('SMTP_PASSWORD', 'votre-mot-de-passe');
```

## 📖 Guide d'utilisation

### **Interface Publique**

#### **Navigation principale**
- **Accueil** : Présentation du service et statistiques
- **Places disponibles** : Catalogue avec filtres par type
- **Abonnements** : Offres et tarifs préférentiels
- **FAQ** : Questions fréquentes
- **À propos** : Informations sur l'entreprise

#### **Système de réservation**

##### **Mode Invité (sans inscription)**
1. Sélectionner une place disponible
2. Choisir date/heure et durée
3. Renseigner email et téléphone
4. Confirmer et payer
5. Recevoir un code de suivi par email

##### **Mode Utilisateur connecté**
1. Se connecter ou créer un compte
2. Sélectionner une place
3. Réserver avec historique automatique
4. Gérer ses réservations depuis le profil

#### **Types de places disponibles**
- **🚗 Standard** : Places de stationnement classiques
- **⚡ Électrique** : Places avec borne de recharge
- **♿ PMR** : Places adaptées aux personnes à mobilité réduite
- **🏍️ Moto/Scooter** : Places dédiées aux deux-roues motorisés
- **🚲 Vélo** : Emplacements pour vélos et trottinettes

### **Interface d'Administration**

#### **Accès administrateur**
- **URL** : `http://localhost/projet/parking_d/admin/dashboard`
- **Compte par défaut** :
  - Email : `sasa@gmail.com`
  - Mot de passe : `sasa`

#### **Fonctionnalités administratives**

##### **Dashboard**
- Statistiques en temps réel
- Graphiques de performance
- Alertes et notifications
- Aperçu des activités récentes

##### **Gestion des utilisateurs**
- Liste complète avec filtres
- Création/modification/suppression
- Activation/désactivation de comptes
- Gestion des rôles et permissions

##### **Gestion des places**
- Ajout de nouvelles places
- Modification des types et statuts
- Gestion des images par type
- Configuration de la disponibilité

##### **Gestion des réservations**
- Vue d'ensemble des réservations
- Modification et annulation
- Suivi des paiements
- Génération de rapports

##### **Gestion des tarifs**
- Configuration par type de place
- Historique des modifications
- Application des réductions d'abonnement
- Calculs automatiques

##### **Gestion des abonnements**
- Création d'offres personnalisées
- Affectation aux utilisateurs
- Suivi des avantages et réductions
- Facturation automatisée

## 🔧 Développement et Maintenance

### **Structure des fichiers**
```
parking_d/
├── backend/
│   ├── config/
│   │   ├── config.php          # Configuration principale
│   │   └── database.php        # Configuration base de données
│   ├── controllers/
│   │   ├── AdminController.php # Gestion administration
│   │   ├── AuthController.php  # Authentification
│   │   ├── HomeController.php  # Pages publiques
│   │   └── ReservationController.php # Réservations
│   ├── models/
│   │   ├── Database.php        # Singleton base de données
│   │   ├── UserModel.php       # Gestion utilisateurs
│   │   ├── PlaceModel.php      # Gestion places
│   │   └── ReservationModel.php # Gestion réservations
│   └── helpers/
│       └── functions.php       # Fonctions utilitaires
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── app.css         # Point d'entrée CSS
│   │   │   ├── variables.css   # Variables globales
│   │   │   ├── components.css  # Composants réutilisables
│   │   │   ├── pages.css       # Styles spécifiques aux pages
│   │   │   └── admin.css       # Interface d'administration
│   │   ├── js/
│   │   │   ├── core/
│   │   │   │   └── app.js      # Initialisation globale
│   │   │   ├── components/
│   │   │   │   ├── unifiedUIManager.js
│   │   │   │   ├── unifiedAdminManager.js
│   │   │   │   └── unifiedReservationManager.js
│   │   │   └── services/
│   │   │       ├── coreAdminService.js
│   │   │       └── coreDataService.js
│   │   └── img/
│   │       ├── places/         # Images des types de places
│   │       ├── team/           # Photos de l'équipe
│   │       └── uploads/        # Uploads utilisateurs
│   └── views/
│       ├── admin/              # Vues d'administration
│       ├── auth/               # Authentification
│       ├── home/               # Pages publiques
│       ├── reservation/        # Système de réservation
│       └── templates/          # Templates réutilisables
├── index.php                   # Point d'entrée principal
├── .htaccess                   # Configuration Apache
├── parking_db.sql             # Schéma de base de données
└── README.md                   # Documentation
```

### **Bonnes pratiques de développement**

#### **CSS**
- Utiliser les variables CSS définies dans `variables.css`
- Respecter la nomenclature BEM pour les classes
- Privilégier Flexbox et Grid pour les layouts
- Maintenir la responsivité sur tous les écrans

#### **JavaScript**
- Utiliser les classes ES6 et les modules
- Éviter les variables globales
- Implémenter la gestion d'erreurs avec try-catch
- Documenter les fonctions complexes

#### **PHP**
- Respecter l'architecture MVC
- Utiliser les requêtes préparées pour la sécurité
- Implémenter la validation côté serveur
- Gérer les erreurs avec des logs appropriés

### **Débogage et logs**

#### **Logs d'erreurs PHP**
```php
// Activer les logs en développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Logs personnalisés
error_log("Message de debug", 0);
```

#### **Console JavaScript**
```javascript
// Utiliser les niveaux appropriés
console.log('Information');
console.warn('Avertissement');
console.error('Erreur');
console.debug('Debug');
```

### **Sécurité**

#### **Mesures implémentées**
- **Validation des entrées** : Sanitisation et validation côté serveur
- **Requêtes préparées** : Protection contre l'injection SQL
- **Hashage des mots de passe** : Utilisation de `password_hash()`
- **Sessions sécurisées** : Configuration appropriée des sessions PHP
- **Protection CSRF** : Tokens de validation pour les formulaires
- **Validation des fichiers** : Contrôle des uploads et types MIME

#### **Recommandations**
- Changer les mots de passe par défaut
- Utiliser HTTPS en production
- Configurer les permissions de fichiers appropriées
- Mettre à jour régulièrement PHP et les dépendances

## 🐛 Dépannage

### **Problèmes courants**

#### **Erreur 404 - Page non trouvée**
- Vérifier que mod_rewrite est activé sur Apache
- Contrôler les permissions du fichier `.htaccess`
- Vérifier la configuration de `BASE_URL` dans `config.php`

#### **Erreur de connexion à la base de données**
- Vérifier les paramètres dans `backend/config/config.php`
- S'assurer que MySQL est démarré
- Contrôler les permissions de l'utilisateur de base de données

#### **Problèmes d'affichage CSS/JS**
- Vider le cache du navigateur (Ctrl+F5)
- Vérifier les chemins des assets dans les templates
- Contrôler la console développeur pour les erreurs 404

#### **Erreurs de permissions**
```bash
# Linux/Mac - Ajuster les permissions
chmod 755 parking_d/
chmod -R 644 parking_d/frontend/assets/
chmod 755 parking_d/frontend/assets/img/uploads/
```

### **Maintenance**

#### **Sauvegarde de la base de données**
```bash
# Sauvegarde complète
mysqldump -u root -p parking_db > backup_$(date +%Y%m%d).sql

# Restauration
mysql -u root -p parking_db < backup_20241222.sql
```

#### **Nettoyage des logs**
```bash
# Nettoyer les logs PHP (si volumineux)
> /var/log/apache2/error.log

# Nettoyer les logs applicatifs
> backend/logs/app.log
```

#### **Mise à jour**
1. Sauvegarder la base de données
2. Sauvegarder les fichiers de configuration
3. Mettre à jour le code source
4. Exécuter les migrations de base de données si nécessaire
5. Tester les fonctionnalités critiques

## 📞 Support et Contact

### **Informations du projet**
- **Nom** : ParkMe In - Système de Gestion de Parking Intelligent
- **Version** : 1.0.0
- **Développeur** : Labidi Sami
- **Email** : labidi.sami@example.com
- **Licence** : Open Source

### **Comptes de test**

#### **Administrateur**
- **Email** : `sasa@gmail.com`
- **Mot de passe** : `sasa`
- **Accès** : Interface d'administration complète

#### **Utilisateur standard**
- Créer un compte via l'interface d'inscription
- Ou utiliser le mode invité pour les réservations

### **Ressources utiles**
- **Documentation PHP** : [https://www.php.net/docs.php](https://www.php.net/docs.php)
- **Bootstrap 5** : [https://getbootstrap.com/docs/5.0/](https://getbootstrap.com/docs/5.0/)
- **Chart.js** : [https://www.chartjs.org/docs/](https://www.chartjs.org/docs/)
- **Font Awesome** : [https://fontawesome.com/icons](https://fontawesome.com/icons)

---

**© 2025 ParkMe In - Tous droits réservés**

*Développé avec ❤️ pour simplifier la gestion du stationnement urbain*