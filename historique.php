<?php
session_start();
require 'config.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.html');
    exit();
}

$id_user = $_SESSION['user_id'];

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérez toutes les commandes de l'utilisateur
$sql = "SELECT * FROM historique_commandes WHERE id_user = ? ORDER BY date_achat DESC";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erreur de préparation de la requête: " . $conn->error);
}
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$commandes = [];
while ($row = $result->fetch_assoc()) {
    $commandes[] = $row;
}

$stmt->close();
$conn->close();

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

// Exemple d'utilisation
if (!empty($commandes)) {
    $commande = $commandes[0]; // La commande la plus récente
    $to = 'client@example.com'; // Remplacez par l'email du client
    $subject = 'Confirmation de votre commande';

    // Construire le message HTML
    $body = "<html><head><title>Confirmation de commande</title></head><body>";
    $body .= "<h2>Merci pour votre commande</h2>";
    $body .= "<table border='1' cellspacing='0' cellpadding='5'>";
    $body .= "<tr><th>Produit</th><th>Prix</th><th>Quantité</th></tr>";

    // Calculer le prix total de la commande
    $prix_total_commande = $commande['prix_total'];
    $produits = json_decode($commande['produits'], true);
    foreach ($produits as $nom_produit => $details) {
        $nom = htmlspecialchars($nom_produit);
        $quantite = isset($details['quantite']) ? htmlspecialchars($details['quantite']) : 'Quantité non disponible';
        $prix = isset($details['prix']) ? htmlspecialchars($details['prix']) : 'Prix non disponible';
        $body .= "<tr><td>$nom</td><td>$prix €</td><td>$quantite</td></tr>";
    }

    $body .= "</table>";
    $body .= "<h3>Prix total: " . number_format($prix_total_commande, 2, ',', '') . "€</h3>";
    $body .= "<p>Votre commande est en cours de traitement.</p>";
    $body .= "</body></html>";

    sendOrderEmail($to, $subject, $body);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Historique des commandes</h2>
        <?php if (!empty($commandes)): ?>
            <div class="row">
                <?php foreach ($commandes as $commande): ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Commande du <?= htmlspecialchars($commande['date_achat']) ?></h5>
                                <p class="card-text">Prix total: <?= htmlspecialchars($commande['prix_total']) ?> €</p>
                                <p class="card-text">Date de livraison: <?= htmlspecialchars($commande['date_livraison']) ?></p>
                                <h6>Produits:</h6>
                                <?php
                                $produits = json_decode($commande['produits'], true);
                                foreach ($produits as $nom_produit => $details) {
                                    $nom = htmlspecialchars($nom_produit);
                                    $quantite = isset($details['quantite']) ? htmlspecialchars($details['quantite']) : 'Quantité non disponible';
                                    $prix = isset($details['prix']) ? htmlspecialchars($details['prix']) : 'Prix non disponible';
                                    $image_path = isset($details['image_path']) ? htmlspecialchars($details['image_path']) : '';

                                    echo "Nom: " . $nom . "<br>";
                                    echo "Quantité: " . $quantite . "<br>";
                                    echo "Prix: " . $prix . " €<br>";
                                    if ($image_path) {
                                        echo "Image: <img src='" . $image_path . "' alt='Image du produit' class='img-fluid'><br>";
                                    } else {
                                        echo "Image non disponible<br>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">Aucune commande trouvée</p>
        <?php endif; ?>
    </div>
</body>
</html>