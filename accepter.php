<?php
session_start();
// Connexion à la base de données (remplacez les valeurs par les vôtres)
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "AAAPROJETWEB";

// Créer une connexion
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Récupérez l'username de la session
$username = $_SESSION['username'];

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['match_username'])) {
        $match_username = $_POST['match_username'];

                // Insérer l'username et le match user dans une table message
        $sql = "INSERT INTO message (sender, receiver, messages) VALUES ('$username', '$match_username', 'nouveau match');";
        // Supprimer l'enregistrement correspondant dans la table matche
        $sql .= "DELETE FROM matche WHERE waiting_user = '$username' AND asker_user = '$match_username';";

        // Exécuter les deux requêtes simultanément
        if ($conn->multi_query($sql) === TRUE) {
            echo "Match accepté";
        } else {
            echo "Erreur lors de l'acceptation du match: " . $conn->error;
        }
        header("Location: menu.php");
        exit;
    }
}

?>