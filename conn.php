<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=webPhp", "koko", "koko");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
