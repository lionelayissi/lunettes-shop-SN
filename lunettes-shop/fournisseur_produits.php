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
        $stmt = $pdo->prepare("UPDATE fournisseurs SET nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $mot_de_passe, $fournisseur_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE fournisseurs SET nom = ?, email = ? WHERE id = ?");
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
        /* Bouton retour en haut à gauche */
        .btn-retour {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            z-index: 1000;
        }
        .btn-retour:hover {
            background-color: #217dbb;
        }

        /* Message succès */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            text-align: center;
        }

        /* Formulaire profil (au cas où CSS externe est absent) */
        .profil-form {
            display: flex;
            flex-direction: column;
            max-width: 400px;
            margin: auto;
        }

        .profil-form label {
            margin-top: 10px;
        }

        .profil-form input {
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .profil-form button {
            margin-top: 20px;
            padding: 10px;
            background-color: #2ecc71;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .profil-form button:hover {
            background-color: #27ae60;
        }

        .overlay {
            position: relative;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin: 60px auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
        }

        video#background-video {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
    </style>
</head>
<body>

<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<!-- Bouton de retour -->
<a href="fournisseur_dashboard.php" class="btn-retour">← Retour</a>

<div class="overlay">
    <h1>👤 Mon Profil</h1>

    <?php if ($message): ?>
        <div class="success-message"><?= htmlspecialchars($message) ?></div>
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
