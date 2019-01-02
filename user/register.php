<?php
session_start();
include_once "../includes/dbconnection.php";
include_once "user.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Form</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/progress.css">
</head>
<body>
<div class="form">
    <h1>Registration Form</h1>
<?php
$username='';
$firstname='';
$emailaddress='';
$showForm = true;
$errMessage = '';

if (isset($_POST['formsubmitted'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : ''; // There are no requirements for the first name ;-)
    $emailaddress = isset($_POST['emailaddress']) ? $_POST['emailaddress'] : '';
    $passone =  isset($_POST['password']) ? $_POST['password'] : '';
    $passtwo =  isset($_POST['confirmpassword']) ? $_POST['confirmpassword'] : '';

    $emailaddressValid = userHelper::isEmailAddressValid($emailaddress);
    $passwordsValid = userHelper::arePasswordsValid($passone, $passtwo);
    $usernameValid = userHelper::isUsernameValid($username);
    $showForm = !($emailaddressValid && $passwordsValid && $usernameValid);
    if($showForm === true) {
        $errMessage = 'ERROR: Either the data entered doesn\'t fullfill the requirements, or the username and/or email already have been taken.';
    }
}
if($showForm === true) {
    if($errMessage !== '')
    {
        echo "<p>" . $errMessage . "</p>";
    }
?>
    <form action="register.php" method="post">
        <input type="text" name="username" value="<?php echo $username ?>" placeholder="Username">
        <input type="text" name="firstname" value="<?php echo $firstname ?>" placeholder="First name">
        <input type="text" name="emailaddress" value="<?php echo $emailaddress ?>" placeholder="Email address">
        <input type="password" name="password" id="password" autocomplete="none" placeholder="Password">
        <progress max="100" value="0" id="strength" class="strengthmeter"></progress>
        <input type="password" name="confirmpassword" id="confirmpassword" autocomplete="none"
               placeholder="Retype password">
        <input type="hidden" name="formsubmitted" value="true">
        <input type="submit">
    </form>
    <p>The username must contain at least 5 alphanumeric characters</p>
    <p>Rules for passwords:</p>
    <ul>
        <li>At least two characters of a-z and/or 0-9</li>
        <li>Special characters: !@#$%^&*()~<>?</li>
        <li>Sufficient length of the password (min. 14 characters)</li>
    </ul>
    <?php
    } else { echo "<p>Save statement goes here!</p>"; ?>
<!-- Insert proper statement here -->
    <?php } ?>
</div>
</body>

<script src="../js/calcstrength.js"></script>
</html>
