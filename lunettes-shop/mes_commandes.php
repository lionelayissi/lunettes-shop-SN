<?php
session_start();
require_once 'includes/connexion.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php'); // adapte le chemin si besoin
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les commandes de l'utilisateur, triées par date décroissante
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE user_id = ? ORDER BY date_commande DESC");
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour formater les prix en FCFA
function formatFCFA($prix) {
    return number_format($prix, 0, ',', ' ') . " FCFA";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mes Commandes</title>
  <link rel="stylesheet" href="commandes.css" />
</head>
<body>
  <div class="background-overlay"></div>
  <div class="commandes-container">
    <h1>Mes Commandes</h1>

    <?php if (empty($commandes)): ?>
      <p class="empty-msg">Vous n'avez aucune commande pour le moment.</p>
    <?php else: ?>
      <div class="commandes-list">
        <?php foreach ($commandes as $commande): ?>
          <div class="commande-card">
            <h3>
              Commande #<?= htmlspecialchars($commande['id']) ?>
              <span><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></span>
            </h3>
            <p>Total : <strong><?= formatFCFA($commande['total']) ?></strong></p>
            <p>Statut : 
              <span class="statut <?= htmlspecialchars($commande['statut']) ?>">
                <?= ucfirst(htmlspecialchars($commande['statut'])) ?>
              </span>
            </p>
            <a href="facture.php?id=<?= urlencode($commande['id']) ?>" class="btn">Voir la facture</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
