
# Documentation Technique - Le Studio Sport & Coaching

## 📋 Sommaire

1. [Vue d'ensemble du projet](#vue-densemble-du-projet)
2. [Architecture et structure](#architecture-et-structure)
3. [Technologies utilisées](#technologies-utilisées)
4. [Structure des fichiers](#structure-des-fichiers)
5. [Pages et fonctionnalités](#pages-et-fonctionnalités)
6. [Conventions CSS](#conventions-css)
7. [Responsive Design](#responsive-design)
8. [SEO et métadonnées](#seo-et-métadonnées)
9. [JavaScript et validation](#javascript-et-validation)
10. [Bonnes pratiques](#bonnes-pratiques)
11. [Maintenance et évolution](#maintenance-et-évolution)

---

## 🎯 Vue d'ensemble du projet

**Le Studio Sport & Coaching** est un site web vitrine développé dans le cadre d'une formation DWWM (Développeur Web et Web Mobile). Le projet présente une salle de sport située à Biarritz avec ses services, activités et formulaire de contact.

### Informations générales
- **Type** : Site web vitrine statique
- **Secteur** : Sport & Coaching
- **Localisation** : Biarritz, France
- **Développeurs** : Sabrina & Eddy
- **Formation** : DWWM 2025 - Avignon

---

## 🏗️ Architecture et structure

### Modèle architectural
Le projet suit une **architecture front-end classique** avec :
- Structure HTML5 sémantique
- Styles CSS modulaires
- JavaScript vanilla pour la validation
- Approche mobile-first avec Bootstrap

### Principe de séparation des responsabilités
```
HTML ➜ Structure et contenu sémantique
CSS  ➜ Présentation et mise en forme
JS   ➜ Interactivité et validation
```

---

## 🛠️ Technologies utilisées

### Frameworks et libraries
| Technologie | Version | Usage |
|-------------|---------|-------|
| **Bootstrap** | 5.3.3 | Framework CSS responsive |
| **Font Awesome** | 6.7.2 | Icônes |
| **Google Fonts** | - | Typographies (Oswald, Roboto) |

### Standards web
- **HTML5** : Structure sémantique
- **CSS3** : Styles modernes (Flexbox, Grid, Transitions)
- **JavaScript ES6+** : Validation côté client

### Outils de développement
- **Git & GitHub** : Versioning et collaboration
- **VS Code** : Environnement de développement
- **GitHub Pages** : Déploiement

---

## 📁 Structure des fichiers

```
Le-studio---GYMS/
├── 📄 index.html              # Page d'accueil
├── 📄 contact.html            # Page de contact
├── 📄 presentation.html       # Page Training Fonctionnel
├── 📄 404.html               # Page d'erreur
├── 📄 README.md              # Documentation projet
├── 📁 css/
│   ├── style-header-footer.css   # Styles navigation & footer
│   ├── style-accueil.css         # Styles page d'accueil
│   ├── style-contact.css         # Styles page contact
│   ├── style-presentation.css    # Styles page présentation
│   └── style-404.css             # Styles page erreur
├── 📁 js/
│   └── validation.js             # Validation formulaire
├── 📁 assets/
│   └── img/                      # Images et assets
└── 📁 lib/
    └── jquery/                   # Bibliothèque jQuery
```

---

## 📄 Pages et fonctionnalités

### 1. Page d'accueil (`index.html`)
**Sections principales :**
- **Header** : Navigation + Carrousel promotionnel
- **Section 1** : Présentation du studio
- **Section 2** : Grille des activités avec overlays
- **Section 3** : Actualités en cards Bootstrap
- **Footer** : Partenaires + Feed Instagram + Informations

**Fonctionnalités clés :**
- Carrousel Bootstrap automatique
- Navigation responsive avec dropdown
- Overlays interactifs sur les activités
- Effets de survol et animations CSS

### 2. Page de contact (`contact.html`)
**Sections principales :**
- **Header** : Navigation + Image de fond
- **Section** : Formulaire + Coordonnées
- **Footer** : Carte + Instagram + Informations

**Fonctionnalités clés :**
- Formulaire avec validation JavaScript temps réel
- Protection CSRF (simulation)
- Layout responsive en deux colonnes
- Géolocalisation avec carte

### 3. Page présentation (`presentation.html`)
**Sections principales :**
- **Header** : Navigation + Image banner
- **Section 1** : Présentation Training Fonctionnel
- **Section 2** : Ateliers (TRX, Boxe, HIIT, Fusion)
- **Footer** : Instagram + Informations

**Fonctionnalités clés :**
- Carrousel flottant des coachs
- Layout texte + image responsive
- Grille d'ateliers adaptative

### 4. Page erreur (`404.html`)
**Fonctionnalités :**
- Design immersif plein écran
- Message d'erreur centré
- Bouton de retour à l'accueil
- Overlay sombre sur image de fond

---

## 🎨 Conventions CSS

### Organisation modulaire
```css
/* Structure standardisée */
/* ==========================================================================
   NOM DE LA SECTION
   Description : Explication du rôle
   ========================================================================== */
```

### Méthodologie de nommage
```css
/* Classes descriptives */
.section-partenaire     /* Section spécifique */
.btn-custom            /* Composant personnalisé */
.image-wrapper         /* Conteneur d'images */
.overlay               /* Superposition */
.hover-menu            /* État de survol */
```

### Palette de couleurs
```css
:root {
  --primary-gold: #CFAD6C;      /* Couleur principale dorée */
  --dark-grey: #282828;         /* Gris foncé footer */
  --black: #141414;             /* Noir profond */
  --white: #ffffff;             /* Blanc pur */
  --overlay-black: rgba(0,0,0,0.6); /* Superposition */
}
```

### Typographie
```css
/* Hiérarchie typographique */
h1, h2, h3, h5 { font-family: "Oswald", sans-serif; }  /* Titres */
p, body { font-family: "Roboto", sans-serif; }         /* Corps de texte */

/* Tailles responsives */
@media (min-width: 1200px) {
  .carousel-caption h2 { font-size: 48px; }
}
```

---

## 📱 Responsive Design

### Breakpoints Bootstrap utilisés
```css
/* Mobile First Approach */
/* ≤ 354px  */ @media (max-width: 354px)    /* Très petits mobiles */
/* ≤ 576px  */ @media (max-width: 576px)    /* Petits mobiles */
/* ≤ 768px  */ @media (max-width: 768px)    /* Mobiles */
/* ≤ 991px  */ @media (max-width: 991.98px) /* Tablettes */
/* ≥ 992px  */ @media (min-width: 992px)    /* Desktop */
/* ≥ 1200px */ @media (min-width: 1200px)   /* Grands écrans */
```

### Stratégies d'adaptation

#### Navigation
- **Mobile** : Menu hamburger avec overlay noir
- **Desktop** : Navigation horizontale avec dropdowns

#### Images
- **Mobile** : Carrousel 500px de hauteur
- **Desktop** : Carrousel 700px avec object-fit

#### Grilles
- **Mobile** : Colonnes empilées (col-12)
- **Tablette** : 2 colonnes (col-md-6)
- **Desktop** : 4 colonnes (col-lg-3)

---

## 🔍 SEO et métadonnées

### Structure des métadonnées
```html
<!-- SEO Standard -->
<meta name="description" content="Description optimisée pour moteurs de recherche">
<meta name="keywords" content="mots-clés, pertinents, localisation">
<meta name="author" content="Studio Sport & Coaching">

<!-- Open Graph (Réseaux sociaux) -->
<meta property="og:title" content="Titre pour partage social">
<meta property="og:description" content="Description pour partage">
<meta property="og:image" content="./assets/img/logo.png">
<meta property="og:type" content="website">
```

### Optimisations appliquées
- Titres hiérarchisés (H1 → H5)
- Attributs `alt` descriptifs sur toutes les images
- URLs sémantiques
- Structure de données locales (Biarritz)
- Mots-clés géolocalisés

---

## ⚡ JavaScript et validation

### Architecture de validation (`validation.js`)

```javascript
// Configuration centralisée
const inputs = [
  {
    element: inputName,
    regex: /^[a-zA-Z\s-]+$/,
    message: "Le nom est invalide"
  },
  // ... autres champs
];

// Validation temps réel
inputs.forEach(input => {
  input.element.addEventListener("input", function (e) {
    RegexTest(this, input.regex, input.message);
  });
});
```

### Fonctionnalités implémentées
- **Validation temps réel** : Retour visuel immédiat
- **Regex personnalisées** : Validation stricte par type de champ
- **Messages d'erreur contextuels** : Guidage utilisateur
- **Protection contre XSS** : Interdiction des balises HTML
- **Validation de soumission** : Vérification globale avant envoi

### Sécurité côté client
```javascript
// Anti-XSS dans le message
regex: /^(?!.*<.*?>)[\s\S]{10,1000}$/,

// Jeton CSRF (simulation)
<input type="hidden" name="csrf_token" value="fake_token_123456">
```

---

## ✅ Bonnes pratiques

### Performance
- **Images optimisées** : Formats adaptés (JPG/PNG)
- **CDN externes** : Bootstrap, Font Awesome, Google Fonts
- **CSS modulaire** : Chargement spécifique par page
- **Lazy loading** : Potentiel d'amélioration future

### Accessibilité
- **Navigation clavier** : Focus visible et logique
- **Attributs ARIA** : Labels descriptifs
- **Contraste** : Couleurs accessibles
- **Textes alternatifs** : Images décrites

### SEO technique
- **HTML sémantique** : `<header>`, `<section>`, `<footer>`
- **Métadonnées complètes** : Toutes les pages
- **URLs propres** : Structure logique
- **Sitemap potentiel** : Pour indexation

### Maintenance
- **Code commenté** : Documentation inline
- **Structure modulaire** : Facilité de modification
- **Conventions cohérentes** : Nommage uniforme

---

## 🚀 Maintenance et évolution

### Améliorations possibles

#### Court terme
- [ ] Optimisation des images (WebP)
- [ ] Lazy loading des images
- [ ] Minification CSS/JS
- [ ] Amélioration des animations

#### Moyen terme
- [ ] Système de gestion de contenu (CMS)
- [ ] Formulaire de contact fonctionnel (backend)
- [ ] Blog dynamique
- [ ] Système de réservation en ligne

#### Long terme
- [ ] Progressive Web App (PWA)
- [ ] Multilingue (français/anglais)
- [ ] Intégration API réseaux sociaux
- [ ] Analytics et tracking

### Workflow de développement
```bash
# Branches Git utilisées
main      # Production
dev       # Développement
sabrina   # Branch développeur 1
eddy      # Branch développeur 2

# Processus
1. Feature branch → dev
2. Pull request → review
3. Merge dev → main
4. Deploy GitHub Pages
```

---

## 📞 Support et contact

**Équipe de développement :**
- **Sabrina** : [@sabek13](https://github.com/sabek13)
- **Eddy** : [@isSpicyCode](https://github.com/isSpicyCode)

**Formation :** DWWM 2025 - Avignon

**Repository :** [GitHub - Le Studio](https://github.com/isSpicyCode/Le-studio---GYMS)

---

*Documentation générée le 2025 - Version 1.0*
