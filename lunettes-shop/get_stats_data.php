<?php
header('Content-Type: application/json');
require_once 'includes/connexion.php'; // Assure-toi que ce fichier définit bien $host, $root, $pass, $lunettes_db

$response = [
    'ca_par_mois' => ['labels' => [], 'values' => []],
    'commandes_par_statut' => ['labels' => [], 'values' => []],
    'top_produits' => ['labels' => [], 'values' => []],
    'top_fournisseurs' => ['labels' => [], 'values' => []]
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$lunettes_db;charset=utf8", $root, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 📈 1. CA par mois (commandes confirmées)
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(date_commande, '%Y-%m') AS mois, SUM(total) AS ca
        FROM commandes
        WHERE statut = 'confirmée'
        GROUP BY mois
        ORDER BY mois ASC
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['ca_par_mois']['labels'][] = $row['mois'];
        $response['ca_par_mois']['values'][] = (float) $row['ca'];
    }

    // 🥧 2. Commandes par statut
    $stmt = $pdo->query("
        SELECT statut, COUNT(*) AS total
        FROM commandes
        GROUP BY statut
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['commandes_par_statut']['labels'][] = ucfirst($row['statut']);
        $response['commandes_par_statut']['values'][] = (int) $row['total'];
    }

    // 📊 3. Top produits (par quantités vendues)
    $stmt = $pdo->query("
        SELECT p.nom, SUM(cd.quantite) AS quantite_vendue
        FROM commande_details cd
        JOIN produits p ON cd.produit_id = p.id
        GROUP BY cd.produit_id
        ORDER BY quantite_vendue DESC
        LIMIT 5
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['top_produits']['labels'][] = $row['nom'];
        $response['top_produits']['values'][] = (int) $row['quantite_vendue'];
    }

    // 🏆 4. Top fournisseurs (par CA)
    $stmt = $pdo->query("
        SELECT f.nom AS fournisseur, SUM(cd.quantite * cd.prix_unitaire) AS total_vente
        FROM commande_details cd
        JOIN produits p ON cd.produit_id = p.id
        JOIN fournisseurs f ON p.fournisseur_id = f.id
        JOIN commandes c ON cd.commande_id = c.id
        WHERE c.statut = 'confirmée'
        GROUP BY f.id
        ORDER BY total_vente DESC
        LIMIT 5
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['top_fournisseurs']['labels'][] = $row['fournisseur'];
        $response['top_fournisseurs']['values'][] = (float) $row['total_vente'];
    }

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
