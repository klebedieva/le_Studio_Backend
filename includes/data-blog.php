<?php
require_once __DIR__ . '/../config/database.php';

// Si un id valide est présent dans l'URL, le stocker ; sinon mettre null (liste des articles)
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

try {
    if ($id) {
        // Chargement d’un article unique (page article.php)
        
        // Récupération de l’article principal avec l’auteur
        $stmt = $pdo->prepare("
            SELECT b.id, b.title, b.content, b.created_at, 
                   CONCAT(u.name_user, ' ', u.surname_user) AS author
            FROM blog b
            LEFT JOIN user u ON b.author_id = u.id_user
            WHERE b.id = :id AND b.is_published = 1
        ");
        $stmt->execute(['id' => $id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        // Récupération des images associées à l’article
        $stmtImg = $pdo->prepare("SELECT filename FROM blog_images WHERE blog_id = :id");
        $stmtImg->execute(['id' => $id]);
        $images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

        // Récupération des tags associés à l’article
        $stmtTags = $pdo->prepare("
            SELECT t.name_tag 
            FROM tags t 
            INNER JOIN blog_tags bt ON t.id_tag = bt.tag_id 
            WHERE bt.blog_id = :id
        ");
        $stmtTags->execute(['id' => $id]);
        $tags = $stmtTags->fetchAll(PDO::FETCH_COLUMN);

        // Récupération des articles similaires via les tags en commun
        $relatedArticles = [];
        if (!empty($tags)) {
            // Construction dynamique de la liste des tags pour la clause IN

            // Génère une chaîne comme '?,?,?' selon le nombre de tags
            // Utilisée dans la requête SQL pour : WHERE t.name_tag IN (?,?,?)
            // Chaque ? sera remplacé par un tag (ex: 'cardio', 'force', 'yoga')
            $placeholders = rtrim(str_repeat('?,', count($tags)), ',');
            
            $similarStmt = $pdo->prepare("
                SELECT DISTINCT b.id, b.title, b.created_at,
                       CONCAT(u.name_user, ' ', u.surname_user) AS author,
                       (SELECT filename FROM blog_images WHERE blog_id = b.id LIMIT 1) AS image
                FROM blog b
                LEFT JOIN user u ON b.author_id = u.id_user
                INNER JOIN blog_tags bt ON bt.blog_id = b.id
                INNER JOIN tags t ON t.id_tag = bt.tag_id
                WHERE t.name_tag IN ($placeholders)
                  AND b.id != ?
                  AND b.is_published = 1
                ORDER BY b.created_at DESC
                LIMIT 3
            ");
            // Exécution avec tous les tags + l’ID de l’article courant pour l’exclure
            $similarStmt->execute([...$tags, $id]);
            $relatedArticles = $similarStmt->fetchAll(PDO::FETCH_ASSOC);
        }

    } else {
        // Chargement de la liste complète des articles (page blog.php)

        // Récupération du mot-clé de recherche s’il existe
        $keyword = $_GET['q'] ?? '';
        $whereClause = "b.is_published = 1";
        $params = [];

        // Si une recherche est faite, on adapte la clause WHERE
        if (!empty($keyword)) {
            $whereClause .= " AND (
                b.title LIKE :kw OR 
                b.content LIKE :kw OR 
                CONCAT(u.name_user, ' ', u.surname_user) LIKE :kw
            )";
            $params['kw'] = '%' . $keyword . '%';
        }

        // Requête SQL principale pour la liste des articles
        $query = "
            SELECT 
                b.id,
                b.title,
                b.content,
                b.created_at,
                CONCAT(u.name_user, ' ', u.surname_user) AS author,
                GROUP_CONCAT(DISTINCT bi.filename) AS images
            FROM blog b
            JOIN user u ON b.author_id = u.id_user
            LEFT JOIN blog_images bi ON b.id = bi.blog_id
            WHERE $whereClause
            GROUP BY b.id
            ORDER BY b.created_at DESC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $articlesRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formatage des articles pour affichage
        $articles = [];
        foreach ($articlesRaw as $article) {
            $article['formatted_date'] = date('d M Y', strtotime($article['created_at']));
            $article['images'] = !empty($article['images']) ? explode(',', $article['images']) : [];
            $articles[] = $article;
        }
    }

} catch (PDOException $e) {
    //  Gestion des erreurs PDO
    $error_message = "Erreur lors du chargement des articles : " . $e->getMessage();
    if (!$id) $articles = [];
}
