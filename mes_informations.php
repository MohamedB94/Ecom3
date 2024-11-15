<?php
   session_start();
   require 'config.php';

   if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
       header('Location: Connexion.html');
       exit();
   }

   $message = '';

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $nouveau_mdp = htmlspecialchars($_POST['nouveau_mdp']);
       $confirmer_mdp = htmlspecialchars($_POST['confirmer_mdp']);

       if ($nouveau_mdp === $confirmer_mdp) {
           $mdp_hache = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
           $email = $_SESSION['email'];

           $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
           if ($conn->connect_error) {
               die("Connexion échouée: " . $conn->connect_error);
           }

           $sql = "UPDATE utilisateur SET mdp=? WHERE email=?";
           $stmt = $conn->prepare($sql);
           $stmt->bind_param("ss", $mdp_hache, $email);

           if ($stmt->execute()) {
               $message = "Informations mises à jour avec succès.";
           } else {
               $message = "Erreur lors de la mise à jour des informations.";
           }

           $stmt->close();
           $conn->close();
       } else {
           $message = "Les mots de passe ne correspondent pas.";
       }
   }
   ?>

   <!DOCTYPE html>
   <html lang="fr">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Mes Informations</title>
       <style>
           body {
               font-family: 'Arial', sans-serif;
               background-color: #f4f4f9;
               margin: 0;
           }

           header {
               background-color: #007bff;
               color: white;
               padding: 10px 20px;
               display: flex;
               justify-content: space-between;
               align-items: center;
           }

           header a {
               color: white;
               text-decoration: none;
               font-weight: bold;
               margin-right: 20px;
           }

           .container {
               background-color: white;
               padding: 40px;
               border-radius: 10px;
               box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
               width: 400px;
               margin: 20px auto;
               text-align: center;
           }

           h1 {
               color: #333;
               margin-bottom: 20px;
               font-size: 24px;
           }

           p {
               color: #555;
               margin-bottom: 10px;
           }

           form {
               margin-top: 20px;
           }

           label {
               display: block;
               margin-bottom: 5px;
               font-weight: bold;
               color: #333;
               text-align: left;
           }

           input[type="password"] {
               width: calc(100% - 20px);
               padding: 10px;
               margin-bottom: 15px;
               border: 1px solid #ccc;
               border-radius: 5px;
               font-size: 16px;
           }

           button {
               background-color: #007bff;
               color: white;
               border: none;
               padding: 10px 20px;
               border-radius: 5px;
               cursor: pointer;
               font-size: 16px;
               transition: background-color 0.3s ease;
           }

           button:hover {
               background-color: #0056b3;
           }

           .search-bar {
               display: flex;
               align-items: center;
           }

           .search-bar input[type="text"] {
               padding: 5px;
               border-radius: 5px;
               border: none;
               margin-right: 10px;
           }
       </style>
   </head>
   <body>
       <header>
           <div>
               <a href="index.php">Accueil</a>
               <a href="ordinateurs.php">Ordinateurs</a>
               <a href="composants.php">Composants</a>
               <a href="peripheriques.php">Périphériques</a>
               <a href="gaming.php">Gaming</a>
           </div>
           <div class="search-bar">
               <form id="searchForm" action="recherche.php" method="GET">
                   <input type="text" name="query" placeholder="Rechercher...">
               </form>
               <?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])): ?>
                   <a href="mes_informations.php">Mes Informations</a>
                   <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
               <?php else: ?>
                   <a href="Connexion.html" class="btn btn-primary">Connexion</a>
                   <a href="Inscription.html" class="btn btn-secondary">Inscription</a>
               <?php endif; ?>
               <a href="panier.php" class="btn btn-warning">Panier <span id="cart-count"><?= isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0 ?></span></a>
           </div>
       </header>
       <div class="container">
           <h1>Mes Informations</h1>
           <p>Nom: <?= $_SESSION['nom'] ?></p>
           <p>Prénom: <?= $_SESSION['prenom'] ?></p>
           <p>Email: <?= $_SESSION['email'] ?></p>

           <h2>Changer le mot de passe</h2>
           <form action="mes_informations.php" method="POST">
               <label for="nouveau_mdp">Nouveau mot de passe:</label>
               <input type="password" id="nouveau_mdp" name="nouveau_mdp" required>
               <label for="confirmer_mdp">Confirmer le mot de passe:</label>
               <input type="password" id="confirmer_mdp" name="confirmer_mdp" required>
               <button type="submit" class="btn btn-primary">Mettre à jour</button>
           </form>
           <p><?= $message ?></p>
       </div>
   </body>
   </html>