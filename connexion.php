CREATE DATABASE IF NOT EXISTS blog_estm;
USE blog_estm;

CREATE TABLE blog_utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE blog_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    contenu TEXT NOT NULL,
    image_couverture VARCHAR(255) DEFAULT NULL,
    auteur_id INT NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES blog_utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE blog_commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    auteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES blog_articles(id) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id) REFERENCES blog_utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;<?php
require_once 'fonctions.php';
require_once 'config/connexion.php'; // Ta connexion PDO

// Si l'utilisateur est déjà connecté, on le redirige vers l'accueil
if (isset($_SESSION['utilisateur_id'])) {
    header('Location: accueil.php');
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mot_de_passe)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        // Recherche de l'utilisateur par son email
        $stmt = $pdo->prepare("SELECT * FROM blog_utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        // Vérification du mot de passe avec password_verify
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // Sécurité exigée : régénération de l'ID de session après connexion réussie
            session_regenerate_id(true);

            // Stockage des informations essentielles en session (SANS le mot de passe)
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['utilisateur_prenom'] = $utilisateur['prenom'];
            $_SESSION['utilisateur_nom'] = $utilisateur['nom'];

            // Redirection vers l'accueil du blog
            header('Location: accueil.php');
            exit();
        } else {
            // Consigne stricte de sécurité : ne pas préciser si c'est l'email ou le MDP qui est faux
            $erreur = "Identifiants incorrects.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Blog ESTM</title>
</head>
<body>
    <h2>Connexion au Blog ESTM</h2>

    <?php if (!empty($erreur)): ?>
        <p style="color: red;"><?= e($erreur) ?></p>
    <?php endif; ?>

    <form action="connexion.php" method="POST">
        <label>Adresse Email :</label><br>
        <input type="email" name="email" value="<?= isset($email) ? e($email) : '' ?>"><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="mot_de_passe"><br><br>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore inscrit ? <a href="inscription.php">Créez un compte ici</a>.</p>
</body>
</html>
