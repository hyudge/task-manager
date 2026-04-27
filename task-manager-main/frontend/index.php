<?php
// Inclusion du fichier auth.php qui contient la connexion à la BDD + gestion session + fonctions
require_once __DIR__ . '/../backend/auth.php';

// Vérifie que l'utilisateur est connecté, sinon redirection vers login
requireLogin();

// Récupère l'id de l'utilisateur connecté depuis la session
$userId = $_SESSION['user']['id'];

// Récupère toutes les tâches de l'utilisateur depuis la base de données
$tasks = getUserTasks($userId);

// Sépare les tâches en 2 catégories :
// - tâches actives (non complétées)
// - tâches complétées
$activeTasks = array_filter($tasks, fn($t) => empty($t['completed']));
$completedTasks = array_filter($tasks, fn($t) => !empty($t['completed']));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Titre de la page -->
    <title>Mes Tâches - Task Manager</title>

    <!-- Bootstrap (framework CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (icônes) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<!-- Classe CSS pour le style du dashboard -->
<body class="dashboard-bg">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container-fluid">

        <!-- Nom de l'application -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-tasks"></i> Task Manager
        </a>

        <!-- Partie droite de la navbar -->
        <div class="d-flex align-items-center">

            <!-- Nom de l'utilisateur connecté -->
            <span class="me-3 text-white">
                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </span>

            <!-- Bouton déconnexion -->
            <a href="logout.php" class="btn btn-light btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>

<!-- CONTENEUR PRINCIPAL -->
<div class="dashboard-container">

    <!-- HEADER du dashboard -->
    <div class="dashboard-header row mb-4 align-items-center">

        <div class="col-md-8">

            <!-- Titre -->
            <h1 class="mb-2">Mes Tâches</h1>

            <!-- Statistiques -->
            <p>
                <strong><?= count($activeTasks) ?></strong> tâche(s) active(s) • 
                <strong><?= count($completedTasks) ?></strong> tâche(s) complétée(s)
            </p>
        </div>

        <!-- Bouton ajouter tâche -->
        <div class="col-md-4 text-end">
            <a href="add_task.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Nouvelle tâche
            </a>
        </div>
    </div>

    <!-- MESSAGE SUCCÈS -->
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Opération réussie !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- MESSAGE ERREUR -->
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> Une erreur est survenue.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- SECTION CONTENU -->
    <div class="dashboard-content">

        <!-- TÂCHES ACTIVES -->
        <div class="mb-5">

            <h3 class="mb-3">
                <i class="fas fa-circle-exclamation text-warning"></i> Tâches actives
            </h3>

            <!-- Si aucune tâche active -->
            <?php if (empty($activeTasks)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucune tâche active. Bravo !
                </div>
            <?php else: ?>

                <!-- Boucle sur les tâches actives -->
                <?php foreach ($activeTasks as $task): ?>
                    <div class="task-item p-4 mb-3">

                        <div class="row align-items-start">

                            <!-- Partie texte -->
                            <div class="col-md-8">

                                <!-- Titre -->
                                <div class="task-title">
                                    <?= htmlspecialchars($task['title']) ?>
                                </div>

                                <!-- Description -->
                                <?php if (!empty($task['description'])): ?>
                                    <div class="task-description">
                                        <?= htmlspecialchars($task['description']) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Date de création -->
                                <div class="task-date">
                                    <i class="fas fa-calendar"></i>
                                    Créée le <?= date('d/m/Y à H:i', strtotime($task['created_at'] ?? 'now')) ?>
                                </div>

                                <!-- Date limite -->
                                <?php if (!empty($task['due_date'])): ?>

                                    <?php 
                                        // Conversion de la date limite
                                        $dueDate = new DateTime($task['due_date']);
                                        $now = new DateTime();

                                        // Vérifie si la tâche est en retard
                                        $isOverdue = $dueDate < $now;
                                    ?>

                                    <div class="task-due-date">
                                        <i class="fas fa-hourglass-end"></i>
                                        Échéance : <?= $dueDate->format('d/m/Y à H:i') ?>

                                        <!-- Badge selon état -->
                                        <?php if ($isOverdue): ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i> EN RETARD
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i>
                                                <?= $dueDate->diff($now)->format('%d j %h h') ?> restants
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- BOUTONS ACTIONS -->
                            <div class="col-md-4 text-end">

                                <div class="btn-group-task justify-content-end">

                                    <!-- Marquer comme complétée -->
                                    <a href="../backend/toggle_task.php?id=<?= urlencode($task['id']) ?>" 
                                       class="btn btn-success btn-sm"
                                       title="Marquer comme complétée">
                                        <i class="fas fa-check"></i> Compléter
                                    </a>

                                    <!-- Supprimer tâche -->
                                    <a href="../backend/delete_task.php?id=<?= urlencode($task['id']) ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Êtes-vous sûr ?')"
                                       title="Supprimer la tâche">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- TÂCHES COMPLÉTÉES -->
        <?php if (!empty($completedTasks)): ?>

            <div>
                <h3 class="mb-3">
                    <i class="fas fa-check-circle text-success"></i> Tâches complétées
                </h3>

                <!-- Boucle tâches complétées -->
                <?php foreach ($completedTasks as $task): ?>
                    <div class="task-item completed p-4 mb-3">

                        <div class="row align-items-start">

                            <div class="col-md-8">

                                <div class="task-title">
                                    <?= htmlspecialchars($task['title']) ?>
                                </div>

                                <?php if (!empty($task['description'])): ?>
                                    <div class="task-description">
                                        <?= htmlspecialchars($task['description']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="task-date">
                                    <i class="fas fa-calendar"></i>
                                    Créée le <?= date('d/m/Y à H:i', strtotime($task['created_at'] ?? 'now')) ?>
                                </div>

                                <?php if (!empty($task['due_date'])): ?>
                                    <div class="task-due-date">
                                        <i class="fas fa-hourglass-end"></i>
                                        Échéance : <?= date('d/m/Y à H:i', strtotime($task['due_date'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- BOUTONS -->
                            <div class="col-md-4 text-end">

                                <div class="btn-group-task justify-content-end">

                                    <!-- Réactiver tâche -->
                                    <a href="../backend/toggle_task.php?id=<?= urlencode($task['id']) ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-redo"></i> Réactiver
                                    </a>

                                    <!-- Supprimer -->
                                    <a href="../backend/delete_task.php?id=<?= urlencode($task['id']) ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Êtes-vous sûr ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</div>

<!-- Bootstrap JS (pour les alerts et composants interactifs) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>