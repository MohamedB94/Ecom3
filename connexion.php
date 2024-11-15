<?php
// Démarrage de la session
session_start();
// Inclusion du fichier de configuration pour les paramètres de la base de données
require 'config.php';

// Vérification si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nettoyage des entrées utilisateur
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Tentative de connexion à la base de données
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // Gestion des erreurs de connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Préparation de la requête SQL pour vérifier l'email de l'utilisateur
    $stmt = $conn->prepare("SELECT id_user, nom, prenom, mdp FROM utilisateur WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérification si un utilisateur correspondant a été trouvé
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Vérification du mot de passe
        if (password_verify($password, $user['mdp'])) {
            // Si le mot de passe est correct, initialisation de la session
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $email;
            // Redirection vers la page d'accueil
            header('Location: index.php');
            exit();
        } else {
            // Gestion du cas où le mot de passe est incorrect
            echo "Mot de passe incorrect";
        }
    } else {
        // Gestion du cas où aucun utilisateur correspondant n'est trouvé
        echo "Utilisateur non trouvé";
    }

    // Fermeture du statement et de la connexion
    $stmt->close();
    $conn->close();
}
?>
