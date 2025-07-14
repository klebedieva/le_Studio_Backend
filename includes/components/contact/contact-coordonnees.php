<div class="col-lg-4 order-2 order-lg-1">
<h3 class="text-md-start text-center">NOS COORDONNÉES</h3>
<img class="mt-1 mb-5 d-block mx-auto mx-md-0" src="./assets/img/bg_titre.jpg" alt="barre sous-titre" />

<div class="row">
  <?php foreach ($coordonnees as $bloc): ?>
    <div class="col-12 col-sm-6 col-lg-12 mb-4 text-center text-lg-start ms-lg-4">
      <p class="mb-0"><strong><?= $bloc['titre'] ?></strong></p>
      <?php foreach ($bloc['lignes'] as $ligne): ?>
        <p class="mb-0"><?= $ligne ?></p>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>
</div>
