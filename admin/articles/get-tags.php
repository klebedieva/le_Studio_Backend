<?php
require_once __DIR__ . '/../../config/database.php';  // Connexion à la base de données
header('Content-Type: application/json'); // Définition du type de contenu pour JSON

if (isset($_GET['blog_id'])) {
    $blog_id = (int)$_GET['blog_id'];
    $tags = $pdo->query("SELECT * FROM tags")->fetchAll(PDO::FETCH_ASSOC);
    $selected = $pdo->prepare("SELECT tag_id FROM blog_tags WHERE blog_id = ?");
    $selected->execute([$blog_id]);
    $selectedTags = $selected->fetchAll(PDO::FETCH_COLUMN);
    
    // Préparation de la réponse avec les tags et les tags sélectionnés
    echo json_encode([
        'tags' => $tags,
        'selected' => $selectedTags
    ]);
    exit;
}

$tags = $pdo->query("SELECT * FROM tags")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($tags); // Retourne tous les tags si aucun blog_id n'est fourni