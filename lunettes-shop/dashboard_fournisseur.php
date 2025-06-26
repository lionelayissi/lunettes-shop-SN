<?php
session_start();
if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_nom = $_SESSION['fournisseur_nom'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Fournisseur</title>
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
    <link rel="stylesheet" href="dashboard_fournisseur.css">
</head>
<body>
    <div class="back-button">
    <button onclick="window.history.back()">🔙 Retour</button>
</div>
    <video autoplay muted loop id="background-video">
        <source src="assets/videos/video5.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo HTML5.
    </video>

    <div class="overlay">
        <header>
            <h1>👋 Bienvenue, <?= htmlspecialchars($fournisseur_nom) ?></h1>
            <p>Panneau de gestion fournisseur</p>
        </header>

        <div class="dashboard-container">

            <a href="fournisseur_produits.php" class="card">
                <h3>📦 Mes produits</h3>
                <p>Ajouter, modifier ou supprimer vos produits</p>
            </a>

            <a href="fournisseur_commandes.php" class="card">
                <h3>🧾 Commandes reçues</h3>
                <p>Voir les commandes contenant vos produits</p>
            </a>

            <a href="fournisseur_stats.php" class="card">
                <h3>📊 Statistiques</h3>
                <p>Suivre vos ventes et performances</p>
            </a>

            <a href="fournisseur_stock_alert.php" class="card">
                <h3>⚠️ Alertes stock</h3>
                <p>Produits avec un stock faible</p>
            </a>

            <a href="fournisseur_export.php" class="card">
                <h3>📁 Exporter</h3>
                <p>Exporter vos ventes en PDF, Excel ou XML</p>
            </a>

            <a href="fournisseur_profil.php" class="card">
                <h3>👤 Mon profil</h3>
                <p>Modifier vos informations personnelles</p>
            </a>

        </div>

        <div class="logout">
            <a href="logout_fournisseur.php">🚪 Se déconnecter</a>
        </div>
    </div>
</body>
</html>
