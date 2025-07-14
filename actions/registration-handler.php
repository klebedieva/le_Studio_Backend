<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Vérifie la validité du token CSRF
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['erreurs'][] = "Jeton CSRF invalide. Veuillez réessayer.";
    header('Location: register.php'); 
    exit;
}

// Supprime le token CSRF pour empêcher la réutilisation
unset($_SESSION['csrf_token']);

// Nettoyage des champs
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';
$hash = password_hash($mot_de_passe, PASSWORD_DEFAULT); // sécurisation

$erreurs = [];

// Validation
if (!$nom || !$prenom || !$email || !$mot_de_passe) {
    $erreurs[] = "Tous les champs sont requis.";
}

// Vérifier si l'email existe déjà dans la base
if (empty($erreurs)) {
    $requete = "SELECT email_user FROM user WHERE email_user = '$email'";
    $resultat = $pdo->query($requete);
    $existe = $resultat->fetch();

    if ($existe) {
        $erreurs[] = "Email déjà utilisé.";
    }
}


// Insertion si tout est bon
if (empty($erreurs)) {
    $requete = "
        INSERT INTO user (name_user, surname_user, email_user, password_user, subscription_date_user, status_user, role_user)
        VALUES (?, ?, ?, ?, NOW(), 'Actif', 'Utilisateur')
    ";
    $stmt = $pdo->prepare($requete);
    $stmt->execute([$nom, $prenom, $email, $hash]);
}

// Envoi de l'e-mail via Mailtrap
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Port = 2525;
    $mail->Username = 'd7b048aa93e141';
    $mail->Password = '35a1b3d885da97';

    $mail->setFrom('inscription@studio.com', 'Studio Coaching');
    $mail->addAddress($email, "$prenom $nom");
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation de votre inscription';
    $mail->Body = "
        <h2>Bonjour $prenom,</h2>
        <p>Merci pour votre inscription sur notre site !</p>
        <p>Vous pouvez maintenant vous connecter avec votre email.</p>
        <br>
        <p style='color:gray;'>Ceci est un message automatique depuis l’environnement de test Mailtrap.</p>
    ";

    $mail->send();
    $_SESSION['succes'] = "Votre compte a été créé avec succès. Un email de confirmation a été envoyé.";
} catch (Exception $e) {
    $_SESSION['succes'] = "Compte créé, mais l’e-mail n’a pas pu être envoyé : {$mail->ErrorInfo}";
}

// Gestion des erreurs
if (!empty($erreurs)) {
    $_SESSION['erreurs'] = $erreurs;
    header('Location: registration.php');
    exit;
}

header('Location: login.php');
exit;
