<?php
session_start();
require_once __DIR__ . '/../../config/database.php'; // Connexion à la base de données

// Vérifier que l'utilisateur est bien connecté en tant qu’administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    $_SESSION['error'] = "Accès refusé. Vous n'avez pas les droits nécessaires.";
    header("Location: ../admin.php?tab=users");
    exit;
}

// Vérifier que la requête est bien POST et que l'id est bien défini
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_user'])) {
    $id_user = filter_var($_POST['id_user'], FILTER_VALIDATE_INT); // Récupération de la valeur envoyée depuis le formulaire (champ "id_user") et validation pour s’assurer qu’il s’agit bien d’un entier.

    if ($id_user === false) {
        $_SESSION['error'] = "ID utilisateur invalide.";
    } else {
        try {
            // Vérifier si l'utilisateur existe avant de supprimer
            $check = $pdo->prepare("SELECT COUNT(*) FROM user WHERE id_user = ?");
            $check->execute([$id_user]);
            $exists = $check->fetchColumn();

            if ($exists) {
                $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = ?");
                $stmt->execute([$id_user]);

                $_SESSION['success'] = "Utilisateur avec l’ID #$id_user supprimé avec succès.";
            } else {
                $_SESSION['error'] = "Utilisateur introuvable.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
} else {
    $_SESSION['error'] = "Requête invalide.";
}

// Redirection vers l’onglet Gestion des Utilisateurs
header("Location: ../admin.php?tab=users");
exit;
