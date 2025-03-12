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
$dateNaissanceFormatee = $patientData ? formatDate($patientData['date_naissance']) : 'Non spécifiée';
$datePremiereEntreeFormatee = $patientData ? formatDate($patientData['date_premiere_entree']) : 'Non spécifiée';

// Récupérer les documents du patient
$sqlDocuments = "SELECT * FROM documents WHERE patient_id = ?";
$stmtDocuments = $conn->prepare($sqlDocuments);
$stmtDocuments->bind_param("i", $patientId);
$stmtDocuments->execute();
$resultDocuments = $stmtDocuments->get_result();
$documents = $resultDocuments->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche du Patient</title>
    <link rel="stylesheet" href="./css/fiche_patient.css">
    <script src=".\js\fiche_patient.js"></script>
</head>

<body>

    <div class="container">
        <header>
            <h1>Fiche du Patient</h1>
            <a href="recherche_patient.php?nom=<?= urlencode($_GET['nom'] ?? '') ?>&motif=<?= urlencode($_GET['motif'] ?? '') ?>&pays=<?= urlencode($_GET['pays'] ?? '') ?>&date_naissance=<?= urlencode($_GET['date_naissance'] ?? '') ?>"
                class="return-btn">Retour</a>
        </header>
        <div class="grid-container">
            <!-- Colonne de gauche : Informations du patient -->
            <div class="patient-info">
                <div class="card">
                    <!-- Affichage des informations du patient -->
                    <h2><?= strtoupper(htmlspecialchars($patientData['nom'] ?? 'Non spécifié')) ?>
                        <?= htmlspecialchars($patientData['prenom'] ?? '') ?>
                    </h2>
                    <p><strong>Date de naissance :</strong> <?= htmlspecialchars($dateNaissanceFormatee) ?></p>
                    <p><strong>Sexe :</strong> <?= htmlspecialchars($patientData['sexe'] ?? 'Non spécifié') ?></p>
                    <p><strong>Pays :</strong> <?= htmlspecialchars($patientData['pays'] ?? 'Non spécifié') ?></p>
                    <p><strong>Motif d'admission :</strong>
                        <?= htmlspecialchars($patientData['motif'] ?? 'Non spécifié') ?></p>
                    <p><strong>Date de première entrée :</strong> <?= htmlspecialchars($datePremiereEntreeFormatee) ?>
                    </p>
                    <p><strong>Numéro de Sécurité Sociale :</strong>
                        <?= htmlspecialchars($patientData['numero_secsoc'] ?? 'Non spécifié') ?></p>
                </div>
            </div>

            <!-- Colonne de droite : Documents et formulaire d'upload -->
            <div class="patient-documents">
                <div class="card">
                    <!-- Section des documents -->
                    <h3>Documents</h3>
                    <?php if (count($documents) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom du fichier</th>
                                    <th>Type</th>
                                    <th>Nature du fichier</th>
                                    <th>Contenu</th>
                                    <th>Date d'upload</th>
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
                                        <td><?= date('d/m/Y', strtotime($doc['date_upload'])) ?></td>
                                        <td>
                                            <a href="<?= htmlspecialchars($doc['chemin']) ?>" target="_blank">Ouvrir</a>
                                            <a href="download.php?file_id=<?= $doc['id'] ?>" download>Télécharger</a>
                                            <a href="#"
                                                onclick="printDocument('<?= htmlspecialchars($doc['chemin']) ?>')">Imprimer</a>
                                            <a href="mail_document.php?file_id=<?= $doc['id'] ?>" target="_blank">Envoyer par
                                                mail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Aucun document trouvé pour ce patient.</p>
                    <?php endif; ?>

                    <!-- Formulaire d'upload de document -->
                    <h3>Ajouter un document</h3>
                    <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="patient_id" value="<?= $patientId ?>">

                        <label for="document">Sélectionner un fichier :</label>
                        <input type="file" name="document" id="document" required><br><br>

                        <label for="type">Type de document :</label>
                        <select name="type" id="type" required>
                            <option value="ordonnance">Ordonnance</option>
                            <option value="prescription">Prescription</option>
                            <option value="identité">Pièce d'identité</option>
                            <option value="autre">Autre</option>
                        </select><br><br>

                        <label for="nature_fichier">Nature du fichier :</label>
                        <select name="nature_fichier" id="nature_fichier" required>
                            <option value="PDF">PDF</option>
                            <option value="Image">Image</option>
                            <option value="Autre">Autre</option>
                        </select><br><br>

                        <label for="contenu">Contenu (description) :</label>
                        <textarea name="contenu" id="contenu" rows="4" required></textarea><br><br>



                        <button type="submit">Uploader</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>