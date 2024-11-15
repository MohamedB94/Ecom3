<?php
   session_start(); // mes info se mettent que quand l'user est co
   require 'config.php';

   if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
       header('Location: Connexion.html');
       exit();
   }
// var message qui se remplira apres 
   $message = '';
// changement mdp
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
               max-width: 600px;
               margin: 0 auto;
               padding: 20px;
               background-color: #f8f9fa;
               border-radius: 8px;
               box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
           }

           h1, h2 {
               text-align: center;
               color: #333;
           }

           p {
               font-size: 16px;
               color: #555;
           }

           form {
               margin-top: 20px;
           }

           label {
               display: block;
               margin-bottom: 5px;
               font-weight: bold;
           }

           input[type="text"],
           input[type="email"],
           input[type="password"] {
               width: 100%;
               padding: 10px;
               margin-bottom: 15px;
               border: 1px solid #ccc;
               border-radius: 4px;
           }

           button {
               width: 100%;
               padding: 10px;
               background-color: #007bff;
               color: white;
               border: none;
               border-radius: 4px;
               cursor: pointer;
               font-size: 16px;
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
       <header> <!-- header a revoir mais fonctionnel -->
           <div>
               <a href="index.php">Accueil</a>
               <a href="#ordinateurs.php">Ordinateurs</a>
               <a href="#composants.php">Composants</a>
               <a href="#peripheriques.php">Périphériques</a>
               <a href="#gaming.php">Gaming</a>
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
       </header> <!-- remplissement du mes informations via la session  -->
       <div class="container">
           <h1>Mes Informations</h1>
           <p>Nom: <?= $_SESSION['nom'] ?></p>
           <p>Prénom: <?= $_SESSION['prenom'] ?></p>
           <p>Email: <?= $_SESSION['email'] ?></p>
           <!--  form pour changer le mdp-->
           <h2>Changer le mot de passe</h2>
           <form action="update_user.php" method="POST">
               <label for="nom">Nom :</label>
               <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_SESSION['nom']) ?>" required>

               <label for="prenom">Prénom :</label>
               <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_SESSION['prenom']) ?>" required>

               <label for="email">Email :</label>
               <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" required>

               <label for="password">Nouveau mot de passe :</label>
               <input type="password" id="password" name="password">

               <label for="confirm_password">Confirmer le mot de passe :</label>
               <input type="password" id="confirm_password" name="confirm_password">

               <button type="submit">Mettre à jour</button>
           </form>
           <!--  reponse du message en haut soit sa passe soit sa casse(sa met pas a jour)-->
           <p><?= $message ?></p>
       </div>
   </body>
   </html>