<?php

session_start();
// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['username'])) {
    header('Location: connexion.php');
    exit;
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: connexion.php');
    exit;
}


// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AAAPROJETWEB";



include 'header.php';
?>
<?php

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "AAAPROJETWEB");

$query = "DELETE FROM matche
WHERE (asker_user, waiting_user) IN (
  SELECT asker_user, waiting_user
  FROM (SELECT asker_user, waiting_user FROM matche) AS tmp
  GROUP BY asker_user, waiting_user
  HAVING COUNT(*) > 1
);";

// Exécution de la requête
$mysqli->query($query);

// Fermeture de la connexion à la base de données
$mysqli->close();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php
// Get the username of the currently logged in user from the session
$current_user = $_SESSION['username'];

// Establish the database connection
$db = new PDO('mysql:host=localhost;dbname=AAAPROJETWEB;charset=utf8', 'root', '');
$stmt = $db->prepare('SELECT u.username 
    FROM user u
    LEFT JOIN matche m1 ON u.username = m1.waiting_user AND m1.asker_user = ?
    LEFT JOIN matche m2 ON u.username = m2.asker_user AND m2.waiting_user = ?
    WHERE u.username <> ? AND m1.asker_user IS NULL AND m2.waiting_user IS NULL');
$stmt->execute([$current_user, $current_user, $current_user]);
$final_usernames = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Utiliser $final_usernames pour afficher la liste des utilisateurs disponibles pour faire un match

ob_start(); // Start output buffering


// Display the usernames and "Demand a Match" buttons within a form
// Display the usernames and "Demand a Match" buttons within a form
echo '<div style="display: flex; flex-direction: row; justify-content: space-between; margin:50px;">';
echo '<div style="width: 300px; height: 500px; border: 1px solid black; overflow: auto;text-align: center">';
echo '<form method="post">';
echo "Trouver la personne qui vous convient :  ";
foreach ($final_usernames as $username) {
    if ($username !== $current_user) {
        $check_query = "SELECT * FROM message WHERE (sender = '$username' AND receiver = '$current_user') OR (sender = '$current_user' AND receiver = '$username')";
        $result = $db->query($check_query);
        if ($result->rowCount() == 0) {
            echo '<div >'; // Add float left and padding
            echo $username . ' <button type="submit" name="match_username" value="'.$username.'">Demand a Match</button>';
            
            // Préparer la requête SQL pour récupérer la photo
            $stmt = $db->prepare("SELECT photo1 FROM user WHERE username = ?");

            // Exécuter la requête SQL
            $stmt->execute([$username]);

            // Récupérer le contenu binaire de l'image
            $image_content = $stmt->fetchColumn();

            // Vérification de l'existence de l'image
            if ($image_content !== false) {
                // Encodage de l'image en base64
                $image_base64 = 'data:image/jpeg;base64,' . base64_encode($image_content);

                // Affichage de l'image dans une balise img
                echo '<div style="margin : 0 auto;width: 200px; height: 200px; border: 1px solid #ccc; background-image: url(' . $image_base64 . '); background-size: cover; background-position: center;background-repeat: repeat;"></div>';
                echo '</div>';
            } else {
                // Affichage d'un message si aucune image n'est trouvée
                echo "Aucune image n'a été trouvée pour cet utilisateur.";
            }

            
        }
    }
}
echo '</form>';
echo '</div>';


$stmt_asked_matches = $db->prepare('SELECT waiting_user FROM matche WHERE asker_user = ?');
$stmt_asked_matches->execute([$current_user]);
$asked_matches = $stmt_asked_matches->fetchAll(PDO::FETCH_COLUMN);
// Afficher les usernames des correspondants pour les matchs demandés par l'utilisateur

echo '<div style="width: 300px; height: 500px; border: 1px solid black; overflow: auto;text-align: center">';
echo "Matchs demandés par vous : <br>";
foreach ($asked_matches as $username) {
    echo "<div>";
    echo $username . "<br>";
    // Préparer la requête SQL pour récupérer la photo
    $stmt = $db->prepare("SELECT photo1 FROM user WHERE username = ?");

    // Exécuter la requête SQL
    $stmt->execute([$username]);

    // Récupérer le contenu binaire de l'image
    $image_content = $stmt->fetchColumn();

    // Vérification de l'existence de l'image
    if ($image_content !== false) {
        // Encodage de l'image en base64
        $image_base64 = 'data:image/jpeg;base64,' . base64_encode($image_content);

        // Affichage de l'image dans une balise img
        echo '<div style="margin : 0 auto; width: 200px; height: 200px; border: 1px solid #ccc; background-image: url(' . $image_base64 . '); background-size: cover; background-position: center;background-repeat: repeat;"></div>';
        echo '</div>';
    } else {
        // Affichage d'un message si aucune image n'est trouvée
        echo "Aucune image n'a été trouvée pour cet utilisateur.";
    }
}echo "</div>";

$stmt_waiting_matches = $db->prepare('SELECT asker_user FROM matche WHERE waiting_user = ?');
$stmt_waiting_matches->execute([$current_user]);
$waiting_matches = $stmt_waiting_matches->fetchAll(PDO::FETCH_COLUMN);



// Récupérer le nom d'utilisateur de la session
$username = $_SESSION['username'];


echo '<div style="width: 300px; height: 500px; border: 1px solid black; overflow: auto;text-align: center">';
// Afficher les usernames des correspondants pour les matchs auxquels l'utilisateur attend une réponse
echo " Match en attente de réponse:";
foreach ($waiting_matches as $match_username) {
    // Afficher le nom d'utilisateur et les boutons d'acceptation et de suppression
    echo $match_username . '<form id="form_accept" method="post" action="accepter.php">
    <input type="hidden" name="match_username" value="' . $match_username . '">
    <button type="submit">Accepter le match</button>
    </form>';

    echo '<form id="form_refuse" method="post" action="refuser.php">
    <input type="hidden" name="match_username" value="' . $match_username . '">
    <button type="submit">Refuser le match</button>
    </form><br>';
}
echo '</div>';


// Récupérer les utilisateurs associés dans la table message
$associated_users_query = "SELECT DISTINCT sender, receiver 
                           FROM message 
                           WHERE sender = '$current_user' OR receiver = '$current_user'";
$associated_users_result = $db->query($associated_users_query);
/* echo '<div style="width: 300px; height: 500px; border: 1px solid black; overflow: auto;text-align: center">';
// Parcourir chaque paire d'utilisateurs
while ($row = $associated_users_result->fetch(PDO::FETCH_ASSOC)) {
    $other_user = ($row['sender'] !== $current_user) ? $row['sender'] : $row['receiver'];
    $conversation = "Conversation with $other_user";
    $messages_query = "SELECT * FROM message WHERE (sender = '$current_user' AND receiver = '$other_user') OR (sender = '$other_user' AND receiver = '$current_user')";
    $messages_result = $db->query($messages_query);
    
    // Afficher la conversation
    echo "<div>$conversation</div>";
    echo '<div>';

    // Afficher les messages
    while ($message = $messages_result->fetch(PDO::FETCH_ASSOC)) {
        echo "<p>From: " . $message['sender'] . "</p>";
        echo "<p>Message: " . $message['messages'] . "</p>";
    }
    
    

    // Ajouter un champ pour écrire un message à l'autre utilisateur
    echo "<form action='send_message.php' method='post'>";
    echo "<input type='text' name='message' placeholder='Write a message'>";
    // Ajouter un champ caché pour passer le destinataire du message
    echo "<input type='hidden' name='receiver' value='$other_user'>";
    // Ajouter un bouton pour envoyer le message
    echo "<input type='submit' name='submit_button' value='Send'>";
    echo "</form>";
}
echo '</div>';*/
echo '</div>';


echo "</div id='ajax' style='border : 1px solid black;'> <br>";



    if (isset($_POST['match_username'])) {
        // Enregistrer la correspondance utilisateur dans la base de données
        $match_username = $_POST['match_username'];

        // Établir la connexion à la base de données
        $db = new PDO('mysql:host=localhost;dbname=AAAPROJETWEB;charset=utf8', 'root', '');

        // Préparer et exécuter la requête d'insertion
        $stmt = $db->prepare('INSERT INTO matche (asker_user, waiting_user) VALUES (?, ?)');
        $stmt->execute([$current_user, $match_username]);

        // Rediriger l'utilisateur vers une autre page
        header('location: menu.php');
        exit;
    }
    ob_end_flush();
?>
<script>

// JavaScript code to reload the entire PHP page every half second
$(document).ready(function() {
  setInterval(function() {
    location.reload();
    console.log('Reloading the page...');
  }, 100000); // Interval in milliseconds (500ms = 0.5 seconds)
});
</script>

</body>
</html>