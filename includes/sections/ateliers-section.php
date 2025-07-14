<section class="py-5">
  <div class="container-md box-container">
    <h2 class="text-center fw-bold">
      LES ATELIERS PRÉSENTS DANS LE TRAINING FONCTIONNEL
    </h2>
    <img
      src="./assets/img/bg_titre.jpg"
      class="mx-auto d-block mb-5"
      alt="barre sous-titre"
    />

    <div class="row text-start">
      <?php foreach ($ateliers as $atelier): ?>
        <div class="col-xl-3 col-md-6 col-sm-6">
          <img
            src="<?= $atelier['image'] ?>"
            alt="<?= ucwords(strtolower($atelier['title'])); ?>"
            class="img-fluid mb-3"
          />
          <h3 class="fw-bold"><?= $atelier['title'] ?></h3>
          <p>
            <?= $atelier['description'] ?><br />
            <strong>Durée : <?= $atelier['duration'] ?></strong>
          </p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="mx-auto pt-5">
      <hr class="w-100 border-1" />
    </div>
  </div>
</section>
