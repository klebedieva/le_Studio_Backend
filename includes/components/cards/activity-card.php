<div class="col-lg-3 col-md-6 col-sm-6 col-12 position-relative">
  <img 
    src="<?= $activity['image']; ?>" 
    alt="<?= ucwords(strtolower($activity['title']));?>" 
    class="images w-100" 
  />
  <div class="overlay d-flex flex-column align-items-center justify-content-center">
    <img 
      src="<?= $activity['icon'] ?>" 
      alt="Icône <?=  ucwords(strtolower($activity['title'])); ?>" 
    />
    <h5 class="text-white mb-2"><?= $activity['title'] ?></h5>
    <p class="fw-light text-center"><?= $activity['description'] ?></p>
    <a 
      href="<?= $activity['link'] ?>" 
      target="_blank" 
      class="mt-3 text-white btn-border-w-responsive underline-anim"
    >
      EN SAVOIR PLUS
    </a>
  </div>
</div>