# Guide de Style Admin - ParkMe In

## 🎨 **Classes CSS Uniformes**

### **Structure de Page**
```html
<!-- Container principal -->
<div class="admin-page-container">
    
    <!-- Header de page -->
    <div class="admin-page-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="admin-page-title">
                    <i class="fas fa-icon"></i>
                    Titre de la page
                </h1>
                <p class="text-muted mb-0">Description optionnelle</p>
            </div>
            <div class="admin-page-actions">
                <button class="admin-btn admin-btn-primary">Action</button>
            </div>
        </div>
    </div>
    
    <!-- Contenu -->
</div>
```

### **Cartes Statistiques**
```html
<div class="admin-stats-grid">
    <div class="admin-stat-card primary">
        <div class="admin-stat-header">
            <div class="admin-stat-content">
                <h3>123</h3>
                <p>Titre statistique</p>
            </div>
            <div class="admin-stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="admin-stat-footer">
            <a href="#"><i class="fas fa-arrow-right"></i> Voir détails</a>
        </div>
    </div>
</div>
```

### **Cartes de Contenu**
```html
<div class="admin-content-card">
    <div class="admin-content-card-header">
        <h3 class="admin-content-card-title">
            <i class="fas fa-table me-2"></i>
            Titre de la section
        </h3>
        <div>
            <button class="admin-btn admin-btn-outline admin-btn-sm">Action</button>
        </div>
    </div>
    <div class="admin-content-card-body">
        <!-- Contenu -->
    </div>
</div>
```

### **Tableaux**
```html
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Colonne 1</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Donnée</td>
                <td>
                    <div class="d-flex gap-2">
                        <button class="admin-btn admin-btn-primary admin-btn-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### **Boutons**
```html
<!-- Boutons principaux -->
<button class="admin-btn admin-btn-primary">Primaire</button>
<button class="admin-btn admin-btn-success">Succès</button>
<button class="admin-btn admin-btn-danger">Danger</button>
<button class="admin-btn admin-btn-secondary">Secondaire</button>
<button class="admin-btn admin-btn-outline">Contour</button>

<!-- Tailles -->
<button class="admin-btn admin-btn-primary admin-btn-sm">Petit</button>
<button class="admin-btn admin-btn-primary">Normal</button>
<button class="admin-btn admin-btn-primary admin-btn-lg">Grand</button>
```

## 🎨 **Palette de Couleurs**

### **Couleurs Principales**
- **Primaire** : `#2c3e50` (bleu-gris foncé)
- **Succès** : `#28a745` (vert)
- **Info** : `#17a2b8` (bleu clair)
- **Attention** : `#ffc107` (jaune)
- **Danger** : `#dc3545` (rouge)

### **Couleurs de Fond**
- **Page** : `#f8f9fa` (gris très clair)
- **Cartes** : `#fff` (blanc)
- **Headers** : `#f8f9fa` (gris clair)
- **Sidebar** : `#fff` (blanc)

## 📐 **Espacements**

### **Marges et Paddings**
- **Container principal** : `padding: 2rem`
- **Cartes** : `padding: 1.5rem`
- **Headers** : `padding: 1.5rem 2rem`
- **Tableaux** : `padding: 1rem 1.25rem`

### **Grilles**
- **Stats** : `gap: 1.5rem`
- **Contenu** : `margin-bottom: 2rem`

## 🔧 **Migration des Pages Existantes**

### **Remplacements à effectuer**

1. **Container Bootstrap → Admin Container**
```html
<!-- Ancien -->
<div class="container-fluid p-4">

<!-- Nouveau -->
<div class="admin-page-container">
```

2. **Headers Bootstrap → Admin Headers**
```html
<!-- Ancien -->
<h1 class="mb-4">Titre</h1>

<!-- Nouveau -->
<div class="admin-page-header">
    <h1 class="admin-page-title">
        <i class="fas fa-icon"></i>
        Titre
    </h1>
</div>
```

3. **Cartes Bootstrap → Admin Cards**
```html
<!-- Ancien -->
<div class="card">
    <div class="card-header">
        <h5>Titre</h5>
    </div>
    <div class="card-body">
        Contenu
    </div>
</div>

<!-- Nouveau -->
<div class="admin-content-card">
    <div class="admin-content-card-header">
        <h3 class="admin-content-card-title">Titre</h3>
    </div>
    <div class="admin-content-card-body">
        Contenu
    </div>
</div>
```

4. **Boutons Bootstrap → Admin Buttons**
```html
<!-- Ancien -->
<button class="btn btn-primary">Action</button>

<!-- Nouveau -->
<button class="admin-btn admin-btn-primary">Action</button>
```

## ✅ **Checklist de Migration**

- [ ] Remplacer le container principal
- [ ] Ajouter le header de page uniforme
- [ ] Convertir les cartes Bootstrap en cartes admin
- [ ] Remplacer les boutons par les boutons admin
- [ ] Utiliser les tableaux admin pour les données
- [ ] Ajouter les cartes statistiques si nécessaire
- [ ] Vérifier la cohérence des couleurs
- [ ] Tester la responsivité

## 🎯 **Avantages du Style Uniforme**

- **Cohérence visuelle** entre toutes les pages
- **Maintenance simplifiée** avec des classes centralisées
- **Design moderne** et professionnel
- **Responsive** par défaut
- **Accessibilité** améliorée
