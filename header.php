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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scripts.js"></script>
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
        <nav class="nav">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="ordinateurs.php">Ordinateurs</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="composants.php">Composants</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="peripheriques.php">Périphériques</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="gaming.php">Gaming</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="favorites.php">Mes Favoris</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="profilDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Mon Profil
                    </a>
                    <div class="dropdown-menu" aria-labelledby="profilDropdown">
                        <a class="dropdown-item" href="mes_informations.php">Mes Informations</a>
                        <a class="dropdown-item" href="historique.php">Historique des Commandes</a>
                    </div>
                </li>
            </ul>
        </nav>
        <form id="searchForm" action="index.php" method="GET" class="form-inline">
            <div class="form-group mx-sm-3 mb-2">
                <label for="search" class="sr-only">Recherche</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Rechercher un produit">
            </div>
        </form>
        <div class="user-actions d-flex align-items-center">
            <?php
            if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                echo '<a href="mes_informations.php" class="btn btn-info mr-2">Mes Informations</a>';
                echo '<a href="deconnexion.php" class="btn btn-danger mr-2">Déconnexion</a>';
            } else {
                echo '<a href="Connexion.html" class="btn btn-primary mr-2">Connexion</a>';
                echo '<a href="Inscription.html" class="btn btn-secondary mr-2">Inscription</a>';
            }
            ?>
            <a href="panier.php" class="btn btn-warning">Panier <span id="cart-count"><?= isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0 ?></span></a>
        </div>
    </header>
</body>
</html>
