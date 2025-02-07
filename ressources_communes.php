<?php
// Configuration des constantes pour la connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Identifiant MySQL : user1
define('DB_PASSWORD', '');        // Mot de passe MySQL : hcetylop
define('DB_NAME', 'hopital_php'); // Nom de la base de données

/**
 * Établit une connexion à la base de données MySQL.
 *
 * @return mysqli Connexion à la base de données.
 * @throws Exception Si la connexion échoue.
 */
function connectDatabase() {
    // Création de la connexion
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Vérifier la connexion
    if ($connection->connect_error) {
        // Afficher l'erreur avant d'arrêter le script
        error_log("Erreur de connexion à la base de données : " . $connection->connect_error);
        die("Échec de la connexion à la base de données. Veuillez réessayer plus tard.");
    }

    return $connection;
}
?>
