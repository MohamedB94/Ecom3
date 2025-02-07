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
$date_livraison = date('Y-m-d H:i:s', strtotime('+7 days'));

// Vérifiez que chaque élément du panier a un chemin d'image
foreach ($_SESSION['panier'] as $item) {
    if (!isset($item['image_path']) || empty($item['image_path'])) {
        echo "<pre>";
        print_r($item); // Affichez l'élément du panier pour le débogage
        echo "</pre>";
        die("Erreur: Un ou plusieurs éléments du panier n'ont pas de chemin d'image.");
    }
}

// Enregistrez la commande dans la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO historique_commandes (id_user, produits, prix_total, date_achat, date_livraison) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $id_user, $produits, $prix_total, $date_achat, $date_livraison);

if ($stmt->execute()) {
    // Vider le panier après l'achat
    $_SESSION['panier'] = array();
    header('Location: historique.php');
} else {
    echo "Erreur lors de l'enregistrement de la commande: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
