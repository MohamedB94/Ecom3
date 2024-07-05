<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="scripts.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
        <a href="admin.php">Ajouter un produit</a>
    <?php endif; ?>
    <div class="container mt-5">
        <h1 class="text-center">Votre Panier</h1>
        <ul class="list-group">
            <?php
            $total = 0;
            // Afficher les produits dans le panier ou un message si le panier est vide
            if (empty($_SESSION['panier'])) {
                echo "<p>Votre panier est vide.</p>"; // Message si le panier est vide
            } else {
                // Boucler sur chaque produit du panier pour l'affichage
                foreach ($_SESSION['panier'] as $produit => $details) {
                    if (is_array($details)) {
                        $prix_total_produit = $details['prix'] * $details['quantite'];
                        $total += $prix_total_produit;
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                        echo "$produit - {$details['prix']} € x{$details['quantite']} = $prix_total_produit €";
                        echo "<form action='panier.php' method='POST' class='d-flex'>";
                        echo "<input type='hidden' name='action' value='supprimer'>";
                        echo "<input type='hidden' name='produit' value='$produit'>";
                        echo "<select name='quantite' class='form-control mr-2'>";
                        for ($i = 1; $i <= $details['quantite']; $i++) {
                            echo "<option value='$i'>$i</option>";
                        }
                        echo "</select>";
                        echo "<button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>";
                        echo "</form>";
                        echo "</li>";
                    }
                }
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo "<strong>Total</strong>";
                echo "<strong id='cart-total'>$total </strong>";
                echo "</li>";
            }
            ?>
        </ul>
        <div class="d-flex justify-content-between mt-4">
            <button onclick="verifierConnexion()" class="btn btn-primary" <?php if (empty($_SESSION['panier'])) echo 'disabled'; ?>>Acheter</button>
            <a href="index.php" class="btn btn-secondary">Continuer vos achats</a>
        </div>
    </div>
    <script>
        function verifierConnexion() {
            <?php if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])): ?>
                if (confirm("Veuillez vous connecter pour acheter. Voulez-vous être redirigé vers la page de connexion ?")) {
                    window.location.href = 'Connexion.html';
                }
            <?php else: ?>
                // Code pour procéder à l'achat
                alert("Achat en cours...");
            <?php endif; ?>
        }
    </script>
</body>
</html>
