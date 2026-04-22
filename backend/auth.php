<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/* ================= USER ================= */

function findUserByEmail($email) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function registerUser($name, $email, $password, &$error = null) {
    global $pdo;

    if (!$name || !$email || !$password) {
        $error = "Tous les champs sont requis.";
        return false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
        return false;
    }

    $id = bin2hex(random_bytes(16));
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (id, name, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$id, $name, $email, $hash]);

        $_SESSION['user'] = [
            'id' => $id,
            'name' => $name,
            'email' => $email
        ];

        return true;
    } catch (PDOException $e) {
        $error = "Email déjà utilisé.";
        return false;
    }
}

function loginUser($email, $password) {
    $user = findUserByEmail($email);

    if (!$user) return false;

    if (!password_verify($password, $user['password'])) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email']
    ];

    return true;
}

function isLoggedIn() {
    return !empty($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function logout() {
    session_destroy();
}

/* ================= TASKS ================= */

function getUserTasks($userId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addTask($userId, $title, $description) {
    global $pdo;

    if (!$title) return false;

    $id = bin2hex(random_bytes(12));

    $stmt = $pdo->prepare("INSERT INTO tasks (id, user_id, title, description, created_at) VALUES (?, ?, ?, ?, NOW())");

    return $stmt->execute([$id, $userId, $title, $description]);
}

function deleteTask($userId, $taskId) {
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    return $stmt->execute([$taskId, $userId]);
}

function findTaskById($taskId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function toggleTaskStatus($userId, $taskId) {
    global $pdo;

    // Récupérer l'état actuel
    $task = findTaskById($taskId);
    if (!$task) return false;

    // Inverser l'état
    $newStatus = $task['completed'] ? 0 : 1;

    $stmt = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([$newStatus, $taskId, $userId]);
}