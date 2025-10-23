# Projet Login PHP - Mode Classique

Ce projet est une implémentation d'un système de gestion d'utilisateurs et de produits en PHP sans framework, dans le cadre de l'exercice "fil rouge" du cours de Programmation Web.

## Configuration de la base de données

Voici les commandes SQL nécessaires pour créer la base de données et les tables requises pour le projet.

```sql
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS webPhp;

-- Sélection de la base de données
USE webPhp;

-- Création de la table 'user'
-- Ajout de la colonne 'role' avec une valeur par défaut 'user'
-- Modification des types TEXT/LONGTEXT en VARCHAR pour de meilleures performances et une taille plus appropriée
CREATE TABLE `user` (
    `user_id` INT NOT NULL AUTO_INCREMENT,
    `user_login` VARCHAR(255) NOT NULL,
    `user_password` VARCHAR(255) NOT NULL, -- Pour stocker les hachages de mots de passe
    `user_compte_id` INT NOT NULL,
    `user_mail` VARCHAR(255) NOT NULL,
    `user_date_new` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_date_login` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `role` VARCHAR(50) NOT NULL DEFAULT 'user', -- 'user' par défaut, peut être 'admin'
    PRIMARY KEY (`user_id`),
    UNIQUE (`user_compte_id`) -- Assure l'unicité de l'identifiant de compte
) ENGINE = InnoDB;

-- Création de la table 'produit'
-- Ajout des colonnes 'ppromo' (pourcentage de promotion) et 'image_path'
CREATE TABLE `produit` (
    `id_p` INT AUTO_INCREMENT PRIMARY KEY,
    `type_p` VARCHAR(100) NOT NULL,
    `designation_p` VARCHAR(255) NOT NULL,
    `prix_ht` DECIMAL(10,2) NOT NULL,
    `date_in` DATE NOT NULL,
    `times_in` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `stock_p` INT DEFAULT 0,
    `ppromo` DECIMAL(5,2) NULL, -- Pourcentage de promotion, peut être NULL
    `image_path` VARCHAR(255) NULL -- Chemin de l'image, peut être NULL
) ENGINE = InnoDB;
```
