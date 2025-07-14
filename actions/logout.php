<?php
session_start(); // Démarre la session pour pouvoir la supprimer
session_unset(); // Supprime toutes les variables de session (nettoyage du contenu)
session_destroy(); // Démarre la session pour pouvoir la supprimer
header('Location: ../indexv2.php'); // Redirection vers la page d’accueil
exit; // Redirection vers la page d’accueils