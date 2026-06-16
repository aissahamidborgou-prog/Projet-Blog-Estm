<?php 
require_once 'composants/header.php'; 
?>

<h1>Bienvenue sur le Blog ESTM</h1>

<?php
// Récupérer tous les articles
$stmt = $pdo->query("SELECT a.*, u.prenom, u.nom, 
                    (SELECT COUNT(*) FROM blog_commentaires WHERE article_id = a.id) as nb_commentaires 
                    FROM blog_articles a 
                    JOIN blog_utilisateurs u ON a.auteur_id = u.id 
                    ORDER BY a.date_publication DESC");

if ($stmt->rowCount() == 0) {
    echo "<p>Aucun article n'a encore été publié.</p>";
} else {
    while ($article = $stmt->fetch()) {
        $extrait = substr(strip_tags($article['contenu']), 0, 150) . '...';
        ?>
        <div style="background:white; padding:15px; margin:15px 0; border-radius:8px;">
            <?php if ($article['image_couverture']): ?>
                <img src="images/articles/<?= e($article['image_couverture']) ?>" alt="Image" style="max-width:100%; height:200px; object-fit:cover;">
            <?php endif; ?>
            
            <h2><a href="article.php?id=<?= $article['id'] ?>"><?= e($article['titre']) ?></a></h2>
            <p>Par <?= e($article['prenom'] . ' ' . $article['nom']) ?> — <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?></p>
            <p><?= e($extrait) ?></p>
            <p><strong><?= $article['nb_commentaires'] ?> commentaire(s)</strong></p>
        </div>
        <?php
    }
}
?>

<?php require_once 'composants/footer.php'; ?><?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

// Requête SQL robuste avec jointure pour l'auteur et sous-requête pour compter les commentaires
$query = "
    SELECT a.*, u.prenom, u.nom,
           (SELECT COUNT(*) FROM blog_commentaires c WHERE c.article_id = a.id) AS nb_commentaires
    FROM blog_articles a
    JOIN blog_utilisateurs u ON a.auteur_id = u.id
    ORDER BY a.date_publication DESC
";
$stmt = $pdo->query($query);
$articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Blog ESTM</title>
</head>
<body>
    <h1>Bienvenue sur le Blog Communautaire ESTM</h1>

    <nav>
        <a href="accueil.php">Accueil</a> | 
        <?php if (isset($_SESSION['utilisateur_id'])): ?>
            <span>Bonjour, <?= e($_SESSION['utilisateur_prenom']) ?> <?= e($_SESSION['utilisateur_nom']) ?></span> | 
            <a href="publier.php">Publier un article</a> | 
            <a href="mes-articles.php">Mes articles</a> | 
            <a href="profil.php">Mon profil</a> | 
            <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            <a href="connexion.php">Connexion</a> | 
            <a href="inscription.php">Inscription</a>
        <?php endif; ?>
    </nav>
    <hr>

    <h2>Articles récents</h2>

    <?php if (empty($articles)): ?>
        <p>Aucun article n'a encore été publié. Soyez le premier !</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <article style="border-bottom: 1px solid #ccc; padding-bottom: 20px;">
                <h3><a href="article.php?id=<?= $article['id'] ?>"><?= e($article['titre']) ?></a></h3>
                
                <p><small>Publié le <?= $article['date_publication'] ?> par <?= e($article['prenom']) ?> <?= e($article['nom']) ?> | <strong><?= $article['nb_commentaires'] ?> commentaire(s)</strong></small></p>

                <?php if (!empty($article['image_couverture'])): ?>
                    <img src="images/articles/<?= e($article['image_couverture']) ?>" alt="Couverture" style="max-width: 200px; display: block; margin-bottom: 10px;">
                <?php endif; ?>

                <p>
                    <?php 
                    $extrait = mb_substr($article['contenu'], 0, 150);
                    echo e($extrait);
                    if (mb_strlen($article['contenu']) > 150) { echo '...'; }
                    ?>
                </p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
