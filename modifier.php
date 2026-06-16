<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

// verification utilisateur
securiser_page();

$id_article = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$utilisateur_id = $_SESSION['utilisateur_id'];

if ($id_article <= 0) {
    header('Location: accueil.php');
    exit();
}

// verification des droits sur l'article 
$stmt = $pdo->prepare("SELECT * FROM blog_articles WHERE id = ?");
$stmt->execute([$id_article]);
$article = $stmt->fetch();

// Si l'article n'existe pas ou si l'auteur ne correspond pas à la session, redirection immédiate
if (!$article || $article['auteur_id'] != $utilisateur_id) {
    header('Location: accueil.php');
    exit();
}

$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    if (empty($titre) || empty($contenu)) {
        $erreur = "Le titre et le contenu ne peuvent pas être vides.";
    } else {
        // On garde par défaut l'ancienne image
        $image_nom = $article['image_couverture'];

        // Si une nouvelle image est soumise, on la traite
        if (isset($_FILES['image_couverture']) && $_FILES['image_couverture']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['image_couverture']['name'];
            $file_tmp = $_FILES['image_couverture']['tmp_name'];
            
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($ext, $extensions_autorisees)) {
                // Nettoyage de l'ancienne image de couverture 
                if (!empty($article['image_couverture'])) {
                    $ancien_chemin = 'images/articles/' . $article['image_couverture'];
                    if (file_exists($ancien_chemin)) {
                        unlink($ancien_chemin);
                    }
                }

                // Générer un nouveau nom unique
                $image_nom = bin2hex(random_bytes(8)) . '.' . $ext;
                move_uploaded_file($file_tmp, 'images/articles/' . $image_nom);
            } else {
                $erreur = "Format d'image non valide.";
            }
        }

        // Si pas d'erreur, mise à jour
        if (empty($erreur)) {
            $update = $pdo->prepare("UPDATE blog_articles SET titre = ?, contenu = ?, image_couverture = ? WHERE id = ?");
            if ($update->execute([$titre, $contenu, $image_nom, $id_article])) {
                header("Location: article.php?id=" . $id_article);
                exit();
            } else {
                $erreur = "Erreur lors de la mise à jour.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'article - Blog ESTM</title>
</head>
<body>
    <p><a href="mes-articles.php">← Retour à mes articles</a></p>

    <h2>Modifier votre article</h2>

    <?php if (!empty($erreur)): ?>
        <p style="color: red;"><?= e($erreur) ?></p>
    <?php endif; ?>

    <form action="modifier.php?id=<?= $article['id'] ?>" method="POST" enctype="multipart/form-data">
        <label>Titre :</label><br>
        <input type="text" name="titre" value="<?= e($article['titre']) ?>"><br><br>

        <label>Contenu :</label><br>
        <textarea name="contenu" rows="10" cols="50"><?= e($article['contenu']) ?></textarea><br><br>

        <?php if (!empty($article['image_couverture'])): ?>
            <p>Image actuelle :</p>
            <img src="images/articles/<?= e($article['image_couverture']) ?>" alt="Couverture" style="max-width: 150px; display:block; margin-bottom:10px;">
        <?php endif; ?>

        <label>Remplacer l'image de couverture (Optionnel) :</label><br>
        <input type="file" name="image_couverture"><br><br>

        <button type="submit">Enregistrer les modifications</button>
    </form>
</body>
</html>
