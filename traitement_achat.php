<?php
session_start();

// Ici, vous devez ajouter la logique pour traiter le paiement
// Par exemple, vérifier les informations de la carte, etc.

// Après le traitement du paiement, redirigez vers la page de remerciement
header('Location: merci.php');
exit();
?>