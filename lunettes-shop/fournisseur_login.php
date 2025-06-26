<?php
session_start();
require_once 'includes/connexion.php'; // ta connexion MySQL

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE email = ?");
    $stmt->execute([$email]);
    $fournisseur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fournisseur && password_verify($mot_de_passe, $fournisseur['mot_de_passe'])) {
        $_SESSION['fournisseur_id'] = $fournisseur['id'];
        $_SESSION['fournisseur_nom'] = $fournisseur['nom'];
        header("Location: dashboard_fournisseur.php");
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Fournisseur</title>
     <link rel="stylesheet" href="fournisseur_login.css">
</head>
<body>
    <video autoplay muted loop class="video-bg">
    <source src="assets/videos/video5.mp4" type="video/mp4">
    Votre navigateur ne supporte pas la vidéo HTML5.
</video>

    <div class="login-container">
        <h2>Connexion Fournisseur</h2>
        <?php if ($erreur): ?>
            <p class="erreur"><?= $erreur ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="fournisseur_register.php">S'inscrire</a></p>
    </div>
</body>
</html>
