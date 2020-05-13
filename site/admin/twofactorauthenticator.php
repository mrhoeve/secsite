<?php
include_once(dirname(__FILE__) . "/encodeduser.php");
include_once(dirname(__FILE__) . "/../includes/logger.php");
use Psr\Log\LogLevel;

if(!$user->hasPermission(PERMISSION_RESET_TOTP)) {
    header('Location: selectuser.php');
}

$freshStart = !(isset($_POST['submit']) && $_POST['submit'] == 'Verwijder 2FA');

if (!$freshStart) {
    $log->log(LogLevel::INFO, 'Removing 2FA of user ' . $retrievedUser->get_username());
    UserHelper::save2FASecret($retrievedUser, null);
}

$encodedUser = base64_encode(serialize($retrievedUser));
$checkcode = UserHelper::calculateCheckcode($encodedUser);

?>

<section id="reset2fatoken">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>2FA verwijderen</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart) { ?>
                            <p>2FA van gebruiker <?php echo $retrievedUser->get_username() ?> verwijderd.</p>
                            <a href="selectuser.php" class="btn btn-success btn-block mt-2">Terug naar overzicht</a>
                        <?php } else { ?>
                            <form action="twofactorauthenticator.php" method="post">
                                <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                <div class="form-group">
                                    <label for="username">Gebruikersnaam</label>
                                    <input type="text" id="username"
                                           value="<?php echo $retrievedUser->get_username() ?>"
                                           disabled="disabled" class="form-control">
                                </div>
                                <p class="alert alert-warning">Door hieronder op de knop 'Verwijder 2FA' te klikken verwijdert u de 2FA gegevens.
                                Hierdoor is het voor de gebruiker mogelijk om weer zonder 2FA in te loggen op de site,
                                en om opnieuw 2FA te activeren. Gebruik deze optie wanneer de gebruiker geen authenticator
                                meer actief heeft die is gekoppeld aan zijn profiel, en na validatie dat de gebruiker echt
                                de gebruiker is.<br/><br/>
                                Let op, 2FA wordt zonder verdere vraag verwijderd!</p>
                                <input type="submit" name="submit" value="Verwijder 2FA" class="btn btn-danger btn-block">
                                <a href="selectuser.php" class="btn btn-success btn-block mt-2">Terug naar overzicht</a>
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

