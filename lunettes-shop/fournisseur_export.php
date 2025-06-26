<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];

// Récupérer les ventes du fournisseur
$sql = "
    SELECT c.id AS commande_id, c.date_commande, u.nom AS client_nom, cd.produit_id, cd.quantite, cd.prix_unitaire, p.nom AS produit_nom
    FROM commandes c
    JOIN commande_details cd ON c.id = cd.commande_id
    JOIN produits p ON cd.produit_id = p.id
    JOIN users u ON c.user_id = u.id
    WHERE p.fournisseur_id = ?
    ORDER BY c.date_commande DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$fournisseur_id]);
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Exporter les ventes</title>
    <link rel="stylesheet" href="fournisseur_export.css">
    <style>
.back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 10;
}

.back-button button {
    padding: 8px 15px;
    background-color: #2ecc71;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
}

.back-button button:hover {
    background-color: #27ae60;
}
</style>
</head>
<body>
<div class="back-button">
    <button onclick="window.history.back()">🔙 Retour</button>
</div>
<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<div class="overlay">
    <header>
        <h1>📁 Exporter mes ventes</h1>
        <p>Exportez vos ventes en quelques clics</p>
    </header>

    <div class="export-actions">
        <form method="post" action="export_pdf.php">
            <button type="submit">📄 Exporter en PDF</button>
        </form>
        <form method="post" action="export_excel.php">
            <button type="submit">📊 Exporter en Excel</button>
        </form>
        <form method="post" action="export_xml.php">
            <button type="submit">📄 Exporter en XML</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Commande</th>
                <th>Date</th>
                <th>Client</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ventes as $vente): ?>
                <tr>
                    <td>#<?= $vente['commande_id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($vente['date_commande'])) ?></td>
                    <td><?= htmlspecialchars($vente['client_nom']) ?></td>
                    <td><?= htmlspecialchars($vente['produit_nom']) ?></td>
                    <td><?= $vente['quantite'] ?></td>
                    <td><?= number_format($vente['prix_unitaire'], 2, ',', ' ') ?> FCFA</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="logout">
        <a href="dashboard_fournisseur.php">🔙 Retour au tableau de bord</a>
    </div>
</div>
</body>
</html>
