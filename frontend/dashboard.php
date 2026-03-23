<?php
require_once __DIR__ . '/auth.php';

requireLogin();

$userId = $_SESSION['user']['id'];
$tasks = getUserTasks($userId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Task Manager</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- TON CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">Task Manager</a>

        <div class="d-flex">
            <span class="me-3">Bonjour <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
            <a href="logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <!-- FORMULAIRE -->
    <div class="card p-4 mb-4">
        <h2 class="mb-3">Ajouter une tâche</h2>

        <form method="POST" action="add_task.php">
            <input type="text" name="title" placeholder="Titre de la tâche" class="form-control mb-2" required>
            <input type="text" name="description" placeholder="Description" class="form-control mb-2">
            <button class="btn btn-primary">Ajouter</button>
        </form>
    </div>

    <!-- LISTE -->
    <div class="card p-4">
        <h3 class="mb-3">Mes tâches</h3>

        <?php if (empty($tasks)): ?>
            <p class="text-muted">Aucune tâche pour le moment</p>
        <?php endif; ?>

        <?php foreach ($tasks as $task): ?>
            <div class="task-item d-flex justify-content-between align-items-center mb-2 p-3">

                <div>
                    <strong><?= htmlspecialchars($task['title']) ?></strong><br>
                    <small><?= htmlspecialchars($task['description']) ?></small>
                </div>

                <form method="POST" action="delete_task.php">
                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                    <button class="btn btn-danger btn-sm">Supprimer</button>
                </form>

            </div>
        <?php endforeach; ?>

    </div>

</div>

</body>
</html>