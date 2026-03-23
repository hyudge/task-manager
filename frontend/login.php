<?php
/**
 * login.php
 *
 * Formulaire de connexion.
 * Utilise loginUser() depuis auth.php pour valider les identifiants.
 */
require_once __DIR__ . '/auth.php';

// Si l'utilisateur est déjà connecté, on le redirige directement.
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailValue = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($emailValue === '' || $password === '') {
        $errors[] = 'Email et mot de passe requis.';
    } elseif (!loginUser($emailValue, $password)) {
        $errors[] = 'Email ou mot de passe incorrect.';
    } else {
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">Task Manager</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
        <div class="card-body p-5">
            <h2 class="card-title text-center mb-4">Connexion</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="votre@email.com" value="<?= htmlspecialchars($emailValue) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>
            </form>

            <hr>

            <p class="text-center text-muted mb-0">
                Pas encore de compte ? <a href="register.php" class="text-primary fw-bold text-decoration-none">S'inscrire</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>