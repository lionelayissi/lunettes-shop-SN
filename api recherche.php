<?php
require_once 'includes/connexion.php';

$texte = strtolower($_GET['texte'] ?? '');
$resultats = [];

if ($texte !== '') {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE nom LIKE ? OR categorie LIKE ?");
    $stmt->execute(["%$texte%", "%$texte%"]);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($resultats);
