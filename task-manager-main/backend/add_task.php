<?php
require_once __DIR__ . '/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $dueDate = $_POST['due_date'] ?? null;

    if (addTask($userId, $title, $description, $dueDate)) {
        header('Location: ../frontend/index.php?success=1');
        exit;
    } else {
        // Rediriger avec erreur
        header('Location: ../frontend/add_task.php?error=1');
        exit;
    }
}
?>
