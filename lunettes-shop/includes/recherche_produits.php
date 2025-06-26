<?php
require_once 'connexion.php';

$where = [];
$params = [];

// Nom (recherche partielle)
if (!empty($_GET['nom'])) {
    $where[] = "nom LIKE ?";
    $params[] = "%" . $_GET['nom'] . "%";
}

// Marque (partielle)
if (!empty($_GET['marque'])) {
    $where[] = "marque LIKE ?";
    $params[] = "%" . $_GET['marque'] . "%";
}

// Prix min
if (!empty($_GET['prix_min'])) {
    $where[] = "prix >= ?";
    $params[] = $_GET['prix_min'];
}

// Prix max
if (!empty($_GET['prix_max'])) {
    $where[] = "prix <= ?";
    $params[] = $_GET['prix_max'];
}

// Catégorie
if (!empty($_GET['categorie'])) {
    $where[] = "categorie = ?";
    $params[] = $_GET['categorie'];
}

$sql = "SELECT * FROM produits";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retour JSON
header('Content-Type: application/json');
echo json_encode($produits);
