<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

require_once 'includes/connexion.php';

// Ajouter une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
    $stmt->execute([$nom, $description]);
    header("Location: categories.php");
    exit();
}

// Supprimer une catégorie
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
    header("Location: categories.php");
    exit();
}

// Récupération de la catégorie à modifier
$categorie_edition = null;
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $categorie_edition = $stmt->fetch();
}

// Modifier la catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_categorie'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE categories SET nom = ?, description = ? WHERE id = ?");
    $stmt->execute([$nom, $description, $id]);
    header("Location: categories.php");
    exit();
}

// Liste des catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Catégories</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
        }
        body, html {
            margin: 0; padding: 0; height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #111;
            color: #eee;
            overflow-x: hidden;
        }
        #background-video {
            position: fixed;
            right: 0; bottom: 0;
            min-width: 100%; min-height: 100%;
            z-index: -1;
            object-fit: cover;
            filter: brightness(0.5);
        }
        .overlay {
            position: relative;
            max-width: 900px;
            margin: 6vh auto 4vh;
            background-color: rgba(0, 0, 0, 0.75);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 30px #000;
        }
        .contenu h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        thead tr {
            background-color: #3f51b5;
            color: #fff;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #444;
            text-align: left;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        a.btn-supprimer,
        a.btn-modifier {
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: #eee;
            font-weight: 600;
            transition: background-color 0.3s ease;
            user-select: none;
            display: inline-block;
            font-size: 1.1rem;
        }
        a.btn-supprimer {
            background-color: #b00020;
        }
        a.btn-supprimer:hover {
            background-color: #7a0012;
        }
        a.btn-modifier {
            background-color: #3f51b5;
            margin-left: 10px;
        }
        a.btn-modifier:hover {
            background-color: #303f9f;
        }
        .form-ajout form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 480px;
            margin: 0 auto;
        }
        .form-ajout h3 {
            margin-bottom: 10px;
            font-weight: 700;
            font-size: 1.3rem;
            text-align: center;
            letter-spacing: 0.5px;
        }
        input[type="text"],
        textarea {
            padding: 10px 12px;
            border-radius: 6px;
            border: none;
            font-size: 1rem;
            resize: vertical;
            outline: none;
        }
        input[type="text"]:focus,
        textarea:focus {
            box-shadow: 0 0 6px #3f51b5;
        }
        button[type="submit"] {
            padding: 12px 0;
            border: none;
            border-radius: 6px;
            background-color: #3f51b5;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #303f9f;
        }
        a[style] {
            color: #bbb;
            font-size: 0.9rem;
            user-select: none;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.1);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a[style]:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        button.retour {
            display: block;
            margin: 20px auto 0;
            background: none;
            border: none;
            color: #bbb;
            font-size: 1rem;
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        button.retour:hover {
            color: #fff;
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .overlay {
                margin: 3vh 15px 3vh;
                padding: 25px 20px;
            }
            table, thead tr, tbody tr, th, td {
                font-size: 0.85rem;
            }
            .form-ajout form {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<video autoplay muted loop id="background-video" playsinline>
    <source src="assets/videos/video5.mp4" type="video/mp4">
    Votre navigateur ne prend pas en charge les vidéos HTML5.
</video>

<div class="overlay">
    <div class="contenu">
        <h2>📂 Gérer les Catégories</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['nom']) ?></td>
                        <td><?= htmlspecialchars($cat['description']) ?></td>
                        <td>
                            <a class="btn-supprimer" href="?supprimer=<?= $cat['id'] ?>" onclick="return confirm('Supprimer cette catégorie ?')">🗑️</a>
                            <a class="btn-modifier" href="?modifier=<?= $cat['id'] ?>">✏️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="form-ajout">
            <form method="post">
                <?php if ($categorie_edition): ?>
                    <h3>✏️ Modifier la catégorie #<?= $categorie_edition['id'] ?></h3>
                    <input type="hidden" name="id" value="<?= $categorie_edition['id'] ?>">
                    <input type="text" name="nom" placeholder="Nom" value="<?= htmlspecialchars($categorie_edition['nom']) ?>" required>
                    <textarea name="description" placeholder="Description" rows="3"><?= htmlspecialchars($categorie_edition['description']) ?></textarea>
                    <button type="submit" name="modifier_categorie">Enregistrer</button>
                    <a href="categories.php" style="margin-left: 10px;">Annuler</a>
                <?php else: ?>
                    <h3>➕ Ajouter une catégorie</h3>
                    <input type="text" name="nom" placeholder="Nom" required>
                    <textarea name="description" placeholder="Description" rows="3"></textarea>
                    <button type="submit" name="ajouter">Ajouter</button>
                <?php endif; ?>
            </form>
        </div>

        <button type="button" class="retour" onclick="history.back()">← Retour</button>

    </div>
</div>

</body>
</html>
