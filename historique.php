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

// Récupérez les commandes de l'utilisateur
$sql = "SELECT * FROM historique_commandes WHERE id_user = ?";
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
        <?php if (count($commandes) > 0): ?>
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