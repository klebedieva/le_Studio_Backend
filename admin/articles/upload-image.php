<?php
// Gestionnaire global des erreurs fatales et des exceptions pour retourner du JSON
function send_json_error($msg) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['error' => $msg]);
  exit;
}

// Quand une exception non gérée survient : cette fonction renvoie un message d’erreur en JSON
set_exception_handler(function($e) {
  send_json_error('Erreur serveur: ' . $e->getMessage());
});
// Quand une erreur PHP non gérée survient : cette fonction renvoie un message d’erreur en JSON
set_error_handler(function($errno, $errstr) {
  send_json_error('Erreur serveur: ' . $errstr);
});

ob_clean(); // Nettoie le tampon de sortie pour éviter les erreurs d'en-tête
ini_set('display_errors', 0); // Désactive l'affichage des erreurs PHP
error_reporting(0); // Désactive tous les rapports d'erreurs
ini_set('memory_limit', '512M'); // Définit la limite de mémoire maximale que le script PHP peut utiliser à 512 mégaoctets, uniquement pendant l'exécution du script
header('Content-Type: application/json'); // Définit l'en-tête de la réponse HTTP pour indiquer que le contenu est au format JSON

// Vérifie si le fichier a bien été envoyé
if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Aucun fichier reçu.']);
  exit;
}

// Définir les types MIME autorisés
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

// Extensions de fichiers autorisées
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

// Taille maximale autorisée (5 Mo)
$maxFileSize = 5 * 1024 * 1024;

// Détecte le vrai type MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
finfo_close($finfo);

// Si le type MIME est interdit
if (!in_array($mime, $allowedMimeTypes)) {
  http_response_code(400);
  echo json_encode(['error' => 'Type de fichier non autorisé.']);
  exit;
}

// Récupère l'extension réelle du fichier
$originalName = basename($_FILES['file']['name']);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

// Protection contre les noms de fichiers dangereux (ex. : script.php.jpg)
if (preg_match('/\.(php|exe|sh|pl|cgi)$/i', $originalName)) {
  http_response_code(400);
  echo json_encode(['error' => 'Nom de fichier non autorisé.']);
  exit;
}

// Vérifie si l'extension est autorisée
if (!in_array($extension, $allowedExtensions)) {
  http_response_code(400);
  echo json_encode(['error' => 'Extension de fichier non autorisée.']);
  exit;
}

// Vérifie la taille du fichier
if ($_FILES['file']['size'] > $maxFileSize) {
  http_response_code(400);
  echo json_encode(['error' => 'Fichier trop volumineux (max 5 Mo).']);
  exit;
}

// Définir le répertoire de destination pour les images
$uploadDir = __DIR__ . '/../../uploads/articles/';
$uploadUrl = '/le_Studio_Backend/uploads/articles/';

// Crée le répertoire s'il n'existe pas
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

// Génère un nom de fichier unique pour éviter les collisions
// uniqid() génère un identifiant unique basé sur le temps actuel en microsecondes
$filename = uniqid('img_') . '.' . $extension;
$targetFile = $uploadDir . $filename;

// Déplace le fichier téléchargé vers le répertoire de destination
list($width, $height) = getimagesize($_FILES['file']['tmp_name']);

// Vérifie la taille minimale de l'image
if ($width < 100 || $height < 100) {
  http_response_code(400);
  echo json_encode(['error' => 'Image trop petite (minimum 100x100px).']);
  exit;
}

// Vérifie si les dimensions de l'image sont valides
$maxWidth = 800;
$maxHeight = 600;

// Calcule les nouvelles dimensions (si redimensionnement nécessaire)
$ratio = min($maxWidth / $width, $maxHeight / $height, 1);
$newWidth = (int)($width * $ratio);
$newHeight = (int)($height * $ratio);

// Crée une ressource image à partir du fichier selon le type
switch ($mime) {
  case 'image/jpeg':
    $src = imagecreatefromjpeg($_FILES['file']['tmp_name']);
    break;
  case 'image/png':
    $src = imagecreatefrompng($_FILES['file']['tmp_name']);
    break;
  case 'image/webp':
    $src = imagecreatefromwebp($_FILES['file']['tmp_name']);
    break;
  default:
    http_response_code(400);
    echo json_encode(['error' => 'Format non supporté.']);
    exit;
}

// Crée une nouvelle image vide avec les nouvelles dimensions
$dst = imagecreatetruecolor($newWidth, $newHeight);

// Gère la transparence pour PNG et WebP
if (in_array($mime, ['image/png', 'image/webp'])) {
  imagealphablending($dst, false);
  imagesavealpha($dst, true);
}

// Redimensionne l'image source dans la nouvelle image
// imagecopyresampled() permet de redimensionner l'image source dans la nouvelle image avec une interpolation de haute qualité
imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

// Enregistre l'image redimensionnée dans le fichier cible
// imagejpeg(), imagepng() et imagewebp() enregistrent l'image dans le format approprié
// Le paramètre de qualité/compression est optionnel et peut être ajusté pour optimiser la taille du fichier
$success = false;
switch ($mime) {
  case 'image/jpeg':
    $success = imagejpeg($dst, $targetFile, 85); // qualité 0-100
    break;
  case 'image/png':
    $success = imagepng($dst, $targetFile, 6); // compression 0-9
    break;
  case 'image/webp':
    $success = imagewebp($dst, $targetFile, 80); // qualité 0-100
    break;
}

// Libère la mémoire
imagedestroy($src);
imagedestroy($dst);

// Vérifie si l'image a été enregistrée avec succès
if ($success) {
  echo json_encode([
    'success' => true,
    'location' => str_replace('\\', '/', $uploadUrl . $filename)
  ], JSON_UNESCAPED_SLASHES);
  exit;
} else {
  send_json_error('Erreur lors de la sauvegarde de l\'image.');
}
