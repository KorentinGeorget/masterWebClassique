<?php
session_start();
require_once "conn.php"; 

$pdo = $conn;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Nettoyer toutes les valeurs POST
    $_POST = array_map('trim', $_POST);

    // Récupérer les données du formulaire
    $login = $_POST["login"];
    $password = $_POST["password"];
        try {
            // Rechercher l'utilisateur dans la base de données
            $prep = $pdo->prepare("SELECT * FROM user WHERE user_login = ?");
            $prep->execute([$login]);
            $user = $prep->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["user_password"])) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["user_login"] = $user["user_login"];
                $_SESSION["user_role"] = $user["role"]; // Stocker le rôle de l'utilisateur

                // Mettre à jour la date de dernière connexion
                $update_stmt = $pdo->prepare("UPDATE user SET user_date_login = CURRENT_TIMESTAMP WHERE user_id = :user_id");
                $update_stmt->execute([':user_id' => $user["user_id"]]);

                header("Location: menu.php");
                exit;
            } else {
                // Identifiants incorrects
                header("Location: index.php?error=" . urlencode("Login ou mot de passe incorrect."));
                exit;
            }
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
?>