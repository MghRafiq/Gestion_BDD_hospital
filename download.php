<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'ressources_communes.php';

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
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file['chemin']) . '"');
    readfile($file['chemin']);
    exit;
} else {
    die("Fichier introuvable.");
}