<?php
session_start();
require_once __DIR__ . '/../../includes/data-admin.php';

// Indique que la réponse sera de type JSON
header('Content-Type: application/json');

// Vérifie que la requête a bien été envoyée en méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère et filtre l'ID du message (doit être un entier)
    $id = filter_var($_POST['id_contact'], FILTER_VALIDATE_INT);

    // Récupère le nouveau statut envoyé par le formulaire (ou AJAX)
    $newStatus = $_POST['status_contact'] ?? '';

    // Vérifie que l’ID est valide et que le statut fait partie des valeurs autorisées
    if ($id && in_array($newStatus, ['Nouveau', 'Lu', 'Répondu'])) {

        // Prépare la requête SQL de mise à jour
        $stmt = $pdo->prepare("UPDATE contact SET status_contact = :status WHERE id_contact = :id");

        // Exécute la requête avec les paramètres sécurisés
        $stmt->execute([
            'status' => $newStatus,
            'id' => $id
        ]);

        // Vérifie si au moins une ligne a été modifiée
        if ($stmt->rowCount() > 0) {
            // Envoie une réponse JSON indiquant le succès
            echo json_encode(['success' => true, 'new_status' => $newStatus]);
            exit;
        } else {
            // Si aucune ligne n’a été modifiée (statut identique ou ID inexistant)
            echo json_encode(['success' => false, 'error' => "Aucune ligne modifiée."]);
            exit;
        }
    } else {
        // Données invalides (ID manquant ou statut non autorisé)
        echo json_encode(['success' => false, 'error' => "Paramètres invalides."]);
        exit;
    }
}

// Si la requête n'est pas envoyée en POST
echo json_encode(['success' => false, 'error' => "Méthode non autorisée."]);
exit;
