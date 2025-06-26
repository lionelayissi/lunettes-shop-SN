<?php
// Connexion à la BDD
$pdo = new PDO('mysql:host=localhost;dbname=lunettes_db', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// 1. Nombre total de produits
$stmt = $pdo->query("SELECT COUNT(*) FROM produits");
$totalProduits = $stmt->fetchColumn();

// 2. Nombre total de commandes (tous statuts)
$stmt = $pdo->query("SELECT COUNT(*) FROM commandes");
$totalCommandes = $stmt->fetchColumn();

// 3. Chiffre d’affaires total (commandes confirmées)
$stmt = $pdo->prepare("SELECT SUM(total) FROM commandes WHERE statut = 'CONFIRMER'");
$stmt->execute();
$chiffreAffaires = $stmt->fetchColumn();
if (!$chiffreAffaires) $chiffreAffaires = 0;

// 4. Nombre total d’utilisateurs
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

// 5. Top 5 produits les plus vendus (quantité totale vendue)
// On somme les quantités dans commande_details
$stmt = $pdo->query("
    SELECT p.nom, SUM(cd.quantite) AS total_vendu
    FROM commande_details cd
    JOIN produits p ON cd.produit_id = p.id
    GROUP BY cd.produit_id
    ORDER BY total_vendu DESC
    LIMIT 5
");
$topProduits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6. Ventes par catégorie (quantité totale vendue par catégorie)
$stmt = $pdo->query("
    SELECT c.nom AS categorie, SUM(cd.quantite) AS total_vendu
    FROM commande_details cd
    JOIN produits p ON cd.produit_id = p.id
    JOIN categories c ON p.categorie_id = c.id
    GROUP BY c.id
");
$ventesParCategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Statistiques - Admin</title>
    <link rel="stylesheet" href="statistiques.css" />
    <style>
.back-button {
    margin-top: 20px;
    text-align: center;
}

.back-button button {
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
}

.back-button button:hover {
    background-color: #2980b9;
}
</style>
</head>
<body>
    <!-- Vidéo en fond global -->
<video autoplay muted loop id="background-video" preload="auto" playsinline>
    <source src="assets/videos/video5.mp4" type="video/mp4" />
    Ton navigateur ne supporte pas la vidéo en fond.
</video>

<div class="container">
    <h1>Tableau de bord des statistiques</h1>

    <div class="cards">

        <div class="card">
            <h2>Total Produits</h2>
            <p><?= $totalProduits ?></p>
        </div>

        <div class="card">
            <h2>Total Commandes</h2>
            <p><?= $totalCommandes ?></p>
        </div>

        <div class="card">
            <h2>Chiffre d'affaires (confirmées)</h2>
            <p><?= number_format($chiffreAffaires, 2, ',', ' ') ?> FCFA</p>
        </div>

        <div class="card">
            <h2>Total Utilisateurs</h2>
            <p><?= $totalUsers ?></p>
        </div>

    </div>

    

    <div class="list-section">

        <h2>Top 5 Produits les plus vendus</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité vendue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topProduits as $prod): ?>
                    <tr>
                        <td data-label="Produit"><?= htmlspecialchars($prod['nom']) ?></td>
                        <td data-label="Quantité vendue"><?= $prod['total_vendu'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Ventes par catégorie</h2>
        <table>
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Quantité vendue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventesParCategorie as $cat): ?>
                    <tr>
                        <td data-label="Produit"><?= htmlspecialchars($cat['categorie']) ?></td>
                        <td data-label="Quantité vendue"><?= $cat['total_vendu'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

<div class="export-buttons">
    <button onclick="window.location.href='export_xml.php'">📤 Exporter en XML</button>
</div>

<div class="back-button">
    <button onclick="window.history.back()">🔙 Retour</button>
</div>

</body>
</html>
