<?php session_start();

$loggedIn = isset($_SESSION['loggedin']) ? true : false;

if(!$loggedIn) {
    header('Location: login.php');
}

?>