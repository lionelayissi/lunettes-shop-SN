<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];
$fournisseur_nom = $_SESSION['fournisseur_nom'];

// Statistiques globales
$stmt = $pdo->prepare("
    SELECT SUM(cd.quantite * cd.prix_unitaire) AS total_ventes, 
           SUM(cd.quantite) AS total_produits
    FROM commande_details cd
    JOIN produits p ON cd.produit_id = p.id
    WHERE p.fournisseur_id = ?
");
$stmt->execute([$fournisseur_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Produits les plus vendus
$top_stmt = $pdo->prepare("
    SELECT p.nom, SUM(cd.quantite) AS quantite_vendue
    FROM commande_details cd
    JOIN produits p ON cd.produit_id = p.id
    WHERE p.fournisseur_id = ?
    GROUP BY p.nom
    ORDER BY quantite_vendue DESC
    LIMIT 5
");
$top_stmt->execute([$fournisseur_id]);
$top_products = $top_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques Fournisseur</title>
    <link rel="stylesheet" href="fournisseur_stats.css">
</head>
<body>
    <video autoplay muted loop id="background-video">
        <source src="assets/videos/video5.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo HTML5.
    </video>

    <div class="overlay">
        <header>
            <h1>📊 Statistiques de <?= htmlspecialchars($fournisseur_nom) ?></h1>
            <p>Suivi des performances de vos ventes</p>
        </header>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Total des ventes (FCFA)</h3>
                <p><?= number_format($stats['total_ventes'] ?? 0, 2, ',', ' ') ?> €</p>
            </div>
            <div class="stat-card">
                <h3>Produits vendus</h3>
                <p><?= $stats['total_produits'] ?? 0 ?></p>
            </div>
        </div>

        <div class="top-products">
            <h2>🏆 Top 5 Produits Vendus</h2>
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité vendue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['nom']) ?></td>
                            <td><?= $product['quantite_vendue'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="logout">
            <a href="logout.php">🚪 Déconnexion</a>
            <a href="fournisseur_graph.php"> Graphique</a>
        </div>
    </div>
</body>
</html>
