<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="dashboard_admin.css">
</head>
<body>

<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
    Votre navigateur ne prend pas en charge les vidéos HTML5.
</video>

<div class="overlay">
    <header>
        <h1>Tableau de bord Administrateur</h1>
        <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_nom']); ?> 👋</p>
          <a href="fournisseur_login.php" class="fournisseur-link">Se connecter en tant que fournisseur</a>
    </header>

    <div class="dashboard-container">
        <a href="fournisseurs.php" class="card">
            <h3>👤 Gérer les fournisseurs</h3>
            <p>Ajoutez, modifiez ou supprimez des fournisseurs</p>
        </a>

        <a href="categories.php" class="card">
            <h3>📂 Gérer les catégories</h3>
            <p>Classez les produits dans les bonnes catégories</p>
        </a>

        <a href="produits.php" class="card">
            <h3>🛍️ Gérer les produits</h3>
            <p>Ajoutez ou éditez tous les produits</p>
        </a>

        <a href="commandes_admin.php" class="card">
            <h3>📦 Voir les commandes</h3>
            <p>Consultez et gérez les commandes reçues</p>
        </a>

        <a href="admin_users.php" class="card">
            <h3>👥 Voir les utilisateurs</h3>
            <p>Liste des clients enregistrés</p>
        </a>

        <a href="statistiques.php" class="card">
            <h3>📊 Statistiques</h3>
            <p>Visualisez les données clés (produits, ventes...)</p>
        </a>
    </div>

    <div class="logout">
        <a href="logout_admin.php">🚪 Déconnexion</a>
    </div>
</div>

</body>
</html>
