<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle admin
if (!isset($_SESSION['user_login']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: menu.php?error=Accès non autorisé.");
    exit;
}

require_once "conn.php";

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // On nettoie tout le tableau $_POST
    $_POST = array_map('trim', $_POST);

    $login = $_POST["user_login"];
    $email = $_POST["user_mail"];

    // Vérifier si le login existe déjà
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE user_login = :login");
    $stmt->execute([':login' => $login]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: ajouter_utilisateur.php?error=Ce nom d'utilisateur est déjà pris.");
        exit;
    }

    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE user_mail = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: ajouter_utilisateur.php?error=Cet email est déjà enregistré.");
        exit;
    }

    // On hashe le mot de passe avant insertion
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Requête d'insertion
    $sql = "INSERT INTO user (user_login, user_password, user_compte_id, user_mail, role)
            VALUES (?, ?, NULL, ?, 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$login, $hashedPassword, $email]);

    header("Location: gestion_utilisateurs.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un utilisateur</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 text-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-gray-900 border border-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md">
    <h1 class="text-2xl font-bold text-indigo-400 text-center mb-6">Ajouter un utilisateur</h1>

    <form method="post" class="space-y-5">

      <div>
        <label class="block text-gray-300 mb-1">Nom d'utilisateur :</label>
        <input type="text" name="user_login" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Mot de passe :</label>
        <input type="password" name="user_password" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>


      <div>
        <label class="block text-gray-300 mb-1">Adresse e-mail :</label>
        <input type="email" name="user_mail" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div class="flex justify-between mt-6">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-lg shadow">Ajouter</button>
        <a href="gestion_utilisateurs.php" class="text-gray-400 hover:text-gray-200 underline">Annuler</a>
      </div>

    </form>
  </div>
</body>
</html>