<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Choix</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1>Que souhaitez-vous faire ?</h1>

        <!-- Bouton Rechercher un patient -->
        <a href="recherche_patient.php" class="btn">Rechercher un patient</a>

        <!-- Bouton Rechercher des documents -->
        <a href="recherche_documents.php" class="btn">Rechercher des documents</a>

        <!-- Bouton Déconnexion en bas -->
        <a href="logout.php" class="btn logout">Déconnexion</a>
    </div>
</body>

</html>