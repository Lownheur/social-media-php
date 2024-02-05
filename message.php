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
include 'header.php';


$current_user = $_SESSION['username'];

$db = new PDO('mysql:host=localhost;dbname=AAAPROJETWEB;charset=utf8', 'root', '');




// Récupérer les utilisateurs associés dans la table message
$associated_users_query = "SELECT DISTINCT sender, receiver 
                           FROM message 
                           WHERE sender = '$current_user' OR receiver = '$current_user'";
$associated_users_result = $db->query($associated_users_query);




echo '<div style="width: 500px; height: 500px; border: 1px solid black; overflow: auto;text-align: center, justify-content: center; align-item: center;">';
// Parcourir chaque paire d'utilisateurs
while ($row = $associated_users_result->fetch(PDO::FETCH_ASSOC)) {
    $other_user = ($row['sender'] !== $current_user) ? $row['sender'] : $row['receiver'];
    $conversation = "Conversation with $other_user";
    $messages_query = "SELECT * FROM message WHERE (sender = '$current_user' AND receiver = '$other_user') OR (sender = '$other_user' AND receiver = '$current_user')";
    $messages_result = $db->query($messages_query);
    
    // Afficher la conversation

    
    echo "<div>$conversation</div>";
    
    echo '<div>';
    // Préparer la requête SQL pour récupérer la photo
    $stmt = $db->prepare("SELECT photo1 FROM user WHERE username = ?");

    // Exécuter la requête SQL
    $stmt->execute([$current_user]);

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


    
        // Afficher les messages     
    echo '<div style="width: 400px; height: 200px; border: 1px solid black; overflow: auto;" class="message-container">';
    while ($message = $messages_result->fetch(PDO::FETCH_ASSOC)) {
        echo "<p>From " . $message['sender'] . " : " . $message['messages'] . "</p>";
    }
    echo '</div>';

    
        

    // Ajouter un champ pour écrire un message à l'autre utilisateur
    echo "<form action='send_message.php' method='post'>";
    echo "<input type='text' name='message' placeholder='Write a message'>";
    // Ajouter un champ caché pour passer le destinataire du message
    echo "<input type='hidden' name='receiver' value='$other_user'>";
    // Ajouter un bouton pour envoyer le message
    echo "<input type='submit' name='submit_button' value='Send'>";
    echo "</form>";
}
echo '</div>';echo '</div>';


// Placer le code JavaScript à la fin du corps de la page
echo '<script>';
echo 'document.addEventListener("DOMContentLoaded", function() {';
echo '  var messageContainers = document.querySelectorAll(".message-container");';
echo '  messageContainers.forEach(function(container) {';
echo '    container.scrollTop = container.scrollHeight;';
echo '  });';
echo '});';
echo '</script>';
?>