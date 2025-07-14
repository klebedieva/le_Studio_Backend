<?php
session_start();
require_once __DIR__ . '/config/database.php'; 

$user_id = $_SESSION['user']['id_user'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name_user'] ?? '');
    $surname = trim($_POST['surname_user'] ?? '');
    $email = trim($_POST['email_user'] ?? '');

    if ($name && $surname && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("UPDATE user SET name_user = ?, surname_user = ?, email_user = ? WHERE id_user = ?");
        $stmt->execute([$name, $surname, $email, $user_id]);

        $_SESSION['user']['name_user'] = $name;
        $_SESSION['user']['surname_user'] = $surname;
        $_SESSION['user']['email_user'] = $email;

        $_SESSION['success'] = "Vos informations ont été mises à jour avec succès.";
header("Location: dashboard-client.php");
exit;
    } else {
        $error = "Veuillez remplir tous les champs correctement.";
    }
}

// Données actuelles
$stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil - Le Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #2c2c2c;
        }
        .btn-studio {
            background: #DAB978;
            color: #2C2C2C;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .btn-studio:hover {
            background: #b8860b;
            color: white;
        }

        .btn-cancel {
    background-color: #e0e0e0;
    color: #2C2C2C;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 25px;
    font-weight: bold;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 48px;
    text-decoration: none;
}

.btn-cancel:hover {
    background-color: #cccccc;
    color: #000;
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">Modifier votre profil</h2>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="name_user" class="form-control" value="<?php echo htmlspecialchars($user['name_user']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="surname_user" class="form-control" value="<?php echo htmlspecialchars($user['surname_user']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email_user" class="form-control" value="<?php echo htmlspecialchars($user['email_user']); ?>" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-cancel">Annuler</a>
                <button type="submit" class="btn-studio">Enregistrer</button>
            </div>
        </form>
    </div>
</body>
</html>