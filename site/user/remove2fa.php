<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
use Psr\Log\LogLevel;
setLevelToRoot("..");
include_once(dirname(__FILE__) . "/../includes/header.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
    header('Location: ..\index.php');
}
$curUser = UserHelper::validateUserAndTimestamp(unserialize($_SESSION['user']));
// Don't we have a user, or doesn't the user have 2FA enabled? Then go to the index page
if ($curUser->isEmpty() || !$curUser->has2fa()) {
    header('Location: ..\index.php');
}

// Start with a clean sheet
$freshStart = true;
$error = false;

// If we come via Post, then we'll have a username to process (hidden field in form).
if (isset($_POST['username'])) {
    $freshStart = false;
    // Make sure we have the correct user
    $authenticatedUser = UserHelper::authenticateUserWithoutLoggingIn($_POST['username'], $_POST['password'], $_POST['2facode']);
    if (!$authenticatedUser->isEmpty() && $curUser->get_username() === $authenticatedUser->get_username()) {
        $log->log(LogLevel::INFO, "Removing 2FA of user " . $authenticatedUser->get_username());
        UserHelper::save2FASecret($authenticatedUser, null);
    } else {
        $error = true;
    }
}
?>
<section id="remove2fasection">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>2FA verwijderen</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart && !$error) { ?>
                            <p id="success">2FA is succesvol verwijderd.</p>
                            <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2" id="successbutton">Terug naar
                                index</a>
                        <?php } else {
                            // We have a fresh start, or we've got an error
                            if ($error) { ?>
                                <p class="text-danger" id="error">Wachtwoordvaliatie of validatie van de ingevoerde 2FA code
                                    is mislukt.</p>
                            <?php } ?>
                            <form action="remove2fa.php" method="post">
                                <input type="hidden" name="username" value="<?php echo $curUser->get_username() ?>">
                                <div class="form-group">
                                    <label for="username">Gebruikersnaam</label>
                                    <input type="text" id="username" value="<?php echo $curUser->get_username() ?>"
                                           disabled="disabled" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password">Wachtwoord</label>
                                    <input type="password" name="password" id="password" autocomplete="none"
                                           placeholder="Wachtwoord" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="2facode">Geldige 2FA code</label>
                                    <input type="text" name="2facode" id="2facode" autocomplete="none"
                                           placeholder="2FA code" class="form-control">
                                </div>
                                <input type="submit" value="2FA verwijderen" class="btn btn-warning btn-block" id="submit">
                            </form> <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include_once(dirname(__FILE__) . "/../includes/footer.php");
?>
