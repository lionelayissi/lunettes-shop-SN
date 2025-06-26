<?php
session_start();

// Redirection si utilisateur non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/connexion.php';

try {
    $stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0");
    $produits = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur SQL : " . $e->getMessage());
    $produits = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Client - SHABS OPTIQUE 237</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .btn-retour {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }
        .btn-retour:hover {
            background-color: #0056b3;
        }

        /* Style pour réduire la barre de recherche et la centrer */
        #search-container {
          display: flex;
          align-items: center;
          max-width: 300px;
          margin: 20px auto 20px auto; /* marge verticale et centrage horizontal */
          /* margin-left et margin-right automatiques centrent le bloc */
        }

        #search-input {
          flex: 1;
          padding: 6px 10px;
          font-size: 14px;
          border: 1px solid #ccc;
          border-radius: 4px 0 0 4px;
          height: 32px;
          box-sizing: border-box;
        }

        #mic-btn {
          width: 36px;
          height: 32px;
          border: 1px solid #ccc;
          border-left: none;
          background-color: #f0f0f0;
          border-radius: 0 4px 4px 0;
          cursor: pointer;
          font-size: 18px;
          line-height: 1;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: background-color 0.3s ease;
        }

        #mic-btn:hover {
          background-color: #e0e0e0;
        }
    </style>
</head>
<body class="dashboard-body">
    <a href="javascript:history.back()" class="btn-retour">← Retour</a>

    <video autoplay muted loop id="background-video">
        <source src="assets/videos/video1.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo.
    </video>

    <div class="dashboard-overlay">
       <header class="dashboard-header">
         <h1>Bienvenue, <?= isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : 'Utilisateur' ?> 👋</h1>
         <nav>
             <a href="panier.php">🛒 Mon panier</a>
             <a href="mes_commandes.php">📦 Mes commandes</a>
             <a href="modifier_profil1.php">⚙️ Modifier profil</a>
             <a href="logout.php">Déconnexion</a>
         </nav>
       </header>
       
       <div id="search-container">
          <input type="text" id="search-input" placeholder="Rechercher un produit..." autocomplete="off" aria-label="Recherche de produit" />
          <button id="mic-btn" type="button" aria-label="Activer la recherche vocale">🎙️</button>
          <div id="suggestions-box"></div>
       </div>

       <h2><strong>Nos Produits</strong></h2>
       <div class="produits-container" id="produits-list">
         <!-- Les produits s'affichent ici dynamiquement -->
       </div>
    </div>

    <script src="script.js"></script>
    <script src="panier.js"></script>
</body>
</html>
