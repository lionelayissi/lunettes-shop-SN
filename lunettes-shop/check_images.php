<?php
// check_images.php
$host = "localhost";
$dbname = "lunettes_db";
$user = "root";
$pass = "";

$chemin_images = "images/";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, nom, image FROM produits");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Vérification des images dans le dossier <code>/images/</code></h2>";
    echo "<ul>";

    foreach ($produits as $p) {
        $image_path = $chemin_images . $p['image'];
        if (file_exists($image_path)) {
            echo "<li style='color:green'>✔️ <strong>{$p['nom']}</strong> → <code>{$p['image']}</code> existe ✅</li>";
        } else {
            echo "<li style='color:red'>❌ <strong>{$p['nom']}</strong> → <code>{$p['image']}</code> est introuvable ❗</li>";
        }
    }

    echo "</ul>";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
