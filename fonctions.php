<?php
// Fonctions utilitaires du blog

// Sécuriser l'affichage (contre XSS)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Redirection
function redirect($url) {
    header("Location: $url");
    exit();
}

// Vérifier si l'utilisateur est connecté
function est_connecte() {
    return isset($_SESSION['utilisateur_id']);
}

// Vérifier si l'utilisateur est l'auteur d'un article
function est_auteur($article_auteur_id) {
    return est_connecte() && $_SESSION['utilisateur_id'] == $article_auteur_id;
}
?><?php
// On démarre la session globalement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fonction pour sécuriser l'affichage contre les failles XSS
 * (Équivalent plus rapide à écrire que htmlspecialchars)
 */
function e($valeur) {
    return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige un utilisateur s'il n'est pas connecté (protection des pages)
 */
function securiser_page() {
    if (!isset($_SESSION['utilisateur_id'])) {
        header('Location: connexion.php');
        exit();
    }
}
