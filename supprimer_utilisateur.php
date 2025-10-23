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
    echo "ID de l'utilisateur manquant.";
    exit;
}

$user_id = $_GET["id"];

// Supprime directement l'utilisateur
$sql = "DELETE FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Rafraichir la page
header("Location: gestion_utilisateurs.php");
exit;
?>
