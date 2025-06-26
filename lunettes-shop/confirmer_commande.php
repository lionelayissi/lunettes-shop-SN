<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idCommande = intval($_POST['id']);

    // Récupérer les produits associés à la commande
    $sqlProduits = $pdo->prepare("SELECT produit_id, quantite FROM commandes_produits WHERE commande_id = ?");
    $sqlProduits->execute([$idCommande]);
    $produits = $sqlProduits->fetchAll();

    // Réduire le stock pour chaque produit
    foreach ($produits as $p) {
        $updateStock = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
        $updateStock->execute([$p['quantite'], $p['produit_id']]);
    }

    // Changer le statut de la commande
    $updateCommande = $pdo->prepare("UPDATE commandes SET statut = 'confirmée' WHERE id = ?");
    if ($updateCommande->execute([$idCommande])) {
        echo "Commande confirmée avec succès.";
    } else {
        echo "Erreur lors de la confirmation.";
    }
}



