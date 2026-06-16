<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

// Sécurité : Accès réservé uniquement aux membres connectés
securiser_page();

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    // L'ID de l'auteur est récupéré depuis la session, JAMAIS depuis le formulaire
    $auteur_id = $_SESSION['utilisateur_id']; 

    // Validation des champs obligatoires
    if (empty($titre) || empty($contenu)) {
        $erreur = "Le titre et le contenu sont obligatoires.";
    } else {
        $image_nom = null;

        // Gestion du fichier d'image de couverture (optionnelle)
        if (isset($_FILES['image_couverture']) && $_FILES['image_couverture']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['image_couverture']['name'];
            $file_tmp = $_FILES['image_couverture']['tmp_name'];
            
            // Récupérer l'extension du fichier
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($ext, $extensions_autorisees)) {
                // Créer le dossier s'il n'existe pas encore
                $dossier_destination = 'images/articles/';
                if (!is_dir($dossier_destination)) {
                    mkdir($dossier_destination, 0777, true);
                }

                // Génération d'un nom unique automatique
                $image_nom = bin2hex(random_bytes(8)) . '.' . $ext;
                $chemin_final = $dossier_destination . $image_nom;

                // Déplacement du fichier temporaire vers le dossier final
                if (!move_uploaded_file($file_tmp, $chemin_final)) {
                    $erreur = "Erreur lors de l'enregistrement de l'image.";
                    $image_nom = null;
                }
            } else {
                $erreur = "Format d'image non valide (jpg, jpeg, png, webp, gif uniquement).";
            }
        }

        // Si aucune erreur, on procède à l'insertion en BDD via requête préparée
        if (empty($erreur)) {
            $stmt = $pdo->prepare("INSERT INTO blog_articles (titre, contenu, image_couverture, auteur_id) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$titre, $contenu, $image_nom, $auteur_id]);

            if ($success) {
                $id_article = $pdo->lastInsertId();
                // Redirection immédiate vers le détail de l'article créé
                header("Location: article.php?id=" . $id_article);
                exit();
            } else {
                $erreur = "Une erreur est survenue lors de l'enregistrement en base de données.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier un article - Blog ESTM</title>
</head>
<body>
    <h2>Publier un nouvel article</h2>

    <?php if (!empty($erreur)): ?>
        <p style="color: red;"><?= e($erreur) ?></p>
    <?php endif; ?>

    <form action="publier.php" method="POST" enctype="multipart/form-data">
        <label>Titre de l'article :</label><br>
        <input type="text" name="titre" value="<?= isset($titre) ? e($titre) : '' ?>"><br><br>

        <label>Contenu :</label><br>
        <textarea name="contenu" rows="10" cols="50"><?= isset($contenu) ? e($contenu) : '' ?></textarea><br><br>

        <label>Image de couverture (Optionnelle) :</label><br>
        <input type="file" name="image_couverture"><br><br>

        <button type="submit">Publier l'article</button>
    </form>

    <p><a href="accueil.php">Retour à l'accueil</a></p>
</body>
</html>
