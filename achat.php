<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achat</title>
    <link rel="stylesheet" href="achat.css">
</head>
<body>
    <h1>Page d'Achat</h1>
    <p>Merci pour votre achat !</p>
    <?php
    if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
        echo "<a href='index.php' class='btn btn-primary'>Retour à l'accueil</a>";
    } else {
        echo "<a href='#' class='btn btn-primary' onclick='alert(\"Veuillez vous connecter pour accéder à cette page.\")'>Retour à l'accueil</a>";
    }
    ?>
</body>
</html>

