<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];
$fournisseur_nom = $_SESSION['fournisseur_nom'];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);

    // Validation simple
    if ($nom === '') $errors[] = "Le nom du produit est requis.";
    if ($prix <= 0) $errors[] = "Le prix doit être supérieur à 0.";
    if ($stock < 0) $errors[] = "Le stock ne peut pas être négatif.";

    $uploadDir = 'assets/images/';
    $imageName = null;

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
                $imageName = $newFileName;
            } else {
                $errors[] = "Erreur lors du téléchargement de l'image.";
            }
        } else {
            $errors[] = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        }
    } else {
        $errors[] = "Veuillez sélectionner une image valide.";
    }

    $categorie_id = $_POST['categorie_id'] ?? null;

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO produits (image,nom, description, prix, stock, fournisseur_id, categorie_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$imageName, $nom, $description, $prix, $stock, $fournisseur_id, $categorie_id]);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
    <style>
        /* CSS intégré */
        body, html {
            margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; color: #fff;
            background: #222;
            overflow-x: hidden;
        }
        #background-video {
            position: fixed;
            right: 0; bottom: 0;
            min-width: 100%; min-height: 100%;
            z-index: -1;
            object-fit: cover;
        }
        .btn-back-top {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            z-index: 1000;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        .btn-back-top:hover {
            background-color: rgba(0, 0, 0, 0.85);
        }
        .overlay {
            position: relative;
            background-color: rgba(0,0,0,0.7);
            max-width: 600px;
            margin: 50px auto 50px auto;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px #000;
        }
        header h1 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 2em;
            text-align: center;
        }
        header p {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-style: italic;
        }
        .errors {
            background-color: #b00020;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .errors ul {
            margin: 0; padding-left: 20px;
        }
        .success {
            background-color: #1b8a07;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        form.form-produit label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        form.form-produit input[type="text"],
        form.form-produit input[type="number"],
        form.form-produit select,
        form.form-produit textarea,
        form.form-produit input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: none;
            font-size: 1em;
        }
        form.form-produit textarea {
            resize: vertical;
        }
        form.form-produit button {
            margin-top: 20px;
            background-color: #007bff;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        form.form-produit button:hover {
            background-color: #0056b3;
        }
        .btn-back {
            display: inline-block;
            margin-top: 10px;
            color: #ddd;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-back:hover {
            text-decoration: underline;
        }
        .logout {
            text-align: center;
            margin-top: 25px;
        }
        .logout a {
            color: #ccc;
            text-decoration: none;
            font-size: 1.2em;
        }
        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<a href="fournisseur_produits.php" class="btn-back-top">⬅️ Retour</a>

<div class="overlay">
    <header>
        <h1>Ajouter un nouveau produit</h1>
        <p>Fournisseur : <?= htmlspecialchars($fournisseur_nom) ?></p>
    </header>

    <?php if ($success): ?>
        <p class="success">Produit ajouté avec succès ! <a href="fournisseur_produits.php" style="color:#ddd;text-decoration:underline;">Retour à mes produits</a></p>
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

        <form method="POST" enctype="multipart/form-data" class="form-produit">

            <label>Image du produit :</label>
            <input type="file" name="image" accept="image/*" required>

            <label>Nom du produit :</label>
            <input type="text" name="nom" required>

            <label>Description :</label>
            <textarea name="description" rows="4"></textarea>

            <label>Prix (FCFA) :</label>
            <input type="number" step="0.01" name="prix" required min="0">

            <label for="categorie_id">Catégorie :</label>
            <select name="categorie_id" id="categorie_id" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php
                require_once 'includes/connexion.php';
                $stmt = $pdo->query("SELECT id, nom FROM categories");
                while ($categorie = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"" . htmlspecialchars($categorie['id']) . "\">" . htmlspecialchars($categorie['nom']) . "</option>";
                }
                ?>
            </select>

            <label>Stock :</label>
            <input type="number" name="stock" required min="0">

            <button type="submit">Ajouter</button>
            <a href="fournisseur_produits.php" class="btn-back">⬅️ Retour</a>
        </form>
    <?php endif; ?>

    <div class="logout">
        <a href="logout.php">🚪 Déconnexion</a>
    </div>
</div>

</body>
</html>
