<?php
session_start();
ini_set('memory_limit', '512M'); // Augmente la mémoire allouée à PHP pour éviter les erreurs de mémoire insuffisante
require_once __DIR__ . '/../../config/database.php'; // Connexion à la base de données
require_once __DIR__ . '/../../functions/functions.php'; // Fonctions utilitaires

// Vérifie que l'utilisateur est bien connecté et a un rôle
if (!isset($_SESSION['user']['id_user'], $_SESSION['user']['role'])) {
    $_SESSION['error'] = "Accès non autorisé.";
    header("Location: ../../admin.php?tab=articles");
    exit;
}

// Vérifie si le rôle de l'utilisateur est autorisé à modifier un article
$roles_autorises = ['Administrateur', 'Modérateur'];
if (!in_array($_SESSION['user']['role'], $roles_autorises)) {
    $_SESSION['error'] = "Vous n'avez pas les droits pour modifier un article.";
    header("Location: ../../admin.php?tab=articles");
    exit;
}

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du nombre maximum de fichiers autorisés (AVANT le CSRF)
    $maxFiles = 5;
    $fileCount = is_array($_FILES['images']['name']) ? count($_FILES['images']['name']) : 1;
    if ($fileCount > $maxFiles) {
        $_SESSION['error'] = "Maximum $maxFiles fichiers autorisés. Vous avez tenté d'uploader $fileCount fichiers.";
    } else {
        // Vérification du token CSRF (hash_equals compare deux chaînes de caractères (par exemple, deux tokens) de manière sécurisée, sans donner d'indice sur la longueur ou les caractères)
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['error'] = "Token de sécurité invalide. Veuillez réessayer.";
        } else {
            // Récupération des données du formulaire
            $id           = (int)$_POST['id_article'];
            $title        = trim($_POST['title'] ?? '');
            $content      = trim($_POST['content'] ?? '');
            $is_published = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;

            // Vérifie que le titre ne dépasse pas 200 caractères
            if (mb_strlen($title) > 200) {
                $_SESSION['error'] = "Le titre ne peut pas dépasser 200 caractères.";
                header("Location: ../../admin.php?tab=articles");
                exit;
            }

            // Vérifie que le titre et le contenu ne sont pas vides
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
                // --- УДАЛЕНИЕ ИЗОБРАЖЕНИЙ, УДАЛЁННЫХ ИЗ КОНТЕНТА (TINY MCE) ---
                // Получаем старый контент из базы
                $stmtOldContent = $pdo->prepare("SELECT content FROM blog WHERE id = ?");
                $stmtOldContent->execute([$id]);
                $oldContent = $stmtOldContent->fetchColumn();
                // Ищем все картинки в старом и новом контенте
                $oldImages = [];
                $newImages = [];
                if ($oldContent) {
                    preg_match_all('/<img[^>]+src="([^"]+)"/', $oldContent, $oldMatches);
                    $oldImages = $oldMatches[1] ?? [];
                }
                if ($content) {
                    preg_match_all('/<img[^>]+src="([^"]+)"/', $content, $newMatches);
                    $newImages = $newMatches[1] ?? [];
                }
                // Определяем, какие картинки были удалены
                $deletedImages = array_diff($oldImages, $newImages);
                foreach ($deletedImages as $imgUrl) {
                    $relativePath = parse_url($imgUrl, PHP_URL_PATH);
                    $cleanedPath = str_replace('/le_Studio_Backend', '', $relativePath);
                    $cleanedPath = ltrim($cleanedPath, '/');
                    $filePath = __DIR__ . '/../../' . $cleanedPath;
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
                // --- КОНЕЦ БЛОКА УДАЛЕНИЯ ---

                // Mise à jour de l'article dans la base de données
                $stmt = $pdo->prepare("
                    UPDATE blog 
                    SET title = :title, content = :content, is_published = :published, updated_at = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([
                    'title'     => $title,
                    'content'   => $content,
                    'published' => $is_published,
                    'id'        => $id
                ]);

                // Récupération des noms de fichiers associés à l'article (dans blog_images)
                $uploadDir   = __DIR__ . '/../../uploads/articles/';
                $originalDir = $uploadDir . 'original/';
                $thumbDir    = $uploadDir . 'thumb/';
                $mediumDir   = $uploadDir . 'medium/';
                $largeDir    = $uploadDir . 'large/';

                // Suppression des images supprimées par l'utilisateur
                $removedImages = json_decode($_POST['removed_images'] ?? '[]', true); // Décodage du JSON des images supprimées
                if (is_array($removedImages)) {
                    foreach ($removedImages as $filename) {
                        $base = pathinfo($filename, PATHINFO_FILENAME);
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        @unlink($originalDir . $base . '_original.' . $ext);
                        @unlink($thumbDir . $base . "_thumb." . $ext);
                        @unlink($mediumDir . $base . "_medium." . $ext);
                        @unlink($largeDir . $base . "_large." . $ext);

                        $stmt = $pdo->prepare("DELETE FROM blog_images WHERE blog_id = ? AND filename = ?");
                        $stmt->execute([$id, $filename]);
                    }
                }

                // Upload de nouvelles images (upload manuel)
                if (!empty($_FILES['images']['name'][0])) {
                    // --- БЛОК УДАЛЕНИЯ ОРИГИНАЛОВ ПРИ ЗАМЕНЕ УДАЛЁН ---
                    foreach ($_FILES['images']['tmp_name'] as $index => $tmpPath) {
                        $originalName = $_FILES['images']['name'][$index];
                        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                        // Types autorisés
                        $allowedMimeTypes   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                        $allowedExtensions  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                        $maxFileSize        = 5 * 1024 * 1024;

                        // Détecte le vrai type MIME et la taille du fichier
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $tmpPath);
                        finfo_close($finfo);

                        if (!in_array($mimeType, $allowedMimeTypes)) {
                            $_SESSION['error'] = "Le fichier « " . htmlspecialchars($originalName) . " » a un format non pris en charge.";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }
                        if (filesize($tmpPath) > $maxFileSize) {
                            $_SESSION['error'] = "Le fichier « " . htmlspecialchars($originalName) . " » dépasse la taille maximale autorisée (5 Mo).";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }
                        if (!in_array($extension, $allowedExtensions)) {
                            $_SESSION['error'] = "Le fichier « " . htmlspecialchars($originalName) . " » a une extension non autorisée.";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }
                        // Vérifie les dimensions de l'image
                        $imageInfo = getimagesize($tmpPath);
                        if ($imageInfo) {
                            $width = $imageInfo[0];
                            $height = $imageInfo[1];
                            if ($width < 100 || $height < 100) {
                                $_SESSION['error'] = "L'image « " . htmlspecialchars($originalName) . " » est trop petite (minimum 100x100px).";
                                header("Location: ../../admin.php?tab=articles");
                                exit;
                            }
                        } else {
                            $_SESSION['error'] = "Impossible de lire les dimensions de l'image « " . htmlspecialchars($originalName) . " ».";
                            header("Location: ../../admin.php?tab=articles");
                            exit;
                        }

                        // Génération d'un nom de base sécurisé et unique
                        $baseName = uniqid('img_');
                        $safeName = $baseName . '.' . $extension; // pour la base
                        $originalFile = $baseName . '_original.' . $extension; // pour le disque
                        $pathOriginal = $originalDir . $originalFile;
                        $pathThumb    = $thumbDir . $baseName . '_thumb.' . $extension;
                        $pathMedium   = $mediumDir . $baseName . '_medium.' . $extension;
                        $pathLarge    = $largeDir . $baseName . '_large.' . $extension;

                        move_uploaded_file($tmpPath, $pathOriginal);
                        resizeImage($pathOriginal, $pathThumb, 150, 150);
                        resizeImage($pathOriginal, $pathMedium, 400, 300);
                        resizeImage($pathOriginal, $pathLarge, 800, 600);

                        // Enregistrement du nom de fichier dans la base de données (sans _original)
                        $stmt = $pdo->prepare("INSERT INTO blog_images (blog_id, filename) VALUES (?, ?)");
                        $stmt->execute([$id, $safeName]);
                    }
                }

                // Confirmation de la mise à jour de l'article
                $_SESSION['success'] = "Article mis à jour avec succès.";
                unset($_SESSION['csrf_token']);

                // Mise à jour des tags associés à l'article
                if (isset($id)) {
                    // Suppression des anciens tags
                    $stmt = $pdo->prepare("DELETE FROM blog_tags WHERE blog_id = ?");
                    $stmt->execute([$id]);
                    // Ajout des nouveaux tags
                    if (!empty($_POST['tags']) && is_array($_POST['tags'])) {
                        $stmtTag = $pdo->prepare("INSERT INTO blog_tags (blog_id, tag_id) VALUES (?, ?)");
                        foreach ($_POST['tags'] as $tag_id) {
                            $stmtTag->execute([$id, $tag_id]);
                        }
                    }
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }
    }
    
    // Redirection vers la page d'administration
    header('Location: ../../admin.php?tab=articles');
    exit;
}
