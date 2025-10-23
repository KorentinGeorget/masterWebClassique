<?php
session_start();

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_login'])) {
    header("Location: index.php?error=Vous devez être connecté pour accéder à cette page.");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Menu principal</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 text-gray-100 min-h-screen font-sans">

  <nav class="bg-gray-900 border-b border-gray-800 shadow-md">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-indigo-400">Tableau de bord</h1>
      <div class="flex items-center gap-6">
        <?php if (isset($_SESSION['user_login'])): ?>
          <span class="text-gray-400">
            <?= htmlspecialchars($_SESSION['user_login']) ?>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
              <span class="ml-2 px-2 py-0.5 bg-indigo-500 text-white text-xs font-semibold rounded-full">Admin</span>
            <?php endif; ?>
          </span>
          <a href="logout.php" class="text-red-400 hover:text-red-300 transition">Déconnexion</a>
        <?php else: ?>
          <a href="index.html" class="text-indigo-400 hover:text-indigo-300 transition">Connexion</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="max-w-5xl mx-auto mt-16 text-center">
    <h2 class="text-3xl font-bold text-indigo-400 mb-4">Bienvenue</h2>
    <p class="text-gray-400 mb-10">Choisissez une section pour gérer vos données.</p>

    <div class="flex justify-center gap-8">
        <a href="gestion_produits.php" class="bg-green-600 hover:bg-green-500 px-6 py-3 rounded-lg shadow-lg text-white font-semibold transition">
            Voir les produits
        </a>
        <a href="gestion_utilisateurs.php" class="bg-indigo-600 hover:bg-indigo-500 px-6 py-3 rounded-lg shadow-lg text-white font-semibold transition">
          Gérer les utilisateurs
        </a>
    </div>
  </main>

</body>
</html>
