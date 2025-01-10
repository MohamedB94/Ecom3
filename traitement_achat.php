<?php
session_start();
require 'config.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.html');
    exit();
}

// Récupérez les informations du panier et de l'utilisateur
$id_user = $_SESSION['user_id'];
$produits = json_encode($_SESSION['panier']);
$prix_total = array_sum(array_map(function($item) {
    return $item['prix'] * $item['quantite'];
}, $_SESSION['panier']));
$date_achat = date('Y-m-d H:i:s');
$date_livraison = date('Y-m-d H:i:s', strtotime('+7 days')); // Exemple : livraison dans 7 jours
$adresse_livraison = $_POST['adresse_livraison'];
$code_postal = $_POST['code_postal'];
$ville = $_POST['ville'];
$complement_adresse = $_POST['complement_adresse'];

// Enregistrez la commande dans la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$sql = "INSERT INTO historique_commandes (id_user, produits, prix_total, date_achat, date_livraison, adresse_livraison, code_postal, ville, complement_adresse)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issdsssss", $id_user, $produits, $prix_total, $date_achat, $date_livraison, $adresse_livraison, $code_postal, $ville, $complement_adresse);
$stmt->execute();
$stmt->close();
$conn->close();

// Redirigez vers la page de remerciement
header('Location: merci.php');
exit();
?>