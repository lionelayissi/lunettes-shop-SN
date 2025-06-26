<?php
// Connexion BDD (à adapter)
$pdo = new PDO('mysql:host=localhost;dbname=lunettes_db', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);


// Récupérer tous les utilisateurs (exemple simple)
$stmt = $pdo->query("SELECT id, nom, email, date_inscription FROM users ORDER BY date_inscription DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer utilisateurs + total achats (0 si pas d'achat)
$stmt = $pdo->query("
    SELECT u.id, u.nom, u.email, u.date_inscription, 
           COALESCE(SUM(c.total), 0) AS total_achats
    FROM users u
    LEFT JOIN commandes c ON u.id = c.user_id
    GROUP BY u.id
    ORDER BY u.date_inscription DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion Utilisateurs - Admin</title>
    <link rel="stylesheet" href="admin_users.css" />
</head>
<body>

<video autoplay muted loop id="video-bg" playsinline>
    <source src="assets/videos/video5.mp4" type="video/mp4" />
    Votre navigateur ne supporte pas la vidéo HTML5.
</video>

<div class="container">
    <h1>Gestion des Utilisateurs</h1>

    <input type="text" id="searchInput" placeholder="Rechercher un utilisateur..." autofocus autocomplete="off" />

    <table id="usersTable" aria-label="Tableau des utilisateurs">
        <thead>
            <tr>
                <th data-sort="nom" class="sortable">Nom</th>
                <th>Email</th>
                <th data-sort="date" class="sortable">Date inscription &#x25B2;&#x25BC;</th>
                <th>Total Achats (FCFA)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr data-userid="<?= htmlspecialchars($user['id']) ?>">
                <td class="user-name" tabindex="0" role="button"><?= htmlspecialchars($user['nom']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                <td><?= number_format($user['total_achats'], 2, ',', ' ') ?></td>
                <td>
                    <form method="POST" action="delete_user.php" onsubmit="return confirm('Supprimer cet utilisateur ?');" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>" />
                        <button type="submit" aria-label="Supprimer <?= htmlspecialchars($user['nom']) ?>">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal détail des achats -->
<div id="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc" hidden>
    <div class="modal-content">
        <button id="modalClose" aria-label="Fermer la fenêtre modale">&times;</button>
        <h2 id="modalTitle">Achats de l'utilisateur</h2>
        <div id="modalBody">
            <p>Chargement...</p>
        </div>
    </div>
</div>

<script src="admin_users.js"></script>
</body>
</html>
