<?php
session_start(); // Démarrage de la session

// Connexion à la base de données
$servername = "localhost"; // Nom du serveur
$username = "root"; // Nom d'utilisateur de la base de données
$password = ""; // Mot de passe de la base de données (vide pour localhost)
$dbname = "ecom"; // Nom de la base de données

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname); // Création de l'objet de connexion

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error); // Arrêt du script si la connexion échoue
}

// Vérifier les informations de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Vérifie si la méthode de requête est POST
    $nom = $_POST['nom']; // Récupération du nom depuis le formulaire
    $prenom = $_POST['prenom']; // Récupération du prénom depuis le formulaire
    $admin_pass = $_POST['admin_pass']; // Récupération du mot de passe depuis le formulaire

    // Préparation de la requête SQL pour vérifier les informations de l'administrateur
    $stmt = $conn->prepare("SELECT * FROM admins WHERE nom=? AND prenom=? AND password=?");
    $stmt->bind_param("sss", $nom, $prenom, $admin_pass); // Liaison des paramètres
    $stmt->execute(); // Exécution de la requête
    $result = $stmt->get_result(); // Récupération du résultat

    // Vérifier si les informations de connexion sont correctes
    if ($result->num_rows > 0) {
        // Si les informations de connexion sont correctes
        $_SESSION['admin'] = true;
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        header('Location: index.php'); // Rediriger vers la page d'accueil
        exit();
    } else {
        echo "Nom, prénom ou mot de passe incorrect"; // Message d'erreur
    }

    // Fermer la déclaration
    $stmt->close(); // Fermeture du statement
}

// Fermer la connexion
$conn->close(); // Fermeture de la connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <!-- Définition de la largeur de la fenêtre pour les appareils mobiles -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Connexion</title>
    <!-- Lien vers la feuille de style externe -->
    <link rel="stylesheet" href="adminlog.css">
</head>
<body>
    <div class="container">
        <h1>Connexion Administrateur</h1>
        <form action="admin_login.php" method="post">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>
            <label for="admin_pass">Mot de passe:</label>
            <input type="password" id="admin_pass" name="admin_pass" required>
            <input type="submit" value="Se connecter">
        </form>
    </div>
</body>
</html>
