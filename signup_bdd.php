<?php
require_once "conn.php"; 
$pdo = $conn;

try {
    // Vérifier si le formulaire est soumis
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Nettoyer toutes les valeurs POST
        $_POST = array_map('trim', $_POST);

        // Récupérer et valider les données du formulaire
        $login = $_POST["login"];
        $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL); // Validation email
        $password = $_POST["password"];

        // Vérifier si le login existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE user_login = :login");
        $stmt->execute([':login' => $login]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: signup.php?error=Ce nom d'utilisateur est déjà pris.");
            exit;
        }

        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE user_mail = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: signup.php?error=Cet email est déjà enregistré.");
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hashage du mdp

        $sql = "INSERT INTO user (user_login, user_password, user_compte_id, user_mail, role) VALUES (:login, :password, NULL, :email, 'user')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":login" => $login,
            ":email" => $email,
            ":password" => $hashed_password
        ]);
        header("Location: index.php"); 
        exit;
    }
} catch (PDOException $e) {
    header("Location: signup.php?error=Une erreur est survenue lors de l'inscription. Veuillez réessayer.");
    exit;
}
?>
