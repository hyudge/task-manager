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
            
            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-subtitle">Rejoignez Task Manager</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3 text-start">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Nom complet
                    </label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Votre nom" value="<?= htmlspecialchars($nameValue) ?>" required>
                </div>

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
                    
                    <!-- Critères du mot de passe -->
                    <small class="form-text text-muted d-block mt-2 mb-2">
                        <strong>Le mot de passe doit contenir :</strong>
                    </small>
                    <div class="password-requirements">
                        <small class="requirement requirement-length">
                            <i class="fas fa-circle"></i> Au minimum 12 caractères
                        </small>
                        <small class="requirement requirement-uppercase">
                            <i class="fas fa-circle"></i> Au moins une majuscule
                        </small>
                        <small class="requirement requirement-special">
                            <i class="fas fa-circle"></i> Au moins un caractère spécial (!@#$%^&*...)
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>

            <div class="auth-divider">
                <span>Déjà inscrit ?</span>
            </div>

            <p class="text-muted mb-0">
                Connectez-vous <a href="login.php" class="auth-link">en cliquant ici</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript pour validation des critères -->
<script>
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;

    // Vérifier la longueur
    const lengthReq = document.querySelector('.requirement-length');
    if (password.length >= 12) {
        lengthReq.classList.add('valid');
        lengthReq.classList.remove('invalid');
    } else {
        lengthReq.classList.add('invalid');
        lengthReq.classList.remove('valid');
    }

    // Vérifier la majuscule
    const uppercaseReq = document.querySelector('.requirement-uppercase');
    if (/[A-Z]/.test(password)) {
        uppercaseReq.classList.add('valid');
        uppercaseReq.classList.remove('invalid');
    } else {
        uppercaseReq.classList.add('invalid');
        uppercaseReq.classList.remove('valid');
    }

    // Vérifier le caractère spécial
    const specialReq = document.querySelector('.requirement-special');
    if (/[!@#$%^&*()_+\-=\[\]{}|;'":,./<>?]/.test(password)) {
        specialReq.classList.add('valid');
        specialReq.classList.remove('invalid');
    } else {
        specialReq.classList.add('invalid');
        specialReq.classList.remove('valid');
    }
});
</script>
</body>
</html>