<?php
// Définir le nom de la page et le titre de l’onglet
$page = basename($_SERVER['SCRIPT_NAME'], '.php');
$pageTitle = "LE STUDIO - BLOG";

// Inclure l’en-tête et les données du blog
require_once 'includes/header.php';
require_once 'includes/data-blog.php';
?>

<!-- SECTION ARTICLES -->
<section id="articles">
  <div class="container mb-4">

    <!-- Titre principal -->
    <h2 class="text-center mb-2">NOS ARTICLES</h2>

    <!-- Barre décorative -->
    <div class="text-center">
      <img src="./assets/img/bg_titre.jpg" alt="séparateur" class="mx-auto d-block" style="height: 4px;">
    </div>

    <!-- Sous-titre -->
    <p class="text-center section-subtitle mt-5">
      Découvrez nos conseils d'experts, actualités et guides pour optimiser votre entraînement et atteindre vos objectifs fitness.
    </p>

    <!-- Formulaire de recherche -->
    <form method="GET" class="mt-4 mb-5 d-flex justify-content-center">
      <input type="text" name="q" class="form-control w-50 me-2"
             placeholder="Rechercher un mot-clé ou un tag..."
             value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
      <button type="submit" class="btn btn-dark">Rechercher</button>
    </form>

    <!-- Grille des articles -->
    <div class="mt-4 row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

      <?php if (empty($articles)): ?>
        <!-- Message si aucun résultat trouvé -->
        <p class="text-center text-muted">Aucun article ne correspond à votre recherche.</p>
      <?php else: ?>
        <?php foreach ($articles as $article): ?>
  <?php include 'includes/components/cards/article-card.php'; ?>
<?php endforeach; ?>
      <?php endif; ?>
      
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
