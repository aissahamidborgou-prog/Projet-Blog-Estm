<?php
require_once 'fonctions.php';

// On vide le tableau de session
$_SESSION = array();

// On détruit la session côté serveur
if (session_id() !== "") {
    session_destroy();
}

// Redirection immédiate vers la page de connexion ou d'accueil
header('Location: connexion.php');
exit();
