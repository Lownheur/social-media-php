<?php
session_start();

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['username'])) {
    header('Location: connexion.php');
    exit;
}

// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AAAPROJETWEB";

try {
    // Créer une connexion à la base de données
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Définir le mode d'erreur de PDO sur Exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Échec de la connexion à la base de données: " . $e->getMessage();
    exit;
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: connexion.php');
    exit;
}



include 'header.php';

$username = $_SESSION['username'];

// Préparer la requête SQL pour récupérer la photo
$stmt = $db->prepare("SELECT photo1 FROM user WHERE username = ?");

// Exécuter la requête SQL
$stmt->execute([$username]);

// Récupérer le contenu binaire de l'image
$image_content = $stmt->fetchColumn();

// Vérification de l'existence de l'image
if ($image_content !== null) {

  // Encodage de l'image en base64
  $image_base64 = 'data:image/jpeg;base64,' . base64_encode($image_content);

  // Affichage de l'image dans une boîte
 echo '<div style="width: 200px; height: 200px; border: 1px solid #ccc; background-image: url(' . $image_base64 . '); background-size: cover; background-position: center;background-repeat: repeat;"></div>';
 echo '</div>';


} else {

  // Affichage d'un message si aucune image n'est trouvée
  echo "Aucune image n'a été trouvée pour cet utilisateur.";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    
</head>
<form action="" method="post" enctype="multipart/form-data">
        <input type="file" id="imageInput" name="image" accept="image/*">
        <input type="submit" value="Envoyer">
    </form>
    <img style="max-width: 400px; max-height: 400px;" id="croppedImage" src="" alt="Cropped Image">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.11/cropper.min.js"></script>
    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var image = document.getElementById('croppedImage');
                image.src = e.target.result;
                // Initialize cropper.js on the image
                var cropper = new Cropper(image, {
                    aspectRatio: 1, // Set the aspect ratio for cropping
                    viewMode: 1, // Set the view mode for cropping
                    autoCropArea: 1, // Set the auto crop area to 100% (to crop the entire image)
                    crop: function(event) {
                        // Update the cropped image result
                        var canvas = cropper.getCroppedCanvas({
                            width: 400, // Set the width of the cropped image to 400px
                            height: 400, // Set the height of the cropped image to 400px
                        });
                        // Replace the original image with the cropped image
                        image.src = canvas.toDataURL();
                    }
                });
            }
            reader.readAsDataURL(file);
        });
    </script>

<?php

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AAAPROJETWEB";

$db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

// Récupération du nom d'utilisateur de la session
$username = $_SESSION['username'];

// Traitement de l'envoi d'image
if (isset($_FILES['image'])) {
  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier si le fichier est vide
    if ($_FILES['image']['size'] === 0) {
        // Si le fichier est vide, ne pas faire de mise à jour
        echo "Le fichier est vide.";
        exit;
    }

    $image_name = $_FILES['image']['name'];
    $image_type = $_FILES['image']['type'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];

    // Vérifier la taille et le type de l'image
    if ($image_size > 2097152) {
        echo "L'image est trop grande.";
        exit;
    }

    if (!in_array($image_type, ['image/jpeg', 'image/png', 'image/gif'])) {
        echo "Le type d'image n'est pas valide.";
        exit;
    }

    // Lire le contenu binaire de l'image
    $image_content = file_get_contents($image_tmp_name);

    // Préparer la requête SQL pour la mise à jour
    $stmt = $db->prepare("UPDATE user SET photo1 = ? WHERE username = ?");

    // Exécuter la requête SQL
    $stmt->execute([$image_content, $username]);

        // Exécuter la requête SQL
    $stmt->execute([$image_content, $username]);

    echo "L'image a été téléchargée et mise à jour avec succès.";
    header("Location: menu.php");
    exit;
}
}
?>



    
</body>
</html>