<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header("Location: fournisseur_login.php");
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];
$fournisseur_nom = $_SESSION['fournisseur_nom'];

// Définir le seuil d'alerte stock
$seuil_stock = 5;

// Récupérer les produits avec un stock faible
$stmt = $pdo->prepare("SELECT p.*, c.nom AS categorie_nom 
                       FROM produits p 
                       LEFT JOIN categories c ON p.categorie_id = c.id 
                       WHERE p.fournisseur_id = ? AND p.stock <= ?");
$stmt->execute([$fournisseur_id, $seuil_stock]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Alertes Stock</title>
    <link rel="stylesheet" href="fournisseur_stock_alert.css">
    <style>
        /* Style simple pour le bouton retour */
        .btn-retour {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-retour:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <video autoplay muted loop id="background-video">
        <source src="assets/videos/video5.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo HTML5.
    </video>

    <div class="overlay">
        <header>
            <h1>⚠️ Produits à stock faible</h1>
            <p>Bonjour, <?= htmlspecialchars($fournisseur_nom) ?>. Voici la liste de vos produits dont le stock est critique.</p>
        </header>

        <!-- Bouton retour -->
        <a href="fournisseur_dashboard.php" class="btn-retour">← Retour</a>

        <div class="alert-table-container">
            <?php if (count($produits) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Marque</th>
                            <th>Catégorie</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $produit): ?>
                            <tr>
                                <td><img src="assets/images/<?= htmlspecialchars($produit['image']) ?>" width="60"></td>
                                <td><?= htmlspecialchars($produit['nom']) ?></td>
                                <td><?= htmlspecialchars($produit['marque']) ?></td>
                                <td><?= htmlspecialchars($produit['categorie_nom']) ?></td>
                                <td>
                                    <form method="POST" action="update_stock.php" class="stock-form">
                                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                                        <input type="number" name="nouveau_stock" value="<?= $produit['stock'] ?>" min="0" class="stock-input">
                                        <button type="submit" class="stock-btn">💾</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-alert">Aucune alerte stock actuellement.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
