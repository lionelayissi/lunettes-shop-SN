<?php 
// includes/get_produits.php
header('Content-Type: application/json');

// Connexion à la base de données
$host = "localhost";
$dbname = "lunettes_db";
$user = "root";
$pass = ""; // modifie si besoin

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Nouvelle requête avec jointure pour récupérer le nom de la catégorie
    $sql = "SELECT p.id, p.nom, p.marque, p.description, p.prix, p.image, p.stock, 
                   c.nom AS categorie 
            FROM produits p 
            JOIN categories c ON p.categorie_id = c.id";

    $stmt = $pdo->query($sql);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($produits);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>