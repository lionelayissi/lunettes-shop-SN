<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $id = (int) $_POST['user_id'];

    // Supprimer les commandes associées
    $pdo->prepare("DELETE cd FROM commande_details cd 
                   INNER JOIN commandes c ON c.id = cd.commande_id 
                   WHERE c.user_id = ?")->execute([$id]);

    $pdo->prepare("DELETE FROM commandes WHERE user_id = ?")->execute([$id]);

    // Supprimer l'utilisateur
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
}

header("Location: admin_users.php");
exit;
