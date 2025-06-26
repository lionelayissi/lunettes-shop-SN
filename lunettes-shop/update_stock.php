<!-- fournisseur_stock_alert.php -->
<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

// Ton code pour afficher le stock ou les alertes...

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Alertes Stock Fournisseur</title>
    <style>
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
            padding-top: 50px;
        }
    </style>
</head>
<body>

<a href="javascript:history.back()" class="btn-retour">← Retour</a>

<!-- Le reste de ta page fournisseur_stock_alert.php -->

</body>
</html>
