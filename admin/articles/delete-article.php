<?php
session_start();
require_once __DIR__ . '/../../config/database.php'; // Connexion à la base de données

// Vérifie que l'utilisateur est bien connecté et a un rôle
if (!isset($_SESSION['user']['id_user'], $_SESSION['user']['role'])) {
    $_SESSION['error'] = "Accès non autorisé.";
    header("Location: ../../admin.php?tab=articles");
    exit;
}
// Только администратор может удалять
if ($_SESSION['user']['role'] !== 'Administrateur') {
    $_SESSION['error'] = "Seul un administrateur peut supprimer des articles.";
    header("Location: ../../admin.php?tab=articles");
    exit;
}

// Vérifie que la requête est bien POST et que l'ID d'article est fourni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_article'])) {
    // Vérification du token CSRF (hash_equals compare deux chaînes de caractères (par exemple, deux tokens) de manière sécurisée, sans donner d'indice sur la longueur ou les caractères)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error'] = "Échec de la vérification CSRF.";
        header("Location: ../../admin.php?tab=articles");
        exit;
    }

    $articleId = (int)$_POST['id_article'];

    try {
        // Récupération du contenu de l'article (images insérées via TinyMCE)
        $stmt = $pdo->prepare("SELECT content FROM blog WHERE id = ?");
        $stmt->execute([$articleId]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article && !empty($article['content'])) {
            // Extraction des URLs des images insérées dans le contenu de l'article
            // preg_match_all() permet de trouver toutes les correspondances d'un motif dans une chaîne
            // Le regex extrait uniquement l’URL de chaque image (ce qu’il y a entre les guillemets
            // Toutes ces URL sont ensuite stockées dans $matches[]. 
            preg_match_all('/<img[^>]+src="([^">]+)"/', $article['content'], $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $imgUrl) {
                    $relativePath = parse_url($imgUrl, PHP_URL_PATH); // Extraire uniquement le chemin (path) de l’URL d’image, sans le domaine, les paramètres, etc. (https://monsite.com/le_Studio_backend/uploads/articles/photo.jpg -> /le_Studio_backend/uploads/articles/photo.jpg)

                    $cleanedPath = str_replace('/le_Studio_backend', '', $relativePath); // Suppression de la partie /le_Studio_backend (ex: /uploads/articles/photo.jpg)

                    $cleanedPath = ltrim($cleanedPath, '/'); // Nettoyage du slash initial pour construire le chemin absolu (uploads/articles/photo.jpg)

                    $filePath = __DIR__ . '/../../' . $cleanedPath; // Créer le chemin absolu du fichier sur le disque, à partir du dossier actuel (__DIR__)

                    // Si le fichier existe à cet endroit exact → supprimer l’image avec unlink() (@ évite les erreurs visibles si le fichier est déjà supprimé

                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }
        }

        // Récupération des noms de fichiers associés à l'article (dans blog_images)
        $stmt = $pdo->prepare("SELECT filename FROM blog_images WHERE blog_id = ?");
        $stmt->execute([$articleId]);
        $filenames = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Répertoires avec les différentes tailles d’image
        $originalDir = __DIR__ . '/../../uploads/articles/original/';
        $largeDir    = __DIR__ . '/../../uploads/articles/large/';
        $mediumDir   = __DIR__ . '/../../uploads/articles/medium/';
        $thumbDir    = __DIR__ . '/../../uploads/articles/thumb/';

        // Suppression de toutes les versions des images
        foreach ($filenames as $filename) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $ext  = pathinfo($filename, PATHINFO_EXTENSION);

            $paths = [
                $originalDir . $base . '_original.' . $ext,
                $largeDir    . $base . '_large.' . $ext,
                $mediumDir   . $base . '_medium.' . $ext,
                $thumbDir    . $base . '_thumb.' . $ext,
            ];

            foreach ($paths as $p) {
                if (file_exists($p)) {
                    @unlink($p);
                }
            }
        }

        // Suppression des entrées de la table blog_images
        $pdo->prepare("DELETE FROM blog_images WHERE blog_id = ?")->execute([$articleId]);

        // Suppression de l'article lui-même
        $pdo->prepare("DELETE FROM blog WHERE id = ?")->execute([$articleId]);

        // Suppression des tags associés à l'article
        if (isset($_POST['id_article'])) {
            $stmt = $pdo->prepare("DELETE FROM blog_tags WHERE blog_id = ?");
            $stmt->execute([$_POST['id_article']]);
        }

        // Confirmation + suppression du token CSRF
        $_SESSION['success'] = "L'article a été supprimé avec toutes ses images.";
        unset($_SESSION['csrf_token']);
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Redirection vers la page d'administration
header("Location: ../../admin.php?tab=articles");
exit;
