<?php
$host = "localhost";
$username = "root";
$password = "";

try {
    // Connexion sans BD pour créer la DB
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lire le fichier SQL
    $sql = file_get_contents(__DIR__ . '/setup.sql');
    
    // Exécuter les requêtes
    $pdo->exec($sql);
    
    echo "✅ Base de données créée avec succès!\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
?>
