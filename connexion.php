<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>
<?php
$errorMessage = "hello";



 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "AAAPROJETWEB";
 $conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        if (isset($_POST['login-submit'])) {
             
            // Vérification des informations de connexion
            loginUser();


            
        }
        if (isset($_POST['register-submit'])) {
            insertUser();
        }
    }
}

//appeler lors de la création de compte 
function insertUser()
{
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "AAAPROJETWEB";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérification de l'username
    $newUsername = mysqli_real_escape_string($conn, $_POST['new-username']);
    $usernameQuery = "SELECT * FROM user WHERE username = '$newUsername'";
    $resultUsername = $conn->query($usernameQuery);

    if ($resultUsername->num_rows > 0) {
        
        $errorMessage = "Cet username existe déjà dans la base de données.";
        }
    

    // Vérification de l'email
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $emailQuery = "SELECT * FROM user WHERE email = '$email'";
    $resultEmail = $conn->query($emailQuery);

    if ($resultEmail->num_rows > 0) {
        $errorMessage = "Cet email existe déjà dans la base de données.";
        return;
    }

    // Insertion de l'utilisateur si l'username et l'email sont uniques
    $newPassword = mysqli_real_escape_string($conn, $_POST['new-password']);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $insertQuery = "INSERT INTO user (username, password, email) VALUES ('$newUsername', '$hashedPassword', '$email')";
    $conn->query($insertQuery);

    echo "Utilisateur inséré avec succès!";
}


function loginUser()
{
    
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "AAAPROJETWEB";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Récupération des données du formulaire
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Requête SQL pour récupérer l'utilisateur par son username
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = $conn->query($query);


    
     // 1. L'utilisateur existe dans la base de données
     if ($result->num_rows === 0) {
        echo "<div id='login_error_message' class='error'>User does not exist.</div>";
        $loginError = "User does not exist.";
        return;
        
        
    }
    // Récupération du mot de passe hashé de l'utilisateur
    $user = $result->fetch_assoc();
    $hashedPassword = $user['password'];
    

    // Vérification du mot de passe
    if (password_verify($password, $hashedPassword)) 
    {
        echo "Connexion réussie !";
        session_start();
        $_SESSION['username']= $_POST['username']; // Remplacez $user_id par l'identifiant de l'utilisateur

    // Redirection vers la page user-connected.php
        header('Location: accueil.php');
        exit;
        // Faites ici les actions à effectuer après la connexion réussie
    } else {
        echo "<div id='login_error_message' class='error'>Password incorrect</div>";
    }
    

    
}



?>
    <style>.error {
    color: red;
    position:fixed;
    top:200px;
    bottom:0;
    /* Add any other styles for error messages */
}</style>
    

    <section id="connexionform" class="login-form">
       <h1> Connexion :</h1>
        <form  method="post">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username">
            <br>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password">
            <br>
            <input type="submit" name="login-submit" value="Connexion">
            
        </form>
        
        
        </div>
        
        <br>
        
    </section>

    <section id="registerform" class = "register-form" >
        <h1>Création de compte : </h1>
        <form  method="post" >
            <label for="new-username">Nom d'utilisateur :</label>
            <input type="text" id="new-username" name="new-username" required>
            <br>
            <label for="new-password">Mot de passe :</label>
            
            <input type="password" id="new-password" name="new-password" onkeyup="validatePassword()" required>
            <span id="password-error-message" style="color: red;"></span>
            <br>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            <br>
            <input type="submit" name="register-submit" value="Créer le compte" >
            
        </form>
        <div id="errormessageregister" style="color:red;">zfjkfz</div>
        
        <br>
        
    </section>

    
       
    <script>
       
        function validatePassword()
         {
            const passwordInput = document.getElementById('new-password');
            const password = passwordInput.value;

            const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;
            const isValidPassword = passwordPattern.test(password);

            const errorMessage = document.getElementById('password-error-message');

            if (password.length > 0) {
                if (!isValidPassword) {
                errorMessage.textContent = 'Le mot de passe doit contenir au moins 8 caractères et avoir à la fois des lettres et des chiffres.';
                } else {
                errorMessage.textContent = '';
                }
            } else {
                errorMessage.textContent = '';
            }
        }
        


    </script>

</body>
</html>