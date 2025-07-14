<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    $_SESSION['error'] = "Accès refusé. Seuls les administrateurs peuvent ajouter des utilisateurs.";
    header("Location: ../../admin.php?tab=users");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupèrer et nettoyer les données du formulaire
    $name     = trim($_POST['name']);
    $surname  = trim($_POST['surname']);
    $email    = trim($_POST['email']);
    $rawPassword = $_POST['password']; // Garder le mot de passe original pour l'envoi
    $password = password_hash($rawPassword, PASSWORD_DEFAULT); // Hachage sécurisé
    $role     = $_POST['role'];
    $status   = $_POST['status'];

    try {
        // Vérifier si l'adresse email existe déjà dans la base
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email_user = :email");
        $checkStmt->execute(['email' => $email]);
        $emailExists = $checkStmt->fetchColumn();

        if ($emailExists) {
            $_SESSION['error'] = "L'adresse email \"$email\" est déjà utilisée par un autre utilisateur.";
            header("Location: ../../admin.php?tab=users");
            exit;
        }

        // Insertion du nouvel utilisateur
        $stmt = $pdo->prepare("
            INSERT INTO user (name_user, surname_user, email_user, password_user, role_user, status_user, subscription_date_user)
            VALUES (:name, :surname, :email, :password, :role, :status, NOW())
        ");

        $stmt->execute([
            'name'     => $name,
            'surname'  => $surname,
            'email'    => $email,
            'password' => $password,
            'role'     => $role,
            'status'   => $status,
        ]);

        // Envoi du mail via Mailtrap
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'd7b048aa93e141';
            $mail->Password   = '35a1b3d885da97';
            $mail->Port       = 2525;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $mail->setFrom('admin@studio.com', 'Administration Studio');
            $mail->addAddress($email, "$name $surname");
            $mail->isHTML(true);
            $mail->Subject = 'Vos identifiants pour accéder à votre compte';

           $mail->Body = "
                         <h2>Bienvenue, $name $surname</h2>
                         <p>Votre compte a été créé avec succès.</p>
                         <p><strong>Email :</strong> $email</p>
                         <p><strong>Mot de passe :</strong> " . htmlspecialchars($rawPassword) . "</p>
                         <p>Vous pouvez maintenant vous connecter à votre espace personnel en cliquant sur le lien ci-dessous :</p>
                         <p><a href='http://localhost/le_Studio_backend/login.php'>Se connecter</a></p>
                         ";

            $mail->send();
        } catch (Exception $e) {
            // Capture une erreur lors de l’envoi de l’email après l’ajout de l’utilisateur et affiche un message en session.
            $_SESSION['error'] = "Utilisateur ajouté, mais erreur d’envoi d'email : { $mail->ErrorInfo}";
            header("Location: ../../admin.php?tab=users");
            exit;
        }
        // Message de succès + redirection vers l’onglet Gestion des Utilisateurs
        $_SESSION['success'] = "Nouvel utilisateur $name $surname ajouté avec succès.";
        header("Location: ../../admin.php?tab=users");
        exit;

    } catch (PDOException $e) {
        // Capture une erreur SQL lors de l’ajout de l’utilisateur et enregistre le message dans la session.
        $_SESSION['error'] = "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage();
        header("Location: ../../admin.php?tab=users");
        exit;
    }
}
