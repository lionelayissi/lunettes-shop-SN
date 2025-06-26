<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_SESSION['fournisseur_id'])) {
    header('Location: fournisseur_login.php');
    exit();
}

$fournisseur_id = $_SESSION['fournisseur_id'];
$fournisseur_nom = $_SESSION['fournisseur_nom'];

// Récupérer les ventes groupées par produit
$stmt = $pdo->prepare("
    SELECT p.nom AS produit, SUM(cd.quantite) AS total_vendus
    FROM commande_details cd
    JOIN produits p ON cd.produit_id = p.id
    JOIN commandes c ON cd.commande_id = c.id
    WHERE p.fournisseur_id = ? AND c.statut = 'confirmée'
    GROUP BY p.nom
");
$stmt->execute([$fournisseur_id]);
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Transformer les données pour Chart.js
$labels = array_column($ventes, 'produit');
$quantites = array_column($ventes, 'total_vendus');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques de ventes</title>
    <link rel="stylesheet" href="fournisseur_graph.css">
    <style>
.back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 10;
}

.back-button button {
    padding: 8px 15px;
    background-color: #2ecc71;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
}

.back-button button:hover {
    background-color: #27ae60;
}
</style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <div class="back-button">
    <button onclick="window.history.back()">🔙 Retour</button>
</div>
<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
</video>

<div class="overlay">
    <header>
        <h1>📊 Statistiques de <?= htmlspecialchars($fournisseur_nom) ?></h1>
        <p>Visualisez vos performances de vente</p>
    </header>

    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>

    <div class="buttons">
        <button onclick="exportPDF()">📄 Télécharger PDF</button>
        <button onclick="exportExcel()">📊 Exporter Excel</button>
    </div>
</div>

<script>
const labels = <?= json_encode($labels) ?>;
const quantites = <?= json_encode($quantites) ?>;

const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Produits vendus',
            data: quantites,
            backgroundColor: '#3498db',
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Ventes par produit'
            }
        }
    }
});

// Export PDF
function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Statistiques de ventes - <?= addslashes($fournisseur_nom) ?>", 10, 10);
    labels.forEach((label, i) => {
        doc.text(`${label} : ${quantites[i]} ventes`, 10, 20 + i * 10);
    });

    doc.save("statistiques_ventes.pdf");
}

// Export Excel
function exportExcel() {
    const data = [["Produit", "Quantité"]];
    labels.forEach((label, i) => {
        data.push([label, quantites[i]]);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Statistiques");

    XLSX.writeFile(wb, "statistiques_ventes.xlsx");
}
</script>

</body>
</html>
