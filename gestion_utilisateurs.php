<?php
session_start();
require_once "conn.php"; 

// Vérifier que l’utilisateur est connecté
if (!isset($_SESSION['user_login'])) {
    header("Location: index.php?error=Vous devez être connecté pour accéder à cette page.");
    exit;
}

// Récupérer tous les users du plus récent au plus ancien
try {
    $stmt = $conn->query("SELECT * FROM user ORDER BY user_id DESC");
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors du chargement des utilisateurs : " . htmlspecialchars($e->getMessage()));
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des utilisateurs</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 text-gray-100 min-h-screen font-sans">

  <div class="max-w-6xl mx-auto px-6 py-10">

    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-indigo-400">Gestion des utilisateurs</h1>

      <div class="flex gap-3">
        <a href="menu.php"
           class="bg-gray-700 hover:bg-gray-600 text-gray-100 font-medium px-4 py-2 rounded-lg transition shadow-md">
          Retour au menu
        </a>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="ajouter_utilisateur.php"
           class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-lg transition shadow-md">
          Ajouter un utilisateur
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Liste des users -->
    <?php if (empty($user)): ?>
      <p class="text-gray-400">Aucun utilisateur enregistré.</p>
    <?php else: ?>
      <div class="overflow-x-auto border border-gray-800 rounded-xl shadow-lg bg-gray-900">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-800 text-gray-300">
            <tr>
                <th class="px-4 py-3 text-left">ID</th>
                <th class="px-4 py-3 text-left">nom</th>
                <th class="px-4 py-3 text-left">email</th>
                <th class="px-4 py-3 text-left">Date d’inscription</th>
                <th class="px-4 py-2 text-left">Dernière connexion</th>
                <th class="px-4 py-2 text-left">rôle</th>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <th class="px-4 py-3 text-left">Actions</th>
                <?php endif; ?>
          </thead>
          <tbody class="divide-y divide-gray-800">
            <?php foreach ($user as $u): ?>
              <tr class="hover:bg-gray-800 transition">
                <td class="px-4 py-3"><?= htmlspecialchars($u['user_id']) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($u['user_login']) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($u['user_mail']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($u['user_date_new']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($u['user_date_login']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($u['role']) ?></td>
                  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <td class="px-4 py-3">
                  <a href="modifier_utilisateur.php?id=<?= urlencode($u['user_id']) ?>" class="text-indigo-400 hover:text-indigo-300">Modifier</a>
                  <span class="text-gray-500 mx-1">|</span>
                  <a href="supprimer_utilisateur.php?id=<?= urlencode($u['user_id']) ?>" class="text-red-400 hover:text-red-300"
                     onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>