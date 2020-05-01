<?php
include_once(dirname(__FILE__) . "/encodeduser.php");

if(!$user->hasPermission(PERMISSION_RESET_PASSWORD)) {
    header('Location: selectuser.php');
}

$freshStart = !isset($_POST['password']);
$error = false;

$passwordToUse = isset($_POST['password']) ? trim($_POST['password']) : "";
$curchangepwonl = $freshStart ? $retrievedUser->mustChangePasswordOnNextLogon() : isset($_POST['changepwonl']);

if (!empty($passwordToUse)) {
    debugToConsole('Password changed of user ' . $retrievedUser->get_username());
    $savedUser = UserHelper::saveUser($retrievedUser, $passwordToUse, true, $curchangepwonl);
    if ($savedUser->isEmpty()) {
        $techError = true;
    } else {
        if($retrievedUser->get_username() == $user->get_username()) {
            header('Location: ..\index.php');
        }
        $retrievedUser = UserHelper::loadUser($savedUser->get_username());
    }
}

$encodedUser = base64_encode(serialize($retrievedUser));
$checkcode = UserHelper::calculateCheckcode($encodedUser);

?>

<section id="setup2fa">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Wachtwoord wijzigen</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart && !$error) { ?>
                            <p>Wachtwoord gewijzigd.</p>
                            <a href="selectuser.php" class="btn btn-success btn-block mt-2">Terug naar
                                overzicht</a>
                        <?php } else {
                            // We have a fresh start, or we've got an error
                            if ($error) { ?>
                                <p class="text-danger">Er is een fout opgetreden.</p>
                            <?php } ?>
                            <form action="changeuserpassword.php" method="post">
                                <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                <div class="form-group">
                                    <label for="username">Gebruikersnaam</label>
                                    <input type="text" id="username"
                                           value="<?php echo $retrievedUser->get_username() ?>"
                                           disabled="disabled" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password">Wachtwoord</label>
                                    <input type="password" name="password" id="password" autocomplete="none"
                                           placeholder="Wachtwoord" class="form-control">
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="changepwonl"
                                           id="changepwonl"<?php if ($curchangepwonl) {
                                        echo " checked";
                                    } ?>>
                                    <label class="form-check-label" for="changepwonl">Wijzig wachtwoord bij volgende
                                        aanmelding</label>
                                </div>
                                <input type="submit" value="Sla wachtwoord op" class="btn btn-primary btn-block">
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

