<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

// restriction d'acces 
securiser_page();

// verification de la methode de requete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id_article = (int)$_POST['id'];
    $utilisateur_id = $_SESSION['utilisateur_id'];

    // controle de la proriete de l'article avant suppression
    $stmt = $pdo->prepare("SELECT image_couverture FROM blog_articles WHERE id = ? AND auteur_id = ?");
    $stmt->execute([$id_article, $utilisateur_id]);
    $article = $stmt->fetch();

    if ($article) {
        // Optionnel mais propre : Supprimer le fichier image du serveur s'il existe
        if (!empty($article['image_couverture'])) {
            $chemin_image = 'images/articles/' . $article['image_couverture'];
            if (file_exists($chemin_image)) {
                unlink($chemin_image);
            }
        }

        // Suppression de l'article
        // Grâce au ON DELETE CASCADE, les commentaires liés s'effacent automatiquement en BDD !
        $delete = $pdo->prepare("DELETE FROM blog_articles WHERE id = ?");
        $delete->execute([$id_article]);
    }
}

// Redirection vers la page liste quoi qu'il arrive
header('Location: mes-articles.php');
exit();
