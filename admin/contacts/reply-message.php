<?php
session_start();
require_once __DIR__ . '/../../includes/data-admin.php';
require __DIR__ . '/../../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_contact = filter_var($_POST['id_contact'], FILTER_VALIDATE_INT);
    $reply_body = trim($_POST['reply_message'] ?? '');
    $email      = trim($_POST['email_contact'] ?? '');

    if ($id_contact && !empty($reply_body) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Mise à jour du statut dans la base de données
        $stmt = $pdo->prepare("UPDATE contact SET status_contact = 'Répondu' WHERE id_contact = :id");
        $stmt->execute(['id' => $id_contact]);

        // Envoi de l'email avec PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'd7b048aa93e141'; 
            $mail->Password   = '35a1b3d885da97';
            $mail->Port       = 2525;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;


            $mail->setFrom('contact@studio.com', 'Studio Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Réponse à votre message";
            $mail->Body    = nl2br(htmlspecialchars($reply_body)); // Protège contre XSS

            $mail->send();

            $_SESSION['success'] = "Réponse envoyée avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de l’envoi : {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "Données invalides ou incomplètes.";
    }

    header("Location: ../../admin.php?tab=contacts");
    exit;
}
