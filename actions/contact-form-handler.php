<?php
session_start(); // Démarre la session PHP pour pouvoir stocker et récupérer des données entre les pages (comme erreurs ou valeurs du formulaire)

require 'vendor/autoload.php'; // Chargement automatique de PHPMailer via Composer
require_once __DIR__ . '/config/database.php'; // Connexion à la base de données

use PHPMailer\PHPMailer\PHPMailer; // Classe principale qui permet de créer et envoyer des emails
use PHPMailer\PHPMailer\Exception; // Classe utilisée pour gérer les erreurs lors de l'envoi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Protège le site contre les attaques de type CSRF (envoi malveillant de formulaire)
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token'] // Vérifie si les deux jetons CSRF existent et sont identiques
    ) {
        header('Location: contactv2.php'); // Si le jeton est manquant ou invalide, redirection vers la page du formulaire
        exit; // Arrêt immédiat du script
    }

    // Récupère et nettoie les données
    $nom     = trim(htmlspecialchars($_POST['nom'] ?? ''));
    $prenom  = trim(htmlspecialchars($_POST['prenom'] ?? ''));
    $tel     = htmlspecialchars($_POST['tel'] ?? '');
    $email   = trim(htmlspecialchars($_POST['email'] ?? ''));
    $sujet   = trim(htmlspecialchars($_POST['sujet'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    // Validation avec regex
    $erreurs = [];

    // Nom : lettres, espaces, tirets
    if (empty($nom)) {
        $erreurs["nom"] = "Le nom est obligatoire";
    } elseif (!preg_match("/^[a-zA-Z\s-]+$/", $nom)) {
        $erreurs["nom"] = "Le nom est invalide";
    } else {
        $_SESSION['old']['nom'] = $nom; // Permet de pré-remplir le formulaire si une erreur survient (évite de retaper les données)
    }

    // Prénom : lettres, espaces, tirets
    if (empty($prenom)) {
        $erreurs["prenom"] = "Le prénom est obligatoire";
    } elseif (!preg_match("/^[a-zA-Z\s-]+$/", $prenom)) {
        $erreurs["prenom"] = "Le prénom est invalide";
    } else {
        $_SESSION['old']['prenom'] = $prenom;
    }

    // Téléphone : 10 chiffres (facultatif)
    if (!empty($tel)) {
        if (!preg_match("/^[0-9]{10}$/", $tel)) {
            $erreurs["tel"] = "Le téléphone est invalide";
        } else {
            $_SESSION['old']['tel'] = $tel;
        }
    }

    // Email : une suite de caractères autorisés, suivie de @, d’un nom de domaine, puis d’un point et d’au moins deux lettres
    if (empty($email)) {
        $erreurs["email"][] = "L'email est obligatoire";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$/", $email)) {
        $erreurs["email"][] = "L'email n'est pas valide";
    } else {
        $_SESSION['old']['email'] = $email;
    }

    // Sujet : lettres accentuées, tirets, facultatif
    if (!empty($sujet)) {
        if (!preg_match("/^[a-zA-Zà-úÀ-Ú\s-]*$/u", $sujet)) {
            $erreurs["sujet"] = "Le sujet est invalide";
        } else {
            $_SESSION['old']['sujet'] = $sujet;
        }
    }

    // Message : 10 à 1000 caractères, sans balises HTML
    if (empty($message)) {
        $erreurs["message"] = "Le message est obligatoire";
    } elseif (!preg_match("/^(?!.*<.*?>)[\s\S]{10,1000}$/", $message)) {
        $erreurs["message"] = "Le message doit contenir entre 10 et 1000 caractères, sans balises HTML.";
    } else {
        $_SESSION['old']['message'] = $message;
    }

    // Si des erreurs sont présentes, elles sont enregistrées en session puis redirection vers le formulaire pour affichage
    if (!empty($erreurs)) {
        $_SESSION['erreurs'] = $erreurs;
        header('Location: contactv2.php');
        exit;
    }

    // Enregistrement du message dans la base de données
    try {
        $query = "INSERT INTO contact (
            name_contact,
            surname_contact,
            email_contact,
            subject_contact,
            creation_date_contact,
            status_contact,
            message_contact,
            phone_contact,
            id_user
        ) VALUES (?, ?, ?, ?, NOW(), 'Nouveau', ?, ?, NULL)";

        $preparedStatement = $pdo->prepare($query);
        $preparedStatement->execute([$prenom, $nom, $email, $sujet, $message, $tel]);

    } catch (PDOException $e) {
        $_SESSION['erreur'] = "Erreur lors de l'enregistrement du message : " . $e->getMessage();
        $_SESSION['old'] = $_POST;
        header('Location: contactv2.php');
        exit;
    }

    $mail = new PHPMailer(true); // Création de l’objet PHPMailer avec gestion des erreurs (try/catch)

    try {
        // Configuration SMTP (serveur d’envoi)
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Port       = 2525;
        $mail->Username   = 'd7b048aa93e141'; 
        $mail->Password   = '35a1b3d885da97'; 

        // Informations de l’expéditeur
        $mail->setFrom('contact@studio.com', 'Studio Coaching');
        $mail->addReplyTo($email, "$prenom $nom"); // Adresse pour répondre
        $mail->addAddress('contact@studio.com', 'Boîte Mailtrap'); // Destinataire du message

        // Contenu de l’email
        $mail->isHTML(true); // Message en HTML
        $mail->Subject = $sujet ?: 'Nouveau message du formulaire'; // Sujet du message (ou texte par défaut)
        $mail->Body    = "
            <strong>Nom:</strong> $nom<br>
            <strong>Prénom:</strong> $prenom<br>
            <strong>Téléphone:</strong> $tel<br>
            <strong>Email:</strong> $email<br>
            <strong>Sujet:</strong> $sujet<br><br>
            <strong>Message:</strong><br>" . nl2br($message); // Transforme chaque saut de ligne (\n) en <br>

        $mail->send(); // Envoi du message

        // Message de succès stocké dans la session
        $_SESSION['succes'] = "Merci $prenom $nom ! Votre message a bien été envoyé.";
        unset($_SESSION['old']); // Suppression des anciennes données du formulaire

    } catch (Exception $e) {
        // En cas d’erreur : message d’erreur + sauvegarde des anciennes valeurs
        $_SESSION['erreur'] = "Erreur lors de l'envoi du message : {$mail->ErrorInfo}";
        $_SESSION['old'] = $_POST;
    }

    header('Location: contactv2.php');
    exit;
}
