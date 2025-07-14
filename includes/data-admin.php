<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/functions.php';

// Statistiques générales
$message_nouveaux = $pdo->query("SELECT COUNT(*) FROM contact WHERE status_contact = 'Nouveau'")->fetchColumn();
$utilisateurs_actifs = $pdo->query("SELECT COUNT(*) FROM user WHERE status_user = 'Actif'")->fetchColumn();
$message_en_attente = $pdo->query("SELECT COUNT(*) FROM contact WHERE status_contact = 'Lu'")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();
$repliedMessages = $pdo->query("SELECT COUNT(*) FROM contact WHERE status_contact = 'Répondu'")->fetchColumn();
$taux_reponse = $totalMessages > 0 ? round(($repliedMessages / $totalMessages) * 100) : 0;

// Récupération des messages de contact
$contacts = $pdo->query("
    SELECT id_contact, name_contact, surname_contact,
           CONCAT(name_contact, ' ', surname_contact) AS nom_complet,
           email_contact, subject_contact, message_contact,
           DATE_FORMAT(creation_date_contact, '%d %b %Y') AS date_contact,
           status_contact 
    FROM contact
")->fetchAll();

// Construction des filtres utilisateurs
$filters = [];
$filter_params = [];
$filter_conditions = [];

// Recherche générale (nom, email, rôle, statut)
if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $filter_conditions[] = "(CONCAT(u.name_user, ' ', u.surname_user) LIKE :search OR u.email_user LIKE :search OR u.role_user LIKE :search OR u.status_user LIKE :search)";
    $filter_params[':search'] = '%' . $search . '%';
    $filters['search'] = $search;
}

// Filtre par rôle
if (!empty($_GET['role_filter'])) {
    $role = $_GET['role_filter'];
    $filter_conditions[] = "u.role_user = :role";
    $filter_params[':role'] = $role;
    $filters['role_filter'] = $role;
}

// Filtre par statut
if (!empty($_GET['status_filter'])) {
    $status = $_GET['status_filter'];
    $filter_conditions[] = "u.status_user = :status";
    $filter_params[':status'] = $status;
    $filters['status_filter'] = $status;
}

// Filtre par période d'inscription
if (!empty($_GET['date_filter'])) {
    $date_filter = $_GET['date_filter'];
    switch ($date_filter) {
        case 'today':
            $filter_conditions[] = "DATE(u.subscription_date_user) = CURDATE()";
            break;
        case 'week':
            $filter_conditions[] = "u.subscription_date_user >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $filter_conditions[] = "u.subscription_date_user >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $filter_conditions[] = "u.subscription_date_user >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            break;
    }
    $filters['date_filter'] = $date_filter;
}

// Tri
$order_by = "u.subscription_date_user DESC"; // Par défaut
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'name_asc':
            $order_by = "u.name_user ASC, u.surname_user ASC";
            break;
        case 'name_desc':
            $order_by = "u.name_user DESC, u.surname_user DESC";
            break;
        case 'email_asc':
            $order_by = "u.email_user ASC";
            break;
        case 'email_desc':
            $order_by = "u.email_user DESC";
            break;
        case 'date_asc':
            $order_by = "u.subscription_date_user ASC";
            break;
        case 'date_desc':
            $order_by = "u.subscription_date_user DESC";
            break;
    }
    $filters['sort'] = $_GET['sort'];
}

// Construction de la requête SQL
$where_clause = '';
if (!empty($filter_conditions)) {
    $where_clause = ' WHERE ' . implode(' AND ', $filter_conditions);
}

try {
    $sql = "
        SELECT 
            u.id_user,
            u.name_user,
            u.surname_user,
            CONCAT(u.name_user, ' ', u.surname_user) AS nom_complet,
            u.email_user,
            u.role_user,
            u.status_user,
            u.subscription_date_user,
            DATE_FORMAT(u.subscription_date_user, '%d %b %Y') AS subscription_date_formatted,
            s.subscription_type,
            s.subscription_start,
            s.subscription_end,
            s.weekly_sessions,
            s.monthly_price
        FROM user u
        LEFT JOIN subscriptions s ON u.subscription_id = s.id
        $where_clause
        ORDER BY $order_by
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($filter_params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total des utilisateurs (non filtrés)
    $count_sql_total = "SELECT COUNT(*) FROM user";
    $total_users_db = $pdo->query($count_sql_total)->fetchColumn();

    $total_users_filtered = count($users);

    // Rôles distincts
    $roles_sql = "SELECT DISTINCT role_user FROM user WHERE role_user IS NOT NULL ORDER BY role_user";
    $available_roles = $pdo->query($roles_sql)->fetchAll(PDO::FETCH_COLUMN);

    // Statuts distincts
    $status_sql = "SELECT DISTINCT status_user FROM user WHERE status_user IS NOT NULL ORDER BY status_user";
    $available_status = $pdo->query($status_sql)->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
    $users = [];
    $total_users_db = 0;
    $total_users_filtered = 0;
    $available_roles = [];
    $available_status = [];
}

// SECTION GESTION DES ARTICLES

try {
    // Requête SQL pour récupérer tous les articles de blog avec leur auteur
    // Utilisation d’un LEFT JOIN pour inclure les articles même si l’auteur est manquant
    $stmt = $pdo->query("
        SELECT 
            b.id, 
            b.title, 
            b.content, 
            b.created_at, 
            b.updated_at, 
            b.is_published,
            b.views,
            CONCAT(u.name_user, ' ', u.surname_user) AS author_name
        FROM blog b
        LEFT JOIN user u ON b.author_id = u.id_user
        ORDER BY b.created_at DESC
    ");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Deuxième requête : récupération de toutes les images avec leur blog_id
    $stmtImages = $pdo->query("SELECT blog_id, filename FROM blog_images");
    $imagesRaw = $stmtImages->fetchAll(PDO::FETCH_ASSOC);

    // Création d’un tableau associatif regroupant les images par article
    $imagesByBlog = [];
    foreach ($imagesRaw as $img) {
        $imagesByBlog[$img['blog_id']][] = $img['filename'];
    }

    // Formatage final des articles : ajout de la date et des images
    $articlesFormatted = [];
    foreach ($articles as $article) {
        $article['formatted_date'] = date('d M Y', strtotime($article['created_at']));
        $article['images'] = $imagesByBlog[$article['id']] ?? [];
        $articlesFormatted[] = $article;
    }

    // Remplacement de la liste initiale par la version enrichie
    $articles = $articlesFormatted;

} catch (PDOException $e) {
    // Gestion de l’erreur en cas d’échec de la requête
    $articles = [];
    $error_message = "Erreur lors de la récupération des articles : " . $e->getMessage();
}

?>
