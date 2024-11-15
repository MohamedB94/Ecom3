<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
    // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: Connexion.html');
    exit();
}

// Redirigez vers Stripe si l'utilisateur est connecté
header('Location: https://buy.stripe.com/test_00g29f0bGe8ffYs3cc');
exit();
