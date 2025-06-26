<?php
session_start();
require_once 'includes/connexion.php';

$id = $_POST['id'] ?? null;
$quantite = $_POST['quantite'] ?? 1;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID manquant']);
    exit;
}

$id = intval($id);
$quantite = intval($quantite);

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

if (isset($_SESSION['panier'][$id])) {
    $_SESSION['panier'][$id] += $quantite;
} else {
    $_SESSION['panier'][$id] = $quantite;
}

echo json_encode(['status' => 'success']);
exit;
?>

