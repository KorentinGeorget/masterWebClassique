<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle admin
if (!isset($_SESSION['user_login']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: menu.php?error=Accès non autorisé.");
    exit;
}

require_once "conn.php";

// Vérifie si un ID est fourni dans l’URL
if (!isset($_GET["id"])) {
    echo "ID de l'utilisateur manquant.";
    exit;
}

$user_id = $_GET["id"];

// Récupérer l'utilisateur correspondant
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // On nettoie tout le tableau $_POST
    $_POST = array_map('trim', $_POST);
    
    $user_login = $_POST["user_login"];
    $user_mail = $_POST["user_mail"];
    $role = $_POST["role"];

    // Vérifier si le login existe déjà pour un autre utilisateur
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE user_login = :login AND user_id != :user_id");
    $stmt->execute([':login' => $user_login, ':user_id' => $user_id]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: modifier_utilisateur.php?id=" . $user_id . "&error=Ce nom d'utilisateur est déjà pris par un autre compte.");
        exit;
    }

    // Vérifier si l'email existe déjà pour un autre utilisateur
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE user_mail = :email AND user_id != :user_id");
    $stmt->execute([':email' => $user_mail, ':user_id' => $user_id]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: modifier_utilisateur.php?id=" . $user_id . "&error=Cet email est déjà enregistré par un autre compte.");
        exit;
    }

    // Si un nouveau mot de passe est fourni, on le met à jour
    if (!empty($_POST["user_password"])) {
        $user_password = password_hash($_POST["user_password"], PASSWORD_BCRYPT);
        $sql = "UPDATE user SET user_login = ?, user_password = ?, user_mail = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_login, $user_password, $user_mail, $role, $user_id]);
    } else {
        // Sinon, on ne touche pas au mot de passe
        $sql = "UPDATE user SET user_login = ?, user_mail = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_login, $user_mail, $role, $user_id]);
    }

    header("Location: gestion_utilisateurs.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier un utilisateur</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 text-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-gray-900 border border-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md">
    <h1 class="text-2xl font-bold text-indigo-400 text-center mb-6">Modifier un utilisateur</h1>

    <form method="post" class="space-y-5">

      <div>
        <label class="block text-gray-300 mb-1">Login :</label>
        <input type="text" name="user_login" value="<?= htmlspecialchars($user['user_login']) ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Mot de passe (laisser vide pour ne pas changer) :</label>
        <input type="password" name="user_password" placeholder="Nouveau mot de passe" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Email :</label>
        <input type="email" name="user_mail" value="<?= htmlspecialchars($user['user_mail']) ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>


      <div>
        <label class="block text-gray-300 mb-1">Rôle :</label>
        <select name="role" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
          <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>Utilisateur</option>
          <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Administrateur</option>
        </select>
      </div>

      <div class="flex justify-between mt-6">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-lg shadow">Enregistrer</button>
        <a href="gestion_utilisateurs.php" class="text-gray-400 hover:text-gray-200 underline">Annuler</a>
      </div>

    </form>
  </div>

</body>
</html>