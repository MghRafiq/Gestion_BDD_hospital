<?php
// Inclusion des ressources communes pour établir la connexion à la base de données
require_once 'ressources_communes.php';

// Connexion à la base de données
$conn = connectDatabase();

// Vérification de l'identifiant du patient passé en paramètre
$patientId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($patientId <= 0) {
    // Aucun identifiant valide fourni
    echo "<p>Erreur : Aucun patient sélectionné.</p>";
    exit;
}

// La requête SQL pour récupérer les informations du patient
$sql = "
    SELECT 
        p.nom, 
        p.prenom, 
        p.date_naissance, 
        p.numero_secsoc,
        pa.libellé AS pays, 
        m.libellé AS motif, 
        s.libellé AS sexe,
        p.date_premiere_entree
    FROM Patients p
    LEFT JOIN Pays pa ON p.code_pays = pa.code
    LEFT JOIN Motifs m ON p.code_motif = m.code
    LEFT JOIN Sexe s ON p.sexe = s.code
    WHERE p.code = ?
";

// Préparation et exécution de la requête
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si le patient existe
$patientData = $result->fetch_assoc();
if (!$patientData) {
    echo "<p>Erreur : Patient introuvable.</p>";
    exit;
}
// Fonction pour reformater la date en jj-mm-aaaa
function formatDate($date)
{
    if (empty($date)) {
        return 'Non spécifiée';
    }
    $dateObj = new DateTime($date);
    return $dateObj->format('d-m-Y');
}

// Formater les dates
$dateNaissanceFormatee = formatDate($patientData['date_naissance']);
$datePremiereEntreeFormatee = formatDate($patientData['date_premiere_entree']);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche du Patient</title>
    <link rel="stylesheet" href="./css/fiche_patient.css">
</head>

<body>
    <header>
        <h1>Fiche du Patient</h1>
    </header>
    <div class="container">
        <div class="results">
            <div class="card">
                <!-- Affichage des informations du patient -->
                <h2><?= strtoupper(htmlspecialchars($patientData['nom'] ?? 'Non spécifié')) ?>
                    <?= htmlspecialchars($patientData['prenom'] ?? '') ?>
                </h2>
                <p><strong>Date de naissance :</strong> <?= htmlspecialchars($dateNaissanceFormatee) ?></p>
                <p><strong>Sexe :</strong> <?= htmlspecialchars($patientData['sexe'] ?? 'Non spécifié') ?></p>
                <p><strong>Pays :</strong> <?= htmlspecialchars($patientData['pays'] ?? 'Non spécifié') ?></p>
                <p><strong>Motif d'admission :</strong> <?= htmlspecialchars($patientData['motif'] ?? 'Non spécifié') ?>
                </p>
                <p><strong>Date de première entrée :</strong> <?= htmlspecialchars($datePremiereEntreeFormatee) ?></p>
                <p><strong>Numéro de Sécurité Sociale :</strong>
                    <?= htmlspecialchars($patientData['numero_secsoc'] ?? 'Non spécifié') ?></p>
                <!--Retour à la recherche avec les critères précédents -->
                <p>
                    <a
                        href="recherche_patient.php?nom=<?= urlencode($_GET['nom'] ?? '') ?>&motif=<?= urlencode($_GET['motif'] ?? '') ?>&pays=<?= urlencode($_GET['pays'] ?? '') ?>&date_naissance=<?= urlencode($_GET['date_naissance'] ?? '') ?>">
                        ⬅ Retour à la recherche
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>