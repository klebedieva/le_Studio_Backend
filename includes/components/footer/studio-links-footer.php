<?php include 'includes/data.php'; ?>
<div class="col-md-3 fw-light">
<h5 class="fw-bold m-b-30 m-t-20">STUDIO SPORT CORPORATE</h5>
<ul class="list-unstyled">
    <?php foreach ($corporateLinksText as $text): ?>
    <?php echo makeMenuLink($text); ?>
    <?php endforeach; ?>
</ul>
</div>