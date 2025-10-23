<?php
session_start();
require_once "conn.php";

// Vérifier que l’utilisateur est connecté
if (!isset($_SESSION["user_login"])) {
    header(
        "Location: index.php?error=Vous devez être connecté pour accéder à cette page.",
    );
    exit();
}

// Récupérer tous les produits du plus récent au plus ancien
try {
    $stmt = $conn->query("SELECT * FROM produit ORDER BY id_p DESC");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(
        "Erreur lors du chargement des produits : " .
            htmlspecialchars($e->getMessage())
    );
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des produits</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 text-gray-100 min-h-screen font-sans">

  <div class="max-w-6xl mx-auto px-6 py-10">

    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-indigo-400">Gestion des produits</h1>

      <div class="flex gap-3">
        <a href="menu.php"
           class="bg-gray-700 hover:bg-gray-600 text-gray-100 font-medium px-4 py-2 rounded-lg transition shadow-md">
          Retour au menu
        </a>

        <?php if (
            isset($_SESSION["user_role"]) &&
            $_SESSION["user_role"] === "admin"
        ): ?>
        <a href="ajouter_produit.php"
           class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-lg transition shadow-md">
          Ajouter un produit
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Liste des produits -->
    <?php if (empty($produits)): ?>
      <p class="text-gray-400">Aucun produit enregistré.</p>
    <?php else: ?>
      <div class="overflow-x-auto border border-gray-800 rounded-xl shadow-lg bg-gray-900">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-800 text-gray-300">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Type</th>
              <th class="px-4 py-3 text-left">Désignation</th>
              <th class="px-4 py-3 text-left">Prix</th>
              <th class="px-4 py-3 text-left">Date</th>
              <th class="px-4 py-3 text-left">Stock</th>
              <th class="px-4 py-3 text-left">Image</th>
                <?php if (
                    isset($_SESSION["user_role"]) &&
                    $_SESSION["user_role"] === "admin"
                ): ?>
                    <th class="px-4 py-3 text-left">Actions</th>
                <?php endif; ?>
          </thead>
          <tbody class="divide-y divide-gray-800">
            <?php foreach ($produits as $p): ?>
              <tr class="hover:bg-gray-800 transition">
                <td class="px-4 py-3"><?= htmlspecialchars($p["id_p"]) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($p["type_p"]) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars(
                    $p["designation_p"],
                ) ?></td>
                <td class="px-4 py-3">
                    <?php if (!empty($p["ppromo"]) && $p["ppromo"] > 0): ?>
                        <del class="text-gray-500"><?= number_format(
                            $p["prix_ht"],
                            2,
                            ",",
                            " ",
                        ) ?> €</del>
                        <span class="text-red-400 font-bold"><?= number_format(
                            $p["prix_ht"] * (1 - $p["ppromo"] / 100),
                            2,
                            ",",
                            " ",
                        ) ?> €</span>
                        <span class="text-xs text-green-400">(-<?= htmlspecialchars(
                            $p["ppromo"],
                        ) ?>%)</span>
                    <?php else: ?>
                        <?= number_format($p["prix_ht"], 2, ",", " ") ?> €
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3"><?= htmlspecialchars(
                    $p["date_in"],
                ) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars(
                    $p["stock_p"],
                ) ?></td>
                <td class="px-4 py-3">
                  <?php $imageSrc =
                      !empty($p["image_path"]) && file_exists($p["image_path"])
                          ? htmlspecialchars($p["image_path"])
                          : "uploads/no-image.jpg"; ?>
                  <img src="<?= $imageSrc ?>" alt="Image produit" class="w-12 h-12 object-cover rounded-md">
                </td>
                <?php if (
                    isset($_SESSION["user_role"]) &&
                    $_SESSION["user_role"] === "admin"
                ): ?>
                <td class="px-4 py-3">
                  <a href="modifier_produit.php?id=<?= urlencode(
                      $p["id_p"],
                  ) ?>" class="text-indigo-400 hover:text-indigo-300">Modifier</a>
                  <span class="text-gray-500 mx-1">|</span>
                  <a href="supprimer_produit.php?id=<?= urlencode(
                      $p["id_p"],
                  ) ?>" class="text-red-400 hover:text-red-300"
                     onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
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
