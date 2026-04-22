
<?php
/**
 * login.php
 *
 * Formulaire de connexion.
 * Utilise loginUser() depuis auth.php pour valider les identifiants.
 */
require_once __DIR__ . '/../backend/auth.php';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="auth-bg">

<div class="container-fluid h-100 d-flex align-items-center justify-content-center py-5">
    <div class="card auth-card">
        <div class="card-body text-center">
            <div class="mb-4">
                <i class="fas fa-tasks" style="font-size: 3rem; color: #667eea;"></i>
            </div>
            
            <h1 class="auth-title">Connexion</h1>
            <p class="auth-subtitle">Accédez à votre Task Manager</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="votre@email.com" value="<?= htmlspecialchars($emailValue) ?>" required>
                </div>

                <div class="mb-3 text-start">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="auth-divider">
                <span>Nouveau client ?</span>
            </div>

            <p class="text-muted mb-0">
                Créez un compte <a href="register.php" class="auth-link">en cliquant ici</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
