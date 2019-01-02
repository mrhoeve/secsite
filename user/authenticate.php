<?php
session_start();
include_once "../includes/dbconnection.php";
include_once "user.php";

if (!isset($_POST['username'], $_POST['password'])) {
    // Could not get the data that should have been sent.
    die ('Username and/or password does not exist!');
}
$user = userHelper::loadUser($_POST['username'], $_POST['password']);
if($_SESSION['loggedin'] === TRUE) {
    echo "Welcome " . $user->get_firstName();
} else {
    echo 'Unknown username and/or password';
}
?>
