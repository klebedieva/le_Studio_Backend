<?php
session_start();

// 🔐 Génération du token CSRF s’il n’existe pas déjà
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/config/database.php'; 

// Récupérer l'identifiant de l'utilisateur connecté
$user_id = $_SESSION['user']['id_user'] ?? null;

// Si l'utilisateur n'est pas connecté, le rediriger vers la page de connexion
if (!$user_id) {
    header('Location: login.php');
    exit;
}

try {
    // Получение данных пользователя
    $stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: login.php');
        exit();
    }

    // Получение активной подписки пользователя
    $stmt = $pdo->prepare("
        SELECT s.*, 
               DATEDIFF(s.subscription_end, CURDATE()) as days_remaining,
               CASE 
                   WHEN s.subscription_end < CURDATE() THEN 'Expirée'
                   WHEN DATEDIFF(s.subscription_end, CURDATE()) <= 7 THEN 'Expire bientôt'
                   ELSE 'Active'
               END as subscription_status
        FROM subscriptions s 
        WHERE s.id = (
            SELECT MAX(id) FROM subscriptions 
            WHERE status = 'Actif'
        )
        LIMIT 1
    ");
    $stmt->execute();
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calcul des statistiques utilisateur
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_visits,
        SUM(MONTH(session_date) = MONTH(CURDATE()) AND YEAR(session_date) = YEAR(CURDATE())) AS visits_this_month
    FROM sessions
    WHERE id_user = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

$total_visits = $stats['total_visits'] ?? 0;
$visits_this_month = $stats['visits_this_month'] ?? 0;
$avg_per_week = $visits_this_month > 0 ? round($visits_this_month / 4, 1) : 0;

    // Récupération des prochaines séances depuis la base de données
$stmt = $pdo->prepare("
    SELECT session_type, trainer_name, session_date, session_time, duration
    FROM sessions
    WHERE id_user = ? AND session_date >= CURDATE()
    ORDER BY session_date ASC, session_time ASC
    LIMIT 5
");
$stmt->execute([$user_id]);
$upcoming_sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des cours disponibles aujourd'hui (sessions ouvertes à tous)
$stmt = $pdo->prepare("
    SELECT session_type AS name, session_time AS time, 10 AS spots
    FROM sessions
    WHERE session_date = CURDATE()
    ORDER BY session_time ASC
");
$stmt->execute();
$available_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des derniers contacts/messages (optionnel)
    $stmt = $pdo->prepare("
        SELECT * FROM contact 
        WHERE id_user = ? 
        ORDER BY creation_date_contact DESC 
        LIMIT 3
    ");
    $stmt->execute([$user_id]);
    $recent_contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erreur base de données: " . $e->getMessage());
    // Données de fallback en cas d'erreur
    $user = [
        'name_user' => 'Utilisateur',
        'surname_user' => 'Inconnu',
        'email_user' => 'user@example.com',
        'subscription_date_user' => date('Y-m-d'),
        'role_user' => 'Utilisateur',
        'status_user' => 'Actif'
    ];
    $subscription = null;
    $visits_this_month = 0;
    $total_visits = 0;
    $avg_per_week = 0;
}

// Formatage des données pour l'affichage
$full_name = trim($user['name_user'] . ' ' . $user['surname_user']);
$first_name = $user['name_user'];
$membership_type = $subscription ? $subscription['subscription_type'] : 'Aucun abonnement';
$member_since = $user['subscription_date_user'];
$next_payment = $subscription ? $subscription['subscription_end'] : null;
$subscription_status = $subscription ? $subscription['subscription_status'] : 'Inactif';

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Le Studio Sport & Coaching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --studio-gold: #DAB978;
            --studio-dark: #2C2C2C;
            --studio-light: #F8F9FA;
            --studio-accent: #B8860B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--studio-light);
            line-height: 1.6;
        }

        /* Header Styles */
        .header {
            background-color: var(--studio-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            color: var(--studio-gold);
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--studio-gold);
        }

        .user-info {
            color: white;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--studio-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--studio-dark);
            font-weight: bold;
        }

        /* Dashboard Styles */
        .dashboard-container {
            padding: 2rem 0;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--studio-dark) 0%, #404040 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: var(--studio-gold);
            font-size: 1.2rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .card-title {
            color: var(--studio-dark);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-icon {
            color: var(--studio-gold);
            font-size: 1.2rem;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--studio-gold);
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .membership-badge {
            background: var(--studio-gold);
            color: var(--studio-dark);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .membership-badge.expired {
            background: #dc3545;
            color: white;
        }

        .membership-badge.expiring {
            background: #ffc107;
            color: var(--studio-dark);
        }

        .session-item {
            background: #f8f9fa;
            border-left: 4px solid var(--studio-gold);
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s ease;
        }

        .session-item:hover {
            background: #f0f0f0;
            transform: translateX(5px);
        }

        .session-type {
            font-weight: bold;
            color: var(--studio-dark);
            font-size: 1.1rem;
        }

        .session-details {
            color: #666;
            margin-top: 0.5rem;
        }

        .btn-studio {
            background: var(--studio-gold);
            color: var(--studio-dark);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-studio:hover {
            background: var(--studio-accent);
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-studio {
            border: 2px solid var(--studio-gold);
            color: var(--studio-gold);
            background: transparent;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-studio:hover {
            background: var(--studio-gold);
            color: var(--studio-dark);
        }

        .class-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .class-item:hover {
            border-color: var(--studio-gold);
            box-shadow: 0 2px 10px rgba(218, 185, 120, 0.2);
        }

        .spots-badge {
            background: var(--studio-gold);
            color: var(--studio-dark);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        /* .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(var(--studio-gold) <?php echo ($visits_this_month * 24); ?>%, #e9ecef <?php echo ($visits_this_month * 24); ?>%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            position: relative;
        } */

        .progress-circle::before {
            content: '';
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--studio-dark);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            background: white;
            border: 2px solid var(--studio-gold);
            color: var(--studio-dark);
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn:hover {
            background: var(--studio-gold);
            color: var(--studio-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(218, 185, 120, 0.3);
        }

        .action-icon {
            font-size: 2rem;
            color: var(--studio-gold);
        }

        .action-btn:hover .action-icon {
            color: var(--studio-dark);
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .btn-orange {
    background-color:rgb(162, 135, 68);
    color: white;
    border: none;
}

.btn-orange:hover {
    background-color: #996c00;
    color: white;
}

.modal-content {
    background-color: white;
    color: black;
}


        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .dashboard-card {
                padding: 1.5rem;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="indexv2.php" class="logo"> <img src="./assets/img/logo.png" alt="Le Studio" height="50"></a>
                
                <nav class="nav-links">
                    <a href="#" class="active">Tableau de Bord</a>
                    <a href="#">Planning</a>
                    <a href="#">Mes Réservations</a>
                    <a href="#">Profil</a>
                </nav>
                
                <div class="user-info">
                    <span><?php echo htmlspecialchars($full_name); ?></span>
                    <div class="user-avatar">
                    <?php echo strtoupper(substr($user['name_user'], 0, 1) . substr($user['surname_user'], 0, 1)); ?>
                    </div>

                    <a href="actions/logout.php" class="btn btn-sm btn-outline-light ms-2">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="welcome-title">Bonjour, <?php echo htmlspecialchars($first_name); ?> !</h1>
                    <p class="welcome-subtitle">Prêt(e) pour votre prochaine séance d'entraînement ?</p>
                    <div class="mt-3 d-flex gap-3 flex-wrap">
                    

                    <!-- Nouveau bouton pour ouvrir la modale -->
<button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
  <i class="fas fa-paper-plane me-2"></i> Envoyer un message
</button>

<!-- Modal -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="send-message.php">
        <div class="modal-header">
          <h5 class="modal-title" id="sendMessageModalLabel">
            <i class="fas fa-envelope me-2"></i> Envoyer un message
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($user_id); ?>">

            <div class="mb-3">
    <label class="form-label">Nom complet</label>
    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name_user'] . ' ' . $user['surname_user']); ?>" readonly>
  </div>

          <div class="mb-3">
            <label for="sujet" class="form-label">Sujet</label>
            <input type="text" name="sujet" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-orange">Envoyer</button>
        </div>
      </form>
    </div>
  </div>
</div>

                    <a href="#messages" class="btn btn-orange">
                    <i class="fas fa-inbox me-2"></i> Voir mes messages
                    </a>
                    </div>
                    <?php if ($user['status_user'] !== 'Actif'): ?>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            Votre compte n'est pas actif. Contactez l'administration.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-center">
                    <div class="progress-circle">
                        <div class="progress-text"><?php echo $visits_this_month; ?></div>
                    </div>
                    <p class="text-white">Visites ce mois</p>
                </div>
            </div>
        </div>
    </section>



     <?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
    <!-- Dashboard Content -->
    <div class="container dashboard-container">
        <div class="row">
            <!-- Membership Info -->
            <div class="col-lg-4 col-md-6">
                <div class="dashboard-card">
                    <h3 class="card-title">
                        <i class="fas fa-crown card-icon"></i>
                        Mon Abonnement
                    </h3>
                    <div class="text-center mb-3">
                        <span class="membership-badge <?php 
                            if ($subscription) {
                                echo $subscription['subscription_status'] === 'Expirée' ? 'expired' : 
                                     ($subscription['subscription_status'] === 'Expire bientôt' ? 'expiring' : '');
                            } else {
                                echo 'expired';
                            }
                        ?>">
                            <?php echo htmlspecialchars($membership_type); ?>
                        </span>
                    </div>
                    
                    <?php if ($subscription): ?>
                        <div class="stat-row">
                            <span class="stat-label">Membre depuis</span>
                            <span class="stat-value"><?php echo date('d/m/Y', strtotime($member_since)); ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Fin d'abonnement</span>
                            <span class="stat-value"><?php echo date('d/m/Y', strtotime($subscription['subscription_end'])); ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Séances/semaine</span>
                            <span class="stat-value"><?php echo $subscription['weekly_sessions']; ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Prix mensuel</span>
                            <span class="stat-value"><?php echo number_format($subscription['monthly_price'], 2); ?>€</span>
                        </div>
                        <?php if ($subscription['days_remaining'] <= 7 && $subscription['days_remaining'] > 0): ?>
                            <div class="alert alert-warning mt-3">
                                <small><i class="fas fa-exclamation-triangle"></i> 
                                Votre abonnement expire dans <?php echo $subscription['days_remaining']; ?> jour(s)</small>
                            </div>
                        <?php elseif ($subscription['days_remaining'] <= 0): ?>
                            <div class="alert alert-danger mt-3">
                                <small><i class="fas fa-times-circle"></i> 
                                Votre abonnement a expiré</small>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Aucun abonnement actif
                        </div>
                    <?php endif; ?>
                    
                    <div class="stat-row">
                        <span class="stat-label">Visites totales</span>
                        <span class="stat-value"><?php echo $total_visits; ?></span>
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn-outline-studio">Gérer l'abonnement</a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="col-lg-8 col-md-6">
                <div class="dashboard-card">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt card-icon"></i>
                        Prochaines Séances
                    </h3>
                    <?php if (!empty($upcoming_sessions)): ?>
                        <?php foreach($upcoming_sessions as $session): ?>
                            <div class="session-item">
                                <div class="session-type"><?php echo htmlspecialchars($session['session_type']); ?></div>
                                <div class="session-details">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($session['trainer_name']); ?> • 
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($session['session_date'])); ?> • 
                                    <i class="fas fa-clock"></i> <?php echo $session['session_time']; ?> (<?php echo $session['duration']; ?>)
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Aucune séance programmée
                        </div>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="#" class="btn-studio">Voir tout le planning</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quick Stats -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line card-icon"></i>
                        Mes Statistiques
                    </h3>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-value"><?php echo $total_visits; ?></div>
                            <div class="stat-label">Séances Totales</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-value"><?php echo $visits_this_month; ?></div>
                            <div class="stat-label">Ce Mois</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-value"><?php echo $avg_per_week; ?></div>
                            <div class="stat-label">Moy/Semaine</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-value">--</div>
                            <div class="stat-label">Poids Actuel</div>
                        </div>
                        <div class="col-6">
                            <div class="stat-value">--</div>
                            <div class="stat-label">Objectif</div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Mettez à jour vos données dans votre profil
                        </small>
                    </div>
                </div>
            </div>

            <!-- Available Classes Today -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <h3 class="card-title">
                        <i class="fas fa-dumbbell card-icon"></i>
                        Cours Disponibles Aujourd'hui
                    </h3>
                    <?php foreach($available_classes as $class): ?>
                        <div class="class-item">
                            <div>
                                <strong><?php echo htmlspecialchars($class['name']); ?></strong><br>
                                <small class="text-muted"><?php echo $class['time']; ?></small>
                            </div>
                            <div>
                                <span class="spots-badge"><?php echo $class['spots']; ?> places</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="#" class="btn-outline-studio">Réserver une place</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Messages (if any) -->
        <?php if (!empty($recent_contacts)): ?>
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card">
                    <h3 class="card-title">
                        <i class="fas fa-envelope card-icon"></i>
                        Mes Derniers Messages
                    </h3>
                    <?php foreach($recent_contacts as $contact): ?>
                        <div class="session-item">
                            <div class="session-type">
                                <?php echo htmlspecialchars($contact['subject_contact']); ?>
                                <span class="badge bg-<?php echo $contact['status_contact'] === 'Répondu' ? 'success' : 'warning'; ?> ms-2">
                                    <?php echo $contact['status_contact']; ?>
                                </span>
                            </div>
                            <div class="session-details">
                                <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($contact['creation_date_contact'])); ?>
                                <?php if (!empty($contact['message_contact'])): ?>
                                    <br><small><?php echo htmlspecialchars(substr($contact['message_contact'], 0, 100)) . '...'; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="#" class="action-btn">
                <i class="fas fa-calendar-plus action-icon"></i>
                <span>Réserver une Séance</span>
            </a>
            <a href="edit-profile.php" class="action-btn">
    <i class="fas fa-user-edit action-icon"></i>
    <span>Modifier Profil</span>
</a>

            <a href="#" class="action-btn">
                <i class="fas fa-history action-icon"></i>
                <span>Historique</span>
            </a>
            <a href="#" class="action-btn">
                <i class="fas fa-headset action-icon"></i>
                <span>Support</span>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer toutes les cartes du dashboard
        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Animation des éléments de session au hover
        document.querySelectorAll('.session-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(10px) scale(1.02)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0) scale(1)';
            });
        });

        // Mise à jour dynamique de l'heure
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const timeElements = document.querySelectorAll('.current-time');
            timeElements.forEach(el => el.textContent = timeString);
        }

        setInterval(updateTime, 1000);
        updateTime();

        // Notification simulation
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-success position-fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Simulation de réservation
        document.querySelectorAll('.class-item').forEach(item => {
            item.addEventListener('click', function() {
                const className = this.querySelector('strong').textContent;
                showNotification(`Réservation pour ${className} confirmée !`);
            });
        });

        // Alerte pour abonnements expirés
        <?php if ($subscription && $subscription['days_remaining'] <= 0): ?>
            setTimeout(() => {
                if (confirm('Votre abonnement a expiré. Souhaitez-vous le renouveler maintenant ?')) {
                    window.location.href = 'subscription.php';
                }
            }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>