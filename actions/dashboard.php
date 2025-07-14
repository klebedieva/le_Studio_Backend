<?php
session_start();
$role = strtolower($_SESSION['user']['role'] ?? '');
switch ($role) {
    case 'Administrateur':
    case 'Modérateur':
        header('Location: admin.php');
        break;
    default:
        header('Location: ../dashboard-client.php');
        break;
}
exit;
?>

