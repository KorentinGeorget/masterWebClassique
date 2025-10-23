<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle admin
if (!isset($_SESSION["user_login"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: menu.php?error=Accès non autorisé.");
    exit();
}

require_once "conn.php";

// Charger les catégories depuis le fichier JSON
$categories = [];
$categories_json_path = __DIR__ . "/categories.json";
if (file_exists($categories_json_path)) {
    $categories_json = file_get_contents($categories_json_path);
    $categories = json_decode($categories_json, true);
}

// Vérifie si un ID est fourni dans l’URL
if (!isset($_GET["id"])) {
    echo "ID du produit manquant.";
    exit();
}

$id_p = $_GET["id"];

// Récupérer le produit correspondant
$sql = "SELECT * FROM produit WHERE id_p = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_p]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    echo "Produit introuvable.";
    exit();
}

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // On nettoie tout le tableau $_POST
    $_POST = array_map("trim", $_POST);

    $type_p = $_POST["type_p"];
    $designation_p = $_POST["designation_p"];
    $prix_ht = $_POST["prix_ht"];
    $date_in = $_POST["date_in"];
    $stock_p = $_POST["stock_p"];
    $ppromo = !empty($_POST["ppromo"]) ? $_POST["ppromo"] : null;
    $image_path = $produit["image_path"]; // Conserver le chemin de l'image existante par défaut

    // Gestion de l'upload de l'image
    if (
        isset($_FILES["product_image"]) &&
        $_FILES["product_image"]["error"] === UPLOAD_ERR_OK
    ) {
        $fileTmpPath = $_FILES["product_image"]["tmp_name"];
        $fileName = $_FILES["product_image"]["name"];
        $fileMimeType = $_FILES["product_image"]["type"];

        $allowedMimeTypes = ["image/jpeg", "image/gif", "image/png"];
        if (in_array($fileMimeType, $allowedMimeTypes)) {
            $uploadFileDir = "./uploads/";
            $mime_to_ext = [
                "image/jpeg" => "jpeg",
                "image/png" => "png",
                "image/gif" => "gif",
            ];
            $fileExtension = $mime_to_ext[$fileMimeType];
            $newFileName = md5(time() . $fileName) . "." . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Supprimer l'ancienne image si elle existe
                if (
                    !empty($produit["image_path"]) &&
                    file_exists($produit["image_path"])
                ) {
                    unlink($produit["image_path"]);
                }
                $image_path = $dest_path;
            } else {
                header(
                    "Location: modifier_produit.php?id=" .
                        $id_p .
                        "&error=Erreur lors du déplacement du fichier téléchargé.",
                );
                exit();
            }
        } else {
            header(
                "Location: modifier_produit.php?id=" .
                    $id_p .
                    "&error=Type de fichier image non autorisé.",
            );
            exit();
        }
    } elseif (
        isset($_FILES["product_image"]) &&
        $_FILES["product_image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        header(
            "Location: modifier_produit.php?id=" .
                $id_p .
                "&error=Erreur lors du téléchargement de l'image.",
        );
        exit();
    }

    // Vérifier si la désignation existe déjà pour un autre produit
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM produit WHERE designation_p = :designation_p AND id_p != :id_p_current",
    );
    $stmt->execute([
        ":designation_p" => $designation_p,
        ":id_p_current" => $id_p,
    ]);
    if ($stmt->fetchColumn() > 0) {
        header(
            "Location: modifier_produit.php?id=" .
                $id_p .
                "&error=Un autre produit avec cette désignation existe déjà.",
        );
        exit();
    }

    $sql =
        "UPDATE produit SET type_p = ?, designation_p = ?, prix_ht = ?, date_in = ?, stock_p = ?, ppromo = ?, image_path = ? WHERE id_p = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $type_p,
        $designation_p,
        $prix_ht,
        $date_in,
        $stock_p,
        $ppromo,
        $image_path,
        $id_p,
    ]);

    header("Location: gestion_produits.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier un produit</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 text-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-gray-900 border border-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md">
    <h1 class="text-2xl font-bold text-indigo-400 text-center mb-6">Modifier un produit</h1>

    <form method="post" class="space-y-5" enctype="multipart/form-data">

      <div>
        <label class="block text-gray-300 mb-1">Type :</label>
        <select name="type_p" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
          <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars(
                $category["categorie"],
            ) ?>" <?= $produit["type_p"] === $category["categorie"]
    ? "selected"
    : "" ?>><?= htmlspecialchars($category["categorie"]) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Désignation :</label>
        <input type="text" name="designation_p" value="<?= htmlspecialchars(
            $produit["designation_p"],
        ) ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Prix HT :</label>
        <input type="number" step="0.01" name="prix_ht" value="<?= htmlspecialchars(
            $produit["prix_ht"],
        ) ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Date d'entrée :</label>
        <input type="date" name="date_in" value="<?= htmlspecialchars(
            $produit["date_in"],
        ) ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Stock :</label>
        <input type="number" name="stock_p" value="<?= htmlspecialchars(
            $produit["stock_p"],
        ) ?>" min="0" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Pourcentage de promotion (ppromo) :</label>
        <input type="number" step="0.01" name="ppromo" value="<?= htmlspecialchars(
            $produit["ppromo"],
        ) ?>" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Ex: 10.50 pour 10.5%">
      </div>

      <div>
        <label class="block text-gray-300 mb-1">Image actuelle :</label>
        <?php if (
            !empty($produit["image_path"]) &&
            file_exists($produit["image_path"])
        ): ?>
          <img src="<?= htmlspecialchars(
              $produit["image_path"],
          ) ?>" alt="Image actuelle" class="w-32 h-32 object-cover rounded-lg mb-2">
        <?php else: ?>
          <p class="text-gray-400">Aucune image</p>
        <?php endif; ?>
        <label class="block text-gray-300 mb-1">Changer l'image :</label>
        <input type="file" name="product_image" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <div class="flex justify-between mt-6">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-lg shadow">Enregistrer</button>
        <a href="gestion_produits.php" class="text-gray-400 hover:text-gray-200 underline">Annuler</a>
      </div>

    </form>
  </div>

</body>
</html>
