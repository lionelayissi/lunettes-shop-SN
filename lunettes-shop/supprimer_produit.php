<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: fournisseur_produits.php');
    exit();
}

$produit_id = intval($_GET['id']);

// Supprimer uniquement si appartient au fournisseur
$stmt = $pdo->prepare("DELETE FROM produits WHERE id = ? AND fournisseur_id = ?");
$stmt->execute([$produit_id, $fournisseur_id]);

header('Location: fournisseur_produits.php');
exit();
