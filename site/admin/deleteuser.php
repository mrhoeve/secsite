<?php
include_once(dirname(__FILE__) . "/encodeduser.php");
include_once(dirname(__FILE__) . "/../includes/logger.php");
use Psr\Log\LogLevel;

if(!$user->hasPermission(PERMISSION_DELETE_ACCOUNT)) {
    header('Location: selectuser.php');
}

$freshStart = !(isset($_POST['submit']) && $_POST['submit'] == 'Verwijder gebruiker');

if (!$freshStart && !$CSRFTokenerror) {
    $log->log(LogLevel::INFO, 'User ' . $user->get_username() . ' removed user ' . $retrievedUser->get_username());
    UserHelper::removeUser($retrievedUser);
}

$encodedUser = base64_encode(serialize($retrievedUser));
$checkcode = UserHelper::calculateCheckcode($encodedUser);

?>

<section id="deleteuser">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Gebruiker verwijderen</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart && !$CSRFTokenerror) { ?>
                            <p id="success">Gebruiker <?php echo $retrievedUser->get_username() ?> verwijderd.</p>
                            <a href="selectuser.php" class="btn btn-success btn-block mt-2" id="successbutton">Terug naar overzicht</a>
                        <?php } else { ?>
                            <form action="deleteuser.php" method="post">
                                <input type="hidden" name="CSRFToken" value="<?php echo $CSRFToken ?>">
                                <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                <div class="form-group">
                                    <label for="username">Gebruikersnaam</label>
                                    <input type="text" id="username"
                                           value="<?php echo $retrievedUser->get_username() ?>"
                                           disabled="disabled" class="form-control">
                                </div>
                                <p class="alert alert-danger">Door hieronder op de knop 'Verwijder gebruiker' te klikken verwijdert u de hierboven genoemde gebruiker.
                                Deze actie is niet ongedaan te maken.<br/><br/>
                                Let op, de gebruiker wordt zonder verdere vraag verwijderd!</p>
                                <input type="submit" name="submit" value="Verwijder gebruiker" class="btn btn-danger btn-block" id="removeUser">
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

