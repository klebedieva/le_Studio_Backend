<?php include 'includes/data.php'; ?>
<div class="col-md-6 fw-light">
    <h5 class="fw-bold m-b-30 m-t-20">
    FITNESS, CROSSFIT ET TRAINING AU PAYS BASQUE
    </h5>
    <p>
<!-- Récupérer le dernier élément du tableau pour éviter la virgule après lui -->
<?php $last = end($clubLinks); ?> 
<?php foreach ($clubLinks as $text): ?>
    <?= makeTextLink($text) ?>
<!-- Ajouter une virgule si ce n’est pas le dernier élément -->
    <?php if ($text !== $last): ?>, <?php endif; ?>
<?php endforeach; ?>
</p>
</div>