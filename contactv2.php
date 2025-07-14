<?php
session_start(); // démarre une session pour stocker des données côté serveur
// 🔐 Génération du token CSRF s’il n’existe pas déjà
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page = basename($_SERVER['SCRIPT_NAME'], '.php');
$pageTitle = 'LE STUDIO - Contact';
require_once 'includes/data.php';
$meta = $allMeta[$page] ?? [];
// Récupère les messages s'il y en a
$message_succes = $_SESSION['succes'] ?? '';
$message_erreur = $_SESSION['erreur'] ?? '';
$erreurs = $_SESSION['erreurs'] ?? [];
$old = ($message_succes) ? [] : ($_SESSION['old'] ?? []); // Si $message_succes existe, les champs sont vides. Sinon, les anciennes valeurs de la session sont utilisées.

// Efface les messages de la session
// unset($_SESSION['old'], $_SESSION['succes'], $_SESSION['erreur'], $_SESSION['erreurs']);
// var_dump($_SESSION);
require_once 'includes/header.php';
?>

<!-- ==========================================================================
     SECTION - Formulaire et Coordonnées
     Description : Contient le formulaire de contact et les coordonnées du studio
     ========================================================================== -->
<section class="contact-section">
  <!-- Formulaire -->
  <?php require_once 'includes/components/contact/contact-form.php'; ?>
  <?php unset($_SESSION['succes'], $_SESSION['erreur'], $_SESSION['erreurs'], $_SESSION['old']); ?>
  
  <!-- Coordonnées -->
  <?php require_once 'includes/components/contact/contact-coordonnees.php'; ?>
</section>

<?php
require_once 'includes/footer.php';
?>
