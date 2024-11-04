<?php
session_start();
$_SESSION['action'] = 'ngofood'; // Set the session variable
header('Location: ngofood.php'); // Redirect to the desired page
exit();
?>