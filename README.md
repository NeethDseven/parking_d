# Parking D - Application de gestion de parking

## Présentation
Parking D est une application web complète de gestion de parking, permettant la gestion des réservations, des utilisateurs, des abonnements, des tarifs et des places de stationnement. L'interface d'administration est moderne, responsive et optimisée pour une utilisation sur desktop et mobile.

## Fonctionnalités principales
- Gestion des utilisateurs (ajout, modification, suppression)
- Gestion des places de parking (ajout, modification, suppression, visualisation)
- Gestion des réservations (création, suivi, annulation)
- Gestion des abonnements (création, modification, affectation à des utilisateurs)
- Gestion des tarifs (par type de place, édition, historique)
- Tableau de bord avec statistiques et graphiques
- Notifications et alertes
- Interface responsive et moderne

## Technologies utilisées et leur fonctionnement

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

## Installation
1. **Cloner le dépôt**
2. **Configurer l'environnement**
   - Copier le dossier dans `htdocs` (XAMPP) ou le répertoire web de votre serveur
   - Importer `parking_db.sql` dans votre base de données MySQL
   - Configurer les accès à la base dans `backend/config/config.php`
3. **Lancer le serveur**
   - Démarrer Apache et MySQL via XAMPP
   - Accéder à l'application via [http://127.0.0.1/projet/parking_d/](http://127.0.0.1/projet/parking_d/)

## Utilisation
- L'administration est accessible via `/admin` (ex : `/projet/parking_d/admin/dashboard`)
  
- Les utilisateurs peuvent réserver, consulter les places, s'abonner, etc.

## Conseils
- Vider le cache navigateur après chaque modification de CSS/JS
- Les chemins des assets sont relatifs à la racine du projet (`/projet/parking_d/frontend/assets/...`)
- Pour toute modification de structure, adapter les inclusions dans les templates (`header.php`, `footer.php`)

## Auteurs
- Projet ParkMe In
- Contact : [Labidi Sami]

--- compte admin pour connection : sasa@gmail.com
mot de passe : sasa

**Licence :** Projet open-source 