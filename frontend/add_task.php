<?php
// On inclut le fichier d'authentification (session, fonctions, BDD)
require_once __DIR__ . '/../backend/auth.php';

// Vérifie que l'utilisateur est connecté
// Sinon → redirection vers login
requireLogin();

// Vérifie si un paramètre "error" est présent dans l'URL
$error = isset($_GET['error']);

// Vérifie si un paramètre "success" est présent dans l'URL
$success = isset($_GET['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Titre de la page -->
    <title>Ajouter une tâche - Task Manager</title>

    <!-- Bootstrap pour le style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Ton CSS personnalisé -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<!-- Classe personnalisée pour le style global -->
<body class="page-container">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-translucent shadow navbar-content">
    <div class="container-fluid">

        <!-- Logo / titre du site -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-tasks"></i> Task Manager
        </a>

        <!-- Partie droite de la navbar -->
        <div class="d-flex align-items-center">

            <!-- Nom de l'utilisateur connecté (sécurisé avec htmlspecialchars) -->
            <span class="me-3 text-white">
                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </span>

            <!-- Bouton de déconnexion -->
            <a href="logout.php" class="btn btn-light btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>

<!-- CONTENU PRINCIPAL -->
<div class="main-content">
    <div class="container">

        <!-- Carte Bootstrap -->
        <div class="card">
            <div class="card-body">

                <!-- Bouton retour -->
                <a href="index.php" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Retour aux tâches
                </a>

                <!-- Titre de la page -->
                <h2 class="mb-4">
                    <i class="fas fa-plus-circle text-primary"></i> Ajouter une nouvelle tâche
                </h2>

        <!-- Message d'erreur si présent dans l'URL -->
        <?php if ($error): ?>
            <div class="alert alert-danger">
                Erreur lors de l'ajout de la tâche.
            </div>
        <?php endif; ?>

                <!-- FORMULAIRE -->
                <form action="../backend/add_task.php" method="POST">

                    <!-- CHAMP TITRE -->
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading"></i> Titre *
                        </label>

                        <!-- Champ obligatoire -->
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            class="form-control" 
                            placeholder="Entrez le titre de votre tâche" 
                            required
                        >
                    </div>

                    <!-- CHAMP DESCRIPTION -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i> Description
                        </label>

                        <!-- Champ facultatif -->
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-control" 
                            rows="4" 
                            placeholder="Décrivez votre tâche..."
                        ></textarea>
                    </div>

                    <!-- CHAMP DATE -->
                    <div class="mb-3">
                        <label for="due_date" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Date limite (optionnel)
                        </label>

                        <!-- Input date + heure -->
                        <input 
                            type="datetime-local" 
                            id="due_date" 
                            name="due_date" 
                            class="form-control" 
                            placeholder="Sélectionnez la date et l'heure limites"
                        >

                        <!-- Texte d'aide -->
                        <small class="form-text text-muted">
                            Si vous définissez une date limite, vous recevrez une alerte email si la tâche n'est pas complétée.
                        </small>
                    </div>

                    <!-- BOUTONS -->
                    <div class="d-flex gap-2">

                        <!-- Bouton submit -->
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Ajouter la tâche
                        </button>

                        <!-- Bouton annuler -->
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