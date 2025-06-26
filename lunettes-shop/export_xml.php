<?php
header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=export_stats.xml");

require_once 'includes/connexion.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$lunettes_db;charset=utf8", $root, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<export>\n";

    // Résumé
    $stmt = $pdo->query("SELECT COUNT(*) AS nb_commandes, SUM(total) AS ca_total FROM commandes WHERE statut='confirmée'");
    $resume = $stmt->fetch(PDO::FETCH_ASSOC);

    $ca_total = htmlspecialchars($resume['ca_total']);
    $nb_commandes = htmlspecialchars($resume['nb_commandes']);

    echo "<resume>\n";
    echo "<ca_total>$ca_total</ca_total>\n";
    echo "<nb_commandes>$nb_commandes</nb_commandes>\n";
    echo "</resume>\n";

    // À compléter...

    echo "</export>";

} catch (PDOException $e) {
    // En cas d’erreur, renvoyer un XML avec message d’erreur
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<error>" . htmlspecialchars($e->getMessage()) . "</error>";
    exit;
}
