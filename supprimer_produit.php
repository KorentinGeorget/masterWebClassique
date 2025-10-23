<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle admin
if (!isset($_SESSION['user_login']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: menu.php?error=Accès non autorisé.");
    exit;
}

require_once "conn.php";

// Vérifie si un ID est fourni
if (!isset($_GET["id"])) {
    echo "ID du produit manquant.";
    exit;
}

$id_p = $_GET["id"];

// Supprime directement le produit
$sql = "DELETE FROM produit WHERE id_p = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_p]);

// Rafraichir la page
header("Location: gestion_produits.php");
exit;
?>
