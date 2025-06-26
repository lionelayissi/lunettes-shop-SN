<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Ajout
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $marque = $_POST['marque'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $fournisseur_id = $_POST['fournisseur_id'];
    $categorie_id = $_POST['categorie_id'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $img_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'assets/images/' . $img_name);
        $image = 'assets/images/' . $img_name;
    }

    $stmt = $pdo->prepare("INSERT INTO produits (nom, marque, description, prix, stock, fournisseur_id, categorie_id, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $marque, $description, $prix, $stock, $fournisseur_id, $categorie_id, $image]);

    header("Location: produits.php");
    exit();
}

// Suppression
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $pdo->prepare("DELETE FROM produits WHERE id = ?")->execute([$id]);
    header("Location: produits.php");
    exit();
}

// Modification
if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $marque = $_POST['marque'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $fournisseur_id = $_POST['fournisseur_id'];
    $categorie_id = $_POST['categorie_id'];

    $image = $_POST['ancienne_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $img_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'assets/images/' . $img_name);
        $image = 'assets/images/' . $img_name;
    }

    $stmt = $pdo->prepare("UPDATE produits SET nom=?, marque=?, description=?, prix=?, stock=?, fournisseur_id=?, categorie_id=?, image=? WHERE id=?");
    $stmt->execute([$nom, $marque, $description, $prix, $stock, $fournisseur_id, $categorie_id, $image, $id]);

    header("Location: produits.php");
    exit();
}

// Récupération
$produits = $pdo->query("SELECT produits.*, fournisseurs.nom AS fournisseur_nom, categories.nom AS categorie_nom 
                         FROM produits 
                         LEFT JOIN fournisseurs ON produits.fournisseur_id = fournisseurs.id
                         LEFT JOIN categories ON produits.categorie_id = categories.id
                         ORDER BY produits.id DESC")->fetchAll();

$fournisseurs = $pdo->query("SELECT * FROM fournisseurs")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits</title>
    <link rel="stylesheet" href="produits.css">
    <style>
        .btn-retour {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }

        .btn-retour:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<a href="javascript:history.back()" class="btn-retour">← Retour</a>

<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<div class="overlay">
    <div class="contenu">
        <h2>🛍️ Gérer les Produits</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Marque</th>
                    <th>Catégorie</th>
                    <th>Fournisseur</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nom']) ?></td>
                    <td><?= htmlspecialchars($p['marque']) ?></td>
                    <td><?= htmlspecialchars($p['categorie_nom']) ?></td>
                    <td><?= htmlspecialchars($p['fournisseur_nom']) ?></td>
                    <td><?= number_format($p['prix'], 2) ?>FCFA</td>
                    <td><?= $p['stock'] ?></td>
                    <td><img src="<?= $p['image'] ?>" width="60" alt="Image produit"></td>
                    <td>
                        <form method="post" enctype="multipart/form-data" style="display:inline-block;">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <input type="text" name="nom" value="<?= htmlspecialchars($p['nom']) ?>" required>
                            <input type="text" name="marque" value="<?= htmlspecialchars($p['marque']) ?>" required>
                            <input type="text" name="description" value="<?= htmlspecialchars($p['description']) ?>" required>
                            <input type="number" step="0.01" name="prix" value="<?= $p['prix'] ?>" required>
                            <input type="number" name="stock" value="<?= $p['stock'] ?>" required>
                            <select name="fournisseur_id" required>
                                <?php foreach ($fournisseurs as $f): ?>
                                    <option value="<?= $f['id'] ?>" <?= $p['fournisseur_id'] == $f['id'] ? 'selected' : '' ?>><?= $f['nom'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="categorie_id" required>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $p['categorie_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['nom'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="ancienne_image" value="<?= $p['image'] ?>">
                            <input type="file" name="image">
                            <button type="submit" name="modifier">✏️</button>
                            <a class="btn-supprimer" href="?supprimer=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')">🗑️</a>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="form-ajout">
            <h3>➕ Ajouter un produit</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="marque" placeholder="Marque" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="number" name="prix" placeholder="Prix (FCFA)" step="0.01" required>
                <input type="number" name="stock" placeholder="Stock" required>
                <select name="fournisseur_id" required>
                    <option value="">-- Fournisseur --</option>
                    <?php foreach ($fournisseurs as $f): ?>
                        <option value="<?= $f['id'] ?>"><?= $f['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="categorie_id" required>
                    <option value="">-- Catégorie --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="image" required>
                <button type="submit" name="ajouter">Ajouter</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
