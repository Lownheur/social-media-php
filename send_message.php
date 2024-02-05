<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $receiver = $_POST['receiver']; // Use the correct key 'receiver'
    $sender = $_SESSION['username'];

    // Assuming you have a database connection
    $db = new PDO("mysql:host=localhost;dbname=AAAPROJETWEB", "root", "");

    $stmt = $db->prepare("INSERT INTO message (sender, receiver, messages) VALUES (?, ?, ?)");
    $stmt->execute([$sender, $receiver, $message]);

    // Redirect to a success page or display a success message
    header("Location: menu.php");
    exit;
} else {
    // Handle the case where the form is not submitted
    // Redirect or display an error message
    header("Location: error_page.php");
    exit;
}
?>