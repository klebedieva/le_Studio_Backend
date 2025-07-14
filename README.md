# Blog pour une salle de sport

Ce projet est une application web complète de blog pour une salle de sport, avec une interface publique (site) et une interface d’administration (back-office).

## Fonctionnalités principales

- Affichage des articles de blog sur le site public
- Création, modification et suppression d’articles via l’admin
- Gestion des images (upload, redimensionnement automatique, plusieurs formats)
- Gestion des tags (mots-clés)
- Éditeur de texte enrichi (TinyMCE) pour le contenu des articles
- Système de rôles (admin, modérateur)
- Sécurité avancée (CSRF, XSS, validation serveur, requêtes préparées)
- Design responsive (Bootstrap)

## Technologies utilisées

- PHP 8+
- MySQL (PDO)
- HTML5, CSS3, Bootstrap
- JavaScript, jQuery
- TinyMCE (éditeur WYSIWYG)
- GD Library (traitement d’images)
- Git

## Structure du projet

```
le_Studio_Backend/
├── admin/                # Back-office (gestion articles, utilisateurs, contacts)
│   └── articles/         # Scripts pour CRUD articles et upload images
│   └── users/            # Gestion des utilisateurs
│   └── contacts/         # Gestion des messages de contact
├── actions/              # Scripts pour login, logout, inscription, etc.
├── includes/             # Templates header/footer, composants réutilisables
├── functions/            # Fonctions PHP utilitaires (resizeImage, sécurité, etc.)
├── config/               # Configuration (connexion BDD)
├── uploads/              # Images uploadées (original, large, medium, thumb)
├── css/                  # Feuilles de style CSS
├── js/                   # Scripts JS (validation, config TinyMCE)
├── blog.php              # Page liste des articles (frontend)
├── article.php           # Page d’un article (frontend)
├── admin.php             # Page principale de l’admin
├── indexv2.php           # Page d’accueil
├── login.php, registration.php # Authentification
└── README.md             # Ce fichier
```

## Lancement du projet

1. **Cloner le dépôt**
2. **Configurer la base de données**
   - Importer le schéma SQL fourni (tables users, blog, blog_images, tags, blog_tags...)
   - Adapter les identifiants dans `config/database.php`
3. **Lancer le serveur local** (WAMP, XAMPP, MAMP ou PHP built-in)
4. **Accéder au site**
   - Interface publique : `http://localhost/le_Studio_Backend/indexv2.php`
   - Interface admin : `http://localhost/le_Studio_Backend/admin.php`

## Sécurité

- **CSRF** : Tous les formulaires sont protégés par un token unique
- **XSS** : Toutes les données affichées sont échappées avec `htmlspecialchars`
- **Validation serveur** : Tous les champs sont vérifiés côté serveur
- **Requêtes préparées** : Toutes les requêtes SQL utilisent PDO avec des paramètres
- **Gestion des rôles** : Seuls les admins/modérateurs peuvent gérer les articles
- **Upload sécurisé** : Vérification du type, extension, taille et dimensions des images
- **Sessions sécurisées** : ID de session régénéré, timeout, contrôle d’accès

## Fonctionnement des images

- Upload possible via le formulaire ou directement dans le texte (TinyMCE)
- Génération automatique de 4 formats :
  - original (pour téléchargement)
  - large (800x600, affichage principal)
  - medium (400x300, aperçu)
  - thumb (150x150, miniatures)
- Les images sont stockées dans `uploads/articles/` par format

## Fonctionnement de TinyMCE

- Permet de rédiger du contenu riche (gras, listes, liens, images...)
- Upload d’images intégré (drag & drop ou bouton)
- Les images insérées dans le texte sont uploadées via AJAX et redimensionnées
- Seules les balises HTML sûres sont autorisées dans le contenu

## Auteur

Projet réalisé par Mohammed Afis dans le cadre du module "Réalisation d’un blog pour une salle de sport" (2025).

---

Pour toute question ou amélioration, n’hésitez pas à ouvrir une issue ou à contacter l’auteur.
