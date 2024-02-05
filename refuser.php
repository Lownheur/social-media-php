<?php
    // Démarrez la session
session_start();

// Récupérez l'username de la session
$username = $_SESSION['username'];

// Connexion à la base de données
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "AAAPROJETWEB";

// Créer une connexion
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['match_username'])) {
        $match_username = $_POST['match_username'];

        // Requête de suppression avec les critères demandés
        $sql = "DELETE FROM matche WHERE waiting_user = '$username' AND asker_user = '$match_username'";

        // Exécuter la requête de suppression
        if ($conn->query($sql) === TRUE) {
            echo "Suppression réussie";
        } else {
            echo "Erreur de suppression: " . $conn->error;
        }
    }
}

// Exécuter la requête de suppression
if ($conn->query($sql) === TRUE) {
    echo "Suppression réussie";
} else {
    echo "Erreur de suppression: " . $conn->error;
}
header("Location: menu.php");
// Fermer la connexion
$conn->close();
exit;
?>