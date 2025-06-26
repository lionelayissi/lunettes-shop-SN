<?php
session_start();
require_once 'includes/connexion.php';

$erreur = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = $_POST['nom'];
    $email       = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $entreprise  = $_POST['entreprise'];
    $telephone   = $_POST['telephone'];
    $adresse     = $_POST['adresse'];

    // Vérification si l'email est déjà utilisé
    $stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $erreur = "Cet email est déjà utilisé.";
    } else {
        // Hash du mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Insertion
        $stmt = $pdo->prepare("INSERT INTO fournisseurs (nom, email, mot_de_passe, entreprise, telephone, adresse) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$nom, $email, $mot_de_passe_hash, $entreprise, $telephone, $adresse])) {
            $success = "Inscription réussie. <a href='fournisseur_login.php'>Se connecter</a>";
        } else {
            $erreur = "Erreur lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Fournisseur</title>
    <link rel="stylesheet" href="fournisseur_register.css">
    <style>
        /* Bouton retour */
        .btn-retour {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-retour:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<video autoplay muted loop class="video-bg">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<div class="register-container">
    <!-- Bouton retour -->
    <a href="index.php" class="btn-retour">← Retour</a>

    <h2>Inscription Fournisseur</h2>

    <?php if ($erreur): ?>
        <p class="message error"><?= $erreur ?></p>
    <?php elseif ($success): ?>
        <p class="message success"><?= $success ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="nom" placeholder="Nom complet" required>
        <input type="email" name="email" placeholder="Adresse e-mail" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <input type="text" name="entreprise" placeholder="Nom de l'entreprise" required>
        <input type="text" name="telephone" placeholder="Téléphone" required>
        <textarea name="adresse" placeholder="Adresse" rows="3" required></textarea>
        <button type="submit">Créer mon compte</button>
    </form>
    <p style="text-align:center; margin-top: 15px;">
        <a href="fournisseur_login.php">Déjà inscrit ? Connexion</a>
    </p>
</div>
</body>
</html>
