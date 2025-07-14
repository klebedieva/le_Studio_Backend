<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: blog.php');
    exit;
}

require_once 'includes/data-blog.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($article['title']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="./css/style-article.css">
</head>
<body>
<div class="container article-container">

   <!-- &larr; - entité HTML qui affiche une flèche vers la gauche (left arrow) -->
  <a href="blog.php" class="btn btn-link mb-4">&larr; Retour aux articles</a>

  <?php if ($article): ?>
    <div class="article-card">
      <!-- Image principale ou carrousel -->
      <div class="article-image-wrapper">
        <?php if (count($images) > 1): ?>
          <div id="carouselArticle" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <?php foreach ($images as $index => $img): ?>
                <?php $base = pathinfo($img, PATHINFO_FILENAME); $ext = pathinfo($img, PATHINFO_EXTENSION); $largePath = "/le_Studio_Backend/uploads/articles/large/{$base}_large.{$ext}"; ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <img src="<?= htmlspecialchars($largePath) ?>" class="d-block w-100" alt="Image <?= $index + 1 ?>">
                </div>
              <?php endforeach; ?>
            </div>
            <div class="carousel-indicators">
              <?php foreach ($images as $index => $img): ?>
                <button type="button" data-bs-target="#carouselArticle" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-label="Image <?= $index + 1 ?>"></button>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else:
          $largePath = './assets/img/default.jpg';
          if (!empty($images[0])) {
            $base = pathinfo($images[0], PATHINFO_FILENAME);
            $ext = pathinfo($images[0], PATHINFO_EXTENSION);
            $largePath = "/le_Studio_Backend/uploads/articles/large/{$base}_large.{$ext}";
          }
        ?>
          <img src="<?= htmlspecialchars($largePath) ?>" alt="Image principale">
        <?php endif; ?>
      </div>

      <!-- Informations + contenu -->
      <h1><?= htmlspecialchars($article['title']) ?></h1>
      <div class="article-meta">
        <!-- strtotime() convertit la date (texte) en format timestamp utilisable par date()
        &middot; affiche un point séparateur (·) -->
        Publié le <?= date('d M Y', strtotime($article['created_at'])) ?> &middot; par <?= htmlspecialchars($article['author']) ?>
      </div>
      <div class="article-body">
        <?= $article['content'] ?>
      </div>

      <!-- Tags -->
      <?php if (!empty($tags)): ?>
        <div class="article-tags mt-4">
          <?php foreach ($tags as $tag): ?>
            <span class="tag"><?= htmlspecialchars($tag) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Articles similaires -->
    <?php if (!empty($relatedArticles)): ?>
      <div class="mt-5">
        <h3 class="mb-4">Articles similaires</h3>
        <div class="row row-cols-1 row-cols-md-4 g-4 mb-5">
          <?php foreach ($relatedArticles as $related): ?>
            <div class="col">
              <div class="card h-100 small-card">
                <?php
                  $img = $related['image'];
                  if ($img) {
                      $base = pathinfo($img, PATHINFO_FILENAME);
                      $ext = pathinfo($img, PATHINFO_EXTENSION);
                      $mediumPath = "/le_Studio_Backend/uploads/articles/medium/{$base}_medium.{$ext}";
                  } else {
                      $mediumPath = './assets/img/default.jpg';
                  }
                ?>
                <img src="<?= htmlspecialchars($mediumPath) ?>" class="card-img-top" alt="<?= htmlspecialchars($related['title']) ?>">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?= htmlspecialchars($related['title']) ?></h5>
                  <p class="card-text small text-muted mb-2">
                    Publié le <?= date('d M Y', strtotime($related['created_at'])) ?> &middot; par <?= htmlspecialchars($related['author']) ?>
                  </p>
                  <a href="article.php?id=<?= $related['id'] ?>" class="btn btn-outline-dark mt-auto">Lire la suite</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <div class="alert alert-warning mt-5">Article introuvable.</div>
  <?php endif; ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>