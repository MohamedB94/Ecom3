<?php
require 'config.php'; // Inclure le fichier de configuration pour les informations de connexion à la base de données

$message = ''; // Initialiser le message qui sera affiché à l'utilisateur

// Vérifier si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nettoyer et assigner les valeurs entrées par l'utilisateur
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $mdp = htmlspecialchars($_POST['password']);
  

    // Hacher le mot de passe pour la sécurité
    $mdp_hache = password_hash($mdp, PASSWORD_BCRYPT);

    // Tenter de se connecter à la base de données
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // Gérer les erreurs de connexion
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
    }


    // Préparer la requête SQL pour insérer les données de l'utilisateur
    $sql = "INSERT INTO utilisateur (nom, prenom, email, mdp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nom, $prenom, $email, $mdp_hache);
    // Exécuter la requête et vérifier si elle a réussi
    if ($stmt->execute()) {
        $message = "Votre inscription a été réalisée avec succès. Vous pouvez maintenant vous <a href='Connexion.html'>connecter</a>.";
    } else {
        $message = "Erreur: " . $sql . "<br>" . $conn->error;
    }

    // Fermer le statement et la connexion
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="Inscription.css">
</head>
<body>
    <div class="inscription">
        <h1>Inscription</h1>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php else: ?>
            <form action="register.php" method="POST">
                <label for="Nom">Nom:</label><br>
                <input type="text" id="Nom" name="Nom" required><br>
                <label for="Prenom">Prénom:</label><br>
                <input type="text" id="Prenom" name="Prenom" required><br>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required><br>
                <label for="mdp">Mot de passe:</label><br>
                <input type="password" id="mdp" name="mdp" required><br><br>
                <input type="submit" value="S'inscrire">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
