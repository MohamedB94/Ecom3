<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci pour votre paiement</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous d'avoir un fichier CSS pour le style -->
</head>
<body>
    <div class="container">
        <h1>Merci pour votre paiement !</h1>
        <p>Votre paiement a été traité avec succès.</p>
        <p>Nous vous remercions de votre achat, <?= htmlspecialchars($_SESSION['prenom']) ?> !</p>
        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</body>
</html>