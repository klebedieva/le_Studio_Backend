<?php
session_start();
require_once __DIR__ . '/../../config/database.php'; // Connexion à la base de données

// Vérifier que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    $_SESSION['error'] = "Accès refusé. Seuls les administrateurs peuvent modifier les utilisateurs.";
    header("Location: ../admin.php?tab=users");
    exit;
}

// Vérifier que la requête est bien envoyée en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer les données du formulaire
    $id_user = filter_var($_POST['id_user'], FILTER_VALIDATE_INT); // Récupèrer la valeur envoyée par le formulaire ($_POST['id_contact']) et vérifier qu’il s’agit bien d’un entier valide (un nombre entier).
    
    // Si l'ID n'est pas un entier valide, affiche une erreur et interrompt le traitement.
if ($id_user === false) {
    $_SESSION['error'] = "ID de l'utilisateur invalide.";
    header("Location: ../admin.php?tab=users");
    exit;
}
    $fullName = trim($_POST['full_name'] ?? '');           // Nom complet
    $email    = trim($_POST['email'] ?? '');               // Email
    // Vérifier que l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Adresse email invalide.";
    header("Location: admin.php?tab=users");
    exit;
}
    $role     = trim($_POST['role'] ?? '');                // Rôle
    $status   = trim($_POST['status'] ?? '');              // Statut
   
    // Séparer prénom et nom à partir du champ "Nom complet"
    $name = '';
    $surname = '';
    if (!empty($fullName)) {
        $parts = explode(' ', $fullName);
        $surname = array_pop($parts);      // Dernier mot = nom
        $name = implode(' ', $parts);      // Le reste = prénom(s)
    }

    // Vérifier que tous les champs sont remplis
    if ($id_user && $name && $surname && $email && $role && $status) {

        // Vérifier que l'utilisateur existe
        $check = $pdo->prepare("SELECT COUNT(*) FROM user WHERE id_user = :id_user");
        $check->execute(['id_user' => $id_user]);
        if ($check->fetchColumn() == 0) {
        $_SESSION['error'] = "Utilisateur introuvable.";
        header("Location: admin.php?tab=users");
        exit;
        }

        // Vérifier que le nouvel email n’est pas déjà utilisé (par un autre utilisateur)
        $check = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email_user = :email AND id_user != :id_user");
        $check->execute([
            'email' => $email,
            'id_user' => $id_user
        ]);

        if ($check->fetchColumn() > 0) {
            $_SESSION['error'] = "Cet email est déjà utilisé par un autre utilisateur.";
            header("Location: admin.php?tab=users");
            exit;
        }

        try {
            // Mettre à jour l'utilisateur
            $stmt = $pdo->prepare("
                UPDATE user
                SET 
                    name_user = :name,
                    surname_user = :surname,
                    email_user = :email,
                    role_user = :role,
                    status_user = :status
                WHERE id_user = :id_user
            ");
            $stmt->execute([
                'name'     => $name,
                'surname'  => $surname,
                'email'    => $email,
                'role'     => $role,
                'status'   => $status,
                'id_user'  => $id_user
            ]);

            // Redirection vers l’onglet Gestion des Utilisateurs
            $_SESSION['success'] = "Utilisateur $name $surname mis à jour avec succès.";
            header("Location: admin.php?tab=users");
            exit;

        } catch (PDOException $e) {
        // En cas d’erreur SQL, on affiche un message d’erreur
        $_SESSION['error'] = "Erreur lors de la modification de l'utilisateur : " . $e->getMessage();
        header("Location: admin.php?tab=users");
        exit;
    } 
    } else {
          $_SESSION['error'] = "Tous les champs sont obligatoires.";
    }
}
?>
