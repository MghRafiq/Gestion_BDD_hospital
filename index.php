<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: choix.php"); // Rediriger si déjà connecté
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérification des identifiants (exemple simplifié)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user'] = $username;
        header("Location: choix.php");
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1>Connexion</h1>
        <?php if (isset($error))
            echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>

</html>