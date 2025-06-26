<?php
require_once 'config.php';

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT nom, email FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

$commandes = $pdo->prepare("SELECT * FROM commandes WHERE user_id = ?");
$commandes->execute([$id]);
$all = $commandes->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de l'utilisateur</title>
    <link rel="stylesheet" href="admin_users.css">
    <style>
        /* Bouton retour en haut à gauche */
        .btn-retour {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }
        .btn-retour:hover {
            background-color: #0056b3;
        }
        body {
            padding-top: 50px; /* Pour éviter que le bouton chevauche le contenu */
        }
    </style>
</head>
<body>
    <!-- Bouton Retour -->
    <a href="javascript:history.back()" class="btn-retour">← Retour</a>

    <div class="container">
        <h2>Détails de <?= htmlspecialchars($user['nom']) ?></h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>

        <h3>Commandes</h3>
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Date</th>
                    <th>Montant (€)</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?></td>
                        <td><?= number_format($c['total'], 2, ',', ' ') ?> €</td>
                        <td><?= htmlspecialchars($c['statut']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</body>
</html>
