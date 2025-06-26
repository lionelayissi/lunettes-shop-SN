<?php
session_start();
require_once 'includes/connexion.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login_user.php");
    exit;
}

// Récupération du nom d'utilisateur
$user_stmt = $pdo->prepare("SELECT nom FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);
$nom_utilisateur = $user['nom'] ?? 'Utilisateur';

// Récupérer les commandes
$stmt = $pdo->prepare("
    SELECT c.id AS commande_id, c.date_commande, c.total, c.statut,
           p.nom, p.prix, d.quantite
    FROM commandes c
    JOIN commande_details d ON c.id = d.commande_id
    JOIN produits p ON d.produit_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.date_commande DESC
");
$stmt->execute([$user_id]);
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$commandes = [];
foreach ($resultats as $row) {
    $id = $row['commande_id'];
    if (!isset($commandes[$id])) {
        $commandes[$id] = [
            'date' => $row['date_commande'],
            'statut' => $row['statut'],
            'total' => $row['total'],
            'produits' => []
        ];
    }
    $commandes[$id]['produits'][] = [
        'nom' => $row['nom'],
        'prix' => $row['prix'],
        'quantite' => $row['quantite']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="assets/css/commandes.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
<div class="container">
    <h1 class="titre-principal">MES COMMANDES</h1>

    <?php if (empty($commandes)): ?>
        <p>Vous n'avez passé aucune commande.</p>
    <?php else: ?>
        <?php foreach ($commandes as $id => $commande): ?>
            <div class="facture" id="facture-<?= $id ?>">
                <div class="en-tete-facture">
                    <div class="info-client">
                        <p><strong><?= htmlspecialchars($nom_utilisateur) ?></strong></p>
                        <p><strong>N° Facture :</strong> <?= $id ?></p>
                        <p><strong>Date :</strong> <?= $commande['date'] ?></p>
                        <p><strong>Statut :</strong> <?= htmlspecialchars($commande['statut']) ?></p>
                    </div>
                    <div class="titre-centre">
                        <h2>Shabs Optique 237</h2>
                      </div>

                    <div class="logo-container">
                        <img src="assets/images/logo.jpg" alt="Logo" class="logo">
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commande['produits'] as $produit): ?>
                            <tr>
                                <td><?= htmlspecialchars($produit['nom']) ?></td>
                                <td><?= $produit['quantite'] ?></td>
                                <td><?= number_format($produit['prix'], 0, ',', ' ') ?> FCFA</td>
                                <td><?= number_format($produit['quantite'] * $produit['prix'], 0, ',', ' ') ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total">
                    <strong>Total :</strong> <?= number_format($commande['total'], 0, ',', ' ') ?> FCFA
                </div>

              <?php if ($commande['statut'] === 'en attente'): ?>
                   <button class="btn-valider" onclick="confirmerCommande(<?= $id ?>)">Valider l'achat</button>
                <?php else: ?>
                    <span class="statut-confirmé">✅ Commande confirmée</span>
                <?php endif; ?>

                <button onclick="genererPDF(<?= $id ?>)">📥 Télécharger PDF</button>
                <button onclick="imprimerFacture('facture-<?= $id ?>')">🖨️ Imprimer</button>
                <p class="merci">Merci <?= htmlspecialchars($nom_utilisateur) ?> pour votre commande chez Shabs optique 237.<br>Nous vous remercions pour la confiance. Votre confort en vue est notre priorité.</p>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="commande.js"></script>
</body>
</html>
