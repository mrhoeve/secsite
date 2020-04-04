<?php
include_once "includes/definitions.php";
setLevelToRoot(".");
include_once(dirname(__FILE__) .  "/includes/BodyAndHeader.php");

if($_SESSION['loggedin'] === TRUE) {
    echo "Welcome " . $user->get_firstName();
} else { ?>

    <div class="form">
        <h1>Login Form</h1>
        <form action="user/authenticate.php" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" id="password" autocomplete="none" placeholder="Password">
            <input type="submit">
        </form>
    </div>


    <?php
}
include_once "includes/Footer.php";
?>
