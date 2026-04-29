<?php
// On inclut le fichier auth.php (connexion, fonctions, gestion session, etc.)
require_once __DIR__ . '/auth.php';

// Vérifie que l'utilisateur est connecté
// Si ce n'est pas le cas, il sera redirigé (souvent vers login)
requireLogin();

// On vérifie que la requête envoyée est bien une requête POST
// (donc que le formulaire a été soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // On récupère l'id de l'utilisateur depuis la session
    $userId = $_SESSION['user']['id'];

    // On récupère les données envoyées par le formulaire
    // Si le champ n'existe pas, on met une valeur par défaut
    $title = $_POST['title'] ?? ''; // titre vide par défaut
    $description = $_POST['description'] ?? ''; // description vide
    $dueDate = $_POST['due_date'] ?? null; // date null si non remplie

    // On appelle la fonction addTask pour ajouter la tâche en base de données
    if (addTask($userId, $title, $description, $dueDate)) {

        // Si ça marche → redirection vers la page principale avec un message de succès
        header('Location: ../frontend/index.php?success=1');
        exit; // IMPORTANT : stoppe le script après la redirection

    } else {

        // Si ça échoue → redirection vers le formulaire avec un message d'erreur
        header('Location: ../frontend/add_task.php?error=1');
        exit; // stoppe le script
    }
}
?>