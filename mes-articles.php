<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

// Sécurité : Accès réservé aux membres connectés
securiser_page();

$auteur_id = $_SESSION['utilisateur_id'];

// Requête préparée pour récupérer uniquement les articles de cet auteur
$query = "
    SELECT a.*, 
           (SELECT COUNT(*) FROM blog_commentaires c WHERE c.article_id = a.id) AS nb_commentaires
    FROM blog_articles a
    WHERE a.auteur_id = ?
    ORDER BY a.date_publication DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$auteur_id]);
$articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Articles - Blog ESTM</title>
</head>
<body>
    <p><a href="accueil.php">← Retour à l'accueil</a></p>
    
    <h2>Mes articles publiés</h2>

    <?php if (empty($articles)): ?>
        <p>Vous n'avez pas encore publié d'article. <a href="publier.php">Publiez votre premier article ici !</a></p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f2f2f2;">
                    <th>Titre</th>
                    <th>Date de publication</th>
                    <th>Commentaires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td>
                            <a href="article.php?id=<?= $article['id'] ?>"><strong><?= e($article['titre']) ?></strong></a>
                        </td>
                        <td><?= $article['date_publication'] ?></td>
                        <td><?= $article['nb_commentaires'] ?></td>
                        <td>
                            <!-- Lien vers la modification -->
                            <a href="modifier.php?id=<?= $article['id'] ?>">Modifier</a> | 
                            
                             // Formulaire de suppression securise avec confirmation
                            <form action="supprimer.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                                <button type="submit" style="color: red; background: none; border: none; padding: 0; cursor: pointer; text-decoration: underline;">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
