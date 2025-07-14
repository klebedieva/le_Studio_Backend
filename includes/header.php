<?php
include __DIR__ . '/../functions/functions.php';
include __DIR__ . '/data.php';
$meta = $allMeta[$page] ?? [];
// $_SERVER['SCRIPT_NAME'] - une variable superglobale qui contient le chemin du fichier actuellement exécuté par le serveur (/contact.php)
// La fonction basename() permet d’extraire le nom du fichier à partir d’un chemin complet (basename('/contact.php') => 'contact.php')
?>
<!DOCTYPE html>
<html lang="fr">
  <!-- ==========================================================================
       HEAD - Métadonnées, styles et scripts
       Description : Contient les métadonnées, liens CSS/JS et polices
       ========================================================================== -->
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <!-- Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    />
    <link rel="apple-touch-icon" href="/le_Studio_Backend/assets/img/favicon.png" />
    <link rel="icon" href="/le_Studio_Backend/assets/img/favicon.png" type="image/png" />

    <!-- Sélectionne la feuille de style à inclure en fonction de la page actuelle -->
    <?php
      switch ($page) {
        case 'indexv2':
          echo '<link rel="stylesheet" href="./css/style-accueil.css" />';
          break;
        case 'presentationv2':
          echo '<link rel="stylesheet" href="./css/style-presentation.css" />';
          break;
        case 'contactv2':
          echo '<link rel="stylesheet" href="./css/style-contact.css" />';
          break;
        case 'registration':  
          echo '<link rel="stylesheet" href="./css/style-registration.css" />';
          break;
          case 'login':  
          echo '<link rel="stylesheet" href="./css/style-login.css" />';
          break;
        case 'dashboard-client':  
          echo '<link rel="stylesheet" href="./css/style-dashboard.css" />';
          break;
        case 'blog':  
          echo '<link rel="stylesheet" href="./css/style-blog.css" />';
          break;
        case '404v2':  
          echo '<link rel="stylesheet" href="./css/style-404.css" />';
          break;
      }
    ?>
    <link rel="stylesheet" href="./css/style-header-footer.css" />
<?php
if (in_array($page, ['indexv2', 'contactv2', 'presentationv2', '404v2', 'blog', 'registration', 'login', 'dashboard-client'])) {
    if (isset($pageTitle)) {
        echo "<title>" . htmlspecialchars($pageTitle) . "</title>";
    }

    if (!empty($meta['metaDescription'])) {
        echo "<meta name=\"description\" content=\"" . htmlspecialchars($meta['metaDescription']) . "\">";
    }

    if (!empty($meta['og'])) {
        echo "<meta property=\"og:title\" content=\"" . htmlspecialchars($meta['og']['title']) . "\">";
        echo "<meta property=\"og:description\" content=\"" . htmlspecialchars($meta['og']['description']) . "\">";
        echo "<meta property=\"og:image\" content=\"" . htmlspecialchars($meta['og']['image']) . "\">";
        echo "<meta property=\"og:type\" content=\"" . htmlspecialchars($meta['og']['type']) . "\">";
    }

    if (!empty($meta['keywords'])) {
        echo "<meta name=\"keywords\" content=\"" . htmlspecialchars($meta['keywords']) . "\">";
    }

    if (!empty($meta['author'])) {
        echo "<meta name=\"author\" content=\"" . htmlspecialchars($meta['author']) . "\">";
    }
}
?>
</head>
  <body>
    <!-- ==========================================================================
         HEADER - Navigation et Carrousel
         Description : Contient la barre de navigation et le carrousel principal
         ========================================================================== -->
    <header>
      <!-- Navigation -->
      <?php echo render_navbar($main_menu, $activites_menu, $socialLinks); ?>
      <?php include 'includes/components/sliders/slider-index.php' ?>

      <!-- Affiche une image de fond spécifique pour la page "Présentation" -->
      <?php if ($page === 'presentationv2') : ?>
        <div class="position-relative">
          <div class="w-100 position-relative">
            <div class="position-absolute top-0 start-0 w-100 h-100 overlay-banner"></div>
            <img
              src="./assets/img/visuel/visuel_3.jpg"
              alt="Training Fonctionnel"
              class="img-fluid w-100 image-banner"
            />
          </div>
        </div>
      <?php endif; ?>
      
      <!-- Affiche une image de fond spécifique pour la page "Contact" -->
      <?php if ($page === 'contactv2') : ?>
        <div class="position-relative">
          <div class="banner-img">
            <div
              class="position-absolute top-0 start-0 w-100 h-100"
              style="background-color: #000000; opacity: 0.6"
            ></div>
            <img
              src="./assets/img/visuel/header-contact.jpg"
              alt="Training Fonctionnel"
            />
          </div>
        </div>
      <?php endif; ?>

            <?php if ($page === 'registration') : ?>
        <div class="position-relative">
          <div class="banner-img">
            <div
              class="position-absolute top-0 start-0 w-100 h-100"
              style="background-color: #000000; opacity: 0.6"
            ></div>
            <img
              src="./assets/img/visuel/header-blog.jpg"
              alt=""
            />
          </div>
        </div>
      <?php endif; ?>

        <?php if ($page === 'login') : ?>
        <div class="position-relative">
          <div class="banner-img">
            <div
              class="position-absolute top-0 start-0 w-100 h-100"
              style="background-color: #000000; opacity: 0.4"
            ></div>
            <img
              src="./assets/img/visuel/header-martial.jpg"
              alt=""
            />
          </div>
        </div>
      <?php endif; ?>


        <?php if ($page === 'dashboard-client') : ?>
        <div class="position-relative">
          <div class="banner-img">
            <div
              class="position-absolute top-0 start-0 w-100 h-100"
              style="background-color: #000000; opacity: 0.4"
            ></div>
            <img
              src="./assets/img/visuel/dashboard-banner.jpg"
              alt=""
            />
          </div>
        </div>
      <?php endif; ?>

              <?php if ($page === 'blog') : ?>
        <div class="position-relative">
          <div class="banner-img">
            <div
              class="position-absolute top-0 start-0 w-100 h-100"
              style="background-color: #000000; opacity: 0.4"
            ></div>
            <img
              src="./assets/img/visuel/visuel_5.jpg"
              alt=""
            />
          </div>
        </div>
      <?php endif; ?>



     <?php if ($page === '404v2') : ?>
        <div class="overlay"></div>
        <img src="./assets/img/404.jpg" alt="404 Error Page Image" class="responsive-img" />
        <div class="message404 btn-404">
          <h1>404</h1>
          <h2>Page non trouvée</h2>
          <a href="./index.php">Retourner à l'accueil</a>
        </div>
      <?php endif; ?>
    </header>

    <main>
