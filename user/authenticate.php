<?php
session_start();
include_once "../includes/dbconnection.php";

if(!isset($pdoread)) {
    die('Failed to setup a database connection');
}
// Now we check if the data was submitted, isset will check if the data exists.
if (!isset($_POST['username'], $_POST['password'])) {
    // Could not get the data that should have been sent.
    die ('Username and/or password does not exist!');
}
// Prepare our SQL
if ($stmt = $pdoread->prepare('SELECT userid, password FROM user WHERE userid = :userid')) {
    // Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
    $stmt->bindParam(':userid', $_POST['username']);
    $stmt->execute();
    if($stmt->rowCount() === 1) {
        $result = $stmt->fetch();
        // Account exists, now we verify the password.
        if (password_verify($_POST['password'], $result['password'])) {
            // Verification success! User has loggedin!
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            echo 'Welcome ' . $_SESSION['name'] . '!';
        } else {
            echo 'Incorrect username and/or password!';
        }
    } else {
        echo 'Incorrect username and/or password!';
    }
} else {
    echo 'Could not prepare statement!';
}
?>
