<?php
session_start();
require 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];
$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = !empty($_POST['mot_de_passe']) ? password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT) : null;

    if ($mot_de_passe) {
        $stmt = $conn->prepare("UPDATE fournisseurs SET nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $mot_de_passe, $fournisseur_id]);
    } else {
        $stmt = $conn->prepare("UPDATE fournisseurs SET nom = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $fournisseur_id]);
    }

    $_SESSION['fournisseur_nom'] = $nom;
    $message = "✅ Profil mis à jour avec succès.";
}

// Récupération des données actuelles
$stmt = $pdo->prepare("SELECT nom, email FROM fournisseurs WHERE id = ?");
$stmt->execute([$fournisseur_id]);
$fournisseur = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="fournisseur_profil.css">
    <style>
        .btn-retour {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
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
</video>

<div class="overlay">
    <!-- Bouton retour -->
    <a href="fournisseur_dashboard.php" class="btn-retour">← Retour</a>

    <h1>👤 Mon Profil</h1>

    <?php if ($message): ?>
        <div class="success-message"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="profil-form">
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($fournisseur['nom']) ?>" required>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($fournisseur['email']) ?>" required>

        <label>Nouveau mot de passe :</label>
        <input type="password" name="mot_de_passe" placeholder="Laisser vide pour ne pas changer">

        <button type="submit">💾 Mettre à jour</button>
    </form>
</div>

</body>
</html>
