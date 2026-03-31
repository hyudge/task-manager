<?php

/**
 * auth.php
 *
 * Gestion simple de l'authentification (inscription, connexion, session)
 * en stockant les utilisateurs dans un fichier JSON (`users.json`).
 *
 * Ce fichier expose des helpers :
 *  - registerUser() : créer un compte + loguer
 *  - loginUser()    : connecter un utilisateur existant
 *  - requireLogin() : rediriger vers login si non connecté
 *  - logout()       : déconnecter proprement
 */

// Démarre la session si elle n'est pas déjà ouverte.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getUserStorePath(): string
{
    // Chemin du fichier JSON utilisé pour stocker les comptes utilisateurs.
    // Utiliser un emplacement local au script permet de garder le système autonome.
    return __DIR__ . '/users.json';
}

function getUsers(): array
{
    // Lit tous les utilisateurs depuis le fichier JSON.
    // Si le fichier n'existe pas ou n'est pas valide, renvoie une liste vide.
    $path = getUserStorePath();
    if (!file_exists($path)) {
        return [];
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return [];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return [];
    }

    return $data;
}

function saveUsers(array $users): bool
{
    // Écrit la liste des utilisateurs dans users.json.
    // Utilise JSON_PRETTY_PRINT pour faciliter la lecture manuelle.
    $path = getUserStorePath();
    $dir = dirname($path);

    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return false;
    }

    $json = json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents($path, $json) !== false;
}

function findUserByEmail(string $email): ?array
{
    // Recherche un utilisateur par adresse email (comparaison insensible à la casse).
    // Retourne l'utilisateur (tableau) ou null si aucun utilisateur ne correspond.
    $users = getUsers();
    foreach ($users as $user) {
        if (isset($user['email']) && strtolower($user['email']) === strtolower($email)) {
            return $user;
        }
    }

    return null;
}

/**
 * Valide la sécurité d'un mot de passe.
 *
 * Critères :
 *  - Minimum 12 caractères
 *  - Au moins une majuscule
 *  - Au moins un caractère spécial (!@#$%^&*()_+-=[]{}|;':",./<>?)
 *
 * @param string $password Mot de passe à valider
 * @return bool True si le mot de passe est valide, false sinon.
 */
function isPasswordValid(string $password): bool
{
    // Minimum 12 caractères
    if (strlen($password) < 12) {
        return false;
    }

    // Au moins une majuscule
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }

    // Au moins un caractère spécial
    if (!preg_match('~[!@#$%^&*()_+\-=\[\]{}|;\'":,./<>?]~', $password)) {
        return false;
    }

    return true;
}

/**
 * Crée un nouvel utilisateur et l'enregistre dans users.json.
 *
 * @param string $name Nom complet de l'utilisateur
 * @param string $email Adresse email utilisée comme identifiant
 * @param string $password Mot de passe en clair (sera hashé)
 * @param string|null &$error En sortie, message d'erreur si l'inscription échoue
 *
 * @return bool True si l'utilisateur a bien été créé et connecté, false sinon.
 */
function registerUser(string $name, string $email, string $password, ?string &$error = null): bool
{
    // Crée un nouvel utilisateur et l'enregistre dans users.json.
    // Le mot de passe est correctement hashé (PASSWORD_DEFAULT).
    $name = trim($name);
    $email = trim($email);

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Tous les champs sont requis.';
        return false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'L\'adresse email n\'est pas valide.';
        return false;
    }

    if (findUserByEmail($email) !== null) {
        $error = 'Un compte existe déjà avec cette adresse email.';
        return false;
    }

    // Valide la sécurité du mot de passe
    if (!isPasswordValid($password)) {
        $error = 'Le mot de passe doit contenir au minimum 12 caractères, une majuscule et un caractère spécial.';
        return false;
    }

    $users = getUsers();
    $newUser = [
        'id' => bin2hex(random_bytes(16)),
        'name' => $name,
        'email' => $email,
        // Stocke uniquement le hash du mot de passe (jamais le mot de passe en clair).
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('c'),
    ];

    $users[] = $newUser;

    if (!saveUsers($users)) {
        $error = 'Impossible d\'enregistrer l\'utilisateur.';
        return false;
    }

    // Log in immediately for a smoother UX.
    $_SESSION['user'] = [
        'id' => $newUser['id'],
        'name' => $newUser['name'],
        'email' => $newUser['email'],
    ];

    return true;
}

/**
 * Tente de connecter un utilisateur.
 *
 * @param string $email
 * @param string $password
 * @return bool True si connexion réussie, false sinon.
 */
function loginUser(string $email, string $password): bool
{
    // Vérifie les identifiants et initialise la session si c'est OK.
    $user = findUserByEmail($email);
    if ($user === null) {
        return false;
    }

    if (!isset($user['password']) || !password_verify($password, $user['password'])) {
        return false;
    }

    // Ne stocke jamais le hash du mot de passe dans la session.
    $_SESSION['user'] = [
        'id' => $user['id'] ?? null,
        'name' => $user['name'] ?? '',
        'email' => $user['email'] ?? '',
    ];

    return true;
}

/**
 * Vérifie si un utilisateur est connecté et redirige vers login.php si ce n'est pas le cas.
 */
function requireLogin(): void
{
    // Si l'utilisateur n'est pas connecté, il est redirigé vers login.php.
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Retourne true si l'utilisateur est connecté.
 */
function isLoggedIn(): bool
{
    // Vérifie simplement si la session contient un utilisateur.
    return !empty($_SESSION['user']);
}

/**
 * Déconnecte l'utilisateur en supprimant la session et le cookie de session.
 */
function logout(): void
{
    // Nettoie la session et le cookie de session pour déconnecter proprement.
    if (session_status() !== PHP_SESSION_NONE) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
}

/**
 * Chemin du fichier de données des tâches.
 */
function getTaskStorePath(): string
{
    return __DIR__ . '/tasks.json';
}

/**
 * Lit toutes les tâches depuis tasks.json.
 */
function getTasks(): array
{
    $path = getTaskStorePath();
    if (!file_exists($path)) {
        return [];
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return [];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return [];
    }

    return $data;
}

/**
 * Enregistre toutes les tâches dans tasks.json.
 */
function saveTasks(array $tasks): bool
{
    $path = getTaskStorePath();
    $dir = dirname($path);

    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return false;
    }

    $json = json_encode(array_values($tasks), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents($path, $json) !== false;
}

/**
 * Retourne les tâches d'un utilisateur donné.
 */
function getUserTasks(string $userId): array
{
    $tasks = getTasks();
    $userTasks = array_filter($tasks, function ($task) use ($userId) {
        return isset($task['user_id']) && $task['user_id'] === $userId;
    });

    usort($userTasks, function ($a, $b) {
        return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
    });

    return array_values($userTasks);
}

/**
 * Ajoute une nouvelle tâche pour un utilisateur.
 */
function addTask(string $userId, string $title, string $description = ''): bool
{
    $title = trim($title);
    $description = trim($description);
    if ($title === '') {
        return false;
    }

    $tasks = getTasks();
    $tasks[] = [
        'id' => bin2hex(random_bytes(12)),
        'user_id' => $userId,
        'title' => $title,
        'description' => $description,
        'completed' => false,
        'created_at' => date('c'),
    ];

    return saveTasks($tasks);
}

/**
 * Recherche une tâche par ID.
 */
function findTaskById(string $taskId): ?array
{
    foreach (getTasks() as $task) {
        if (isset($task['id']) && $task['id'] === $taskId) {
            return $task;
        }
    }

    return null;
}

/**
 * Supprime une tâche pour un utilisateur.
 */
function deleteTask(string $userId, string $taskId): bool
{
    $tasks = getTasks();
    $filtered = array_filter($tasks, function ($task) use ($userId, $taskId) {
        return !($task['id'] === $taskId && $task['user_id'] === $userId);
    });

    if (count($filtered) === count($tasks)) {
        return false;
    }

    return saveTasks($filtered);
}

/**
 * Basculer l'état "completed" d'une tâche.
 */
function toggleTaskStatus(string $userId, string $taskId): bool
{
    $tasks = getTasks();
    $updated = false;

    foreach ($tasks as &$task) {
        if (isset($task['id'], $task['user_id']) && $task['id'] === $taskId && $task['user_id'] === $userId) {
            $task['completed'] = empty($task['completed']) ? true : false;
            $updated = true;
            break;
        }
    }
    unset($task);

    if (!$updated) {
        return false;
    }

    return saveTasks($tasks);
}