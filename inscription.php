<?php
require_once 'fonctions.php';
require_once 'config/connexion.php'; // Ta connexion PDO ($pdo)

// Si l'utilisateur est déjà connecté, on le redirige vers l'accueil
if (isset($_SESSION['utilisateur_id'])) {
    header('Location: accueil.php');
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    // 1. Validation des champs obligatoires
    if (empty($prenom) || empty($nom) || empty($email) || empty($mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires.";
    } 
    // 2. Validation du format de l'email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } 
    // 3. Validation de la confirmation du mot de passe
    elseif ($mot_de_passe !== $confirmation) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } 
    else {
        // 4. Vérification si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM blog_utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée par un autre membre.";
        } else {
            // 5. Hachage du mot de passe et insertion
            $mdp_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            $insert = $pdo->prepare("INSERT INTO blog_utilisateurs (prenom, nom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
            $success = $insert->execute([$prenom, $nom, $email, $mdp_hache]);

            if ($success) {
                // 6. Connexion automatique (on récupère l'ID généré)
                $_SESSION['utilisateur_id'] = $pdo->lastInsertId();
                $_SESSION['utilisateur_prenom'] = $prenom;
                $_SESSION['utilisateur_nom'] = $nom;

                // Redirection vers l'accueil
                header('Location: accueil.php');
                exit();
            } else {
                $erreur = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Blog ESTM</title>
</head>
<body>
    <h2>Inscription au Blog ESTM</h2>
    
    <?php if (!empty($erreur)): ?>
        <p style="color: red;"><?= e($erreur) ?></p>
    <?php endif; ?>

    <form action="inscription.php" method="POST">
        <label>Prénom :</label><br>
        <input type="text" name="prenom" value="<?= isset($prenom) ? e($prenom) : '' ?>"><br><br>

        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?= isset($nom) ? e($nom) : '' ?>"><br><br>

        <label>Adresse Email :</label><br>
        <input type="email" name="email" value="<?= isset($email) ? e($email) : '' ?>"><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="mot_de_passe"><br><br>

        <label>Confirmez le mot de passe :</label><br>
        <input type="password" name="confirmation"><br><br>

        <button type="submit">S'inscrire</button>
    </form>
    
    <p>Déjà inscrit ? <a href="connexion.php">Connectez-vous ici</a>.</p>
</body>
</html>
