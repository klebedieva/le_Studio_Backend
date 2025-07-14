<?php
session_start(); // Démarre la session pour stocker des messages ou données entre les pages
require_once __DIR__ . '/../config/database.php'; // Connexion à la base de données


// Vérifie la validité du token CSRF
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['erreurs'][] = "Jeton CSRF invalide. Veuillez réessayer.";
    header('Location: ../login.php');
    exit;
}

// Supprime le token pour éviter sa réutilisation
unset($_SESSION['csrf_token']);

// Récupération des données du formulaire (email et mot de passe)
$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

// Vérifie que les champs ne sont pas vides
if (empty($email) || empty($mot_de_passe)) {
    $_SESSION['erreurs'][] = "Tous les champs sont obligatoires.";
    header('Location: ../login.php'); // Redirection vers le formulaire de connexion
    exit;
}

// Recherche d’un utilisateur correspondant à l’email et au mot de passe
$user_find = null; // Variable pour stocker l’utilisateur trouvé

$query = "SELECT * FROM user WHERE email_user = ?";
$preparedStatement = $pdo->prepare($query);
$preparedStatement->execute([$email]);
$user = $preparedStatement->fetch(PDO::FETCH_ASSOC); // Récupère la première ligne du résultat sous forme de tableau associatif (clé = nom de colonne)

// Vérifie que l'email correspond et que le mot de passe est correct (via password_verify)
if ($user && password_verify($mot_de_passe, $user['password_user'])) {

    // Si le hash du mot de passe est ancien, on le met à jour avec un nouveau
    if (password_needs_rehash($user['password_user'], PASSWORD_DEFAULT)) {
        $nouveauHash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE user SET password_user = ? WHERE id_user = ?");
        $update->execute([$nouveauHash, $user['id_user']]);
    }

    $user_find = $user; // Stocke l'utilisateur trouvé
}
// $index => $user pour pouvoir mettre à jour un utilisateur précis grâce à son index dans le tableau (sinon $user n’est qu’une copie, et les modifications ne sont pas enregistrées).

// Si un utilisateur a été trouvé → connexion réussie
if ($user_find) {
    $_SESSION['succes'] = "Connexion réussie. Bienvenue " . $user_find['name_user'] . " !";
    // Sauvegarde des informations utilisateur dans la session
    $_SESSION['user'] = [
        'id_user' => $user_find['id_user'], 
        'nom' => $user_find['surname_user'],
        'prenom' => $user_find['name_user'],
        'email' => $user_find['email_user'],
        'role' => $user_find['role_user'] ?? 'Utilisateur'
    ];
    $role = strtolower($user_find['role_user'] ?? '');
    switch ($role) {
        case 'administrateur':
        case 'modérateur':
            header('Location: ../admin.php');
            break;
        default:
            header('Location: ../dashboard-client.php');
            break;
    }
    exit;
} else {
    // Si aucun utilisateur trouvé → message d’erreur
    $_SESSION['erreurs'][] = "Identifiants incorrects. Veuillez réessayer.";
    header('Location: ../login.php');  // Retour vers le formulaire de connexion
    exit;
}
?>