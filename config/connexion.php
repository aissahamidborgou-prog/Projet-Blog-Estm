<?php
// Connexion PDO - NE JAMAIS METTRE CE FICHIER SUR GITHUB !
$host = 'localhost';
$dbname = 'blog_estm';
$username = 'root';
$password = '';   // Vide avec XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>