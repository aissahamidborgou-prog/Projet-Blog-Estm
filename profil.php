<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

// Sécurité : Accès réservé aux membres connectés
securiser_page();

$utilisateur_id = $_SESSION['utilisateur_id'];
$erreur = "";
$succes = "";

// Récupérer les données fraîches de l'utilisateur depuis la BDD
$stmt = $pdo->prepare("SELECT * FROM blog_utilisateurs WHERE id = ?");
$stmt->execute([$utilisateur_id]);
$utilisateur = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $nouveau_mdp = $_POST['nouveau_mot_de_passe'] ?? '';

    if (empty($prenom) || empty($nom)) {
        $erreur = "Le prénom et le nom sont obligatoires.";
    } else {
        // Optionnel : Gestion du changement de mot de passe
        if (!empty($nouveau_mdp)) {
            // Un nouveau mot de passe a été saisi -> On le hache
            $mot_de_passe_final = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
        } else {
            // Le champ est vide -> On conserve l'ancien mot de passe issu de la BDD
            $mot_de_passe_final = $utilisateur['mot_de_passe'];
        }

        // Requête de mise à jour des informations
        $update = $pdo->prepare("UPDATE blog_utilisateurs SET prenom = ?, nom = ?, mot_de_passe = ? WHERE id = ?");
        if ($update->execute([$prenom, $nom, $mot_de_passe_final, $utilisateur_id])) {
            // On met à jour les données stockées dans la session
            $_SESSION['utilisateur_prenom'] = $prenom;
            $_SESSION['utilisateur_nom'] = $nom;
            
            $succes = "Votre profil a été mis à jour avec succès.";
            
            // Recharger les données locales de la page
            $utilisateur['prenom'] = $prenom;
            $utilisateur['nom'] = $nom;
        } else {
            $erreur = "Une erreur est survenue lors de la mise à jour.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - Blog ESTM</title>
</head>
<body>
    <p><a href="accueil.php">← Retour à l'accueil</a></p>

    <h2>Mon Profil</h2>

    <?php if (!empty($erreur)): ?>
        <p style="color: red;"><?= e($erreur) ?></p>
    <?php endif; ?>

    <?php if (!empty($succes)): ?>
        <p style="color: green;"><?= e($succes) ?></p>
    <?php endif; ?>

    <form action="profil.php" method="POST">
        <label>Adresse Email (Non modifiable) :</label><br>
        <input type="email" value="<?= e($utilisateur['email']) ?>" disabled><br><br>

        <label>Prénom :</label><br>
        <input type="text" name="prenom" value="<?= e($utilisateur['prenom']) ?>"><br><br>

        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?= e($utilisateur['nom']) ?>"><br><br>

        <label>Nouveau mot de passe (Laissez vide pour conserver l'actuel) :</label><br>
        <input type="password" name="nouveau_mot_de_passe"><br><br>

        <button type="submit">Mettre à jour mon profil</button>
    </form>
</body>
</html>
