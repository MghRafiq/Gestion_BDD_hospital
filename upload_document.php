<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'ressources_communes.php';

// Connexion à la base de données
$conn = connectDatabase();

$patientId = isset($_POST['patient_id']) ? (int) $_POST['patient_id'] : 0;
if ($patientId <= 0) {
    die("Patient invalide.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomFichier = basename($_FILES['document']['name']);
    $chemin = "documents/$patientId/" . time() . "_$nomFichier";
    if (!is_dir("documents/$patientId"))
        mkdir("documents/$patientId", 0755, true);

    // Récupérer les données du formulaire
    $type = $_POST['type'];
    $natureFichier = $_POST['nature_fichier'];
    $contenu = $_POST['contenu'];
    // Vérifier si un document similaire existe déjà
    $stmtCheck = $conn->prepare("
        SELECT * FROM documents 
        WHERE patient_id = ? 
        AND type = ? 
        AND nature_fichier = ? 
        AND contenu = ? 
    ");
    $stmtCheck->bind_param("isss", $patientId, $type, $natureFichier, $contenu);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        $error = "Un document similaire existe déjà.";
    } else {
        // Enregistrer le fichier
        if (move_uploaded_file($_FILES['document']['tmp_name'], $chemin)) {
            $stmt = $conn->prepare("
                INSERT INTO documents (patient_id, nom_fichier, chemin, type, nature_fichier, contenu, date_upload) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("isssss", $patientId, $nomFichier, $chemin, $type, $natureFichier, $contenu);
            $stmt->execute();
            header("Location: fiche_patient.php?id=$patientId");
            exit;
        } else {
            $error = "Erreur lors de l'upload du fichier.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Upload Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1>Ajouter un document</h1>
        <?php if (isset($error))
            echo "<p class='error'>$error</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
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
</body>

</html>