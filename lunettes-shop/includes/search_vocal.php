<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');

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
        echo json_encode(['error' => 'Requête vide']);
        exit;
    }

    // Nettoyer et découper la recherche en mots
    $words = preg_split('/\s+/', strtolower($query));
    $where = [];
    $params = [];

    foreach ($words as $word) {
        if (strlen($word) > 2) { // ignorer les petits mots
            $where[] = "(nom LIKE ? OR marque LIKE ? OR description LIKE ? OR categorie LIKE ?)";
            for ($i = 0; $i < 4; $i++) {
                $params[] = "%$word%";
            }
        }
    }

    if (count($where) === 0) {
        echo json_encode([]);
        exit;
    }

    $sql = "SELECT id, nom, marque, description, prix, categorie FROM produits WHERE " . implode(" AND ", $where) . " LIMIT 50";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    // Convertir prix en float
    foreach ($results as &$result) {
        $result['prix'] = (float)$result['prix'];
    }

    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}
