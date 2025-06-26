<?php
require_once 'includes/connexion.php';

$nom = "admin";
$email = "admin@example.com";
$password = "monmotdepasse";

// Hasher le mot de passe sans afficher le hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insertion dans la base
$stmt = $pdo->prepare("INSERT INTO admins (nom, email, password) VALUES (?, ?, ?)");
$stmt->execute([$nom, $email, $hash]);

// Redirection ou message de confirmation (facultatif)
header("Location: admin_login.php");  // ou message silencieux
exit;
?>
