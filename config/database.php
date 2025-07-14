<?php
$serveur = 'localhost';
$port = 3307; 
$baseDeDonnees = 'admin'; 
$utilisateur = 'root';
$motDePasse = ''; 

try {
    $pdo = new PDO(
        "mysql:host=$serveur;port=$port;dbname=$baseDeDonnees;charset=utf8mb4",
        $utilisateur,
        $motDePasse
    );


    // Demande à PDO d’afficher une erreur (exception) si une requête SQL échoue
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "✅ Connecté à la base de données MariaDB !";
}
catch (Exception $e) {
    // 🚨 Si ça ne marche pas, on affiche l'erreur
    echo "❌ Erreur : " . $e->getMessage();
    die(); // On arrête le script
}
?>