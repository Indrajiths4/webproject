<?php
session_start();
$_SESSION['action'] = 'ngoorders'; // Set the session variable
header('Location: ngoorders.php'); // Redirect to the desired page
exit();
?>