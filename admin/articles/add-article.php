<?php
session_start();

// Augmente la limite mémoire pour gérer les grandes images
ini_set('memory_limit', '512M');

require_once __DIR__ . '/../../config/database.php'; // Connexion à la base de données
require_once __DIR__ . '/../../functions/functions.php'; // Fonctions personnalisées

// Vérifie que l'utilisateur est bien connecté et a un rôle
if (!isset($_SESSION['user']['id_user'], $_SESSION['user']['role'])) {
    $_SESSION['error'] = "Accès non autorisé.";
    header("Location: ../../admin.php?tab=articles");
    exit;
}

// Vérifie si le rôle de l'utilisateur est autorisé à ajouter un article
$roles_autorises = ['Administrateur', 'Modérateur'];
if (!in_array($_SESSION['user']['role'], $roles_autorises)) {
    $_SESSION['error'] = "Vous n'avez pas les droits pour ajouter un article.";
    header("Location: ../../admin.php?tab=articles");
    exit;
}

// Traitement du formulaire après envoi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du nombre maximum de fichiers autorisés (AVANT le CSRF)
    $maxFiles = 5;
    $fileCount = is_array($_FILES['images']['name']) ? count($_FILES['images']['name']) : 1;
    if ($fileCount > $maxFiles) {
        $_SESSION['error'] = "Maximum $maxFiles fichiers autorisés. Vous avez tenté d'uploader $fileCount fichiers.";
    } else {
        // Vérification du token CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['error'] = "Token de sécurité invalide. Veuillez réessayer.";
        } else {
            // Récupération des données du formulaire
            $title        = trim($_POST['title'] ?? '');
            $content      = trim($_POST['content'] ?? '');
            $is_published = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;

            // Vérifie que le titre ne dépasse pas 200 caractères
            if (mb_strlen($title) > 200) {
                $_SESSION['error'] = "Le titre ne peut pas dépasser 200 caractères.";
                header("Location: ../../admin.php?tab=articles");
                exit;
            }

            // Vérifie que le titre et le contenu sont bien remplis
            if (empty($title) || empty($content)) {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires.";
                header("Location: ../../admin.php?tab=articles");
                exit;
            }

            // Vérifie que la valeur de publication est valide (0 ou 1)
            if (!in_array($is_published, [0, 1], true)) {
                $_SESSION['error'] = "Valeur de publication invalide.";
                header("Location: ../../admin.php?tab=articles");
                exit;
            }

            try {
                $author_id = $_SESSION['user']['id_user'];

                // Insère l'article dans la base de données
                $stmt = $pdo->prepare("
                    INSERT INTO blog (title, content, created_at, updated_at, author_id, is_published, views)
                    VALUES (:title, :content, NOW(), NULL, :author_id, :is_published, 0)
                ");
                $stmt->execute([
                    ':title'        => $title,
                    ':content'      => $content,
                    ':author_id'    => $author_id,
                    ':is_published' => $is_published
                ]);

                $blog_id = $pdo->lastInsertId(); // Récupère l'ID du nouvel article

                // Si des tags ont été sélectionnés dans le formulaire et que c’est bien un tableau
                if (!empty($_POST['tags']) && is_array($_POST['tags'])) {
                    // Prépare l'insertion dans la table de liaison blog_tags (relation article <-> tag)
                    $stmtTag = $pdo->prepare("INSERT INTO blog_tags (blog_id, tag_id) VALUES (?, ?)");
                    // Pour chaque tag sélectionné, insère un lien entre l'article et le tag
                    foreach ($_POST['tags'] as $tag_id) {
                        $stmtTag->execute([$blog_id, $tag_id]);
                    }
                }

                // Traitement des images uploadées via le champ input
                if (!empty($_FILES['images']['name'])) {
                    // Définition des dossiers de destination
                    $uploadDir    = __DIR__ . '/../../uploads/articles/';
                    $originalDir  = $uploadDir . 'original/';
                    $largeDir     = $uploadDir . 'large/';
                    $mediumDir    = $uploadDir . 'medium/';
                    $thumbDir     = $uploadDir . 'thumb/';

                    // Crée les dossiers s'ils n'existent pas
                    foreach ([$originalDir, $largeDir, $mediumDir, $thumbDir] as $dir) {
                        if (!file_exists($dir)) {
                            mkdir($dir, 0777, true);
                        }
                    }

                    // Traitement de chaque fichier envoyé
                    $fileNames = is_array($_FILES['images']['name']) ? $_FILES['images']['name'] : [$_FILES['images']['name']];
                    $tmpNames  = is_array($_FILES['images']['tmp_name']) ? $_FILES['images']['tmp_name'] : [$_FILES['images']['tmp_name']];
                    $errors    = is_array($_FILES['images']['error']) ? $_FILES['images']['error'] : [$_FILES['images']['error']];

                    // Parcourt chaque fichier temporaire envoyé via le formulaire
                    foreach ($tmpNames as $index => $tmpName) {
                        // Si une erreur est détectée pendant l’envoi du fichier
                        if ($errors[$index] !== UPLOAD_ERR_OK) {
                            // Enregistre un message d'erreur dans le journal du serveur (log)
                            error_log("Erreur de téléversement : " . $fileNames[$index]);
                            // Passe au fichier suivant sans continuer le traitement pour celui-ci
                            continue;
                        }

                        // Vérifie que le fichier n'est pas vide
                        if (empty($tmpName) || !file_exists($tmpName)) {
                            $_SESSION['error'] = "Le fichier « " . htmlspecialchars($fileNames[$index]) . " » est vide ou n'existe pas.";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }

                        // Nettoyage du nom de fichier
                        $baseName = pathinfo($fileNames[$index], PATHINFO_FILENAME);
                        $ext      = strtolower(pathinfo($fileNames[$index], PATHINFO_EXTENSION));
                        // Génère un nom de base de fichier sécurisé et unique
                        $safeBase = time() . '_' . preg_replace('/[^a-zA-Z0-9-_]/', '_', $baseName);

                        // Types autorisés
                        $allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                        $maxFileSize       = 5 * 1024 * 1024; // 5 Mo

                        // Détecte le vrai type MIME du fichier (basé sur son contenu, pas sur l’extension)
                        $finfo = finfo_open(FILEINFO_MIME_TYPE); // Ouvre un fichier pour obtenir son type MIME
                        $mimeType = finfo_file($finfo, $tmpName); // Obtient le type MIME du fichier temporaire
                        finfo_close($finfo);

                        // Vérifie si le type MIME détecté est dans la liste des types autorisés
                        if (!in_array($mimeType, $allowedMimeTypes)) {
                            $_SESSION['error'] = "Le fichier « " . htmlspecialchars($fileNames[$index]) . " » a un format non pris en charge.";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }

                        // Vérifie si le fichier dépasse la taille maximale autorisée (5 Mo)
                        if (filesize($tmpName) > $maxFileSize) {
                            $_SESSION['error'] = "Le fichier « " . htmlspecialchars($fileNames[$index]) . " » dépasse la taille maximale autorisée (5 Mo).";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }

                        // Vérifie si l'extension du fichier est autorisée
                        if (!in_array($ext, $allowedExtensions)) {
                            $_SESSION['error'] = "Le fichier « " . htmlspecialchars($fileNames[$index]) . " » a une extension non autorisée.";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }

                        // Vérifie les dimensions de l'image
                        $imageInfo = getimagesize($tmpName);
                        if ($imageInfo) {
                            $width = $imageInfo[0];
                            $height = $imageInfo[1];
                            // Vérifie que l'image a une taille minimale de 100x100 pixels
                            if ($width < 100 || $height < 100) {
                                $_SESSION['error'] = "L'image « " . htmlspecialchars($fileNames[$index]) . " » est trop petite (minimum 100x100px).";
                                header("Location: ../../admin.php?tab=articles");
                                exit;
                            }
                        } else {
                            $_SESSION['error'] = "Impossible de lire les dimensions de l'image « " . htmlspecialchars($fileNames[$index]) . " ».";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }

                        // Prépare les chemins de destination pour les différentes tailles d'image
                        $originalFile = $safeBase . '_original.' . $ext;
                        $largeFile    = $safeBase . '_large.' . $ext;
                        $mediumFile   = $safeBase . '_medium.' . $ext;
                        $thumbFile    = $safeBase . '_thumb.' . $ext;

                        $originalPath = $originalDir . $originalFile;
                        $largePath    = $largeDir . $largeFile;
                        $mediumPath   = $mediumDir . $mediumFile;
                        $thumbPath    = $thumbDir . $thumbFile;

                        // Déplace le fichier temporaire vers le dossier original
                        if (move_uploaded_file($tmpName, $originalPath)) {
                            resizeImage($originalPath, $largePath, 800, 600);
                            resizeImage($originalPath, $mediumPath, 400, 300);
                            resizeImage($originalPath, $thumbPath, 150, 150);

                            // Enregistre les informations de l'image dans la base de données (sans _original)
                            $stmt = $pdo->prepare("
                                INSERT INTO blog_images (blog_id, filename)
                                VALUES (:blog_id, :filename)
                            ");
                            $stmt->execute([
                                ':blog_id'  => $blog_id,
                                ':filename' => $safeBase . '.' . $ext
                            ]);
                        }
                    }
                }

                // Succès : message de confirmation
                $_SESSION['success'] = "Article ajouté avec succès.";
                unset($_SESSION['csrf_token']);

            } catch (PDOException $e) {
                // Gestion des erreurs PDO
                $_SESSION['error'] = "Erreur lors de l'ajout : " . $e->getMessage();
            }
        }
    }

    // Redirection finale
    header("Location: ../../admin.php?tab=articles");
    exit;
}
