<?php
require_once __DIR__ . '/../../includes/data-admin.php';

// Vérifie que la requête est de type POST et qu'un ID a bien été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_contact'])) {
    $id = intval($_POST['id_contact']); // Sécurise l'ID en le convertissant en entier

    // Prépare et exécute la requête SQL pour mettre à jour le statut du message en 'Lu'
    $stmt = $pdo->prepare("UPDATE contact SET status_contact = 'Lu' WHERE id_contact = ?");
    $stmt->execute([$id]);

    // Retourne un succès au format JSON
    echo json_encode(['success' => true]);
    exit;
}

// Si l'ID est manquant ou la méthode n'est pas POST, retourne un échec
echo json_encode(['success' => false]);  
