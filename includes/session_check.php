<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['User_type'])) {
    
    header('Location: ../index.php');
    exit();
}
?>
