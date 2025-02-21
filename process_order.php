<?php
session_start();
require 'config.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.html');
    exit();
}

$id_user = $_SESSION['user_id'];
$produits = $_POST['produits']; // Les produits commandés
$prix_total = $_POST['prix_total']; // Le prix total de la commande

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Enregistrer la commande dans la base de données
$sql = "INSERT INTO historique_commandes (id_user, produits, prix_total, date_achat) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erreur de préparation de la requête: " . $conn->error);
}
$stmt->bind_param("isd", $id_user, json_encode($produits), $prix_total);
$stmt->execute();
$stmt->close();

// Envoyer l'email de confirmation de commande
$to = 'client@example.com'; // Remplacez par l'email du client
$subject = 'Confirmation de votre commande';

// Construire le message HTML
$body = "<html><head><title>Confirmation de commande</title></head><body>";
$body .= "<h2>Merci pour votre commande</h2>";
$body .= "<table border='1' cellspacing='0' cellpadding='5'>";
$body .= "<tr><th>Produit</th><th>Prix</th><th>Quantité</th></tr>";

foreach ($produits as $nom_produit => $details) {
    $nom = htmlspecialchars($nom_produit);
    $quantite = isset($details['quantite']) ? htmlspecialchars($details['quantite']) : 'Quantité non disponible';
    $prix = isset($details['prix']) ? htmlspecialchars($details['prix']) : 'Prix non disponible';
    $body .= "<tr><td>$nom</td><td>$prix €</td><td>$quantite</td></tr>";
}

$body .= "</table>";
$body .= "<h3>Prix total: " . number_format($prix_total, 2, ',', '') . "€</h3>";
$body .= "<p>Votre commande est en cours de traitement.</p>";
$body .= "</body></html>";

sendOrderEmail($to, $subject, $body);

$conn->close();

header('Location: historique.php');
exit();

// Fonction pour envoyer un email de confirmation de commande
function sendOrderEmail($to, $subject, $body) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <no-reply@votre-site.com>' . "\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo 'Email envoyé avec succès';
    } else {
        echo "L'email n'a pas pu être envoyé.";
    }
}
?>