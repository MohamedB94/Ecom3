<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de configuration
require 'config.php';

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérifier si une recherche a été effectuée
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Préparer la requête SQL pour obtenir les produits
if ($query) {
    $sql = "SELECT * FROM modele WHERE Nom LIKE '%" . $conn->real_escape_string($query) . "%'";
} else {
    $sql = "SELECT * FROM modele";
}

$result = $conn->query($sql);

// Afficher les résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculer la moyenne des notes
        $id_modele = $row['id_modele'];
        $sql_notes = "SELECT AVG(note) as moyenne FROM notes WHERE id_modele = $id_modele";
        $result_notes = $conn->query($sql_notes);
        $moyenne = $result_notes->fetch_assoc()['moyenne'];
        $moyenne = $moyenne ? round($moyenne, 1) : 'Pas de notes';

        echo "<div class='col-md-4'>";
        echo "<div class='card mb-4 shadow-sm'>";
        echo "<img src='" . $row['Image'] . "' class='card-img-top' alt='" . $row['Nom'] . "'>";
        echo "<div class='card-body'>";
        echo "<p class='card-text'>" . $row['Nom'] . "</p>";
        echo "<p class='card-text'>" . $row['Description'] . "</p>";
        echo "<p class='card-text'>Note moyenne: $moyenne étoiles</p>";
        echo "<form action='noter_produit.php' method='POST'>";
        echo "<input type='hidden' name='produit_id' value='" . $row['id_modele'] . "'>";
        echo "<label for='note'>Note :</label>";
        echo "<select name='note' id='note' required>";
        echo "<option value='1'>1 étoile</option>";
        echo "<option value='2'>2 étoiles</option>";
        echo "<option value='3'>3 étoiles</option>";
        echo "<option value='4'>4 étoiles</option>";
        echo "<option value='5'>5 étoiles</option>";
        echo "</select>";
        echo "<button type='submit'>Noter</button>";
        echo "</form>";
        echo "<button class='btn btn-primary' onclick='openModal(" . $row['id_modele'] . ")'>Noter</button>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p class='text-center'>Aucun produit trouvé</p>";
}
$conn->close();
?>

<!-- Fenêtre modale pour noter un produit -->
<div id="noteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Quelle note mettez-vous ?</h2>
        <form id="noteForm" action="noter_produit.php" method="POST">
            <input type="hidden" name="produit_id" id="modalProduitId">
            <label for="modalNote">Note :</label>
            <select name="note" id="modalNote" required>
                <option value="1">1 étoile</option>
                <option value="2">2 étoiles</option>
                <option value="3">3 étoiles</option>
                <option value="4">4 étoiles</option>
                <option value="5">5 étoiles</option>
            </select>
            <button type="submit">Soumettre</button>
        </form>
    </div>
</div>

<script>
function openModal(produitId) {
    <?php if (isset($_SESSION['user_id'])): ?>
        document.getElementById('modalProduitId').value = produitId;
        document.getElementById('noteModal').style.display = 'block';
    <?php else: ?>
        alert('Veuillez vous connecter pour noter ce produit.');
    <?php endif; ?>
}

document.querySelector('.close').onclick = function() {
    document.getElementById('noteModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('noteModal')) {
        document.getElementById('noteModal').style.display = 'none';
    }
}
</script>

<style>
/* Style pour la fenêtre modale */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>