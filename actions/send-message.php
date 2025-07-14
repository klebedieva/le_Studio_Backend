<?php
session_start();
require 'vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie le token CSRF
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: dashboard-client.php');
        exit;
    }

    $id_user = intval($_POST['id_user'] ?? 0);
    $sujet = trim(htmlspecialchars($_POST['sujet'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    if (empty($sujet) || empty($message)) {
        $_SESSION['success'] = "Le sujet et le message sont obligatoires.";
        header("Location: dashboard-client.php");
        exit;
    }

    try {
        // Récupération des données utilisateur
        $stmt = $pdo->prepare("SELECT name_user, surname_user, email_user FROM user WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['success'] = "Utilisateur introuvable.";
            header("Location: dashboard-client.php");
            exit;
        }

        // Enregistrement du message en BDD
        $stmt = $pdo->prepare("INSERT INTO contact (
            name_contact, surname_contact, email_contact,
            subject_contact, creation_date_contact, status_contact,
            message_contact, id_user
        ) VALUES (?, ?, ?, ?, NOW(), 'Nouveau', ?, ?)");
        $stmt->execute([
            $user['name_user'], $user['surname_user'], $user['email_user'],
            $sujet, $message, $id_user
        ]);

        // Envoi du mail
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd7b048aa93e141';
        $mail->Password = '35a1b3d885da97';
        $mail->setFrom('contact@studio.com', 'Le Studio Sport');
        $mail->addReplyTo($user['email_user'], $user['name_user']);
        $mail->addAddress('contact@studio.com');
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = "
            <strong>Nom:</strong> {$user['surname_user']}<br>
            <strong>Prénom:</strong> {$user['name_user']}<br>
            <strong>Email:</strong> {$user['email_user']}<br><br>
            <strong>Message:</strong><br>" . nl2br($message);

        $mail->send();

        $_SESSION['success'] = "Votre message a été envoyé avec succès.";
        header("Location: dashboard-client.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['success'] = "Erreur : " . $e->getMessage();
        header("Location: dashboard-client.php");
        exit;
    }
}
