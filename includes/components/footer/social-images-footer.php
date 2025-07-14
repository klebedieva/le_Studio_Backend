 <?php include 'includes/data.php'; ?>
 <div class="container-fluid p-0">
        <div class="row g-0 align-items-end position-relative">
            <?php foreach ($socialImages as $socialImage): ?>
          <div class="col-xl-2 col-lg-4 col-sm-6 col-12 image-wrapper">
            <img
              src="<?= $socialImage['image'] ?>"
              alt="<?= $socialImage['alt'] ?>"
              class="images box-img-insta"
            />
            <span class="box-hover-insta">
              <i class="fa-solid fa-comment" style="color: #ffffff"><?= $socialImage['comments'] ?></i>
              <i class="fa-solid fa-heart" style="color: #ffffff"><?= $socialImage['likes'] ?></i>
            </span>
          </div>
          <?php endforeach; ?>
          <a
            href="<?= $instaButton['link'] ?>"
            aria-label="<?= $instaButton['aria-label'] ?>"
            target="_blank"
            class="btn-insta position-absolute top-0 start-50 translate-middle bg-white fw-bold text-decoration-none text-black text-center z-1"
          >
            <?= $instaButton['text'] ?>
          </a>
        </div>
</div>