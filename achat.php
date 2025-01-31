<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
    // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: Connexion.html');
    exit();
}

// Calculer le total à partir du panier
$total = 0;
if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $produit => $details) {
        if (is_array($details)) {
            $total += $details['prix'] * $details['quantite'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achat</title>
    <link rel="stylesheet" href="achat.css">
</head>
<body>
    <div class="container">
        <h1>Page d'Achat</h1>
        <?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])): ?>
            <form action="traitement_achat.php" method="POST">
    <div class="form-group">
        <label for="nom_titulaire">Nom du titulaire de la carte :</label>
        <input type="text" id="nom_titulaire" name="nom_titulaire" pattern="[A-Za-zÀ-ÿ\s]+" title="Veuillez entrer uniquement des lettres." required>
    </div>
    <div class="form-group">
        <label for="numero_carte">Numéro de carte bancaire :</label>
        <input type="tel" id="numero_carte" name="numero_carte" pattern="[0-9]*" inputmode="numeric" required>
        <small>Veuillez entrer uniquement des chiffres.</small>
    </div>
    <div class="form-group">
        <label for="date_expiration">Date d'expiration (MM/AA) :</label>
        <input type="text" id="date_expiration" name="date_expiration" pattern="^(0[1-9]|1[0-2])\/?([0-9]{2})$" title="Format : MM/AA" required>
    </div>
    <div class="form-group">
        <label for="code_securite">Code de sécurité (CVV) :</label>
        <input type="text" id="code_securite" name="code_securite" pattern="[0-9]{3,4}" title="Veuillez entrer 3 ou 4 chiffres." required>
    </div>
    <button id="buyButton" class="btn btn-primary">Valider le paiement avec Stripe</button>
</form>
            <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
            <div class="total-container">
                <h2>Total à payer :</h2>
                <p><?= $total ?> €</p>
            </div>
        <?php else: ?>
            <p>Veuillez vous connecter pour effectuer un achat.</p>
            <a href="Connexion.html" class="btn btn-primary">Se connecter</a>
        <?php endif; ?>
    </div>
    <form action="verification_payment.php" method="post">
        <button type="submit">Payer</button>
    </form>
</body>
</html>