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
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

img {
    vertical-align: middle;
}
</style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Historique des Commandes</h1>
        <?php if (empty($commandes)): ?>
            <p>Vous n'avez pas encore passé de commande.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Date d'achat</th>
                        <th>Date de livraison</th>
                        <th>Prix total</th>
                        <th>Produits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td><?= $commande['id_commande'] ?></td>
                            <td><?= $commande['date_achat'] ?></td>
                            <td><?= $commande['date_livraison'] ?></td>
                            <td><?= $commande['prix_total'] ?> €</td>
                            <td>
                                <ul>
                                    <?php 
                                    $produits = json_decode($commande['produits'], true);
                                    if (is_array($produits)): 
                                        foreach ($produits as $nom_produit => $details): ?>
                                            <li>
                                                <?php
                                                // Assurez-vous que les informations sur les produits sont disponibles
                                                $image = isset($details['image']) ? $details['image'] : 'default.png';
                                                $nom = isset($nom_produit) ? $nom_produit : 'Nom non disponible';
                                                ?>
                                                <img src="images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($nom); ?>" />
                                                <?= htmlspecialchars($nom) ?>
                                            </li>
                                        <?php endforeach; 
                                    else: ?>
                                        <li>Erreur lors de la récupération des produits.</li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>