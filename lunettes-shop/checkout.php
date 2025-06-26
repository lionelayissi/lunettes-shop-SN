<?php
session_start();
require_once 'includes/connexion.php'; // Connexion à la base de données

$erreurs = [];
$succes = false;

// Nettoyer les produits invalides (ex : ID = 0 ou pas numérique)
foreach ($_SESSION['panier'] as $id => $quantite) {
    if (!is_numeric($id) || $id <= 0) {
        unset($_SESSION['panier'][$id]);
    }
}

// Vérifier que le panier n'est pas vide
if (empty($_SESSION['panier'])) {
    $erreurs[] = "Votre panier est vide.";
} else {
    // Calcul du total
    $total_commande = 0;

    foreach ($_SESSION['panier'] as $id => $quantite) {
        // Récupérer le stock et le prix unitaire du produit
        $stmt = $pdo->prepare("SELECT stock, prix FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        $produit = $stmt->fetch();

        if (!$produit) {
            $erreurs[] = "Produit ID $id non trouvé.";
            continue;
        }

        if ($produit['stock'] < $quantite) {
            $erreurs[] = "Stock insuffisant pour le produit ID $id.";
            continue;
        }

        // Réduire le stock
        $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$quantite, $id]);

        // Calcul total (quantité * prix)
        $total_commande += $quantite * $produit['prix'];
    }

    // Si pas d'erreurs, enregistrer la commande dans la base de données
    if (empty($erreurs)) {
        $user_id = $_SESSION['user_id'];

        // Insérer la commande avec statut "en attente"
        $stmt = $pdo->prepare("INSERT INTO commandes (user_id, date_commande, total, statut) VALUES (?, NOW(), ?, 'CONFIRMER')");
        $stmt->execute([$user_id, $total_commande]);

        // Récupérer l'ID de la commande insérée
        $commande_id = $pdo->lastInsertId();

        // Insérer les détails de la commande
        $stmt_details = $pdo->prepare("INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");

        foreach ($_SESSION['panier'] as $id => $quantite) {
            // Récupérer le prix unitaire à nouveau (optionnel si prix n'a pas changé)
            $stmt_produit = $pdo->prepare("SELECT prix FROM produits WHERE id = ?");
            $stmt_produit->execute([$id]);
            $produit = $stmt_produit->fetch();

            $stmt_details->execute([$commande_id, $id, $quantite, $produit['prix']]);
        }

        // Vider le panier
        $_SESSION['panier'] = [];
        $succes = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Validation de l'achat</title>
  <link rel="stylesheet" href="checkout.css">
</head>
<body>
    <video autoplay muted loop id="bg-video">
  <source src="assets/videos/lunettes.mp4" type="video/mp4">
  Votre navigateur ne supporte pas la vidéo.
</video>

<div class="dashboard-overlay">

  <?php if ($succes): ?>
    <div class="success-message">
      <h2>✅ Achat validé avec succès !</h2>
      <p>Merci pour votre commande.</p>
      <a href="client_dashboard.php" class="btn btn-retour">Retour à l'accueil</a>
      <!-- Lien vers la facture -->
      <a href="facture.php?id=<?= $commande_id ?>" class="btn btn-facture">Voir ma facture</a>
    </div>
  <?php else: ?>
    <div class="error-message">
      <h2>❌ Erreur(s) pendant la validation :</h2>
      <ul>
        <?php foreach ($erreurs as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
      <a href="panier.php" class="btn btn-retour">Retour au panier</a>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
