<?php if ($page === 'indexv2') : ?>
        <!-- Carrousel -->
        <div
          id="carouselExampleCaptions"
          class="carousel slide position-relative z-1 font-oswald"
          data-bs-ride="carousel"
        >
          <!-- Indicateurs du carrousel -->
          <div class="carousel-indicators">
            <?php foreach ($slides as $index => $slide): ?>
              <!-- Crée un bouton en bas du carrousel pour changer de diapositive -->
              <button
                type="button"
                data-bs-target="#carouselExampleCaptions"
                data-bs-slide-to="<?= $index ?>" 
                class="round-btn <?= $index === 0 ? 'active' : '' ?>"
                <?= $index === 0 ? 'aria-current="true"' : '' ?>
                aria-label="Slide <?= $index + 1 ?>" 
              ></button>
            <?php endforeach; ?>
          </div>

          <!-- Génère automatiquement les diapositives du carrousel à partir d’un tableau PHP -->
          <div class="carousel-inner">
            <?php foreach ($slides as $index => $slide): ?>
              <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <img 
                  src="<?= $slide['image'] ?>" 
                  class="d-block w-100" 
                  alt="<?= $slide['alt'] ?>" 
                />
                <div class="carousel-caption d-block mb-3 text-white text-center">
                  <h2 class="mb-4"><?= $slide['title'] ?></h2>
                  <a href="<?= $slide['link'] ?>" class="btn btn-outline-light btn-slider-info">
                    PLUS D'INFOS
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>