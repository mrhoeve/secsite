<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
include_once(dirname(__FILE__) . "/../includes/user.php");

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE) {
    header('Location: ..\index.php');
} else {
    // Start with a clean sheet
    $freshStart = true;
    $error = false;

    $user = new User();
    if (isset($_POST['username']) || isset($_POST['password'])) {
        $freshStart = false;
        $facode = isset($_POST['2facode']) ? $_POST['2facode'] : "";
        $user = UserHelper::authenticateAndLoginUser($_POST['username'], $_POST['password'], $facode);
        if ($user->isEmpty()) $error = true;
    }

    setLevelToRoot("..");
    include_once(dirname(__FILE__) . "/../includes/header.php");

    ?>
    <section id="loginsection">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4>Account Login</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!$freshStart && !$error) { ?>
                                <p>Welkom <?php echo $user->get_firstName() ?>.</br>U bent succesvol ingelogd.</p>
                                <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2">Terug naar
                                    index <span id="countdown"></span></a>
                            <?php } else {
                                // We have a fresh start, or we've got an error
                                if ($error) { ?>
                                    <p class="text-danger" id="error">Gebruikersnaam, wachtwoord of code onjuist.</p>
                                <?php } ?>
                                <form action="login.php" method="post">
                                    <div class="form-group">
                                        <label for="username">Gebruikersnaam</label>
                                        <input type="text" name="username" id="username" placeholder="Gebruikersnaam"
                                               class="form-control">
                                    </div>
                                    <a href="requestresetpassword.php" class="btn btn-outline-warning btn-block" id="forgottenpassword">Wachtwoord vergeten? Klik hier...</a>
                                    <div class="form-group">
                                        <label for="password">Wachtwoord</label>
                                        <input type="password" name="password" id="password" autocomplete="none"
                                               placeholder="Wachtwoord" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="2facode">2FA code (indien van toepassing)</label>
                                        <input type="text" name="2facode" id="2facode" autocomplete="none"
                                               placeholder="2FA code" class="form-control">
                                    </div>
                                    <input type="submit" value="Login" class="btn btn-primary btn-block" id="buttonLogin">
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    include_once(dirname(__FILE__) . "/../includes/jsscripts.php");
    if (!$freshStart && !$error) { ?>
        <script type="text/javascript">

            var seconds = 3;

            function countdown() {
                seconds = seconds - 1;
                if (seconds < 0) {
                    window.location = "<?php echo LEVEL ?>index.php";
                } else {
                    // Update remaining seconds
                    document.getElementById("countdown").innerHTML = "(" + seconds + "...)";
                    // Count down using javascript
                    window.setTimeout("countdown()", 1000);
                }
            }

            // Run countdown function
            countdown();

        </script>
        <?php
    }
}
include_once(dirname(__FILE__) . "/../includes/footer.php");
?>
