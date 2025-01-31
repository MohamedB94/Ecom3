<?php
session_start(); // mes info se mettent que quand l'user est co
require 'config.php';

if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
    header('Location: Connexion.html');
    exit();
}
// var message qui se remplira apres 
$message = ''; // Initialisation de la variable message
// changement mdp
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nouveau_mdp = htmlspecialchars($_POST['nouveau_mdp']);
    $confirmer_mdp = htmlspecialchars($_POST['confirmer_mdp']);

    if ($nouveau_mdp === $confirmer_mdp) {
        $mdp_hache = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
        $email = $_SESSION['email'];

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($conn->connect_error) {
            die("Connexion échouée: " . $conn->connect_error);
        }

        $sql = "UPDATE utilisateur SET mdp=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $mdp_hache, $email);

        if ($stmt->execute()) {
            $message = "Informations mises à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour des informations.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $message = "Les mots de passe ne correspondent pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Informations</title>
    <!-- Inclusion des feuilles de style Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Inclusion de votre fichier CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <!-- Contenu principal de la page -->
    <div class="container mt-5">
        <h1>Mes Informations</h1>
        <p>Nom: <?= htmlspecialchars($_SESSION['nom']) ?></p>
        <p>Prénom: <?= htmlspecialchars($_SESSION['prenom']) ?></p>
        <p>Email: <?= htmlspecialchars($_SESSION['email']) ?></p>
        <!-- Form to change the password -->
        <h2>Changer le mot de passe</h2>
        <form action="update_user.php" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_SESSION['nom']) ?>" class="form-control" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_SESSION['prenom']) ?>" class="form-control" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" class="form-control" required>

            <label for="password">Nouveau mot de passe :</label>
            <input type="password" id="password" name="password" class="form-control">

            <label for="confirm_password">Confirmer le mot de passe :</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control">

            <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
        </form>
        <!-- Display the message -->
        <p><?= htmlspecialchars($message) ?></p>
    </div>
    <!-- Inclusion des scripts jQuery et Bootstrap pour la fonctionnalité interactive -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>