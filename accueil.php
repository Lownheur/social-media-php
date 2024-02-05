<?php
$page='accueil';
$page_selection=empty($_GET['page'])? $page : $_GET['page'];
?>

<?php



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>

<?php include 'menu.php'?>
    <?php if($page_selection=='annuel'):?>
                <section id="annuel">
                <?php include 'menu.php'?>
                </section>
            <?php endif ?>
            <?php if($page_selection=='mensuel'):?>
                <section id="mensuel">
                <?php include 'profile.php'?>
                </section>
            <?php endif ?>
            
    

</body>
</html>