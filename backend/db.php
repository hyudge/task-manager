<?php
// Informations de connexion à la base de données
$host = "localhost";       // serveur (local ici)
$dbname = "task_manager"; // nom de la base
$username = "root";       // utilisateur MySQL
$password = "";           // mot de passe (vide en local)

// Bloc try/catch pour gérer les erreurs de connexion
try {
    // Création de l'objet PDO (connexion à MySQL)
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    // Active les erreurs PDO sous forme d'exceptions (très important 👍)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Si erreur → arrêt du script + message
    die("Erreur DB : " . $e->getMessage());
}