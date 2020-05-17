<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
use Psr\Log\LogLevel;

setLevelToRoot("..");
include_once(dirname(__FILE__) . "/../includes/header.php");

if(!$user->hasPermission(PERMISSION_CREATE_ACCOUNT)) {
    header('Location: selectuser.php');
}

$freshStart = !(isset($_POST['username']) || isset($_POST['firstname']) || isset($_POST['emailaddress']) || isset($_POST['password']));

$username = isset($_POST['username']) ? $_POST['username'] : '';
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$emailaddress = isset($_POST['emailaddress']) ? $_POST['emailaddress'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$emailaddressValid = UserHelper::isEmailAddressValid($emailaddress);
$usernameValid = UserHelper::isUsernameValid($username);

$error = !($usernameValid && $emailaddressValid && !empty($firstname));
$techError = false;

if (!$freshStart && !$error) {
    $createUser = new User($username, $firstname, $emailaddress, false, null, array(), false, false, null);
    $log->log(LogLevel::INFO, 'New user ' . $createUser->toString() . '  created by user ' . $user->get_username());
    $newUser = UserHelper::saveUser($createUser, $password, false);
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
                                <p id="success">Account is aangemaakt.</p>
                                <a href="<?php echo LEVEL ?>\admin\selectuser.php" class="btn btn-success btn-block mt-2" id="successbutton">Terug naar
                                    index</a>
                            <?php } else {
                                // We have a fresh start, or we've got an error
                                if (!$freshStart && $error) { ?>
                                    <div class="alert alert-danger" id="error">
                                        <p>Het account kan niet worden aangemaakt vanwege (één van) deze
                                            redenen:</p>
                                        <ul>
                                            <li>Gebruikersnaam bestaat reeds.</li>
                                            <li>Ongeldig of reeds gebruikt emailadres.</li>
                                            </li>
                                        </ul>
                                    </div>
                                <?php }
                                if ($techError) { ?>
                                    <p class="alert alert-danger">Er is een technische fout opgetreden bij het aanmaken van uw
                                        account.</br>
                                        Probeer het later nogmaals, of neem contact op met de beheerder.</p>
                                <?php } ?>
                                <form action="createnewuser.php" method="post">
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
                                    <input type="submit" value="Account aanmaken" class="btn btn-primary btn-block" id="submit">
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
            $usernamett = "Gebruikersnaam moet minimaal 5 tekens lang zijn.";
            $('#username').tooltip({'trigger': 'focus', 'placement': 'top', 'html': true, 'title': $usernamett});
        });

    </script>
<?php
include_once(dirname(__FILE__) . "/../includes/footer.php");
