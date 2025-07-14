<!-- Carrousel Bootstrap des coachs généré dynamiquement avec PHP  -->
  <div class="float-slider">
    <div
      id="carouselExampleSlidesOnly"
      class="carousel slide"
      data-bs-ride="carousel"
    >
      <div class="carousel-inner">
        <?php foreach ($coachSlides as $index => $slide): ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <img
              src="<?= $slide['image'] ?>"
              class="d-block w-100"
              alt="<?= $slide['alt'] ?>"
            />
          </div>
        <?php endforeach; ?>
      </div>
      </div>
</div>