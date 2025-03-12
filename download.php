<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'ressources_communes.php';
$conn = connectDatabase();
$fileId = isset($_GET['file_id']) ? (int) $_GET['file_id'] : 0;
if ($fileId <= 0) {
    die("Fichier invalide.");
}

$stmt = $conn->prepare("SELECT chemin FROM documents WHERE id = ?");
$stmt->bind_param("i", $fileId);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();

if ($file && file_exists($file['chemin'])) {
    $filePath = $file['chemin'];
    $filename = basename($filePath);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Définition des types MIME
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];

    header('Content-Description: File Transfer');
    header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die("Fichier introuvable.");
}