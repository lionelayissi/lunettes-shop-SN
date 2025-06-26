<?php
session_start();
require_once 'includes/connexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Commande invalide.");
}

$commande_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'] ?? 0;

// Récupérer la commande si elle appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ? AND user_id = ?");
$stmt->execute([$commande_id, $user_id]);
$commande = $stmt->fetch();

if (!$commande) {
    die("Commande introuvable ou accès refusé.");
}

// Récupérer les détails de la commande (produits)
$stmt = $pdo->prepare("
    SELECT p.nom, cd.quantite, cd.prix_unitaire 
    FROM commande_details cd
    JOIN produits p ON cd.produit_id = p.id
    WHERE cd.commande_id = ?
");
$stmt->execute([$commande_id]);
$details = $stmt->fetchAll();

function formatFCFA($prix) {
    return number_format($prix, 0, ',', ' ') . " FCFA";
}

// ✅ Calcul du total général AVANT le XML
$totalGeneral = 0;
foreach ($details as $d) {
    $totalGeneral += $d['quantite'] * $d['prix_unitaire'];
}

$commande_id = $commande['id'];
$nom_client = htmlspecialchars($_SESSION['user_nom'] ?? 'Client');
$date = $commande['date_commande'];
$statut = $commande['statut'];
$totalGeneral = 0;

// 1. Crée un XML de base avec lien vers le fichier XSL
$xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xmlContent .= '<?xml-stylesheet type="text/xsl" href="facture_style.xsl"?>' . "\n";
$xmlContent .= "<facture></facture>";

$xml = new SimpleXMLElement($xmlContent);

// 2. Ajoute les données principales
$xml->addChild('numero', $commande_id);
$xml->addChild('date', $date);
$xml->addChild('client', $nom_client);
$xml->addChild('statut', $statut);

// 3. Section produits
$produitsNode = $xml->addChild('produits');

foreach ($details as $d) {
    $produit = $produitsNode->addChild('produit');
    $produit->addChild('nom', htmlspecialchars($d['nom']));
    $produit->addChild('quantite', $d['quantite']);
    $produit->addChild('prix_unitaire', number_format($d['prix_unitaire'], 2, '.', ''));
    $total = $d['quantite'] * $d['prix_unitaire'];
    $produit->addChild('total', $total);
    $totalGeneral += $total;
}

// 4. Total général
$xml->addChild('total', $totalGeneral);

// 5. Enregistre dans exports/
$chemin = "exports/facture_$commande_id.xml";
$xml->asXML($chemin);

?>


<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Facture N° <?= htmlspecialchars($commande['id']) ?></title>
<link rel="stylesheet" href="facture.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    
<div class="background-wrapper">
  <video autoplay loop muted playsinline class="background-video">
    <source src="assets/videos/optic-background.mp4" type="video/mp4">
  </video>
  <div class="background-overlay"></div>
</div>


<div class="invoice-container">

    <header class="invoice-header">
        <h1>Shabs Optique 237</h1>
        <?php
// Exemple minimal pour l'image en base64
$imagePath = __DIR__ . '/assets/images/logo.jpg';

if (!file_exists($imagePath)) {
    die("Image logo introuvable !");
}

$type = pathinfo($imagePath, PATHINFO_EXTENSION);
$data = file_get_contents($imagePath);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>

<div class="logo">
  <img src="<?= $base64 ?>" alt="Logo Shabs Optique" style="max-width: 150px; height: auto;" />
</div>

    </header>

    <section class="invoice-info">
        <p><strong>Nom client :</strong> <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Client') ?></p>
        <p><strong>N° Facture :</strong> <?= htmlspecialchars($commande['id']) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
        <p><strong>Statut :</strong> <?= htmlspecialchars(ucfirst($commande['statut'])) ?></p>
    </section>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalGeneral = 0;
            foreach ($details as $d):
                $totalLigne = $d['quantite'] * $d['prix_unitaire'];
                $totalGeneral += $totalLigne;
            ?>
            <tr>
                <td><?= htmlspecialchars($d['nom']) ?></td>
                <td><?= (int)$d['quantite'] ?></td>
                <td><?= formatFCFA($d['prix_unitaire']) ?></td>
                <td><?= formatFCFA($totalLigne) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="invoice-total">
        Total : <strong><?= formatFCFA($totalGeneral) ?></strong>
    </div>

    <div class="invoice-thanks">
        Merci <?= htmlspecialchars($_SESSION['user_nom'] ?? 'cher client') ?> pour votre commande chez Shabs optique 237.<br/>
        Nous vous remercions pour la confiance. Votre confort en vue est notre priorité.
    </div>

    <button id="downloadPdfBtn" class="btn-pdf">Télécharger la facture PDF</button>
    <a href="exports/facture_<?= $commande_id ?>.xml" class="btn-xml" download>Télécharger la facture XML</a>
</div>

<script>
document.getElementById('downloadPdfBtn').addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(20);
    doc.setTextColor("#0033cc");
    doc.text("Shabs Optique 237", 105, 20, null, null, "center");

    doc.setFontSize(12);
    doc.setTextColor(0);
    doc.text("Nom client : <?= addslashes(htmlspecialchars($_SESSION['user_nom'] ?? 'Client')) ?>", 14, 35);
    doc.text("N° Facture : <?= $commande['id'] ?>", 14, 43);
    doc.text("Date : <?= $commande['date_commande'] ?>", 14, 51);
    doc.text("Statut : <?= ucfirst(htmlspecialchars($commande['statut'])) ?>", 14, 59);

    // Table headers
    doc.setFillColor(230,230,230);
    doc.rect(14, 65, 182, 10, 'F');
    doc.setTextColor(0);
    doc.text("Produit", 16, 72);
    doc.text("Quantité", 90, 72);
    doc.text("Prix Unitaire", 130, 72);
    doc.text("Total", 170, 72);

    let y = 80;
    <?php foreach ($details as $d): 
        $totalLigne = $d['quantite'] * $d['prix_unitaire'];
        $produit = addslashes(htmlspecialchars($d['nom']));
        $quantite = (int)$d['quantite'];
        $pu = number_format($d['prix_unitaire'], 0, ',', ' ') . " FCFA";
        $totalLigneF = number_format($totalLigne, 0, ',', ' ') . " FCFA";
    ?>
    doc.text("<?= $produit ?>", 16, y);
    doc.text("<?= $quantite ?>", 90, y);
    doc.text("<?= $pu ?>", 130, y);
    doc.text("<?= $totalLigneF ?>", 170, y);
    y += 8;
    <?php endforeach; ?>

    doc.setFontSize(14);
    doc.text("Total : <?= number_format($totalGeneral, 0, ',', ' ') ?> FCFA", 150, y + 10);

    doc.setFontSize(12);
    doc.text("Merci <?= addslashes(htmlspecialchars($_SESSION['user_nom'] ?? 'cher client')) ?> pour votre commande chez Shabs optique 237.", 14, y + 25);
    doc.text("Nous vous remercions pour la confiance. Votre confort en vue est notre priorité.", 14, y + 33);

    // Logo (optionnel, charger une image si tu veux)
    // doc.addImage(imageData, 'PNG', 160, 5, 40, 20);

    doc.save("facture_<?= $commande['id'] ?>.pdf");
});
</script>

</body>
</html>

