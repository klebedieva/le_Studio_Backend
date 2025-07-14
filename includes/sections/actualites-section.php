<section class="py-5 mb-3" id="actualites">
  <?php 
    include 'includes/data-blog.php'; 
    // Récupération des 3 dernières actualités depuis les articles
    $actualites = array_slice($articles, 0, 3); 
  ?>
  <div class="container-lg mt-3">
    <h2 class="text-center">NOS DERNIÈRES ACTUALITÉS</h2>

    <div class="container text-center">
      <img src="./assets/img/bg_titre.jpg" class="mx-auto d-block" alt="barre sous-titre" />
    </div>

    <div class="row g-3 text-left mt-4">
      <?php foreach ($actualites as $article): ?>
        <?php include 'includes/components/cards/actualite-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
