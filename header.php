

<h1 style="text-align:center;"> Meet you </h1>
<div>
<div style="display: flex; flex-direction: column; justify-content: center ;align-items: center; height: 10vh;">
    <nav class="nav-bloc">
        <a  href="/AAAPROJETWEB/menu.php">Menu</a>
        <a  href="/AAAPROJETWEB/message.php">Messagerie</a>
        <a  href="/AAAPROJETWEB/profile.php">Profile</a>
        
    </nav>
    <?php if (isset($_SESSION['username'])) : ?>
        <p>Bienvenue, <?php echo $_SESSION['username']; ?></p>
        <form action="" method="POST">
            <button type="submit" name="logout" style="display: inline;">
                Déconnexion
            </button>
        </form>
    <?php endif; ?>
</div>