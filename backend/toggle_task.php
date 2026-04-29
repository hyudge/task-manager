<?php
// Inclusion des fonctions (auth, BDD, etc.)
require_once __DIR__ . '/auth.php';

// Vérifie que l'utilisateur est connecté
requireLogin();

// Récupération de l'ID utilisateur
$userId = $_SESSION['user']['id'];

// Récupération de l'ID de la tâche depuis l'URL
$taskId = $_GET['id'] ?? '';

// Si aucun ID → erreur
if ($taskId === '') {
    header('Location: ../frontend/index.php?error=1');
    exit;
}

// Récupère la tâche en base
$task = findTaskById($taskId);

// Vérifie que la tâche existe ET appartient à l'utilisateur
if (!$task || $task['user_id'] !== $userId) {
    header('Location: ../frontend/index.php?error=1');
    exit;
}

// Inverse le statut (terminée / non terminée)
if (toggleTaskStatus($userId, $taskId)) {
    header('Location: ../frontend/index.php?success=1');
} else {
    header('Location: ../frontend/index.php?error=1');
}

// Stop le script
exit;
?>