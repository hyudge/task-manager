<?php
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- TON CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">Task Manager</a>

        <div class="d-flex">
            <a href="login.php" class="btn btn-primary btn-sm me-2">Connexion</a>
            <a href="register.php" class="btn btn-secondary btn-sm">Inscription</a>
        </div>
    </div>
</nav>

<!-- CONTENU -->
<div class="container py-5">

    <div class="card shadow-lg p-5 text-center">

        <h1 class="mb-3">Bienvenue 👋</h1>

        <p class="text-muted mb-4">
            Gérez vos tâches simplement et efficacement.
        </p>

        <div class="d-flex justify-content-center gap-3">
            <a href="login.php" class="btn btn-primary">Se connecter</a>
            <a href="register.php" class="btn btn-secondary">Créer un compte</a>
        </div>

    </div>

</div>

</body>
</html>