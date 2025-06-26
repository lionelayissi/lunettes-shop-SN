<?php
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php'); // adapter la page de connexion si besoin
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Merci pour votre achat</title>
  <style>
    body, html {
      margin: 0; padding: 0;
      height: 100%; overflow: hidden;
      font-family: Arial, sans-serif;
      color: white;
      text-align: center;
      background-color: #000; /* fallback si vidéo ne charge pas */
    }
    #background-video {
      position: fixed;
      right: 0; bottom: 0;
      min-width: 100%; min-height: 100%;
      z-index: -1;
      object-fit: cover;
    }
    .content {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.6);
      padding: 40px;
      border-radius: 20px;
      max-width: 90%;
      box-sizing: border-box;
    }
    a {
      color: #0af;
      font-weight: bold;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <!-- Vidéo en fond, muette et en boucle -->
  <video autoplay muted loop id="background-video" playsinline>
    <source src="assets/videos/lunettes.mp4" type="video/mp4" />
    Votre navigateur ne supporte pas la vidéo HTML5.
  </video>

  <div class="content" role="main" aria-label="Message de confirmation d'achat">
    <h1>Merci pour votre commande ! 🎉</h1>
    <p>Votre achat a été confirmé. Une facture est disponible dans <strong>"Mes commandes"</strong>.</p>
    <a href="client_dashboard.php" aria-label="Retour au tableau de bord client">Retour au tableau de bord</a>
  </div>
</body>
</html>
