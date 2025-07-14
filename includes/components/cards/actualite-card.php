<div class="col-md-4">
  <div class="card border-0 h-100">

    <!-- 🖼️ Image de l’article -->
    <?php
      // ✅ Récupération de la première image associée à l'article
      $imgFile = $article['images'][0] ?? null;

      // ✅ Construction du chemin vers l’image au format "medium"
      if ($imgFile) {
        $base = pathinfo($imgFile, PATHINFO_FILENAME); // nom de fichier sans extension
        $ext = pathinfo($imgFile, PATHINFO_EXTENSION); // extension du fichier
        $mediumPath = "/le_Studio_Backend/uploads/articles/medium/{$base}_medium.{$ext}";
      } else {
        // ✅ Image par défaut si aucune image n’est trouvée
        $mediumPath = './assets/img/default.jpg';
      }
    ?>

    <!-- ✅ Image de couverture -->
    <img
      src="<?= htmlspecialchars($mediumPath) ?>"
      alt="<?= htmlspecialchars($article['title']) ?>"
      class="news-image"
    />

    <!-- 📝 Contenu de la carte -->
    <div class="d-flex flex-column flex-grow-1">
      
      <!-- 📅 Date de publication -->
      <p class="text-muted mb-2 mt-3"><?= htmlspecialchars($article['formatted_date']) ?></p>

      <!-- 🔹 Titre de l’article -->
      <h5 class="fw-bold mb-2"><?= htmlspecialchars($article['title']) ?></h5>

      <!-- 🧾 Extrait de l’article (limité à 180 caractères) -->
      <p class="mb-3">
        <?= mb_strimwidth(strip_tags($article['content']), 0, 180, '...') ?>
      </p>

      <!-- 🔗 Lien vers l’article complet -->
      <a href="article.php?id=<?= $article['id'] ?>"
         class="btn-border-b-responsive anim-none-responsive mt-auto style-link">
        LIRE LA SUITE
      </a>
    </div>
  </div>
</div>
