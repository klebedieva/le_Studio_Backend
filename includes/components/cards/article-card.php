<div class="col">
  <div class="article-card bg-white p-0 rounded-3 shadow-sm d-flex flex-column h-100">

    <!-- Image de l'article -->
    <div class="overflow-hidden rounded-top">
      <?php
        $imgFile = $article['images'][0] ?? null;
        if ($imgFile) {
          $base = pathinfo($imgFile, PATHINFO_FILENAME); // // Récupère le nom du fichier sans l’extension (ex: 'image01' à partir de 'image01.jpg')
          $ext = pathinfo($imgFile, PATHINFO_EXTENSION); // Récupère l’extension du fichier (ex: 'jpg' à partir de 'image01.jpg')
          $mediumPath = "/le_Studio_Backend/uploads/articles/medium/{$base}_medium.{$ext}";
        } else {
          $thumbPath = './assets/img/default.jpg';
        }
      ?>
      <img src="<?= htmlspecialchars($mediumPath) ?>"
           alt="<?= htmlspecialchars($article['title']) ?>"
           class="news-image">
    </div>

    <!-- Contenu de l'article -->
    <div class="p-4 d-flex flex-column flex-grow-1">

      <!-- Date de publication -->
      <p class="text-muted mb-2">
        <i class="fas fa-calendar-alt me-1"></i>
        <?= htmlspecialchars($article['formatted_date']) ?>
      </p>

      <!-- Titre de l’article -->
      <h3 class="h5 fw-bold mb-2">
        <a href="article.php?id=<?= $article['id'] ?>"
           class="text-decoration-none text-dark">
          <?= htmlspecialchars($article['title']) ?>
        </a>
      </h3>

      <!-- Aperçu du contenu -->
      <p class="article-paragraph mb-3">
        <!-- /Affiche un extrait du contenu sans balises HTML, limité à 280 caractères avec "..." à la fin si trop long -->
        <?= mb_strimwidth(strip_tags($article['content']), 0, 280, '...') ?>
      </p>

      <!--  Auteur et lien -->
      <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
        <span class="text-muted small">Par <?= htmlspecialchars($article['author']) ?></span>
        <a href="article.php?id=<?= $article['id'] ?>"
           class="btn btn-sm btn-outline-dark">LIRE LA SUITE</a>
      </div>

    </div>
  </div>
</div>
