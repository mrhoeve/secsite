<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
use Psr\Log\LogLevel;
setLevelToRoot("..");
loadProgressCss();
changeOwnPassword();
include_once(dirname(__FILE__) . "/../includes/header.php");

// We can't change a password if someone isn't logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] === true) {
    header('Location: ..\index.php');
}

$freshStart = !(isset($_POST['username']) || isset($_POST['currentpassword']) || isset($_POST['2facode']) || isset($_POST['password']) || isset($_POST['confirmpassword']));

$username = isset($_POST['username']) ? htmlspecialchars(filter_var(strip_tags($_POST['username']))) : '';
$currentpassword = isset($_POST['currentpassword']) ? htmlspecialchars(filter_var(strip_tags($_POST['currentpassword']))) : '';
$facode = isset($_POST['2facode']) ? htmlspecialchars(filter_var(strip_tags($_POST['2facode']))) : '';
$passone = isset($_POST['password']) ? htmlspecialchars(filter_var(strip_tags($_POST['password']))) : '';
$passtwo = isset($_POST['confirmpassword']) ? htmlspecialchars(filter_var(strip_tags($_POST['confirmpassword']))) : '';
$error = false;

if(!$freshStart) {
    $currentUser = UserHelper::authenticateUserWithoutLoggingIn($username, $currentpassword, $facode);
    $userError = $currentUser->isEmpty() || $currentUser->get_username() != $user->get_username();

    $passwordsValid = UserHelper::arePasswordsValid($passone, $passtwo);

    $error = $userError || !$passwordsValid;
    $techError = false;
}

if (!empty($_POST['CSRFToken'])) {
    if (!hash_equals($_SESSION['token'], $_POST['CSRFToken'])) {
        $error = true;
    }
}

if (!$freshStart && !$error) {
    $log->log(LogLevel::NOTICE, 'Password changed of user ' . $currentUser->get_username());
    UserHelper::saveUser($currentUser, $passone, true);
    $user = UserHelper::authenticateAndLoginUser($currentUser->get_username(), $passone, $facode);
    if ($user->isEmpty()) $techError = true;
} ?>

    <section id="changepasswordsection">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4>Wijzigen wachtwoord</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!$freshStart && !$error && !$techError) { ?>
                                <p class="alert alert-success" id="success">Uw wachtwoord is gewijzigd...</p>
                                <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2" id="successbutton">Terug naar
                                    index</a>
                            <?php } else {
                                // We have a fresh start, or we've got an error
                                if (!$freshStart && $error) { ?>
                                    <p class="alert alert-danger" id="error">Uw wachtwoord kan niet worden gewijzigd omdat de wachtwoorden niet overeen komen of niet voldoen aan de complexiteitseisen.</p>
                                <?php }
                                if ($techError) { ?>
                                    <p class="alert alert-danger" id="technicalerror">Er is een technische fout opgetreden bij het aanmaken van uw
                                        account.</br>
                                        Probeer het later nogmaals, of neem contact op met de beheerder.</p>
                                <?php } ?>
                                <?php if($user->mustChangePasswordOnNextLogon()) { ?>
                                    <p class="alert alert-info" id="mustchangenotification">U moet uw wachtwoord verplicht wijzigen voordat u verder kunt.</p>
                                <?php } ?>
                                <form action="changepassword.php" method="post">
                                    <input type="hidden" name="username" value="<?php echo $user->get_username() ?>">
                                    <input type="hidden" name="CSRFToken" value="<?php echo $CSRFToken ?>">
                                    <div class="form-group">
                                        <label for="username">Gebruikersnaam</label>
                                        <input type="text" id="username" value="<?php echo $user->get_username() ?>"
                                               disabled="disabled" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Huidig wachtwoord</label>
                                        <input type="password" name="currentpassword" id="currentpassword"
                                               autocomplete="none"
                                               placeholder="Huidig wachtwoord" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Nieuw wachtwoord</label>
                                        <input type="password" name="password" id="password" autocomplete="none"
                                               placeholder="Wachtwoord" class="form-control">
                                    </div>
                                    <div class="form-group progress-form-group">
                                        <progress max="100" value="0" id="strength" class="strengthmeter"></progress>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Bevestig uw nieuwe wachtwoord</label>
                                        <input type="password" name="confirmpassword" id="confirmpassword"
                                               autocomplete="none"
                                               placeholder="Bevestig uw wachtwoord" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="2facode">2FA code (indien van toepassing)</label>
                                        <input type="text" name="2facode" id="2facode" autocomplete="none"
                                               placeholder="2FA code" class="form-control">
                                    </div>
                                    <input type="submit" value="Wijzig wachtwoord" class="btn btn-primary btn-block" id="submit">
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
            $passwordtt = "<p><u>Wachtwoord complexiteitseisen</u>:</p><ul class=\"text-left\"><li>Tenminste 2 tekens van a-z en/of 0-9</li><li>Eén of meer speciale karakters: !@#$%^&*()~<>?</li><li>Minimaal 14 tekens lang</li></ul>";
            $('#password').tooltip({'trigger': 'focus', 'placement': 'top', 'html': true, 'title': $passwordtt});
        });

    </script>
<?php
include_once(dirname(__FILE__) . "/../includes/footer.php");
