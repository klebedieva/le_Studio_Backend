<?php
session_start();
$page = basename($_SERVER['SCRIPT_NAME'], '.php');
$pageTitle = 'Créer un compte';
require_once 'includes/data.php';
require_once 'includes/header.php';
$erreurs = $_SESSION['erreurs'] ?? [];
unset($_SESSION['erreurs']);

$succes = $_SESSION['succes'] ?? '';
unset($_SESSION['succes']);

// Génère un token CSRF si inexistant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<section class="container mt-5">
  <h2 class="text-center">Créer un compte</h2>
  <img class="mt-1 mb-5 d-block mx-auto" src="./assets/img/bg_titre.jpg" alt="barre sous-titre">

  <!-- Conteneur centré -->
  <div class="col-md-6 mx-auto">

    <!-- Message de succès -->
    <?php if ($succes): ?>
      <div class="alert alert-success mt-4"><?= $succes ?></div>
    <?php endif; ?>

    <!-- Messages d'erreur -->
    <?php foreach ($erreurs as $error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endforeach; ?>

    <!-- Formulaire -->
    <form action="actions/registration-handler.php" method="POST" class="mt-4 mb-5">
       <input 
    type="hidden" 
    name="csrf_token" 
    value="<?= $_SESSION['csrf_token'] ?>" 
    aria-hidden="true" 
    data-description="Jeton CSRF généré côté serveur" 
  />

      <div class="mb-3">
        <label for="nom" class="form-label">Nom</label>
        <input type="text" name="nom" id="nom" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="prenom" class="form-label">Prénom</label>
        <input type="text" name="prenom" id="prenom" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="mot_de_passe" class="form-label">Mot de passe</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
      </div>

      <div class="text-center">
        <button type="submit" class="register-button mt-4 mb-5">S'inscrire</button>
      </div>
    </form>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
