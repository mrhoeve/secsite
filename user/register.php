<?php
session_start();
include_once "../includes/dbconnection.php";
include_once "user.php";

//if (!isset($_POST['username'], $_POST['password'])) {
//    // Could not get the data that should have been sent.
//    die ('Username and/or password does not exist!');
//}
//$user = userHelper::loadUser($_POST['username'], $_POST['password']);
//if($_SESSION['loggedin'] === TRUE) {
//    echo "Welcome " . $user->get_firstName();
//} else {
//    echo 'Unknown username and/or password';
//}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login Form</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/progress.css">
</head>
<body>
<div class="form">
    <h1>Registration Form</h1>
    <form action="register.php" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="text" name="emailaddress" placeholder="Email address">
        <input type="password" name="password" id="password" autocomplete="none" placeholder="Password">
        <progress max="100" value="0" id="strength" class="strengthmeter"></progress>
        <input type="password" name="confirmpassword" id="confirmpassword" autocomplete="none" placeholder="Retype password">
        <input type="submit">
    </form>
    <p>Rules for passwords:</p>
    <ul>
        <li>At least two characters of a-z and/or 0-9</li>
        <li>Special characters: !@#$%^&*()</li>
        <li>Special characters: ~<>?</li>
        <li>Sufficient length of the password</li>
    </ul>
</div>
</body>

<script type="text/javascript">
    var pass = document.getElementById('password');
    pass.addEventListener('keyup', function() {
        var strengthBar = document.getElementById('strength');
        var strength = 0;
        var lenValue = 0;
        var passValue = pass.value;
        if(passValue.match(/[a-zA-Z0-9][a-zA-Z0-9]+/)) {
            strength +=10;
        }
        if(passValue.match(/[~<>?]+/)) {
            strength +=15;
        }
        if(passValue.match(/[!@#$%^&*()]+/)) {
            strength +=15;
        }
        if(passValue.length > 8) {
            lenValue = (passValue.length - 8) * 5;
            if(lenValue > 60)
                lenValue = 60;
            strength += lenValue;
        }
        // Remove the classes
        strengthBar.classList.remove('weak', 'lowmedium', 'highmedium', 'lowgood', 'good');
        // Apply correct class
        // We're not using switch but if, explanation here: https://stackoverflow.com/questions/6665997/switch-statement-for-greater-than-less-than
        if(strength < 21) {
            strengthBar.classList.add('weak');
        } else if(strength<41) {
            strengthBar.classList.add('lowmedium');
        } else if (strength<61) {
            strengthBar.classList.add('highmedium');
        } else if (strength<81) {
            strengthBar.classList.add('lowgood');
        } else {
            strengthBar.classList.add('good');
        }
        strengthBar.value = strength;
    })
</script>
</html>
