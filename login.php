
<?php
session_start();
// Génère un token CSRF si inexistant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


$page = basename($_SERVER['SCRIPT_NAME'], '.php');
$pageTitle = 'Connexion';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/header.php';

$erreurs = $_SESSION['erreurs'] ?? [];
unset($_SESSION['erreurs']);

$succes = $_SESSION['succes'] ?? '';
unset($_SESSION['succes']);

?>

<section class="container mt-5">
  <h2 class="text-center">Se connecter</h2>
  <img class="mt-1 mb-5 d-block mx-auto" src="/le_Studio_Backend/assets/img/bg_titre.jpg" alt="barre sous-titre">

  <div class="col-md-6 mx-auto">

    <?php if ($succes): ?>
      <div id="message-success" class="alert alert-success mt-4 text-center"><?= $succes ?></div>
    <?php endif; ?>

    <?php foreach ($erreurs as $error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endforeach; ?>

    <form action="actions/login-handler.php" method="POST" class="mt-4 mb-5">
      <!-- CSRF token -->
      <input 
        type="hidden" 
        name="csrf_token" 
        value="<?= $_SESSION['csrf_token'] ?>" 
        aria-hidden="true"
      />

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="mot_de_passe" class="form-label">Mot de passe</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
      </div>

      <div class="text-center">
        <button type="submit" class="login-button mt-4 mb-5">Se connecter</button>
      </div>
    </form>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
