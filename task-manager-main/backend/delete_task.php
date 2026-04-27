<?php
// Inclusion des fonctions (auth, BDD, etc.)
require_once __DIR__ . '/auth.php';

// Vérifie que l'utilisateur est connecté
requireLogin();

// Récupération de l'ID utilisateur depuis la session
$userId = $_SESSION['user']['id'];

// Récupération de l'ID de la tâche depuis l'URL (GET)
$taskId = $_GET['id'] ?? '';

// Si aucun ID → redirection avec erreur
if ($taskId === '') {
    header('Location: ../frontend/index.php?error=1');
    exit;
}

// Récupération de la tâche en base
$task = findTaskById($taskId);

// Vérifie que :
// 1. la tâche existe
// 2. elle appartient bien à l'utilisateur connecté
if (!$task || $task['user_id'] !== $userId) {
    header('Location: ../frontend/index.php?error=1');
    exit;
}

// Suppression de la tâche
if (deleteTask($userId, $taskId)) {
    // Succès → redirection avec message
    header('Location: ../frontend/index.php?success=1');
} else {
    // Échec → redirection avec erreur
    header('Location: ../frontend/index.php?error=1');
}

// Stoppe le script après redirection
exit;
?>