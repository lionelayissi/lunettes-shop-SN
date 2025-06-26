<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];
$fournisseur_nom = $_SESSION['fournisseur_nom'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: fournisseur_produits.php');
    exit();
}

$produit_id = intval($_GET['id']);

// Vérifier que le produit appartient bien au fournisseur
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ? AND fournisseur_id = ?");
$stmt->execute([$produit_id, $fournisseur_id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    header('Location: fournisseur_produits.php');
    exit();
}

$errors = [];
$success = false;
$uploadDir = 'uploads/'; // Ajout du dossier d'upload

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);

    if ($nom === '') $errors[] = "Le nom du produit est requis.";
    if ($prix <= 0) $errors[] = "Le prix doit être supérieur à 0.";
    if ($stock < 0) $errors[] = "Le stock ne peut pas être négatif.";

    $imageName = $produit['image']; // image actuelle par défaut

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Supprimer ancienne image si existe et différente
                if ($imageName && file_exists($uploadDir . $imageName)) {
                    unlink($uploadDir . $imageName);
                }
                $imageName = $newFileName;
            } else {
                $errors[] = "Erreur lors du téléchargement de l'image.";
            }
        } else {
            $errors[] = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        }
    }

    if (empty($errors)) {
        // Requête corrigée : on ajoute la mise à jour de l'image dans la requête SQL
        $stmt = $pdo->prepare("UPDATE produits SET image = ?, nom = ?, description = ?, prix = ?, stock = ? WHERE id = ? AND fournisseur_id = ?");
        $stmt->execute([$imageName, $nom, $description, $prix, $stock, $produit_id, $fournisseur_id]);
        $success = true;

        // Recharger les données modifiées pour affichage dans le formulaire
        $produit['image'] = $imageName;
        $produit['nom'] = $nom;
        $produit['description'] = $description;
        $produit['prix'] = $prix;
        $produit['stock'] = $stock;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier produit</title>
    <link rel="stylesheet" href="fournisseur_produits.css">
</head>
<body>
<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<div class="overlay">
    <header>
        <h1>Modifier le produit</h1>
        <p>Fournisseur : <?= htmlspecialchars($fournisseur_nom) ?></p>
    </header>

    <?php if ($success): ?>
        <p class="success">Produit modifié avec succès ! <a href="fournisseur_produits.php">Retour à mes produits</a></p>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-produit" enctype="multipart/form-data">
            <label>Image du produit :</label>
            <?php if ($produit['image'] && file_exists($uploadDir . $produit['image'])): ?>
                <img src="<?= htmlspecialchars($uploadDir . $produit['image']) ?>" alt="Image produit" style="max-width:150px; display:block; margin-bottom:10px;">
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">

            <label>Nom du produit :</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" required>

            <label>Description :</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($produit['description']) ?></textarea>

            <label>Prix (FCFA):</label>
            <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($produit['prix']) ?>" required min="0">

            <label>Stock :</label>
            <input type="number" name="stock" value="<?= htmlspecialchars($produit['stock']) ?>" required min="0">

            <button type="submit">Modifier</button>
            <a href="fournisseur_produits.php" class="btn-back">⬅️ Retour</a>
        </form>
    <?php endif; ?>

    <div class="logout">
        <a href="logout.php">🚪 Déconnexion</a>
    </div>
</div>
</body>
</html>
