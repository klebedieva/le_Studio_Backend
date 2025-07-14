<?php
session_start();
require_once __DIR__ . '/../../config/database.php'; // Connexion à la base de données

// Vérifier que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    $_SESSION['error'] = "Accès refusé. Seuls les administrateurs peuvent supprimer des messages.";
    header("Location: admin.php?tab=contacts");
    exit;
}

// Vérifier que la requête est de type POST et que l'ID du message est fourni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_contact'])) {
    $id = filter_var($_POST['id_contact'], FILTER_VALIDATE_INT); // Récupèrer la valeur envoyée par le formulaire ($_POST['id_contact']) et vérifier qu’il s’agit bien d’un entier valide (un nombre entier).
    
    // Si l'ID n'est pas un entier valide, affiche une erreur et interrompt le traitement.
    if ($id === false) {
    $_SESSION['error'] = "ID du message invalide.";
    header("Location: admin.php?tab=contacts");
    exit;
}

   try {
        // Avant de supprimer, vérifier si le message existe
        $check = $pdo->prepare("SELECT COUNT(*) FROM contact WHERE id_contact = ?");
        $check->execute([$id]);
        if ($check->fetchColumn()) {
            // Supprimer le message s'il existe
            $stmt = $pdo->prepare("DELETE FROM contact WHERE id_contact = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = "Le message a été supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Message introuvable.";
        }
    } catch (PDOException $e) {
        // En cas d’erreur SQL, on affiche un message d’erreur
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Rediriger vers l'onglet  Gestion des Messages
header("Location: admin.php?tab=contacts");
exit;
