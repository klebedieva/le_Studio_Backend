<?php
$page = basename($_SERVER['SCRIPT_NAME'], '.php');
$pageTitle = "LE STUDIO - Accueil";
require_once 'includes/header.php';
?>
    <!-- ==========================================================================
         SECTION 1 - Introduction
         Description : Présentation du Studio Sport & Coaching
         ========================================================================== -->
    <section class="container my-5">
      <h2 class="mt-5 font-oswald max">
        LE STUDIO SPORT & COACHING, SALLE DE SPORT, FITNESS ET CROSSFIT À
        BIARRITZ
      </h2>
      <div class="container text-center mt-3">
        <img src="./assets/img/bg_titre.jpg" class="mx-auto d-block" alt="barre sous-titre" />
      </div>
      <p class="mt-4 text-start">
        Aujourd'hui beaucoup de salles de sport v Snip vendent des abonnements
        où vous avez accès à tous les services du club (cours collectifs,
        plateau musculation...) mais combien vous connaissent au point de
        connaître vos objectifs et de savoir si vous êtes en bonne voie pour les
        atteindre ?
      </p>
      <p class="mt-3 text-start">
        Beaucoup de gens estiment être livrés à eux-mêmes dans ce genre de
        salle. Nous avons choisi la direction diamétralement opposée ! Notre
        seule priorité ? La qualité du Service. Notre nouveau concept de salle
        de sport SUR MESURE trouve une solution adaptée à votre budget et vos
        disponibilités.
      </p>
      <p class="mt-3 text-start">
        Vous voulez plus de Motivation, plus de Résultats, plus vite...
        <span class="fw-bold"
          >Alors bienvenue au Studio Sport & Coaching !</span
        >
      </p>
    </section>

    <!-- ==========================================================================
         SECTION 2 - Activités
         Description : Présentation des activités proposées par le studio
         ========================================================================== -->
<?php include 'includes/sections/activities-section.php'; ?>


<!-- ==========================================================================
     SECTION 3 - Actualités
     Description : Affichage des dernières actualités du studio
     ========================================================================== -->
 <?php include 'includes/sections/actualites-section.php'; ?>
</main>

<?php
require_once 'includes/footer.php';
?>
