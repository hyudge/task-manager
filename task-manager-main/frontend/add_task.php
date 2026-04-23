<?php
require_once __DIR__ . '/../backend/auth.php';

requireLogin();

$error = isset($_GET['error']);
$success = isset($_GET['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une tâche - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="page-container">

<nav class="navbar navbar-expand-lg navbar-dark navbar-translucent shadow navbar-content">
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

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <a href="index.php" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Retour aux tâches
                </a>
                <h2 class="mb-4">
                    <i class="fas fa-plus-circle text-primary"></i> Ajouter une nouvelle tâche
                </h2>

        <?php if ($error): ?>
            <div class="alert alert-danger">Erreur lors de l'ajout de la tâche.</div>
        <?php endif; ?>

                <form action="../backend/add_task.php" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading"></i> Titre *
                        </label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Entrez le titre de votre tâche" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i> Description
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Décrivez votre tâche..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Date limite (optionnel)
                        </label>
                        <input type="datetime-local" id="due_date" name="due_date" class="form-control" placeholder="Sélectionnez la date et l'heure limites">
                        <small class="form-text text-muted">Si vous définissez une date limite, vous recevrez une alerte email si la tâche n'est pas complétée.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Ajouter la tâche
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
