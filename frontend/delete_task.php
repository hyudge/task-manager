<?php
require_once __DIR__ . '/auth.php';

requireLogin();

$userId = $_SESSION['user']['id'];
$taskId = $_GET['id'] ?? '';

if ($taskId === '') {
    header('Location: index.php?error=1');
    exit;
}

$task = findTaskById($taskId);

// Vérifier que la tâche appartient à l'utilisateur
if (!$task || $task['user_id'] !== $userId) {
    header('Location: index.php?error=1');
    exit;
}

if (deleteTask($userId, $taskId)) {
    header('Location: index.php?success=1');
} else {
    header('Location: index.php?error=1');
}
exit;
?>
