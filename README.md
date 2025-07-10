# ParkMe In - Système de Gestion de Parking Intelligent

## 🚗 Présentation
**ParkMe In** est une application web complète de gestion de parking développée avec une architecture MVC moderne. Elle offre une solution intelligente pour la gestion des places de stationnement, des réservations, des utilisateurs et des abonnements, avec une interface d'administration avancée et une expérience utilisateur optimisée.

## ✨ Fonctionnalités Principales

### 🏠 **Interface Publique**
- **Page d'accueil** avec présentation du service et statistiques en temps réel
- **Catalogue des places** avec filtrage par type (standard, électrique, PMR, moto/scooter, vélo)
- **Pagination AJAX** intelligente avec mise à jour d'URL et navigation navigateur
- **Affichage responsive** : 3 places par ligne sur desktop, adaptatif sur mobile/tablette
- **Images personnalisées** par type de place (elec1-6.webp, standard, velo, moto, pmr)
- **Système de réservation** avec mode invité et utilisateur connecté
- **Réservations immédiates** pour un stationnement instantané avec chronométrage en temps réel
- **Suivi des réservations** en temps réel avec codes QR d'accès et de sortie
- **Mise à jour automatique** des créneaux disponibles (toutes les 30 secondes)
- **Système d'abonnements** avec réductions et avantages (5%, 15%, 30%)
- **Pages informatives** (À propos, Contact, FAQ, Carrières, Conditions d'utilisation)
- **Interface responsive** optimisée pour tous les appareils

### 👤 **Gestion des Utilisateurs**
- **Inscription/Connexion** avec validation sécurisée
- **Profil utilisateur** avec onglets dynamiques (Informations, Réservations, Notifications, Abonnements)
- **Navigation par ancres** : liens directs vers les onglets (#notifications, #reservations)
- **Historique des réservations** avec statuts détaillés et badges colorés
- **Codes QR d'accès** : génération automatique pour entrée/sortie parking
- **Conversion automatique** des réservations invité vers compte utilisateur
- **Système de notifications** avec badges de comptage en temps réel
- **Gestion des abonnements** utilisateur avec avantages automatiques

### 🅿️ **Gestion des Places de Parking**
- **Types de places multiples** : Standard, Électrique, PMR (handicapé), Moto/Scooter, Vélo
- **Images spécialisées** : elec1-6.webp pour électriques, velo.webp, moto.webp, pmr.webp
- **Statuts dynamiques** : Libre, Occupé, Maintenance avec indicateurs visuels
- **Cartes transparentes** avec images de fond visibles et design cohérent
- **Numérotation flexible** et gestion des emplacements
- **Disponibilité en temps réel** avec vérification des conflits
- **Mise à jour automatique** des créneaux après réservations terminées

### 📅 **Système de Réservations**
- **Réservations planifiées** avec sélection date/heure et durée flexible
- **Réservations immédiates** avec chronométrage en temps réel et calcul automatique
- **Mode invité** sans inscription obligatoire (système 'guest')
- **Codes QR d'accès** : génération automatique pour entrée et sortie
- **Vérification de disponibilité** en temps réel avec API dédiée
- **Gestion des conflits** et suggestions d'alternatives
- **Statuts multiples** : Confirmée, En cours, Terminée, Annulée avec badges colorés
- **Suivi en temps réel** : page dédiée avec container transparent et chronométrage
- **Système de paiement** intégré avec génération de factures PDF
- **Notifications automatiques** de rappel et confirmation
- **Modals responsives** sans wrapper dialog, adaptées à tous les écrans

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
- **Badges de comptage** : rouge vif, parfaitement ronds, taille optimisée (16px)
- **Dropdown notifications** avec liens directs vers les onglets du profil
- **Alertes de disponibilité** pour places demandées
- **Rappels de réservation** automatiques
- **Notifications administratives** pour les gestionnaires
- **Navigation intelligente** : liens directs vers #notifications dans le profil
- **Marquage automatique** comme lu lors de la consultation

### 🛠️ **Interface d'Administration**
- **Dashboard moderne** avec disposition optimisée en grille 2x2 pour desktop
- **Statistiques visuelles** : 6 macarons informatifs (utilisateurs total/nouveaux, revenus, réservations, places libres, types de places)
- **Graphiques intelligents** : revenus mensuels, répartition des places, état des places (libre/occupées/maintenance), réservations par statut, abonnements détaillés
- **Sidebar responsive** : pleine hauteur, toggle hamburger, design cohérent, masquage automatique sur mobile
- **Navigation optimisée** : breadcrumbs masqués, layouts pleine largeur, utilisation maximale de l'espace
- **Gestion des utilisateurs** : création, modification, activation/désactivation, suppression avec interface moderne
- **Gestion des places** : ajout, modification, suppression avec gestion des types et images personnalisées
- **Gestion des réservations** : visualisation, modification, annulation, suivi avec graphiques de statut
- **Gestion des abonnements** : création, modification, affectation aux utilisateurs avec statistiques détaillées
- **Gestion des tarifs** : configuration par type de place, historique des modifications
- **Système de logs** complet pour traçabilité des actions avec activité récente
- **Interface responsive** optimisée pour desktop (grille 6x1), tablette (3x2), mobile (2x3)
- **Modales full-screen** : sans wrapper dialog, adaptées à l'écran, responsive
- **Design cohérent** : navbar blanc, ombres élégantes, styling unifié
- **Boutons d'action** : couleur #2c3e50, texte et icônes blancs

## � **Améliorations UX/UI Récentes**

### **Design et Interface**
- **Palette de couleurs cohérente** : #2c3e50 (principal), #2980b9 (headers), rouge vif pour notifications
- **Cartes de places transparentes** avec images de fond visibles et design moderne
- **Badges de statut élégants** : "Terminée" (vert avec icône flag-checkered), "Annulée" (rouge vif avec icône times), design uniforme
- **Modals responsives** : suppression des wrappers dialog, centrage parfait, adaptation automatique à l'écran
- **Navigation navbar** : distribution pleine largeur, éléments bien espacés, réduction des espaces vides
- **Dashboard admin moderne** : disposition en grille optimisée, cartes avec ombres subtiles, graphiques harmonieux

### **Expérience Utilisateur**
- **Pagination AJAX intelligente** : pas de rechargement, URL mise à jour, navigation navigateur fonctionnelle
- **Mise à jour temps réel** : créneaux actualisés automatiquement toutes les 30s sans intervention
- **Navigation par onglets** : liens directs avec ancres (#notifications, #reservations, #abonnements)
- **Chronométrage en direct** : réservations immédiates avec timer temps réel et container transparent
- **Codes QR automatiques** : génération à l'ouverture des modals, copie en un clic
- **Interface de suivi** : page dédiée avec fond transparent et mise à jour automatique
- **Messages utilisateur-admin** : interface intégrée dans le profil pour lire les réponses administratives

### **Responsive Design**
- **Grille adaptative** : 3x2 places sur desktop, responsive sur mobile/tablette avec pagination filtrée
- **Dashboard responsive** : 6 macarons (desktop), 3x2 (tablette), 2x3 (mobile), 1x6 (très petit écran)
- **Modals full-screen** : adaptation automatique à la taille d'écran, suppression des scrolls internes
- **Sidebar admin responsive** : toggle hamburger, masquage intelligent, pleine hauteur
- **Badges optimisés** : taille parfaite (16px), lisibilité maximale, couleurs cohérentes

### **Performance et Optimisation**
- **Chargement dynamique** : scripts JS chargés selon la page active
- **Gestionnaires unifiés** : réduction des conflits, code plus maintenable
- **Mise en cache intelligente** : optimisation des requêtes et des assets
- **Code nettoyé** : suppression des fichiers de test, optimisation CSS/JS

## �🏗️ Architecture et Technologies

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

#### **Statuts de réservation**
- **🕐 En attente** : Réservation créée, en attente de confirmation/paiement
- **✅ Confirmée** : Réservation validée et payée, en attente de début
- **▶️ En cours** : Réservation active, véhicule sur la place
- **⚡ En cours immédiat** : Réservation immédiate avec chronométrage en temps réel
- **🏁 Terminée** : Réservation complétée avec succès
- **❌ Annulée** : Réservation annulée par l'utilisateur ou l'administrateur
- **⏰ Expirée** : Réservation expirée (délai de paiement dépassé)
- **💳 En attente paiement** : Réservation immédiate en attente de finalisation

### **Interface d'Administration**

#### **Accès administrateur**
- **URL** : `http://localhost/projet/parking_d/admin/dashboard`
- **Compte par défaut** :
  - Email : `sasa@gmail.com`
  - Mot de passe : `sasa`

#### **Fonctionnalités administratives**

##### **Dashboard**
- **Statistiques en temps réel** avec 6 macarons informatifs :
  - Utilisateurs total et nouveaux utilisateurs du mois
  - Revenus totaux et du mois en cours
  - Réservations totales et du jour
  - Places libres sur total disponible
  - Types de places disponibles
- **Graphiques intelligents** :
  - Revenus mensuels (graphique principal en barres)
  - Répartition des places par type (donut)
  - État des places : libre/occupées/maintenance (camembert)
  - Réservations par statut : confirmées, en attente, annulées, terminées (barres)
  - Abonnements détaillés avec légende personnalisée (donut)
- **Informations en temps réel** :
  - Réservations actives avec détails utilisateur et horaires
  - Activité récente avec logs des actions administratives
- **Disposition responsive** : grille optimisée selon la taille d'écran

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

#### **Nouvelles fonctionnalités d'interface**

##### **Sidebar responsive**
- **Desktop** : Sidebar fixe visible en permanence
- **Mobile/Tablette** : Toggle hamburger (#2c3e50) en haut à droite
- **Animation fluide** : transition 0.3s avec overlay semi-transparent
- **Fermeture intelligente** : clic sur overlay ou bouton toggle

##### **Modals optimisées**
- **Full-screen responsive** : adaptation automatique à la taille d'écran
- **Sans wrapper dialog** : suppression des contraintes de taille
- **Centrage parfait** : positionnement optimal sur tous les appareils
- **Scroll intelligent** : pas de scroll interne, adaptation au contenu

##### **Dashboard moderne**
- **Disposition optimisée** : grille 2x2 avec utilisation maximale de l'espace
- **Graphiques harmonieux** : tailles adaptées, pas de graphiques redondants
- **Informations denses** : réservations actives et activité récente côte à côte
- **Responsive intelligent** : adaptation selon la taille d'écran

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

### **Fonctionnalités Techniques Avancées**

#### **Gestion des images par type de place**
- **Images spécialisées** : elec1-6.webp pour places électriques (rotation automatique)
- **Support multi-format** : .webp, .jpg, .jpeg avec fallback automatique
- **Mapping intelligent** : association automatique type de place → image correspondante
- **Optimisation** : images WebP pour performance, JPEG en fallback

#### **Système de pagination intelligent**
- **AJAX sans rechargement** : navigation fluide sans perte de contexte
- **URL dynamique** : mise à jour de l'URL pour navigation navigateur
- **Filtrage intégré** : pagination adaptée aux filtres actifs (par type)
- **Responsive** : adaptation automatique mobile/desktop

#### **Dashboard administratif moderne**
- **Grille responsive** : 6 macarons (desktop) → 3x2 (tablette) → 2x3 (mobile) → 1x6 (très petit)
- **Graphiques intelligents** : filtrage automatique des données nulles
- **Mise à jour temps réel** : actualisation automatique des statistiques
- **Optimisation espace** : suppression des espaces vides, disposition compacte

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
- **Version** : 2.0.0
- **Développeur** : Labidi Sami
- **Email** : labidi.neeth@gmail.com
- **Licence** : Open Source
- **Dernière mise à jour** : Juin 2025
- **Fonctionnalités principales** :
  - Interface moderne responsive
  - Dashboard administratif optimisé
  - Système de réservation intelligent
  - Gestion complète des utilisateurs et places
  - Notifications en temps réel

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

*Développé avec ❤️ pour simplifier la gestion du stationnement souterrain*
