<?php
// Inclusion des ressources communes pour la connexion MySQL
require_once 'ressources_communes.php';

// Connexion à la base de données
$conn = connectDatabase();

// Préparation des données pour les sélections du formulaire
// Récupérer les motifs d'admission
$queryMotifs = "SELECT code, libellé FROM Motifs ORDER BY libellé";
$resultMotifs = $conn->query($queryMotifs);

$listMotifs = [];
if ($resultMotifs) {
    while ($row = $resultMotifs->fetch_assoc()) {
        $listMotifs[] = $row;
    }
}

// Récupérer les pays
$queryPays = "SELECT code, libellé FROM Pays ORDER BY libellé";
$resultPays = $conn->query($queryPays);

$listPays = [];
if ($resultPays) {
    while ($row = $resultPays->fetch_assoc()) {
        $listPays[] = $row;
    }
}

// Initialisation des valeurs pour les champs du formulaire
$nomPatient = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$motifSelectionne = isset($_GET['motif']) ? $_GET['motif'] : '';
$paysSelectionne = isset($_GET['pays']) ? $_GET['pays'] : '';
$dateNaissance = isset($_GET['date_naissance']) ? $_GET['date_naissance'] : '1920-01-01'; // Valeur par défaut

// Construction de la requête pour la recherche des patients
$sql = "SELECT code, nom, prenom FROM Patients WHERE 1=1";
$params = [];
$paramTypes = '';

// Ajouter des filtres selon les champs renseignés
if (!empty($nomPatient)) {
    $sql .= " AND nom LIKE ?";
    $params[] = "%$nomPatient%";
    $paramTypes .= 's';
}

if (!empty($motifSelectionne)) {
    $sql .= " AND code_motif = ?";
    $params[] = $motifSelectionne;
    $paramTypes .= 'i';
}

if (!empty($paysSelectionne)) {
    $sql .= " AND code_pays = ?";
    $params[] = $paysSelectionne;
    $paramTypes .= 's';
}

// Ajouter un filtre sur la date de naissance
if (!empty($dateNaissance) && $dateNaissance !== '1920-01-01') {
    $sql .= " AND date_naissance >= ?";
    $params[] = $dateNaissance;
    $paramTypes .= 's';
}

// Si aucun critère n'est spécifié, ne retourner aucun patient
if (empty($nomPatient) && empty($motifSelectionne) && empty($paysSelectionne) && ($dateNaissance === '1920-01-01' || empty($dateNaissance))) {
    $sql .= " AND 1=0"; // Condition toujours fausse pour ne retourner aucun patient
}

// Trier les résultats par nom et prénom
$sql .= " ORDER BY nom ASC, prenom ASC";

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$resultPatients = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Patients</title>
    <link rel="stylesheet" href="./css/recherche_patient.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Recherche de Patients</h1>
        </header>
        <form method="GET" action="recherche_patient.php">
            <label for="nom">Nom (tout ou partie) :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nomPatient) ?>"
                placeholder="Saisissez un nom">

            <label for="motif">Motif d'admission :</label>
            <select id="motif" name="motif">
                <option value="">Tous</option>
                <?php foreach ($listMotifs as $motif): ?>
                    <option value="<?= $motif['code'] ?>" <?= ($motif['code'] == $motifSelectionne) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($motif['libellé']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="pays">Pays :</label>
            <select id="pays" name="pays">
                <option value="">Tous</option>
                <?php foreach ($listPays as $pays): ?>
                    <option value="<?= $pays['code'] ?>" <?= ($pays['code'] == $paysSelectionne) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pays['libellé']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="date_naissance">Date de naissance (après) :</label>
            <input type="date" id="date_naissance" name="date_naissance"
                value="<?= htmlspecialchars($dateNaissance) ?>">

            <button type="submit">Rechercher</button>
            <button type="button" class="reset-btn"
                onclick="window.location.href='recherche_patient.php'">Réinitialiser</button>
        </form>

        <div class="results">
            <?php if ($resultPatients && $resultPatients->num_rows > 0): ?>
                <h2>Résultats :</h2>
                <?php while ($patient = $resultPatients->fetch_assoc()): ?>
                    <p>
                        <a
                            href="fiche_patient.php?id=<?= $patient['code'] ?>&nom=<?= urlencode($nomPatient) ?>&motif=<?= urlencode($motifSelectionne) ?>&pays=<?= urlencode($paysSelectionne) ?>&date_naissance=<?= urlencode($dateNaissance) ?>">
                            <strong><?= htmlspecialchars(strtoupper($patient['nom'])) ?></strong>
                            <?= htmlspecialchars($patient['prenom']) ?>
                        </a>
                    </p>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-message">Aucun patient trouvé pour ces critères.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
<script src="./js/recherche_patient.js"></script>

</html>