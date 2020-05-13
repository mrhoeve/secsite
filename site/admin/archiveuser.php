<?php
include_once(dirname(__FILE__) . "/../includes/logger.php");
use Psr\Log\LogLevel;

include_once(dirname(__FILE__) . "/encodeduser.php");

if(!$user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) {
    header('Location: selectuser.php');
}

$freshStart = !(isset($_POST['submit']) && $_POST['submit'] == 'Opslaan');
$curdisabled = $freshStart ? $retrievedUser->isDisabled() : isset($_POST['archivedAccount']);

if (!$freshStart) {
    $log->log(LogLevel::INFO, 'User ' . $retrievedUser->get_username() . $curdisabled ? ' is archived' : ' is not archived');
    UserHelper::editUserArchiveStatus($retrievedUser, $curdisabled);
}

$encodedUser = base64_encode(serialize($retrievedUser));
$checkcode = UserHelper::calculateCheckcode($encodedUser);

?>

<section id="archiveuser">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Gebruiker (de)archiveren</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart) { ?>
                            <p>Gebruiker <?php echo $retrievedUser->get_username() ?> opgeslagen.</p>
                        <?php } ?>
                            <form action="archiveuser.php" method="post">
                                <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                <div class="form-group">
                                    <label for="username">Gebruikersnaam</label>
                                    <input type="text" id="username"
                                           value="<?php echo $retrievedUser->get_username() ?>"
                                           disabled="disabled" class="form-control">
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="archivedAccount"
                                           id="archivedAccount"<?php if ($curdisabled) {
                                        echo " checked";
                                    } ?>>
                                    <label class="form-check-label" for="archivedAccount">Account gearchiveerd</label>
                                </div>
                                <input type="submit" name="submit" value="Opslaan" class="btn btn-warning btn-block">
                                <a href="selectuser.php" class="btn btn-success btn-block mt-2">Terug naar overzicht</a>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include_once(dirname(__FILE__) . "/../includes/footer.php");
?>

