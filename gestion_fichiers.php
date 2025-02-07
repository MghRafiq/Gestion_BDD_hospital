<?php
require_once 'ressources_communes.php';

$conn = connectDatabase();

// Afficher les documents d'un patient spécifique
if (isset($_GET['id_patient'])) {
    $id_patient = intval($_GET['id_patient']);
    $sql = "SELECT * FROM Documents WHERE id_patient = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_patient);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<h2>Documents du patient</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nom du fichier</th><th>Type</th><th>Date d'ajout</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['nom_fichier']}</td>";
        echo "<td>{$row['type']}</td>";
        echo "<td>{$row['date_ajout']}</td>";
        echo "<td>
                <a href='telecharger.php?file={$row['nom_fichier']}'>Télécharger</a> |
                <a href='ouvrir.php?file={$row['nom_fichier']}' target='_blank'>Ouvrir</a> |
                <a href='imprimer.php?file={$row['nom_fichier']}'>Imprimer</a> |
                <a href='envoyer_email.php?file={$row['nom_fichier']}'>Envoyer par Email</a>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Gérer l'upload de fichiers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $id_patient = intval($_POST['id_patient']);
    $nom_fichier = basename($_FILES['document']['name']);
    $chemin = "uploads/" . $nom_fichier;
    
    if (move_uploaded_file($_FILES['document']['tmp_name'], $chemin)) {
        $type = mime_content_type($chemin);
        $sql = "INSERT INTO Documents (id_patient, nom_fichier, type, date_ajout) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $id_patient, $nom_fichier, $type);
        $stmt->execute();
        echo "Fichier téléchargé avec succès.";
    } else {
        echo "Erreur lors du téléchargement.";
    }
}

?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_patient" value="<?php echo $_GET['id_patient'] ?? ''; ?>">
    <input type="file" name="document" required>
    <button type="submit">Uploader</button>
</form>
