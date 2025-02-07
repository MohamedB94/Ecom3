<?php

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_title = $_POST["product_title"];
    $nom = $_POST["nom"];
    $commentaire = $_POST["commentaire"];
    $note = intval($_POST["note"]);
    
    $stmt = $pdo->prepare("INSERT INTO avis (product_title, nom, commentaire, note) VALUES (:product_title, :nom, :commentaire, :note)");
    if ($conn->query($sql) === TRUE) {
        echo "Avis ajouté avec succès";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}