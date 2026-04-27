<?php
/**
 * login.php
 *
 * Page de connexion utilisateur.
 * Permet de vérifier les identifiants et de connecter l'utilisateur.
 */

// Inclusion du backend (auth, session, BDD, fonctions loginUser)
require_once __DIR__ . '/../backend/auth.php';

// Si l'utilisateur est déjà connecté → redirection vers dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Tableau qui stocke les erreurs d'affichage
$errors = [];

// Variable pour garder l'email dans le formulaire en cas d'erreur
$emailValue = '';

// Vérifie si le formulaire a été envoyé en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et nettoyage de l'email
    $emailValue = trim($_POST['email'] ?? '');

    // Récupération du mot de passe
    $password = $_POST['password'] ?? '';

    // Vérifie que les champs ne sont pas vides
    if ($emailValue === '' || $password === '') {
        $errors[] = 'Email et mot de passe requis.';

    // Sinon on tente la connexion
    } elseif (!loginUser($emailValue, $password)) {
        $errors[] = 'Email ou mot de passe incorrect.';

    } else {
        // Connexion réussie → redirection vers dashboard
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Responsive mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion - Task Manager</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<!-- Background spécifique page auth -->
<body class="auth-bg">

<!-- CONTENEUR CENTRÉ -->
<div class="container-fluid h-100 d-flex align-items-center justify-content-center py-5">

    <!-- CARD DE CONNEXION -->
    <div class="card auth-card">

        <div class="card-body text-center">

            <!-- Logo -->
            <div class="mb-4">
                <i class="fas fa-tasks" style="font-size: 3rem; color: #667eea;"></i>
            </div>

            <!-- TITRE -->
            <h1 class="auth-title">Connexion</h1>
            <p class="auth-subtitle">Accédez à votre Task Manager</p>

            <!-- AFFICHAGE ERREURS -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                    <!-- Affiche toutes les erreurs ligne par ligne -->
                    <i class="fas fa-exclamation-circle"></i>
                    <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>

                    <!-- bouton fermer alert Bootstrap -->
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORMULAIRE -->
            <form method="post" novalidate>

                <!-- EMAIL -->
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>

                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        id="email" 
                        placeholder="votre@email.com"
                        value="<?= htmlspecialchars($emailValue) ?>"
                        required
                    >
                </div>

                <!-- MOT DE PASSE -->
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>

                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        id="password" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <!-- BOUTON LOGIN -->
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <!-- SEPARATEUR -->
            <div class="auth-divider">
                <span>Nouveau client ?</span>
            </div>

            <!-- REDIRECTION REGISTER -->
            <p class="text-muted mb-0">
                Créez un compte 
                <a href="register.php" class="auth-link">en cliquant ici</a>
            </p>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>