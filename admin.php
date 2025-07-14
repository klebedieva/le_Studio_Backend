<?php
session_start();

// 🔐 Génération du token CSRF s'il n'existe pas déjà
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/includes/data-admin.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #ecf0f1;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 20px;
            border-radius: 0;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--accent-color);
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .header-card {
            background: linear-gradient(135deg, var(--accent-color), #2980b9);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--light-bg);
            border: none;
            font-weight: 600;
            color: var(--primary-color);
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #dee2e6;
        }

        .btn-action {
            padding: 8px 12px;
            margin: 2px;
            border-radius: 8px;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-new {
            background-color: #e8f5e8;
            color: var(--success-color);
        }

        .status-read {
            background-color: #e6f3ff;
            color: var(--accent-color);
        }

        .status-replied {
            background-color: #fff3e0;
            color: var(--warning-color);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color), #2980b9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            padding-left: 45px;
            border-radius: 25px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .tab-content {
            padding-top: 20px;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            font-weight: 500;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--accent-color);
            color: white;
            border-bottom: 3px solid var(--accent-color);
        }
        .status-clickable {
            cursor: pointer;
            text-decoration: underline dotted;
        }
        

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-4">
            <h4 class="text-white mb-4">
                <i class="fas fa-cogs me-2"></i>
                Admin Panel
            </h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#contacts" data-bs-toggle="tab">
                    <i class="fas fa-envelope"></i>
                    Messages Contact
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#users" data-bs-toggle="tab">
                    <i class="fas fa-users"></i>
                    Gestion Utilisateurs
                </a>
            </li>
            <li class="nav-item">
    <a class="nav-link" href="#articles" data-bs-toggle="tab">
        <i class="fas fa-newspaper"></i>
        Gestion Articles
    </a>
</li>

            <li class="nav-item">
                <a class="nav-link" href="#settings" data-bs-toggle="tab">
                    <i class="fas fa-cog"></i>
                    Paramètres
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
         <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
        <div class="tab-content">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard">
                <div class="header-card">
                    <h2 class="mb-3">
                        <i class="fas fa-chart-bar me-2"></i>
                        Tableau de Bord
                    </h2>
                    <p class="mb-0">Vue d'ensemble de votre administration</p>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, var(--success-color), #229954);">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3 class="fw-bold mb-1"><?= $message_nouveaux ?></h3>
                            <p class="text-muted mb-0">Nouveaux messages</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, var(--accent-color), #2980b9);">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="fw-bold mb-1"><?= $utilisateurs_actifs ?></h3>
                            <p class="text-muted mb-0">Utilisateurs actifs</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, var(--warning-color), #e67e22);">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="fw-bold mb-1"><?= $message_en_attente ?></h3>
                            <p class="text-muted mb-0">En attente</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, var(--danger-color), #c0392b);">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="fw-bold mb-1"><?= $taux_reponse . "%" ?></h3>
                            <p class="text-muted mb-0">Taux de réponse</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts Tab -->
            <div class="tab-pane fade" id="contacts">
                <div class="header-card">
                    <h2 class="mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        Gestion des Messages
                    </h2>
                    <p class="mb-0">Consultez et gérez tous les messages de contact</p>
                </div>

                <div class="table-container">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" placeholder="Rechercher dans les messages...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-filter me-1"></i>
                                Filtrer
                            </button>
                            <button class="btn btn-success">
                                <i class="fas fa-download me-1"></i>
                                Exporter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Expéditeur</th>
                                    <th>Email</th>
                                    <th>Sujet</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php foreach ($contacts as $contact): 
        $name = $contact['name_contact'] ?? '';
        $surname = $contact['surname_contact'] ?? '';

        $initials = strtoupper(substr($name, 0, 1) . substr($surname, 0, 1)); // substr($name, 0, 1) → prend la première lettre de la chaîne $name (en commençant à la position 0, sur une longueur de 1 caractère) // . → concatène deux lettres (par exemple : A + D = AD) // strtoupper(...) → transforme le résultat en majuscules(par exemple : ad → AD)

        $initials = $initials ?: '??'; 

        $statusClass = match ($contact['status_contact']) {
            'Nouveau'  => 'status-new',
            'Lu'       => 'status-read',
            'Répondu'  => 'status-replied',
        };
    ?>
    <tr>
        <td>
            <div class="d-flex align-items-center">
                <div class="user-avatar me-3"><?= $initials ?></div>
                <strong><?= htmlspecialchars($contact['nom_complet']) ?></strong>
            </div>
        </td>
        <td><?= htmlspecialchars($contact['email_contact']) ?></td>
        <td><?= htmlspecialchars($contact['subject_contact']) ?></td>
        <td><?= htmlspecialchars($contact['date_contact']) ?></td>
        <td>
      <span class="status-badge <?= $statusClass ?> status-clickable"
        data-id="<?= $contact['id_contact'] ?>"
        data-current="<?= htmlspecialchars($contact['status_contact']) ?>">
      <?= htmlspecialchars($contact['status_contact']) ?>
  </span>
</td>

    <td>
<button class="btn btn-primary btn-action view-message"
    data-bs-toggle="modal"
    data-bs-target="#viewMessageModal"
    data-id="<?= $contact['id_contact'] ?>"
    data-name="<?= htmlspecialchars($contact['nom_complet']) ?>"
    data-email="<?= htmlspecialchars($contact['email_contact']) ?>"
    data-date="<?= $contact['date_contact'] ?>"
    data-subject="<?= htmlspecialchars($contact['subject_contact']) ?>"
    data-message="<?= nl2br(htmlspecialchars($contact['message_contact'])) ?>">
    <i class="fas fa-eye"></i>
</button>

<!-- 
    <button class="btn btn-success btn-action reply-message"
    title="Répondre"
    data-id="<?= $contact['id_contact'] ?>"
    data-name="<?= htmlspecialchars($contact['nom_complet']) ?>"
    data-email="<?= htmlspecialchars($contact['email_contact']) ?>"
    data-subject="<?= htmlspecialchars($contact['subject_contact']) ?>"
    data-message="<?= htmlspecialchars($contact['message_contact']) ?>"
    data-bs-toggle="modal"
    data-bs-target="#replyMessageModal">
    <i class="fas fa-reply"></i>
</button> -->


  <button class="btn btn-danger btn-action delete-message-btn" 
        title="Supprimer"
        data-id="<?= $contact['id_contact'] ?>">
    <i class="fas fa-trash"></i>
</button>

</td>

    </tr>
    <?php endforeach; ?>
</tbody>

                        </table>
                    </div>

                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Précédent</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

<!-- Users Tab -->
<div class="tab-pane fade" id="users">
    <div class="header-card">
        <h2 class="mb-3">
            <i class="fas fa-users me-2"></i>
            Gestion des Utilisateurs
        </h2>
        <p class="mb-0">Gérez les comptes utilisateurs de votre plateforme</p>
    </div>

    <div class="table-container">
<!-- Panneau de filtres -->
<div class="filter-card mb-4">
    <div class="filter-title mb-3">
        <i class="fas fa-filter me-2"></i>
        Filtres et tri
    </div>

    <form method="GET" id="filterForm" class="row g-3">
        <input type="hidden" name="tab" value="users">

        <!-- Filtre par période -->
        <div class="col-md-3">
            <label for="date_filter" class="form-label">Période d'inscription</label>
            <select class="form-select" name="date_filter" id="date_filter">
                <option value="">Toutes les dates</option>
                <option value="today" <?= ($_GET['date_filter'] ?? '') === 'today' ? 'selected' : '' ?>>Aujourd'hui</option>
                <option value="week" <?= ($_GET['date_filter'] ?? '') === 'week' ? 'selected' : '' ?>>Cette semaine</option>
                <option value="month" <?= ($_GET['date_filter'] ?? '') === 'month' ? 'selected' : '' ?>>Ce mois</option>
                <option value="year" <?= ($_GET['date_filter'] ?? '') === 'year' ? 'selected' : '' ?>>Cette année</option>
            </select>
        </div>

        <!-- Filtre par rôle -->
        <div class="col-md-3">
            <label for="role_filter" class="form-label">Rôle</label>
            <select class="form-select" name="role_filter" id="role_filter">
                <option value="">Tous les rôles</option>
                <?php foreach ($available_roles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>" <?= ($_GET['role_filter'] ?? '') === $role ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($role)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filtre par statut -->
        <div class="col-md-3">
            <label for="status_filter" class="form-label">Statut</label>
            <select class="form-select" name="status_filter" id="status_filter">
                <option value="">Tous les statuts</option>
                <?php foreach ($available_status as $status): ?>
                    <option value="<?= htmlspecialchars($status) ?>" <?= ($_GET['status_filter'] ?? '') === $status ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($status)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tri -->
        <div class="col-md-3">
            <label for="sort" class="form-label">Trier par</label>
            <select class="form-select" name="sort" id="sort">
                <option value="date_desc" <?= ($_GET['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Date (récent)</option>
                <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Date (ancien)</option>
                <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Nom (A-Z)</option>
                <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Nom (Z-A)</option>
                <option value="email_asc" <?= ($_GET['sort'] ?? '') === 'email_asc' ? 'selected' : '' ?>>Email (A-Z)</option>
                <option value="email_desc" <?= ($_GET['sort'] ?? '') === 'email_desc' ? 'selected' : '' ?>>Email (Z-A)</option>
            </select>
        </div>

        <!-- Boutons -->
<div class="col-12 d-flex justify-content-end align-items-end gap-2 mt-2">
    <!-- <button type="submit" class="btn btn-primary">
        <i class="fas fa-search me-1"></i> Appliquer
    </button> -->
    <a href="?tab=users" class="btn btn-outline-secondary">
        <i class="fas fa-times me-1"></i> Réinitialiser
    </a>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus me-1"></i> Nouvel utilisateur
    </button>
</div>
<!--  Résumé des résultats -->
<div class="results-info mb-4">
    <div class="results-count">
        <i class="fas fa-users me-2"></i>
        <strong><?= $total_users_filtered ?></strong> utilisateur(s) affiché(s)
        <?php if ($total_users_filtered !== $total_users_db): ?>
            sur <strong><?= $total_users_db ?></strong> au total
        <?php endif; ?>
    </div>

    <!-- Filtres actifs -->
    <?php if (!empty($filters)): ?>
        <div class="active-filters mt-2">
            <span class="me-2">Filtres actifs :</span>
            <?php 
            foreach ($filters as $filter_key => $filter_value):
                $filter_url = $_SERVER['PHP_SELF'] . '?tab=users';
                foreach ($filters as $key => $value) {
                    if ($key !== $filter_key) {
                        $filter_url .= '&' . $key . '=' . urlencode($value);
                    }
                }

                $filter_labels = [
                    'date_filter' => [
                        'today' => 'Aujourd\'hui',
                        'week' => 'Cette semaine',
                        'month' => 'Ce mois',
                        'year' => 'Cette année'
                    ],
                    'sort' => [
                        'date_desc' => 'Date (récent)',
                        'date_asc' => 'Date (ancien)',
                        'name_asc' => 'Nom (A-Z)',
                        'name_desc' => 'Nom (Z-A)',
                        'email_asc' => 'Email (A-Z)',
                        'email_desc' => 'Email (Z-A)'
                    ]
                ];

                // Gestion des libellés
                if (isset($filter_labels[$filter_key]) && is_array($filter_labels[$filter_key])) {
    $label = $filter_labels[$filter_key][$filter_value] ?? ucfirst(htmlspecialchars($filter_value));
} else {
    $label = ucfirst(htmlspecialchars($filter_value));
}
            ?>
                <span class="badge bg-secondary me-1">
                    <?= $label ?>
                    <a href="<?= $filter_url ?>" class="text-white ms-1"><i class="fas fa-times small"></i></a>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
    </form>
</div>


                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Date d'inscription</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): 
    $name = $user['name_user'] ?? '';
    $surname = $user['surname_user'] ?? '';
    $initials = strtoupper(substr($name, 0, 1) . substr($surname, 0, 1)); // substr($name, 0, 1) → prend la première lettre de la chaîne $name (en commençant à la position 0, sur une longueur de 1 caractère) // . → concatène deux lettres (par exemple : A + D = AD) // strtoupper(...) → transforme le résultat en majuscules(par exemple : ad → AD)
    $initials = $initials ?: '??';
    // nom complet
    $fullName = $user['nom_complet'] ?? '';
    $role = $user['role_user'] ?? '';
        $status = $user['status_user'] ?? '';


     $hasSubscription = !empty($user['subscription_type']);


   $roleSubtitle = match ($role) {
    'Administrateur' => 'Administrateur',
    'Modérateur'     => 'Modérateur',
    'Utilisateur'    => 'Utilisateur',
    default          => 'Inconnu',
};

    $roleBadge = match ($role) {
    'Administrateur' => 'bg-danger',
    'Modérateur'     => 'bg-info',
    'Utilisateur'    => 'bg-primary',
    default          => 'bg-light text-dark',
};


    $status = $user['status_user'] ?? '';


$statusBadge = match ($status) {
    'Actif'     => 'bg-success',
    'Suspendu'  => 'bg-warning',
    'Inactif'   => 'bg-secondary',
    default     => 'bg-light text-dark',
};

?>
<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="user-avatar me-3"><?= $initials ?></div>
            <div>
                <strong><?= htmlspecialchars($fullName) ?></strong><br>
                <small class="text-muted"><?= $roleSubtitle ?></small>
            </div>
        </div>
    </td>
    <td><?= htmlspecialchars($user['email_user'] ?? '') ?></td>
    <td><span class="badge <?= $roleBadge ?>"><?= $role ?></span></td>
    <td><?= $user['subscription_date_formatted'] ?? '' ?></td>
    <td><span class="badge <?= $statusBadge ?>"><?= $status ?></span></td>
    <td>
        <button class="btn btn-primary btn-action view-user"
        data-name="<?= htmlspecialchars($user['name_user']) ?>"
        data-surname="<?= htmlspecialchars($user['surname_user']) ?>"
        data-email="<?= htmlspecialchars($user['email_user']) ?>"
        data-role="<?= htmlspecialchars($user['role_user']) ?>"
        data-status="<?= htmlspecialchars($user['status_user']) ?>"
        data-subscription-date="<?= htmlspecialchars($user['subscription_date_formatted']) ?>"
        data-sub-type="<?= $hasSubscription ? htmlspecialchars($user['subscription_type']) : 'Aucun' ?>"
        data-sub-start="<?= htmlspecialchars($user['subscription_start'] ?? '') ?>"
        data-sub-end="<?= htmlspecialchars($user['subscription_end'] ?? '') ?>"
        data-sub-sessions="<?= htmlspecialchars($user['weekly_sessions'] ?? '') ?>"
        data-sub-price="<?= htmlspecialchars($user['monthly_price'] ?? '') ?>"
        data-has-sub="<?= $hasSubscription ? '1' : '0' ?>"
        data-bs-toggle="modal"
        data-bs-target="#viewUserModal"
>
    <i class="fas fa-eye"></i>
</button>


        <!-- L'attribut data-* permet de stocker des données dans un élément HTML (ex. : un bouton) et de les récupérer facilement en JavaScript avec dataset. -->
        <button 
            class="btn btn-warning btn-action editUserBtn" 
            title="Modifier"
            data-id="<?= $user['id_user'] ?>"
            data-name="<?= htmlspecialchars($user['name_user']) ?>"
            data-surname="<?= htmlspecialchars($user['surname_user']) ?>"
            data-email="<?= htmlspecialchars($user['email_user']) ?>"
            data-role="<?= htmlspecialchars($user['role_user']) ?>"
            data-status="<?= htmlspecialchars($user['status_user']) ?>"
            data-bs-toggle="modal"
            data-bs-target="#editUserModal"
        >
            <i class="fas fa-edit"></i>
        </button>
<button 
    class="btn btn-danger btn-action delete-user-btn" 
    title="Supprimer"
    data-id="<?= $user['id_user'] ?>"
    data-bs-toggle="modal"
    data-bs-target="#deleteModal"
>
    <i class="fas fa-trash"></i>
</button>

    </td>
</tr>
<?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>

                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Précédent</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Onglet Articles -->
<div class="tab-pane fade" id="articles">

    <!-- En-tête de la section -->
    <div class="header-card">
        <h2 class="mb-3">
            <i class="fas fa-newspaper me-2"></i>
            Gestion des Articles
        </h2>
        <p class="mb-0">Créez, modifiez et gérez les articles de blog</p>
    </div>

    <!-- Conteneur principal de la table -->
    <div class="table-container">

        <!-- Barre d'outils : bouton + barre de recherche -->
        <div class="d-flex justify-content-between mb-3">
            <div>
                <!-- Bouton pour ajouter un article -->
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                    <i class="fas fa-plus me-1"></i> Nouvel article
                </button>
            </div>
            <!-- Champ de recherche -->
            <div class="search-box w-50">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Rechercher un article...">
            </div>
        </div>

        <!-- Tableau des articles -->
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 16%;">Titre</th>
                        <th style="width: 13%;">Auteur</th>
                        <th style="width: 23%;">Contenu</th>
                        <th style="width: 10%;">Créé</th>
                        <th style="width: 10%;">Modifié</th>
                        <th style="width: 8%;">Vues</th>
                        <th style="width: 8%;">Statut</th>
                        <th style="width: 12%;">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Boucle d'affichage des articles -->
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?= htmlspecialchars($article['title']) ?></td>
                            <td><?= htmlspecialchars($article['author_name'] ?? 'Inconnu') ?></td>
                            <td><?= mb_strimwidth(strip_tags($article['content']), 0, 60, '...') ?></td>
                            <td><?= date('d M Y', strtotime($article['created_at'])) ?></td>
                            <td>
                                <?= $article['updated_at'] ? date('d M Y', strtotime($article['updated_at'])) : '—' ?>
                            </td>
                            <td><?= (int)$article['views'] ?></td>
                            <td>
                                <?php if ($article['is_published']): ?>
                                    <span class="badge bg-success">Publié</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Brouillon</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Bouton : voir l'article -->
                                <button class="btn btn-primary btn-sm view-article"
                                        data-id="<?= $article['id'] ?>"
                                        data-title="<?= htmlspecialchars($article['title']) ?>"
                                        data-content="<?= htmlspecialchars($article['content']) ?>"
                                        data-images='<?= json_encode($article['images'] ?? []) ?>'
                                        data-date="<?= htmlspecialchars($article['created_at']) ?>">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Bouton : modifier l'article -->
                                <button class="btn btn-warning btn-sm edit-article"
                                        data-id="<?= $article['id'] ?>"
                                        data-title="<?= htmlspecialchars($article['title']) ?>"
                                        data-content="<?= htmlspecialchars($article['content']) ?>"
                                        data-images='<?= json_encode($article['images'] ?? []) ?>'
                                        data-is-published="<?= $article['is_published'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editArticleModal">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Bouton : supprimer l'article -->
                                <button class="btn btn-danger btn-sm delete-article"
                                        data-id="<?= $article['id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Si aucun article -->
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Aucun article trouvé.</td>
                        </tr>
                    <?php endif; ?>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

            <!-- Onglet Settings -->
            <div class="tab-pane fade" id="settings">
                <div class="header-card">
                    <h2 class="mb-3">
                        <i class="fas fa-cog me-2"></i>
                        Paramètres
                    </h2>
                    <p class="mb-0">Configuration générale de l'administration</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="table-container">
                            <h5 class="mb-4">
                                <i class="fas fa-envelope me-2"></i>
                                Configuration Email
                            </h5>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Email administrateur</label>
                                    <input type="email" class="form-control" value="admin@example.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Réponse automatique</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Activer</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Message automatique</label>
                                    <textarea class="form-control" rows="3">Merci pour votre message. Nous vous répondrons dans les plus brefs délais.</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Sauvegarder
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="table-container">
                            <h5 class="mb-4">
                                <i class="fas fa-shield-alt me-2"></i>
                                Sécurité
                            </h5>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Authentification à deux facteurs</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Activer 2FA</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Durée de session (minutes)</label>
                                    <input type="number" class="form-control" value="60">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tentatives de connexion max</label>
                                    <input type="number" class="form-control" value="5">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Sauvegarder
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="table-container">
                            <h5 class="mb-4">
                                <i class="fas fa-database me-2"></i>
                                Sauvegarde et Maintenance
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card border-success mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-download fa-2x text-success mb-2"></i>
                                            <h6>Sauvegarde complète</h6>
                                            <p class="text-muted small">Dernière: 22 Juin 2025</p>
                                            <button class="btn btn-success btn-sm">
                                                <i class="fas fa-download me-1"></i>
                                                Sauvegarder
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-warning mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-broom fa-2x text-warning mb-2"></i>
                                            <h6>Nettoyage cache</h6>
                                            <p class="text-muted small">Dernier: 23 Juin 2025</p>
                                            <button class="btn btn-warning btn-sm">
                                                <i class="fas fa-broom me-1"></i>
                                                Nettoyer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-info mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                            <h6>Optimisation DB</h6>
                                            <p class="text-muted small">Dernière: 20 Juin 2025</p>
                                            <button class="btn btn-info btn-sm">
                                                <i class="fas fa-cogs me-1"></i>
                                                Optimiser
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modals -->
<!-- Modal de visualisation message -->
<div class="modal fade" id="viewMessageModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-eye me-2"></i> Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <form method="POST" action="admin/contacts/reply-message.php">
        <input type="hidden" name="id_contact" id="msg-id">
        <input type="hidden" name="email_contact" id="msg-email-hidden">

        <div class="modal-body">
          <p><strong>Nom :</strong> <span id="msg-name"></span></p>
          <p><strong>Email :</strong> <span id="msg-email"></span></p>
          <p><strong>Date :</strong> <span id="msg-date"></span></p>
          <p><strong>Sujet :</strong> <span id="msg-subject"></span></p>

          <hr>
          <h6 class="mt-3"><strong>Contenu du message</strong></h6>
          <div class="border rounded p-3 mt-2 bg-light" id="msg-body"></div>

          <hr>
          <div class="mb-3">
            <strong>Réponse :</strong>
            <textarea class="form-control mt-2" rows="4" name="reply_message" id="msg-reply" placeholder="Tapez votre réponse ici..." required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-paper-plane me-1"></i> Envoyer la réponse
          </button>
        </div>
      </form>

    </div>
  </div>
</div>


<!-- Modal de confirmation de suppression de message -->
<div class="modal fade" id="deleteContactModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin/contacts/delete-contact.php">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-exclamation-circle text-danger me-2"></i>
            Supprimer le message
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible.</p>
          <input type="hidden" name="id_contact" id="delete-contact-id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i> Supprimer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de visualisation profil utilisateur -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-user me-2"></i> Profil Utilisateur
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
<p><strong>Nom :</strong> <span id="profile-name"></span></p>
<p><strong>Email :</strong> <span id="profile-email"></span></p>
<p><strong>Rôle :</strong> <span id="profile-role"></span></p>
<p><strong>Statut :</strong> <span id="profile-status"></span></p>
<p><strong>Date d'inscription :</strong> <span id="profile-date"></span></p>
<hr>
<h6 class="text-primary mt-3">Abonnement</h6>
<p><strong>Type :</strong> <span id="sub-type"></span></p>
<p><strong>Période :</strong> <span id="sub-period"></span></p>
<p><strong>Séances / semaine :</strong> <span id="sub-sessions"></span></p>
<p><strong>Prix mensuel :</strong> <span id="sub-price"></span></p>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal d'édition utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-user-edit me-2"></i> Modifier l'utilisateur
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="admin/users/update-user.php">
        <div class="modal-body">
          <input type="hidden" name="id_user" id="edit-id">  
          <input type="hidden" name="original_email" id="edit-original-email">

          <div class="mb-3">
            <label class="form-label">Nom complet</label>
            <input type="text" class="form-control" name="full_name" id="edit-full-name">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="edit-email">
          </div>

          <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select class="form-select" name="role" id="edit-role">
              <option value="Utilisateur">Utilisateur</option>
              <option value="Modérateur">Modérateur</option>
              <option value="Administrateur">Administrateur</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select class="form-select" name="status" id="edit-status">
              <option value="Actif">Actif</option>
              <option value="Suspendu">Suspendu</option>
              <option value="Inactif">Inactif</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Sauvegarder
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de confirmation de suppression d'utilisateur -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <form method="POST" action="admin/users/delete-user.php">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
            Confirmer la suppression
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
          <input type="hidden" name="id_user" id="delete-user-id">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i>
            Supprimer
          </button>
        </div>
      </form>
      
    </div>
  </div>
</div>

<!-- Modal d'ajout d'utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin/users/add-user.php">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-user-plus me-2"></i> Nouvel utilisateur
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" name="surname" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select class="form-select" name="role">
              <option value="Utilisateur" selected>Utilisateur</option>
              <option value="Modérateur">Modérateur</option>
              <option value="Administrateur">Administrateur</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select class="form-select" name="status">
              <option value="Actif" selected>Actif</option>
              <option value="Suspendu">Suspendu</option>
              <option value="Inactif">Inactif</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check me-1"></i> Ajouter
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Ajouter un nouvel article -->
<div class="modal fade" id="addArticleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="admin/articles/add-article.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-plus me-2"></i> Nouvel article
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contenu</label>
            <textarea id="contenu" name="content" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Image (optionnel)</label>
            <input type="file" name="images[]" id="images" class="form-control" multiple>
          </div>
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select name="is_published" class="form-select">
              <option value="1">Publié</option>
              <option value="0">Brouillon</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tags</label>
            <div>
              <?php
                $tags = $pdo->query("SELECT * FROM tags")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($tags as $tag) {
                  echo '<div class="form-check form-check-inline">';
                  echo '<input class="form-check-input" type="checkbox" name="tags[]" id="tag_'.$tag['id_tag'].'" value="'.$tag['id_tag'].'">';
                  echo '<label class="form-check-label" for="tag_'.$tag['id_tag'].'">'.htmlspecialchars($tag['name_tag']).'</label>';
                  echo '</div>';
                }
              ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">Ajouter</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de visualisation article -->
<div class="modal fade" id="viewArticleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-eye me-2"></i> Aperçu de l'article
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h3 id="article-title" class="mb-3"></h3>
        <p class="text-muted" id="article-date"></p>
        <div id="article-images" class="d-flex flex-wrap gap-2 mb-4"></div>
        <div id="article-content" class="border-top pt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de modification d'article -->
<div class="modal fade" id="editArticleModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="editArticleForm" action="admin/articles/update-article.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id_article" id="edit-article-id">
          <input type="hidden" name="removed_images" id="removed-images">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Modifier l'article</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control" id="edit-article-title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contenu</label>
            <textarea id="edit-contenu" name="content" class="form-control"></textarea>
          </div>
          <div class="mb-3">
  <label class="form-label">Images actuelles</label>
  <div id="edit-article-images" class="d-flex flex-wrap gap-2"></div>
</div>
          <div class="mb-3">
            <label class="form-label">Ajouter des images</label>
            <input type="file" name="images[]" class="form-control" multiple>
          </div>
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select name="is_published" class="form-select" id="edit-is-published">
              <option value="1">Publié</option>
              <option value="0">Brouillon</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tags</label>
            <div id="edit-tags-checkboxes"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de suppression d'article -->
<div class="modal fade" id="deleteArticleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin/articles/delete-article.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-exclamation-circle text-danger me-2"></i>
            Supprimer l'article
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.</p>
          <input type="hidden" name="id_article" id="delete-article-id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i> Supprimer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>


    <!-- Chargement de TinyMCE depuis le CDN officiel (version 6) -->
    <script src="https://cdn.tiny.cloud/1/zjxgxpp6rpm7ifaolsdswo8r2ahm7qdoz0khaor20isldf7a/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Fichier de configuration personnalisé pour TinyMCE -->
    <script src="/le_Studio_Backend/js/tinymce-config.js"></script>

    <script>
    function cleanUpModalArtifacts() {
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
}
cleanUpModalArtifacts();
        // JavaScript pour les interactions
        document.addEventListener('DOMContentLoaded', function() {
        // Récupère les paramètres de l'URL (après le ?)
                const urlParams = new URLSearchParams(window.location.search);
    // Extrait la valeur du paramètre "tab"
    const tab = urlParams.get('tab');
    // Si un onglet est spécifié dans l'URL
    if (tab) {
         // Parcourt tous les liens de navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            // Si le lien correspond à l'onglet ciblé 
            if (link.getAttribute('href') === `#${tab}`) {
                // Affiche cet onglet avec Bootstrap
                new bootstrap.Tab(link).show();
            }
        });
    }

            // Gestion des onglets de navigation
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Retirer la classe active de tous les liens
                    navLinks.forEach(nl => nl.classList.remove('active'));
                    // Ajouter la classe active au lien cliqué
                    this.classList.add('active');
                });
            });

            // Animation des cartes statistiques
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Fonction de recherche en temps réel
            const searchInputs = document.querySelectorAll('.search-box input');
            searchInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const table = this.closest('.table-container').querySelector('table tbody');
                    const rows = table.querySelectorAll('tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });

            // Effets de notification (simulation)
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                notification.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(notification);

                // Auto-remove après 3 secondes
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 3000);
            }

            // Gestion responsive du sidebar
            function handleResize() {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                
                if (window.innerWidth <= 768) {
                    sidebar.style.transform = 'translateX(-100%)';
                    mainContent.style.marginLeft = '0';
                } else {
                    sidebar.style.transform = 'translateX(0)';
                    mainContent.style.marginLeft = '250px';
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize(); // Appel initial

            // Bouton pour toggle sidebar sur mobile
            const toggleButton = document.createElement('button');
            toggleButton.className = 'btn btn-primary position-fixed d-md-none';
            toggleButton.style.cssText = 'top: 20px; left: 20px; z-index: 1001;';
            toggleButton.innerHTML = '<i class="fas fa-bars"></i>';
            document.body.appendChild(toggleButton);

            toggleButton.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                const isHidden = sidebar.style.transform === 'translateX(-100%)';
                
                if (isHidden) {
                    sidebar.style.transform = 'translateX(0)';
                } else {
                    sidebar.style.transform = 'translateX(-100%)';
                }
            });
        });

// Affiche un message dans la modale à partir des données du bouton.
document.querySelectorAll('.view-message').forEach(button => {
  button.addEventListener('click', () => {
    document.getElementById('msg-id').value = button.dataset.id;
    document.getElementById('msg-name').textContent = button.dataset.name;
    document.getElementById('msg-email').textContent = button.dataset.email;
    document.getElementById('msg-email-hidden').value = button.dataset.email;
    document.getElementById('msg-date').textContent = button.dataset.date;
    document.getElementById('msg-subject').textContent = button.dataset.subject;
    document.getElementById('msg-body').textContent = button.dataset.message;
    document.getElementById('msg-reply').value = ''; // Очистка поля ответа
  });
});


// Ouvre la modale de suppression et insère l'ID du message à supprimer.
document.querySelectorAll('.delete-message-btn').forEach(button => {
    button.addEventListener('click', (event) => {
        const contactId = event.currentTarget.getAttribute('data-id');
        document.getElementById('delete-contact-id').value = contactId;

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteContactModal'));
        modal.show();
    });
});

// Affiche les informations détaillées d'un utilisateur dans la modale.
 document.querySelectorAll('.view-user').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('profile-name').textContent =
            `${button.dataset.name} ${button.dataset.surname}`;
        document.getElementById('profile-email').textContent = button.dataset.email;
        document.getElementById('profile-role').textContent = button.dataset.role;
        document.getElementById('profile-status').textContent = button.dataset.status;
        document.getElementById('profile-date').textContent = button.dataset.subscriptionDate;

        document.getElementById('sub-type').textContent = button.dataset.subType;
        document.getElementById('sub-period').textContent =
            `${button.dataset.subStart} → ${button.dataset.subEnd}`;
        document.getElementById('sub-sessions').textContent = button.dataset.subSessions;
        document.getElementById('sub-price').textContent =
            `${button.dataset.subPrice} € / mois`;
    });
});

// Pré-remplit le formulaire d'édition utilisateur avec les données du bouton cliqué.
document.querySelectorAll('.editUserBtn').forEach(button => {
    button.addEventListener('click', () => {
      const name = button.dataset.name || '';
      const surname = button.dataset.surname || '';
      const email = button.dataset.email || '';
      const role = button.dataset.role || '';
      const status = button.dataset.status || '';
      const id = button.dataset.id || '';

      document.getElementById('edit-id').value = id;
      document.getElementById('edit-original-email').value = email;
      document.getElementById('edit-full-name').value = name + ' ' + surname;
      document.getElementById('edit-email').value = email;
      document.getElementById('edit-role').value = role;
      document.getElementById('edit-status').value = status;
    });
  });

// Insère l'ID de l'utilisateur dans le formulaire de suppression.
document.querySelectorAll('.delete-user-btn').forEach(button => {
    button.addEventListener('click', (event) => {
        const userId = event.currentTarget.getAttribute('data-id');
        document.getElementById('delete-user-id').value = userId;
    });
});

// Au clic du bouton, le formulaire de modification est rempli avec l'ID et le statut actuel du message.
document.querySelectorAll('.update-status-btn').forEach(button => {
  button.addEventListener('click', () => {
    const id = button.dataset.id;
    const current = button.dataset.current;

    document.getElementById('status-id').value = id;
    document.getElementById('status-select').value = current;
  });
});


// Sélectionne tous les éléments HTML ayant la classe "status-clickable"
document.querySelectorAll('.status-clickable').forEach(badge => {
  // Ajoute un écouteur d'événement pour détecter le clic sur le badge
  badge.addEventListener('click', () => {
    const id = badge.dataset.id; // Récupère l'ID du message depuis l'attribut data-id
    const currentStatus = badge.dataset.current; // Récupère le statut actuel depuis l'attribut data-current

    // Détermine le prochain statut selon un ordre cyclique : Nouveau → Lu → Répondu → Nouveau
    const nextStatus = {
      'Nouveau': 'Lu',
      'Lu': 'Répondu',
      'Répondu': 'Nouveau'
    }[currentStatus] || 'Nouveau'; // Par défaut, si statut inconnu → Nouveau

    // Envoie une requête POST à PHP pour mettre à jour le statut dans la base de données
    fetch('update-message-status.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded' // Type de contenu pour formulaire
      },
      body: `id_contact=${encodeURIComponent(id)}&status_contact=${encodeURIComponent(nextStatus)}`
    })
    .then(res => res.json()) // Convertit la réponse en JSON
    .then(data => {
      if (data.success) {
        // Met à jour les données du badge avec le nouveau statut
        badge.dataset.current = nextStatus;
        badge.textContent = nextStatus;

        // Met à jour la classe CSS pour refléter le style du nouveau statut
        badge.classList.remove('status-new', 'status-read', 'status-replied');
        if (nextStatus === 'Nouveau') {
          badge.classList.add('status-new');
        } else if (nextStatus === 'Lu') {
          badge.classList.add('status-read');
        } else if (nextStatus === 'Répondu') {
          badge.classList.add('status-replied');
        }
      } else {
        // Affiche une erreur si la mise à jour a échoué côté serveur
        alert('Erreur lors de la mise à jour.');
      }
    });
  });
});


// Affiche une article dans la modale
document.querySelectorAll('.view-article').forEach(button => {
  button.addEventListener('click', () => {
    const title = button.dataset.title;
    const content = button.dataset.content;
    const date = new Date(button.dataset.date).toLocaleDateString('fr-FR', {
      day: '2-digit', month: 'short', year: 'numeric'
    });

    const images = JSON.parse(button.dataset.images || '[]');

    document.getElementById('article-title').textContent = title;
    document.getElementById('article-date').textContent = `Créé le : ${date}`;
    document.getElementById('article-content').innerHTML = content;

    const container = document.getElementById('article-images');
    container.innerHTML = '';

    images.forEach(filename => {
      const base = filename.substring(0, filename.lastIndexOf('.'));
      const ext = filename.substring(filename.lastIndexOf('.') + 1);
      const thumbPath = `/le_Studio_Backend/uploads/articles/thumb/${base}_thumb.${ext}`;

      const img = document.createElement('img');
      img.src = thumbPath;
      img.alt = 'Image';
      img.className = 'img-thumbnail';
      img.style.maxHeight = '150px';
      container.appendChild(img);
    });

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewArticleModal'));
    modal.show();
  });
});


// Initialisation de TinyMCE à l'ouverture de la modale d'ajout d'article
const addArticleModalEl = document.getElementById('addArticleModal');
if (addArticleModalEl) {
  addArticleModalEl.addEventListener('shown.bs.modal', function () {
    initTinyMCE('contenu');
  });
}

// --- Gestionnaire corrigé pour la modale d'édition d'article ---
document.querySelectorAll('.edit-article').forEach(button => {
  button.addEventListener('click', () => {
    // 🔍 Récupération des données à partir des attributs data-
    const id = button.dataset.id;
    const title = button.dataset.title;
    const content = button.dataset.content;
    const isPublished = button.dataset.isPublished;
    const images = JSON.parse(button.dataset.images || '[]');

    // Remplissage des champs du formulaire AVANT l'ouverture de la modale
    document.getElementById('edit-article-id').value = id;
    document.getElementById('edit-article-title').value = title;
    document.getElementById('edit-is-published').value = isPublished;
    document.getElementById('edit-contenu').value = content;

    // Affichage des images actuelles avec possibilité de suppression
    removedImages = [];
    const container = document.getElementById('edit-article-images');
    container.innerHTML = '';
    images.forEach(filename => {
      const base = filename.substring(0, filename.lastIndexOf('.'));
      const ext = filename.substring(filename.lastIndexOf('.') + 1);
      const thumbPath = `/le_Studio_Backend/uploads/articles/thumb/${base}_thumb.${ext}`;
      const wrapper = document.createElement('div');
      wrapper.className = 'position-relative me-2 mb-2';
      const img = document.createElement('img');
      img.src = thumbPath;
      img.className = 'img-thumbnail';
      img.style.maxHeight = '120px';
      const delBtn = document.createElement('button');
      delBtn.type = 'button';
      delBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
      delBtn.innerHTML = '<i class="fas fa-times"></i>';
      delBtn.addEventListener('click', () => {
        wrapper.remove();
        removedImages.push(filename);
      });
      wrapper.appendChild(img);
      wrapper.appendChild(delBtn);
      container.appendChild(wrapper);
    });

    // Réinitialisation de TinyMCE après remplissage des champs
    initTinyMCE('edit-contenu');

    // 📦 Ouverture de la modale d'édition
    const modalEl = document.getElementById('editArticleModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  });
});

// Envoie la liste des images supprimées dans le champ caché avant l'envoi du formulaire
document.getElementById('editArticleForm').addEventListener('submit', function () {
  const hiddenInput = document.getElementById('removed-images');
  if (hiddenInput) {
    hiddenInput.value = JSON.stringify(removedImages || []);
  }
});

// Gestionnaire de suppression d'article
document.querySelectorAll('.delete-article').forEach(button => {
  button.addEventListener('click', () => {
    const articleId = button.dataset.id;
    document.getElementById('delete-article-id').value = articleId;
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteArticleModal'));
    modal.show();
  });
});


// Vérification de la taille des fichiers avant l'envoi (max 5 Mo)
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const files = e.target.files;
    for (let file of files) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Fichier trop volumineux (maximum 5 Mo)');
            e.target.value = '';
            return;
        }
    }
});

    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Динамическая подгрузка тегов для добавления статьи
      const addArticleModal = document.getElementById('addArticleModal');
      if (addArticleModal) {
        addArticleModal.addEventListener('show.bs.modal', function () {
          fetch('admin/articles/get-tags.php')
            .then(response => response.json())
            .then(tags => {
              let html = '';
              tags.forEach(tag => {
                html += `<div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="tags[]" id="tag_${tag.id_tag}" value="${tag.id_tag}">
                  <label class="form-check-label" for="tag_${tag.id_tag}">${tag.name_tag}</label>
                </div>`;
              });
              const tagsContainer = addArticleModal.querySelector('#add-tags-checkboxes');
              if (tagsContainer) tagsContainer.innerHTML = html;
            });
        });
      }

      // Динамическая подгрузка и автозаполнение тегов для редактирования статьи
      const editArticleModal = document.getElementById('editArticleModal');
      if (editArticleModal) {
        editArticleModal.addEventListener('show.bs.modal', function (event) {
          let blogId = null;
          if (event.relatedTarget && event.relatedTarget.dataset.id) {
            blogId = event.relatedTarget.dataset.id;
          } else {
            blogId = document.getElementById('edit-article-id').value;
          }
          if (!blogId) return;
          fetch('admin/articles/get-tags.php?blog_id=' + encodeURIComponent(blogId))
            .then(response => response.json())
            .then(data => {
              let html = '';
              data.tags.forEach(tag => {
                const checked = data.selected.includes(tag.id_tag) ? 'checked' : '';
                html += `<div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="tags[]" id="edit_tag_${tag.id_tag}" value="${tag.id_tag}" ${checked}>
                  <label class="form-check-label" for="edit_tag_${tag.id_tag}">${tag.name_tag}</label>
                </div>`;
              });
              const tagsContainer = editArticleModal.querySelector('#edit-tags-checkboxes');
              if (tagsContainer) tagsContainer.innerHTML = html;
            });
        });
      }
    });
    </script>
</body>
</html>