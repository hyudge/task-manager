<?php

// Démarre la session seulement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// On inclut la connexion à la base de données (objet $pdo)
require_once __DIR__ . '/db.php';

/* ================= USER ================= */

// Fonction pour récupérer un utilisateur via son email
function findUserByEmail($email) {
    global $pdo;

    // Préparation de la requête (sécurisée contre injection SQL)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    // Retourne l'utilisateur sous forme de tableau associatif
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction d'inscription
function registerUser($name, $email, $password, &$error = null) {
    global $pdo;

    // Vérifie que tous les champs sont remplis
    if (!$name || !$email || !$password) {
        $error = "Tous les champs sont requis.";
        return false;
    }

    // Vérifie que l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
        return false;
    }

    // Génère un ID unique pour l'utilisateur
    $id = 'u' . bin2hex(random_bytes(15));

    // Hash du mot de passe (très bien 👍)
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insertion en base
        $stmt = $pdo->prepare("
            INSERT INTO users (id, name, email, password, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$id, $name, $email, $hash]);

        // Connexion automatique après inscription
        $_SESSION['user'] = [
            'id' => $id,
            'name' => $name,
            'email' => $email
        ];

        return true;

    } catch (PDOException $e) {
        // Probablement email déjà utilisé
        $error = "Email déjà utilisé.";
        return false;
    }
}

// Fonction de connexion
function loginUser($email, $password) {
    // On récupère l'utilisateur
    $user = findUserByEmail($email);

    // Si pas trouvé → échec
    if (!$user) return false;

    // Vérifie le mot de passe avec le hash
    if (!password_verify($password, $user['password'])) {
        return false;
    }

    // Stocke les infos en session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email']
    ];

    return true;
}

// Vérifie si un utilisateur est connecté
function isLoggedIn() {
    return !empty($_SESSION['user']);
}

// Force la connexion (sinon redirection)
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Déconnexion
function logout() {
    session_destroy();
}

/* ================= TASKS ================= */

// Récupère toutes les tâches d’un utilisateur
function getUserTasks($userId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT * FROM tasks
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajoute une tâche
function addTask($userId, $title, $description, $dueDate = null) {
    global $pdo;

    // Si pas de titre → refus
    if (!$title) return false;

    // Génération ID unique
    $id = 't' . bin2hex(random_bytes(15));

    // Gestion de la date
    $formattedDueDate = null;
    if ($dueDate) {
        try {
            // Conversion en objet DateTime
            $dateObj = new DateTime($dueDate);
            // Format compatible SQL
            $formattedDueDate = $dateObj->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            // Si date invalide → on ignore
            $formattedDueDate = null;
        }
    }

    // Insertion en base
    $stmt = $pdo->prepare("
        INSERT INTO tasks (id, user_id, title, description, due_date, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    return $stmt->execute([$id, $userId, $title, $description, $formattedDueDate]);
}

// Supprime une tâche (sécurisé avec user_id 👍)
function deleteTask($userId, $taskId) {
    global $pdo;

    $stmt = $pdo->prepare("
        DELETE FROM tasks
        WHERE id = ? AND user_id = ?
    ");
    return $stmt->execute([$taskId, $userId]);
}

// Trouve une tâche par son ID
function findTaskById($taskId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Change l'état (terminé / non terminé)
function toggleTaskStatus($userId, $taskId) {
    global $pdo;

    // Récupère la tâche actuelle
    $task = findTaskById($taskId);
    if (!$task) return false;

    // Inverse le statut
    $newStatus = $task['completed'] ? 0 : 1;

    // Mise à jour en base
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET completed = ?
        WHERE id = ? AND user_id = ?
    ");
    return $stmt->execute([$newStatus, $taskId, $userId]);
}