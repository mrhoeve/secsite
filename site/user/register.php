<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
setLevelToRoot("..");
loadProgressCss();
include_once(dirname(__FILE__) . "/../includes/header.php");

// We can't create a user when someone is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: ..\index.php');
}

$freshStart = !(isset($_POST['username']) || isset($_POST['firstname']) || isset($_POST['emailaddress']) || isset($_POST['password']) || isset($_POST['confirmpassword']));

$username = isset($_POST['username']) ? $_POST['username'] : '';
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$emailaddress = isset($_POST['emailaddress']) ? $_POST['emailaddress'] : '';
$passone = isset($_POST['password']) ? $_POST['password'] : '';
$passtwo = isset($_POST['confirmpassword']) ? $_POST['confirmpassword'] : '';

$emailaddressValid = UserHelper::isEmailAddressValid($emailaddress);
$passwordsValid = UserHelper::arePasswordsValid($passone, $passtwo);
$usernameValid = UserHelper::isUsernameValid($username);

$error = !($usernameValid && $passwordsValid && $emailaddressValid && !empty($firstname));
$techError = false;

if (!$freshStart && !$error) {
    $createUser = new User($username, $firstname, $emailaddress, false, null, array(), false, false, null);
    debugToConsole('New user created: ' . $createUser->toString());
    $newUser = UserHelper::saveUser($createUser, $passone, false);
    if ($newUser->isEmpty()) $techError = true;
} ?>

    <section id="createaccountsection">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4>Account aanmaken</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!$freshStart && !$error && !$techError) { ?>
                                <p>Uw account is aangemaakt, u kunt er nu mee inloggen.</p>
                                <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2">Terug naar
                                    index</a>
                            <?php } else {
                                // We have a fresh start, or we've got an error
                                if (!$freshStart && $error) { ?>
                                    <p class="text-danger">Uw account kan niet worden aangemaakt vanwege (één van) deze
                                        redenen:</p>
                                    <ul class="text-danger">
                                        <li>Gebruikersnaam bestaat reeds.</li>
                                        <li>Ongeldig of reeds gebruikt emailadres.</li>
                                        <li>Wachtwoorden komen niet overeen of voldoen niet aan de complexiteitseisen.
                                        </li>
                                    </ul>
                                <?php }
                                if ($techError) { ?>
                                    <p class="text-danger">Er is een technische fout opgetreden bij het aanmaken van uw
                                        account.</br>
                                        Probeer het later nogmaals, of neem contact op met de beheerder.</p>
                                <?php } ?>
                                <form action="register.php" method="post">
                                    <div class="form-group">
                                        <label for="username">Gebruikersnaam</label>
                                        <input type="text" name="username" id="username" value="<?php echo $username ?>"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Voornaam</label>
                                        <input type="text" name="firstname" id="firstname"
                                               value="<?php echo $firstname ?>" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Emailadres</label>
                                        <input type="text" name="emailaddress" id="emailaddress"
                                               value="<?php echo $emailaddress ?>" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Wachtwoord</label>
                                        <input type="password" name="password" id="password" autocomplete="none"
                                               placeholder="Wachtwoord" class="form-control">
                                    </div>
                                    <div class="form-group progress-form-group">
                                        <progress max="100" value="0" id="strength" class="strengthmeter"></progress>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Bevestig uw wachtwoord</label>
                                        <input type="password" name="confirmpassword" id="confirmpassword"
                                               autocomplete="none"
                                               placeholder="Bevestig uw wachtwoord" class="form-control">
                                    </div>
                                    <input type="submit" value="Account aanmaken" class="btn btn-primary btn-block">
                                </form> <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
include_once(dirname(__FILE__) . "/../includes/jsscripts.php");
?>
    <script src="../js/calcstrength.js"></script>
    <script>
        $(document).ready(function () {
            $usernamett = "Uw gebruikersnaam moet minimaal 5 tekens lang zijn.";
            $('#username').tooltip({'trigger': 'focus', 'placement': 'top', 'html': true, 'title': $usernamett});

            $passwordtt = "<p><u>Wachtwoord complexiteitseisen</u>:</p><ul class=\"text-left\"><li>Tenminste 2 tekens van a-z en/of 0-9</li><li>Eén of meer speciale karakters: !@#$%^&*()~<>?</li><li>Minimaal 14 tekens lang</li></ul>";
            $('#password').tooltip({'trigger': 'focus', 'placement': 'top', 'html': true, 'title': $passwordtt});
        });

    </script>
<?php
include_once(dirname(__FILE__) . "/../includes/footer.php");
