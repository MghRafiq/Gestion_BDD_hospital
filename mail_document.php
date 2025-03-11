<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Vérifie bien que ce fichier existe

// Connexion à la base de données
require_once 'ressources_communes.php';
$conn = connectDatabase();

// Vérifier si un fichier est bien sélectionné
if (!isset($_GET['file_id']) || empty($_GET['file_id'])) {
    die("Erreur : Aucun fichier sélectionné.");
}

$fileId = (int) $_GET['file_id'];

// Récupérer les informations du document dans la base de données
$sql = "SELECT d.nom_fichier, d.chemin, p.nom AS patient_nom, p.prenom AS patient_prenom, p.numero_secsoc
        FROM documents d
        JOIN patients p ON d.patient_id = p.Code
        WHERE d.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $fileId);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();

if (!$document) {
    die("Erreur : Document introuvable.");
}

$filePath = $document['chemin'];
$patientNom = $document['patient_nom'] ?? 'Inconnu';
$patientPrenom = $document['patient_prenom'] ?? '';
$patientSecSoc = $document['numero_secsoc'] ?? 'Non spécifié';

$mail = new PHPMailer(true);

$successMessage = '';
$errorMessage = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $to = $_POST['email'];
        $subject = $_POST['Sujet'];
        $message = $_POST['Message'];
        // Paramètres du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.mail.yahoo.com'; // serveur SMTP yahoo
        $mail->SMTPAuth = true;
        $mail->Username = 'rbhopital@yahoo.com'; // l'adresse mail je vais le ajouter apres 
        $mail->Password = 'xaqtwqazmkaojmvi'; // meme pour le Mot de passe ou App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Yahoo utilise SSL
        $mail->Port = 465; // Port sécurisé pour Yahoo

        // Expéditeur et destinataire
        $mail->setFrom('rbhopital@yahoo.com', 'M&B Hopital');
        $mail->addAddress($to); // Adresse du destinataire

        // Vérifier si le fichier existe avant de l'attacher
        if (file_exists($filePath)) {
            $mail->addAttachment($filePath, $document['nom_fichier']);
        } else {
            throw new Exception("Le fichier n'existe pas : " . $filePath);
        }

        // Contenu du mail
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Envoi du mail
        if ($mail->send()) {
            $successMessage = "E-mail envoyé avec succès.";
        } else {
            $errorMessage = "Erreur lors de l'envoi de l'e-mail.";
        }
    }
} catch (Exception $e) {
    $errorMessage = "Erreur lors de l'envoi du document : " . $mail->ErrorInfo;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Envoyer par mail</title>
    <link rel="stylesheet" href=".\css\mail_document.css">
</head>

<body>
    <div class="container">
        <h1>Envoyer le document par mail</h1>

        <?php if (!empty($successMessage)): ?>
            <div class="message success"><?= $successMessage ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="message error"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="Sujet">Sujet :</label>
            <input type="text" name="Sujet" id="Sujet" required>

            <label for="email">Adresse email :</label>
            <input type="email" name="email" id="email" required>

            <label for="Message">Corps de Message :</label>
            <textarea name="Message" id="Message" required></textarea>

            <button type="submit">Envoyer</button>
        </form>
    </div>
</body>

</html>