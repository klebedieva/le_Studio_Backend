<!-- ==========================================================================
     FOOTER - Partenaires, Instagram et Informations
     Description : Contient les partenaires, les images Instagram et les informations du footer
     ========================================================================== -->
<footer>
    <?php if ($page === 'indexv2') : ?>
        <!-- Partenaires -->
        <?php include __DIR__ . '/components/footer/partenaires-footer.php'; ?>
    <?php endif; ?>

    <?php if ($page === 'presentationv2') : ?>
        <!-- Liste des activités -->
        <?php include __DIR__ . '/components/footer/activites-footer.php'; ?>
    <?php endif; ?>

    <?php if ($page === 'contactv2') : ?>
        <!-- Carte -->
        <?php include __DIR__ . '/components/footer/map-footer.php'; ?>
    <?php endif; ?>

    <!-- Instagram Images -->
    <?php include __DIR__ . '/components/footer/social-images-footer.php'; ?>

    <!-- Informations du footer -->
    <div class="footer text-white py-5 px-3">
        <div class="container-xl">
            <div class="row">
                <?php include __DIR__ . '/components/footer/a-propos-footer.php' ?>
                <?php include __DIR__ . '/components/footer/studio-links-footer.php' ?>
                <?php include __DIR__ . '/components/footer/salles-footer.php' ?>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/components/footer/copyright-footer.php' ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/le_Studio_Backend/js/validation.js"></script>
<?php if (isset($_SESSION['succes']) || isset($succes)) : ?>
  <script>
    setTimeout(function () {
      const message = document.getElementById('message-success');
      if (message) {
        message.style.transition = 'opacity 0.5s ease';
        message.style.opacity = '0';
        setTimeout(() => message.remove(), 500);
      }
    }, 3000);
  </script>
<?php endif; ?>

</body>
</html>
