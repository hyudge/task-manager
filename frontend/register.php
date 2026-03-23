<?php
/**
 * register.php
 *
 * Formulaire d'inscription. Crée un nouvel utilisateur via registerUser()
 * et redirige vers le dashboard après création.
 */
require_once __DIR__ . '/auth.php';

// Si l'utilisateur est déjà connecté, on le redirige vers le dashboard.
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$nameValue = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nameValue = trim($_POST['name'] ?? '');
    $emailValue = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nameValue === '' || $emailValue === '' || $password === '') {
        $errors[] = 'Tous les champs sont requis.';
    } else {
        $registered = registerUser($nameValue, $emailValue, $password, $registerError);
        if (!$registered) {
            $errors[] = $registerError ?? 'Impossible de créer le compte.';
        } else {
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h2 class="card-title text-center mb-4">Créer un compte</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Votre nom" value="<?= htmlspecialchars($nameValue) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="votre@email.com" value="<?= htmlspecialchars($emailValue) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">S'inscrire</button>
            </form>

            <hr>

            <p class="text-center text-muted mb-0">
                Déjà un compte ? <a href="login.php" class="text-primary fw-bold text-decoration-none">Se connecter</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>