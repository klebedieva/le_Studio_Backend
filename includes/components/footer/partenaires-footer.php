 <?php include 'includes/data.php'; ?>
 
 <div class="section-partenaire text-center">
        <div class="container">
          <h2>STUDIO SPORT & COACHING, NOS PARTENAIRES</h2>
          <img src="./assets/img/bg_titre.jpg" alt="barre sous-titre" />
          <p class="my-5 text-color">
            En tant que membre du Studio Sport & Coaching, vous bénéficierez
            dans ces établissements d'avantages exclusifs.
            <a href="./404.php">Cliquez ici</a>
            pour en savoir plus.
          </p>
          <div class="row justify-content-center align-items-center mt-3">
            <?php foreach ($partners as $partner): ?>
            <div class="col-6 col-md-2 my-3">
              <img
                src="<?= $partner['image'] ?>"
                alt="<?= $partner['alt'] ?>"
                class="images img-fluid"
              />
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>