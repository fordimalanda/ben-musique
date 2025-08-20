DROP DATABASE IF EXISTS `ben_musique`;

CREATE DATABASE IF NOT EXISTS `ben_musique`;

USE `ben_musique`;

CREATE TABLE IF NOT EXISTS `users`(
    `id_user` INT NOT NULL AUTO_INCREMENT,
    `prenom` VARCHAR(25) NOT NULL,
    `nom` VARCHAR(25) NOT NULL,
    `tel` VARCHAR(25),
    `email` VARCHAR(25) NOT NULL,
    `sexe` ENUM("M", "F", "Other"),
    `birth_day` DATE NOT NULL,
    `profil_picture` VARCHAR(255),
    `bio` VARCHAR(255),
    `adresse` VARCHAR(255),
    `pays` VARCHAR(100) NOT NULL,
    `ville` VARCHAR(100) NOT NULL,
    `create_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `password` VARCHAR(255) NOT NULL,

    PRIMARY KEY(`id_user`)
);

CREATE TABLE IF NOT EXISTS `abonnements`(
    `id_abonnement` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(25) NOT NULL,
    `type_abonnement` ENUM("Mensuel", "Annuel") NOT NULL,
    `description` VARCHAR(255),
    `prix` DECIMAL(10,2),
    `duree` ENUM("30", "365") NOT NULL,

    PRIMARY KEY(`id_abonnement`)
);

CREATE TABLE IF NOT EXISTS `applications`(
    `id_app` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `version` VARCHAR(10) NOT NULL,
    `release_date` DATE,
    `status` ENUM("actif", "non-actif"),

    PRIMARY KEY(`id_app`)
);

CREATE TABLE IF NOT EXISTS `payements` (
    `id_payement` INT NOT NULL AUTO_INCREMENT,
    `id_user` INT,
    `id_abonnement` INT,
    `montant` DECIMAL(10,2) NOT NULL,
    `devise` ENUM("USD", "CDF", "EUR") NOT NULL DEFAULT 'USD',
    `status` ENUM("en_attente", "réussi", "échoué") NOT NULL DEFAULT "en_attente",
    `method` ENUM(
        "Orange Money",
        "M-Pesa",
        "Airtel Money",
        "AfriMoney",
        "VISA",
        "MasterCard"
    ) NOT NULL,
    `date_payement` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `date_expiration` DATETIME DEFAULT NULL,

    PRIMARY KEY(`id_payement`),

    FOREIGN KEY(`id_user`) REFERENCES users(`id_user`),
    FOREIGN KEY(`id_abonnement`) REFERENCES abonnements(`id_abonnement`)
);

CREATE TABLE IF NOT EXISTS `authentifications`(
    `id_auth` INT NOT NULL AUTO_INCREMENT,
    `id_user` INT,
    `id_app` INT,
    `password` VARCHAR(255),
    `auth_method` ENUM("google", "icloud", "email", "tel"),
    `last_login_at` DATETIME,
    `adress_ip` VARCHAR(255),

    PRIMARY KEY(`id_auth`),

    FOREIGN KEY(`id_user`) REFERENCES users(`id_user`),
    FOREIGN KEY(`id_app`) REFERENCES applications(`id_app`)
);




INSERT INTO `abonnements`(`name`, `type_abonnement`, `description`, `prix`, `duree`)
VALUES
("Starter", "mensuel", "Abonnement starter pour le mois", 1.99, "30"),
("Business", "mensuel", "Abonnement business pour le mois", 3.99, "30"),
("Premium", "mensuel", "Abonnement premium pour le mois", 4.99, "30"),
("Starter", "Annuel", "Abonnement starter pour une annéé", 299.99, "365"),
("Business", "Annuel", "Abonnement business pour une annéé", 499.99, "365"),
("Premium", "Annuel", "Abonnement premium pour une annéé", 599.99, "365");


INSERT INTO `applications`(`name`, `version`, `release_date`, `status`)
VALUES
("App-Client", "v1.0.0", "2025-08-19", "actif"),
("App-Agent", "v1.0.0", NULL, "non-actif");