<?php
function makeMenuLink($text, $url = '404.php') {
    return '<li><span class="me-1">–</span><a href="' . $url . '" class="footer-link">' . htmlspecialchars($text) . '</a></li>'; // htmlspecialchars() transforme les caractères spéciaux en texte lisible (Exemple : < devient &lt; pour éviter l'affichage ou l'exécution de code HTML)
}

function makeTextLink($text, $url = '404.php') {
    return '<a href="' . $url . '" class="footer-link">' . htmlspecialchars($text) . '</a>'; 
}

function nav_item(string $page, string $label): string {
    return '<li class="nav-item hover-menu ' . active_link($page) . '">
                <a class="nav-link" href="' . $page . '">' . $label . '</a>
            </li>';
}

function active_link(string $page): string {
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $page ? 'active' : '';
}


function dropdown_nav_item(string $label, array $sub_items): string {
    $menu = '<li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle hover-menu"
                   href="#"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">' . $label . '</a>
                <ul class="dropdown-menu bg-black z-3 px-2">';

    foreach ($sub_items as $item) {
        $menu .= '<li>
                    <a class="dropdown-item text-white hover-dropdown ps-3-responsive"
                       href="' . $item['url'] . '">' . $item['label'] . '</a>
                  </li>';
    }

    $menu .= '</ul></li>';
    return $menu;
}

function render_navbar(array $main_menu, array $activites_menu, array $socialLinks): string { // дe mot-clé array sert à indiquer que chaque argument doit être un tableau
    ob_start(); // permet de capturer le contenu HTML généré par le code PHP sans l'afficher tout de suite. Le contenu est enregistré dans un tampon (buffer). Ensuite, on peut le récupérer sous forme de chaîne avec ob_get_clean();
    ?>
    <nav class="navbar navbar-expand-lg bg-transparent position-absolute z-2 w-100">
        <div class="container-fluid menu-logo p-0">
            <a class="navbar-brand m-0" href="./indexv2.php">
                <img src="./assets/img/logo.png" class="logo" alt="Logo Le studio" />
            </a>

            <button
                class="navbar-toggler me-3 custom-toggler d-flex align-items-center gap-2 font-oswald"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="language-badge-inside">
                    <span class="language-text">EN</span>
                </span>
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                <ul class="navbar-nav color-menu font-oswald">
                    <?php
                    // 1. L'EQUIPE
                    if (!empty($main_menu)) {
                        echo nav_item($main_menu[0]['url'], $main_menu[0]['label']); // Vérifie si le tableau $main_menu n’est pas vide. Si ce tableau contient au moins un élément, on affiche le premier lien de menu avec la fonction nav_item().
                    }

                    // 2. LES ACTIVITÉS drop-down
                    echo dropdown_nav_item('LES ACTIVITÉS', $activites_menu); // Affiche un élément de menu déroulant avec le titre 'LES ACTIVITÉS', en utilisant les données contenues dans le tableau $activites_menu.

                    // 3. Other menu items
                    for ($i = 1; $i < count($main_menu); $i++) {
                        echo nav_item($main_menu[$i]['url'], $main_menu[$i]['label']); // Parcourt le tableau $main_menu à partir du 2e élément (index 1) et affiche chaque lien avec la fonction nav_item().
                    }
                    ?>

                    <li class="nav-item">
  <a class="nav-link" href="<?= isset($_SESSION['user']) ? 'actions/logout.php' : 'login.php' ?>">
    <?= isset($_SESSION['user']) ? 'SE DÉCONNECTER' : 'SE CONNECTER' ?>
  </a>
</li>
           

                    <li class="nav-item d-flex align-items-center mx-lg-1">
                        <a class="nav-link d-flex align-items-center justify-content-center gap-2 me-2" href="#">
                            <i class="bi bi-phone"></i> 05.59.47.84.18
                        </a>
                    </li>

                    <li class="nav-item d-flex justify-content-center p-0 align-items-center gap-1">
                        <?php foreach ($socialLinks as $link): ?>
                         <!-- Génère une icône de réseau social avec son lien, sa classe CSS et son label accessible -->
                            <a
                                href="<?= $link['url'] ?>"
                                class="<?= $link['class'] ?>"
                                aria-label="<?= $link['aria'] ?>"
                                target="_blank"
                            ></a>
                        <?php endforeach; ?>
                    </li>

                    <li class="language-badge d-none d-lg-block mx-3">
                        <a href="#" class="language-text" aria-label="Changer la langue en anglais">EN</a>
                    </li>
     </ul>
            </div>
        </div>
    </nav>
    <?php
    return ob_get_clean(); // Retourne le contenu HTML capturé sous forme de chaîne
}


// Affiche un message de succès s’il existe dans la session
function afficherMessageSucces() {
    if (!empty($_SESSION['succes'])) {
        echo '<div class="alert alert-success mb-4">' . htmlspecialchars($_SESSION['succes']) . '</div>'; // Affiche le message dans une alerte Bootstrap de type "success"
        unset($_SESSION['succes']); // Affiche le message dans une alerte Bootstrap de type "success"
    }
}

// Affiche un message d’erreur s’il existe dans la session
function afficherMessageErreur() {
    if (!empty($_SESSION['erreur'])) {
        echo '<div class="alert alert-danger mb-4">' . htmlspecialchars($_SESSION['erreur']) . '</div>'; // Affiche le message dans une alerte Bootstrap de type "danger"
        unset($_SESSION['erreur']); // Supprime le message de la session pour qu’il ne s’affiche qu’une seule fois
    }
}

function getServicesParAbonnement($services, $abonnements, $type_abonnement) {
    $resultat = [];

    // Vérifie si l’abonnement existe dans la liste
    if (!isset($abonnements[$type_abonnement])) {
        return $resultat;
    }

    // Liste des IDs de services autorisés pour cet abonnement
    $services_autorises = $abonnements[$type_abonnement];

    // Parcourt tous les services
    foreach ($services as $service) {
        // Si le service est autorisé, on l'ajoute dans le résultat
        if (in_array($service['id'], $services_autorises)) {
            $resultat[] = $service;
        }
    }

    return $resultat;
}


// Fonction resizeImage : redimensionner une image sans déformer (en gardant les proportions)
function resizeImage($sourcePath, $destPath, $newWidth, $newHeight) {
    // Récupère les informations sur l'image (dimensions + type MIME)
    $info = getimagesize($sourcePath);
    $mime = $info['mime']; // Exemple : 'image/jpeg', 'image/png', etc.

    // 🖼️ Ouvre l'image selon son format (JPEG, PNG ou WEBP)
    switch ($mime) {
        case 'image/jpeg':
            $srcImage = imagecreatefromjpeg($sourcePath); // Ouvre une image JPEG
            break;
        case 'image/png':
            $srcImage = imagecreatefrompng($sourcePath); // Ouvre une image PNG
            break;
        case 'image/webp':
            $srcImage = imagecreatefromwebp($sourcePath); // Ouvre une image WEBP
            break;
        default:
            return false; // ❌ Format non supporté, on arrête ici
    }

    // 🔢 On récupère les dimensions d'origine de l'image
    $width = imagesx($srcImage);   // Largeur originale
    $height = imagesy($srcImage);  // Hauteur originale

    // 📐 Calcul du ratio pour redimensionner sans déformer
    $ratio = min($newWidth / $width, $newHeight / $height); // On garde le plus petit ratio
    $finalWidth = (int)($width * $ratio);   // Nouvelle largeur en respectant les proportions
    $finalHeight = (int)($height * $ratio); // Nouvelle hauteur en respectant les proportions

    // 📄 On crée une nouvelle image vide (de la taille finale)
    $resized = imagecreatetruecolor($finalWidth, $finalHeight);

    // 🎨 Si c’est un PNG, on garde la transparence
    if ($mime === 'image/png') {
        imagealphablending($resized, false); // Désactive le mélange de couleurs
        imagesavealpha($resized, true);      // Active la transparence
    }

    // 🧩 Copie et redimensionne l’image d’origine vers la nouvelle image vide
    imagecopyresampled(
        $resized, $srcImage,
        0, 0, 0, 0,
        $finalWidth, $finalHeight,
        $width, $height
    );

    // 💾 Enregistre l’image redimensionnée dans le bon format
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($resized, $destPath, 90); // Qualité 90%
            break;
        case 'image/png':
            imagepng($resized, $destPath);      // PNG sans perte
            break;
        case 'image/webp':
            imagewebp($resized, $destPath, 90); // WEBP qualité 90%
            break;
    }

    // 🧽 Libère la mémoire utilisée par les images
    imagedestroy($srcImage);   // Libère l’image source
    imagedestroy($resized);    // Libère l’image redimensionnée

    return true; // ✅ Succès : image redimensionnée et sauvegardée
}

// Vérifie la validité d'un fichier image uploadé (type, taille, extension, dimensions)
function validateImageUpload($tmpName, $fileName) {
    // Types MIME autorisés
    $allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    // Extensions autorisées
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    // Taille maximale autorisée (5 Mo)
    $maxFileSize       = 5 * 1024 * 1024;

    // Détecte le vrai type MIME du fichier (basé sur son contenu)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    // Vérifie si le type MIME détecté est autorisé
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return "Format non supporté.";
    }
    // Vérifie la taille du fichier
    if (filesize($tmpName) > $maxFileSize) {
        return "Fichier trop volumineux (max 5 Mo).";
    }
    // Vérifie l'extension du fichier
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        return "Extension non autorisée.";
    }
    // Vérifie les dimensions de l'image
    $imageInfo = getimagesize($tmpName);
    if (!$imageInfo) {
        return "Impossible de lire les dimensions de l'image.";
    }
    if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
        return "Image trop petite (minimum 100x100px).";
    }
    // Retourne les informations nécessaires pour le traitement ultérieur
    return [
        'mime' => $mimeType,
        'extension' => $ext,
        'width' => $imageInfo[0],
        'height' => $imageInfo[1]
    ];
}
