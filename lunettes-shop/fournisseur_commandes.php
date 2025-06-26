<?php
session_start();
if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: login_fournisseur.php');
    exit();
}

require_once 'includes/connexion.php';

$fournisseur_id = $_SESSION['fournisseur_id'];

// Requête pour récupérer les commandes liées aux produits du fournisseur
$sql = "
SELECT c.id AS commande_id, c.date_commande, c.total, c.statut,
       u.nom AS client_nom,
       cd.produit_id, cd.quantite, cd.prix_unitaire,
       p.nom AS produit_nom, p.image
FROM commandes c
JOIN commande_details cd ON c.id = cd.commande_id
JOIN produits p ON cd.produit_id = p.id
JOIN users u ON c.user_id = u.id
WHERE p.fournisseur_id = ?
ORDER BY c.date_commande DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$fournisseur_id]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes Reçues</title>
    <link rel="stylesheet" href="fournisseurs_commandes.css">
    <style>
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
        }
        .back-button button {
            padding: 8px 15px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        .back-button button:hover {
            background-color: #2980b9;
        }
        /* Pour éviter que le contenu soit sous le bouton */
        .overlay {
            padding-top: 70px;
        }
    </style>
</head>
<body>
<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<div class="back-button">
    <button onclick="window.history.back()">🔙 Retour</button>
</div>

<div class="overlay">
    <header>
        <h1>📦 Commandes Reçues</h1>
        <p>Voici toutes les commandes contenant vos produits</p>
    </header>

    <?php if (count($commandes) === 0): ?>
        <p class="no-orders">Aucune commande trouvée.</p>
    <?php else: ?>
        <div class="orders-container">
            <?php
            $currentCommande = null;
            foreach ($commandes as $cmd):
                if ($cmd['commande_id'] !== $currentCommande):
                    if ($currentCommande !== null) echo '</table></div>'; // Fin table précédente
                    $currentCommande = $cmd['commande_id'];
            ?>
                <div class="commande">
                    <h3>🧾 Commande #<?= $cmd['commande_id'] ?> - <?= htmlspecialchars($cmd['client_nom']) ?> - <?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></h3>
                    <p><strong>Statut :</strong> <?= ucfirst(htmlspecialchars($cmd['statut'])) ?></p>
                    <table>
                        <tr>
                            <th>Image</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
            <?php endif; ?>
                        <tr>
                            <td><img src="assets/images/<?= htmlspecialchars($cmd['image']) ?>" alt="img" width="60"></td>
                            <td><?= htmlspecialchars($cmd['produit_nom']) ?></td>
                            <td><?= $cmd['quantite'] ?></td>
                            <td><?= number_format($cmd['prix_unitaire'], 2, ',', ' ') ?> €</td>
                            <td><?= number_format($cmd['quantite'] * $cmd['prix_unitaire'], 2, ',', ' ') ?> €</td>
                        </tr>
            <?php endforeach; echo '</table></div>'; ?>
        </div>
    <?php endif; ?>

    <div class="logout">
        <a href="logout_fournisseur.php">Déconnexion</a>
    </div>
</div>
</body>
</html>
