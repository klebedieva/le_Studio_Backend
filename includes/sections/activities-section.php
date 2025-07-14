<section>
  <?php include 'includes/data.php'; ?>

  <div class="container-fluid p-0 overflow-hidden">
    <h2 class="text-center mt-1 max font-oswald">
      LES ACTIVITÉS PROPOSÉES AU STUDIO SPORT BIARRITZ
    </h2>

    <div class="container text-center mb-4 mt-3">
      <img src="./assets/img/bg_titre.jpg" class="mx-auto d-block" alt="barre sous-titre" />
    </div>

    <div class="row g-0">
      <?php foreach ($activities as $activity) : ?>
        <?php include 'includes/components/cards/activity-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>