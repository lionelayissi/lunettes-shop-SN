<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: max-age=3600');

$host = 'localhost';
$db   = 'lunettes_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    if (empty($query)) {
        echo json_encode([]) ;
        exit;
    }

    // Requête optimisée pour les suggestions
    $sql = "SELECT DISTINCT nom, id, image 
            FROM produits 
            WHERE nom LIKE :query 
            ORDER BY CHAR_LENGTH(nom) ASC
            LIMIT 8";
    
    $stmt = $pdo->prepare($sql);
    $searchTerm = $query . "%"; // Recherche par préfixe pour les suggestions
    $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll();

    echo json_encode($results);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données']);
}
?>