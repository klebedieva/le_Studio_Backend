    <!-- Formulaire de contact -->
    <div class="row justify-content-center">
    <div class="col-lg-8 contact-form px-md-3 px-4 order-1 order-lg-2">


        <!-- Messages de succès ou d'erreur -->
        <?php if ($message_succes): ?>
            <div class="alert alert-success mb-4"><?php echo $message_succes; ?></div>
        <?php endif; ?>
        
        <?php if ($message_erreur): ?>
            <div class="alert alert-danger"><?php echo $message_erreur; ?></div>
        <?php endif; ?>
      <h3 class="text-md-start text-center">FORMULAIRE DE CONTACT</h3>
      <img
        class="mt-1 mb-5 d-block mx-auto mx-md-0"
        src="./assets/img/bg_titre.jpg"
        alt="barre sous-titre"
      />

      <form method="POST" action="actions/contact-form-handler.php">
        <input 
          type="hidden" 
          name="csrf_token" 
          value="<?= $_SESSION['csrf_token'] ?>" 
          aria-hidden="true" 
          data-description="Jeton CSRF généré côté serveur"
        />

        <div class="row mb-3">
          <div class="col-md-6 mb-3 mb-md-0">
            <input
              type="text"
              class="form-control"
              value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
              placeholder="VOTRE NOM"
              id="nom"
              name="nom"
  
            />
            <?php if (!empty($erreurs['nom'])): ?>
        <div class="text-danger"><?= $erreurs['nom'] ?></div>
    <?php endif; ?>
          </div>
          <div class="col-md-6">
            <input
              type="text"
              class="form-control"
              value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
              placeholder="VOTRE PRÉNOM"
              id="prenom"
              name="prenom"

            />
             <?php if (!empty($erreurs['prenom'])): ?>
        <div class="text-danger"><?= $erreurs['prenom'] ?></div>
    <?php endif; ?>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6 mb-3 mb-md-0">
            <input
              type="tel"
              class="form-control"
              value="<?= htmlspecialchars($old['tel'] ?? '') ?>"
              placeholder="VOTRE TÉLÉPHONE"
              id="tel"
              name="tel"
            />
          <?php if (!empty($erreurs['tel'])): ?>
        <div class="text-danger"><?= $erreurs['tel'] ?></div>
    <?php endif; ?>
          </div>
          <div class="col-md-6">
            <input
              type="email"
              class="form-control"
              value="<?= htmlspecialchars($old['email'] ?? '') ?>"
              placeholder="VOTRE EMAIL"
              id="email"
              name="email"

            />
            <?php if (!empty($erreurs['email'])): ?>
        <?php foreach ($erreurs['email'] as $erreur): ?>
            <div class="text-danger"><?= $erreur ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
          </div>
        </div>

        <div class="mb-3">
          <input 
            type="text" 
            class="form-control" 
            value="<?= htmlspecialchars($old['sujet'] ?? '') ?>"
            placeholder="SUJET" 
            id="sujet"
            name="sujet"
          />
         <?php if (!empty($erreurs['sujet'])): ?>
        <div class="text-danger"><?= $erreurs['sujet'] ?></div>
    <?php endif; ?>
        </div>

        <div class="mb-3">
          <textarea
  class="form-control"
  rows="5"
  placeholder="VOTRE MESSAGE"
  id="msg"
  name="message"

><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
           <?php if (!empty($erreurs['message'])): ?>
        <div class="text-danger"><?= $erreurs['message'] ?></div>
    <?php endif; ?>
        </div>

        <div class="text-center text-md-start">
          <button type="submit" class="btn mb-5">ENVOYER</button>
        </div>
      </form>
      </div>