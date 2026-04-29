<?php
/**
 * register.php
 *
 * Page d'inscription utilisateur.
 * Permet de créer un compte avec registerUser().
 */

// Inclusion du backend (auth, session, BDD, fonctions)
require_once __DIR__ . '/../backend/auth.php';

// Si l'utilisateur est déjà connecté → redirection dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Tableau des erreurs à afficher
$errors = [];

// Variables pour garder les valeurs saisies (UX)
$nameValue = '';
$emailValue = '';

// Vérifie si formulaire envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération + nettoyage nom
    $nameValue = trim($_POST['name'] ?? '');

    // Récupération + nettoyage email
    $emailValue = trim($_POST['email'] ?? '');

    // Récupération mot de passe
    $password = $_POST['password'] ?? '';

    // Vérifie que tous les champs sont remplis
    if ($nameValue === '' || $emailValue === '' || $password === '') {
        $errors[] = 'Tous les champs sont requis.';

    } else {

        // Tentative d'inscription
        $registered = registerUser($nameValue, $emailValue, $password, $registerError);

        // Si échec → on récupère l'erreur du backend
        if (!$registered) {
            $errors[] = $registerError ?? 'Impossible de créer le compte.';

        } else {
            // Succès → redirection dashboard
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

    <!-- Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inscription - Task Manager</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS perso -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<!-- Background page auth -->
<body class="auth-bg">

<!-- CENTRAGE DU FORMULAIRE -->
<div class="container-fluid h-100 d-flex align-items-center justify-content-center py-5">

    <!-- CARD INSCRIPTION -->
    <div class="card auth-card">

        <div class="card-body text-center">

            <!-- LOGO -->
            <div class="mb-4">
                <i class="fas fa-tasks" style="font-size: 3rem; color: #667eea;"></i>
            </div>

            <!-- TITRE -->
            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-subtitle">Rejoignez Task Manager</p>

            <!-- AFFICHAGE ERREURS -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show">

                    <!-- Liste des erreurs -->
                    <i class="fas fa-exclamation-circle"></i>
                    <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>

                    <!-- bouton fermeture alert -->
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORMULAIRE -->
            <form method="post" novalidate>

                <!-- NOM -->
                <div class="mb-3 text-start">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Nom complet
                    </label>

                    <input 
                        type="text" 
                        name="name" 
                        class="form-control" 
                        id="name" 
                        placeholder="Votre nom"
                        value="<?= htmlspecialchars($nameValue) ?>"
                        required
                    >
                </div>

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

                    <!-- INFOS MOT DE PASSE -->
                    <small class="form-text text-muted d-block mt-2 mb-2">
                        <strong>Le mot de passe doit contenir :</strong>
                    </small>

                    <!-- CRITÈRES -->
                    <div class="password-requirements">

                        <small class="requirement requirement-length">
                            <i class="fas fa-circle"></i> Au minimum 12 caractères
                        </small>

                        <small class="requirement requirement-uppercase">
                            <i class="fas fa-circle"></i> Au moins une majuscule
                        </small>

                        <small class="requirement requirement-special">
                            <i class="fas fa-circle"></i> Au moins un caractère spécial
                        </small>

                    </div>
                </div>

                <!-- BOUTON INSCRIPTION -->
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>

            <!-- DIVIDER -->
            <div class="auth-divider">
                <span>Déjà inscrit ?</span>
            </div>

            <!-- REDIRECTION LOGIN -->
            <p class="text-muted mb-0">
                Connectez-vous 
                <a href="login.php" class="auth-link">en cliquant ici</a>
            </p>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- VALIDATION MOT DE PASSE EN LIVE -->
<script>
document.getElementById('password').addEventListener('input', function(e) {

    const password = e.target.value;

    // LONGUEUR
    const lengthReq = document.querySelector('.requirement-length');
    if (password.length >= 12) {
        lengthReq.classList.add('valid');
        lengthReq.classList.remove('invalid');
    } else {
        lengthReq.classList.add('invalid');
        lengthReq.classList.remove('valid');
    }

    // MAJUSCULE
    const uppercaseReq = document.querySelector('.requirement-uppercase');
    if (/[A-Z]/.test(password)) {
        uppercaseReq.classList.add('valid');
        uppercaseReq.classList.remove('invalid');
    } else {
        uppercaseReq.classList.add('invalid');
        uppercaseReq.classList.remove('valid');
    }

    // CARACTÈRE SPÉCIAL
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