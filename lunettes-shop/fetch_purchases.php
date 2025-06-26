<?php
header('Content-Type: application/json');
if (!isset($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = (int)$_GET['user_id'];

$pdo = new PDO('mysql:host=localhost;dbname=ton_db', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Requête pour récupérer les achats de l’utilisateur (produit, qté, prix)
$sql = "SELECT p.nom AS produit_nom, cd.quantite, cd.prix_unitaire
        FROM commandes c
        JOIN commande_details cd ON c.id = cd.commande_id
        JOIN produits p ON cd.produit_id = p.id
        WHERE c.user_id = :user_id AND c.statut = 'confirmée'";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$achats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($achats);
