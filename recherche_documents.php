<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Inclusion des ressources communes pour la connexion MySQL
require_once 'ressources_communes.php';

// Connexion à la base de données
$conn = connectDatabase();

// Récupérer les critères de recherche
$type = isset($_GET['type']) ? $_GET['type'] : '';
$nature = isset($_GET['nature']) ? $_GET['nature'] : '';
$contenu = isset($_GET['contenu']) ? $_GET['contenu'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Construction de la requête SQL
$sql = "SELECT * FROM documents WHERE 1=1";
$params = [];
$paramTypes = '';

if (!empty($type)) {
    $sql .= " AND type = ?";
    $params[] = $type;
    $paramTypes .= 's';
}

if (!empty($nature)) {
    $sql .= " AND nature_fichier = ?";
    $params[] = $nature;
    $paramTypes .= 's';
}

if (!empty($contenu)) {
    $sql .= " AND contenu LIKE ?";
    $params[] = "%$contenu%";
    $paramTypes .= 's';
}

if (!empty($date)) {
    $sql .= " AND date_document = ?";
    $params[] = $date;
    $paramTypes .= 's';
}

// Exécuter la requête
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$documents = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Documents</title>
    <link rel="stylesheet" href="./css/recherche_documents.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Recherche de Documents</h1>
            <!-- Bouton de retour -->
            <a href="choix.php" class="return-btn">Retour</a>
        </header>
        <!-- Formulaire de recherche -->
        <form method="GET" action="recherche_documents.php">
            <label for="type">Type de document :</label>
            <select name="type" id="type">
                <option value="">Tous</option>
                <option value="ordonnance" <?= $type === 'ordonnance' ? 'selected' : '' ?>>Ordonnance</option>
                <option value="prescription" <?= $type === 'prescription' ? 'selected' : '' ?>>Prescription</option>
                <option value="identité" <?= $type === 'identité' ? 'selected' : '' ?>>Pièce d'identité</option>
                <option value="autre" <?= $type === 'autre' ? 'selected' : '' ?>>Autre</option>
            </select><br><br>

            <label for="nature">Nature du fichier :</label>
            <select name="nature" id="nature">
                <option value="">Tous</option>
                <option value="PDF" <?= $nature === 'PDF' ? 'selected' : '' ?>>PDF</option>
                <option value="Image" <?= $nature === 'Image' ? 'selected' : '' ?>>Image</option>
                <option value="Autre" <?= $nature === 'Autre' ? 'selected' : '' ?>>Autre</option>
            </select><br><br>

            <label for="contenu">Contenu (description) :</label>
            <input type="text" name="contenu" id="contenu" value="<?= htmlspecialchars($contenu) ?>"><br><br>

            <label for="date">Date du document :</label>
            <input type="date" name="date" id="date" value="<?= htmlspecialchars($date) ?>"><br><br>

            <button type="submit">Rechercher</button>
            <button type="button" onclick="window.location.href='recherche_documents.php'">Réinitialiser</button>
        </form>

        <!-- Résultats de la recherche -->
        <h2>Résultats :</h2>
        <?php if (count($documents) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom du fichier</th>
                        <th>Type</th>
                        <th>Nature du fichier</th>
                        <th>Contenu</th>
                        <th>Date du document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['nom_fichier']) ?></td>
                            <td><?= htmlspecialchars($doc['type']) ?></td>
                            <td><?= htmlspecialchars($doc['nature_fichier']) ?></td>
                            <td><?= htmlspecialchars($doc['contenu']) ?></td>
                            <td><?= htmlspecialchars($doc['date_document']) ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($doc['chemin']) ?>" target="_blank">Ouvrir</a>
                                <a href="download.php?file_id=<?= $doc['id'] ?>" download>Télécharger</a>
                                <a href="mail_document.php?file_id=<?= $doc['id'] ?>">Envoyer par mail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun document trouvé pour ces critères.</p>
        <?php endif; ?>
    </div>
</body>

</html>