<?php
require_once 'includes/connexion.php';

$where = [];
$params = [];

if (!empty($_GET['user_id'])) {
    $where[] = "(u.id = ? OR u.nom LIKE ?)";
    $params[] = $_GET['user_id'];
    $params[] = "%" . $_GET['user_id'] . "%";
}
if (!empty($_GET['date'])) {
    $where[] = "DATE(c.date_commande) = ?";
    $params[] = $_GET['date'];
}
if (!empty($_GET['prix_min'])) {
    $where[] = "c.total >= ?";
    $params[] = $_GET['prix_min'];
}
if (!empty($_GET['prix_max'])) {
    $where[] = "c.total <= ?";
    $params[] = $_GET['prix_max'];
}

$filterSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT c.id AS commande_id, c.date_commande, c.total, c.statut, 
           u.nom AS utilisateur
    FROM commandes c
    JOIN users u ON u.id = c.user_id
    $filterSQL
    ORDER BY c.date_commande DESC
");
$stmt->execute($params);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des commandes</title>
    <style>
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
        }
        .back-button button {
            padding: 8px 15px;
            background-color: #2ecc71;
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        .back-button button:hover {
            background-color: #27ae60;
        }
        .commande-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 70px 20px 20px 20px; /* margin-top espace pour bouton */
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 7px 12px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .statut {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            color: white;
        }
        .statut.confirmée {
            background-color: #27ae60;
        }
        .statut.en_attente {
            background-color: #f39c12;
        }
        .statut.annulée {
            background-color: #c0392b;
        }
        .empty-msg {
            margin-top: 70px;
            text-align: center;
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>

<div class="back-button">
    <button onclick="window.history.back()">🔙 Retour</button>
</div>

<?php
if (empty($commandes)) {
    echo '<p class="empty-msg">Aucune commande trouvée.</p>';
} else {
    foreach ($commandes as $commande) {
        echo '
        <div class="commande-card">
            <h3>' . htmlspecialchars($commande['utilisateur']) . '</h3>
            <p>Facture #' . $commande['commande_id'] . ' <span>' . $commande['date_commande'] . '</span></p>
            <p>Total : <strong>' . number_format($commande['total'], 0, ',', ' ') . ' FCFA</strong></p>
            <p>Statut : <span class="statut ' . strtolower(str_replace(' ', '_', $commande['statut'])) . '">' . htmlspecialchars($commande['statut']) . '</span></p>
            <a href="facture.php?id=' . $commande['commande_id'] . '" class="btn">Voir la facture</a>
        </div>';
    }
}
?>

</body>
</html>
