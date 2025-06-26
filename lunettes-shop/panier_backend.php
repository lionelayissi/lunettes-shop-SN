<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/connexion.php';

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $id = $_POST['id'] ?? null;
    $quantite = intval($_POST['quantite'] ?? 1);

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID produit manquant']);
        exit;
    }

    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id] += $quantite;
    } else {
        $_SESSION['panier'][$id] = $quantite;
    }

    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $quantite = intval($_POST['quantite'] ?? 1);

    if (!$id || $quantite < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
        exit;
    }

    $_SESSION['panier'][$id] = $quantite;
    echo json_encode(['status' => 'success']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Action inconnue']);
