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
) ENGINE=InnoDB;