<?php
session_start();
$_SESSION['action'] = 'ngocart'; // Set the session variable
header('Location: ngocart.php'); // Redirect to the desired page
exit();
?>