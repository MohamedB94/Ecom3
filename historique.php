<?php
session_start();
require 'config.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.html');
    exit();
}

$id_user = $_SESSION['user_id'];

// Récupérez les commandes de l'utilisateur
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$sql = "SELECT * FROM historique_commandes WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$commandes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Commandes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Historique des Commandes</h1>
        <?php if (empty($commandes)): ?>
            <p>Vous n'avez pas encore passé de commande.</p>
        <?php else: ?>
            <?php foreach ($commandes as $commande): ?>
                <div class="commande">
                    <h2>Commande #<?= $commande['id_commande'] ?></h2>
                    <p>Date d'achat : <?= $commande['date_achat'] ?></p>
                    <p>Date de livraison : <?= $commande['date_livraison'] ?></p>
                    <p>Adresse de livraison : <?= $commande['adresse_livraison'] ?>, <?= $commande['code_postal'] ?>, <?= $commande['ville'] ?></p>
                    <p>Complément d'adresse : <?= $commande['complement_adresse'] ?></p>
                    <p>Prix total : <?= $commande['prix_total'] ?> €</p>
                    <h3>Produits :</h3>
                    <ul>
                        <?php 
                        $produits = json_decode($commande['produits'], true);
                        if (is_array($produits)): 
                            foreach ($produits as $produit): ?>
                                <li>
                                    <?php if (isset($produit['image']) && !empty($produit['image'])): ?>
                                        <img src="images/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" style="width: 50px; height: 50px;">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($produit['nom']) ?>
                                </li>
                            <?php endforeach; 
                        else: ?>
                            <li>Erreur lors de la récupération des produits.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>