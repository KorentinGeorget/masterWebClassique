<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 flex items-center justify-center font-sans">

  <div class="bg-gray-900 text-gray-100 w-full max-w-md rounded-2xl shadow-2xl border border-gray-700 p-8">

    <h1 class="text-3xl font-extrabold text-center text-indigo-400 mb-6">Connexion</h1>

    <!-- Message d'erreur -->
    <?php if (isset($_GET['error'])): ?>
      <div class="bg-red-500/20 border border-red-500 text-red-400 rounded-md p-3 mb-5 text-sm text-center">
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="verify_login.php" class="space-y-5">

      <div>
        <label class="block text-sm text-gray-300 mb-1">Identifiant</label>
        <input type="text" name="login" required
          class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-100 placeholder-gray-400"
          placeholder="Votre login" />
      </div>

      <div>
        <label class="block text-sm text-gray-300 mb-1">Mot de passe</label>
        <input type="password" name="password" required
          class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-100 placeholder-gray-400" 
          placeholder="••••••••" />
      </div>

      <button type="submit"
        class="w-full bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 transition-all text-white font-semibold py-2.5 rounded-lg shadow-md">
        Se connecter
      </button>
    </form>

    <p class="text-gray-400 text-sm text-center mt-6">
    Pas encore de compte ? <a href="signup.php" class="text-indigo-400 hover:text-indigo-300 underline">Créer un compte</a>
    </p>
  </div>

</body>
</html>
