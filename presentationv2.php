<?php
$page = basename($_SERVER['SCRIPT_NAME'], '.php');
$pageTitle = 'LE STUDIO - Présentation';
require_once 'includes/header.php';
?>

<!-- ==========================================================================
     SECTION 1 - Présentation du Training Fonctionnel
     Description : Contient la description et le carrousel des coachs
     ========================================================================== -->
<?php include 'includes/sections/training-fonctionnel-section.php' ?>

<!-- ==========================================================================
     SECTION 2 - Ateliers du Training Fonctionnel
     Description : Présentation des ateliers (TRX Core, Boxe, HIIT, TRX Fusion)
     ========================================================================== -->
<?php include 'includes/sections/ateliers-section.php' ?>

</main>

<?php
require_once 'includes/footer.php';
?>
