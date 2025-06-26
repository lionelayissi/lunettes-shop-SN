<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes des Utilisateurs</title>
    <link rel="stylesheet" href="commandes_admin.css">
</head>
<body>
<video autoplay loop muted playsinline class="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>
<div class="background-overlay"></div>

<div class="commandes-container">
    <h1>Commandes des Utilisateurs</h1>

    <form class="filtres-form" id="filtres">
        <input type="text" name="user_id" placeholder="ID utilisateur ou nom">
        <input type="date" name="date">
        <input type="number" name="prix_min" placeholder="Prix min">
        <input type="number" name="prix_max" placeholder="Prix max">
    </form>

    <div id="resultats-commandes">
        <!-- Résultats AJAX ici -->
    </div>
</div>

<script src="commande_admin.js"></script>
</body>
</html>

