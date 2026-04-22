<?php
require_once __DIR__ . '/../backend/auth.php';

requireLogin();

$userId = $_SESSION['user']['id'];
$tasks = getUserTasks($userId);

// Séparer les tâches complétées et non complétées
$activeTasks = array_filter($tasks, fn($t) => empty($t['completed']));
$completedTasks = array_filter($tasks, fn($t) => !empty($t['completed']));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Tâches - Task Manager</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- TON CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="dashboard-bg">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-tasks"></i> Task Manager
        </a>

        <div class="d-flex align-items-center">
            <span class="me-3 text-white">
                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </span>
            <a href="logout.php" class="btn btn-light btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="mb-2">Mes Tâches</h1>
            <p>
                <strong><?= count($activeTasks) ?></strong> tâche(s) active(s) • 
                <strong><?= count($completedTasks) ?></strong> tâche(s) complétée(s)
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="add_task.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Nouvelle tâche
            </a>
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Opération réussie !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> Une erreur est survenue.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Contenu des tâches -->
    <div class="dashboard-content">
        <!-- Tâches actives -->
        <div class="mb-5">
            <h3 class="mb-3">
                <i class="fas fa-circle-exclamation text-warning"></i> Tâches actives
            </h3>

            <?php if (empty($activeTasks)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucune tâche active. Bravo !
                </div>
            <?php else: ?>
                <?php foreach ($activeTasks as $task): ?>
                    <div class="task-item p-4 mb-3">
                        <div class="row align-items-start">
                            <div class="col-md-8">
                                <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                                <?php if (!empty($task['description'])): ?>
                                    <div class="task-description"><?= htmlspecialchars($task['description']) ?></div>
                                <?php endif; ?>
                                <div class="task-date">
                                    <i class="fas fa-calendar"></i>
                                    Créée le <?= date('d/m/Y à H:i', strtotime($task['created_at'] ?? 'now')) ?>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group-task justify-content-end">
                                    <a href="../backend/toggle_task.php?id=<?= urlencode($task['id']) ?>" 
                                       class="btn btn-success btn-sm"
                                       title="Marquer comme complétée">
                                        <i class="fas fa-check"></i> Compléter
                                    </a>
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

        <!-- Tâches complétées -->
        <?php if (!empty($completedTasks)): ?>
            <div>
                <h3 class="mb-3">
                    <i class="fas fa-check-circle text-success"></i> Tâches complétées
                </h3>

                <?php foreach ($completedTasks as $task): ?>
                    <div class="task-item completed p-4 mb-3">
                        <div class="row align-items-start">
                            <div class="col-md-8">
                                <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                                <?php if (!empty($task['description'])): ?>
                                    <div class="task-description"><?= htmlspecialchars($task['description']) ?></div>
                                <?php endif; ?>
                                <div class="task-date">
                                    <i class="fas fa-calendar"></i>
                                    Créée le <?= date('d/m/Y à H:i', strtotime($task['created_at'] ?? 'now')) ?>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group-task justify-content-end">
                                    <a href="../backend/toggle_task.php?id=<?= urlencode($task['id']) ?>" 
                                       class="btn btn-warning btn-sm"
                                       title="Marquer comme non complétée">
                                        <i class="fas fa-redo"></i> Réactiver
                                    </a>
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
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>