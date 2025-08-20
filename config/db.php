<?php
// config/db.php

$host = 'localhost';         // ou 127.0.0.1
$db   = 'ben_musique';       // nom de ta base de données
$user = 'root';              // utilisateur MySQL
$pass = '';                  // mot de passe MySQL (souvent vide en local)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Pour afficher les erreurs SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Pour récupérer des tableaux associatifs
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Pour sécuriser les requêtes préparées
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}