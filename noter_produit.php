<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_modele = intval($_POST['id_modele']); // Remplacer produit_id par id_modele
    $user_id = $_SESSION['user_id']; // Assurez-vous que l'utilisateur est connecté
    $note = intval($_POST['note']);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
    }

    $sql = "INSERT INTO notes (id_modele, id_user, note) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $id_modele, $user_id, $note);
    if ($stmt->execute()) {
        echo "Note enregistrée avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>