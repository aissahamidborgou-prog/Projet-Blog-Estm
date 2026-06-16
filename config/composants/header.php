<?php
session_start();
require_once '../config/connexion.php';
require_once '../fonctions.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog ESTM</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
        header { background: #333; color: white; padding: 15px; }
        nav a { color: white; margin: 0 15px; text-decoration: none; }
        .container { max-width: 1100px; margin: 20px auto; padding: 20px; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="accueil.php">Accueil</a>
            <?php if (est_connecte()): ?>
                <a href="publier.php">Publier</a>
                <a href="mes-articles.php">Mes Articles</a>
                <a href="profil.php">Profil</a>
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="inscription.php">Inscription</a>
                <a href="connexion.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">