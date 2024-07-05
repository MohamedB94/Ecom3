<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de configuration
require 'config.php';

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérifier si une recherche a été effectuée
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Préparer la requête SQL pour obtenir les produits
if ($query) {
    $sql = "SELECT * FROM modele WHERE Nom LIKE '%" . $conn->real_escape_string($query) . "%'";
} else {
    $sql = "SELECT * FROM modele";
}

$result = $conn->query($sql);

// Afficher les résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='col-md-4'>";
        echo "<div class='card mb-4 shadow-sm'>";
        echo "<img src='" . $row['Image'] . "' class='card-img-top' alt='" . $row['Nom'] . "'>";
        echo "<div class='card-body'>";
        echo "<p class='card-text'>" . $row['Nom'] . "</p>";
        echo "<p class='card-text'>" . $row['Description'] . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p class='text-center'>Aucun produit trouvé</p>";
}
$conn->close();
?>
