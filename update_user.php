<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['notification'] = 'Erreur : utilisateur non connecté.';
        header('Location: index.php');
        exit();
    }

    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['notification'] = 'Les mots de passe ne correspondent pas.';
        header('Location: index.php');
        exit();
    }

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    $sql = "UPDATE utilisateur SET nom=?, prenom=?, email=?";
    $params = [$nom, $prenom, $email];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", mdp=?";
        $params[] = $hashed_password;
    }

    $sql .= " WHERE id_user=?";
    $params[] = $user_id;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    if ($stmt->execute()) {
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['email'] = $email;
        $_SESSION['notification'] = 'Informations mises à jour avec succès.';
    } else {
        $_SESSION['notification'] = 'Erreur: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header('Location: index.php');
    exit();
}