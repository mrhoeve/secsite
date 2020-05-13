<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
use Psr\Log\LogLevel;
setLevelToRoot("..");
include_once(dirname(__FILE__) . "/../includes/header.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
    header('Location: ..\index.php');
}
$curUser = UserHelper::validateUserAndTimestamp(unserialize($_SESSION['user']));
// Don't we have a user, or does the user already have 2FA enabled? Then go to the index page
if ($curUser->isEmpty() || $curUser->has2fa()) {
    header('Location: ..\index.php');
}

// Start with a clean sheet
$freshStart = true;
$error = false;

// If we come via Post, then we'll have a username to process (hidden field in form).
if (isset($_POST['username'])) {
    $freshStart = false;
    // Make sure we have the correct user
    $authenticatedUser = UserHelper::authenticateUserWithoutLoggingIn($_POST['username'], $_POST['password'], '');
    if (!empty($authenticatedUser->get_username()) && $curUser->get_username() === $authenticatedUser->get_username()) {
        $fasecret = $_POST['2fasecret'];
        $facode = $_POST['2facode'];
        $ga = new PHPGangsta_GoogleAuthenticator();
        if ($ga->verifyCode($fasecret, $facode, 1)) {
            $log->log(LogLevel::NOTICE, "2FA enabled and secret saved");
            UserHelper::save2FASecret($authenticatedUser, $fasecret);
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}

if ($freshStart || $error) {
    $ga = new PHPGangsta_GoogleAuthenticator();
    try {
        $secret = $ga->createSecret(32);
        $url = $ga->getQRCodeGoogleUrl($curUser->get_username(), $secret, "SomeSite");
    } catch (Exception $e) {
        // We doen nog niets met excepties
    }
}
?>
<section id="setup2fasection">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>2FA instellen</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart && !$error) { ?>
                            <p id="succes2fa">2FA is succesvol toegevoegd.</p>
                            <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2" id="succes2fabutton">Terug naar
                                index</a>
                        <?php } else {
                            // We have a fresh start, or we've got an error
                            if ($error) { ?>
                                <p class="text-danger" id="error2fa">Wachtwoordvaliatie of validatie van de ingevoerde 2FA code
                                    is mislukt.</p>
                            <?php } ?>
                            <form action="setup2fa.php" method="post">
                                <input type="hidden" name="2fasecret" value="<?php echo $secret ?>">
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
                                    <p class="text-center">Scan deze barcode met een geschikte app</p>
                                    <img src="<?php echo $url ?>" class="img-fluid mx-auto d-block">
                                    <p class="text-center">of voer deze code in:</br>
                                        <b><span id="secret"><?php echo $secret ?></span></b></p>
                                </div>
                                <div class="form-group">
                                    <label for="2facode">Genereer nu een 2FA code en voer deze in</label>
                                    <input type="text" name="2facode" id="2facode" autocomplete="none"
                                           placeholder="2FA code" class="form-control">
                                </div>
                                <input type="submit" value="2FA vastleggen" class="btn btn-primary btn-block" id="submit2fa">
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
