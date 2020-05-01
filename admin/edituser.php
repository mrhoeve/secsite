<?php
include_once(dirname(__FILE__) . "/encodeduser.php");

if(!$user->hasPermission(PERMISSION_UPDATE_ACCOUNT)) {
    header('Location: selectuser.php');
}

$encodedUser = base64_encode(serialize($retrievedUser));
$checkcode = UserHelper::calculateCheckcode($encodedUser);

function loadRoles()
{
    global $pdoread;

    debugToConsole("Loading all roles...");

    // Check if we have a valid read connection
    if (!isset($pdoread)) {
        die('Failed to setup a database connection');
    }

    $roles = [];

    // Prepare our SQL
    if ($stmt = $pdoread->prepare('select role from role')) {
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($result = $stmt->fetch()) {
                // Add the role to the array
                array_push($roles, $result['role']);
            }
        }
    } else {
        die('Internal error setting up the database connection');
    }
    return $roles;
}

$freshStart = !isset($_POST['name']);
$error = false;
$saveerror = false;
$roles = loadRoles();

if (isset($_POST['name'])) {
    $curfirstname = trim($_POST['name']);
    if (empty($curfirstname)) {
        $error = true;
        $curfirstname = $retrievedUser->get_firstName();
    }
} else {
    $curfirstname = $retrievedUser->get_firstName();
}

if (isset($_POST['email'])) {
    $curemail = trim($_POST['email']);
    if ($curemail != $retrievedUser->get_email() && !UserHelper::isEmailAddressValid($curemail)) {
        $error = true;
        $curemail = $retrievedUser->get_email();
    }
} else {
    $curemail = $retrievedUser->get_email();
}

$currole = isset($_POST['rol']) ? $_POST['rol'] : $retrievedUser->get_role();
$curchangepwonl = $freshStart ? $retrievedUser->mustChangePasswordOnNextLogon() : isset($_POST['changepwonl']);
$curdisabled = $freshStart ? $retrievedUser->isDisabled() : isset($_POST['archivedAccount']);
$passwordToUse = isset($_POST['password']) ? trim($_POST['password']) : "";

if (!$freshStart && !$error) {
    $userToSave = new User($retrievedUser->get_username(), $curfirstname, $curemail, $retrievedUser->has2fa(), $currole, array(), $curchangepwonl, $curdisabled);
    $savedUser = UserHelper::saveUser($userToSave, $passwordToUse);
    if (!$savedUser->isEmpty()) {
        $retrievedUser = UserHelper::loadUser($userToSave->get_username());
        $encodedUser = base64_encode(serialize($retrievedUser));
        $checkcode = UserHelper::calculateCheckcode($encodedUser);
    } else {
        $saveerror = true;
    }
}
?>

<section id="setup2fa">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Account bewerken</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart && !$error) { ?>
                            <p class="alert alert-success">Account is opgeslagen.</p>
                        <?php }
                        // We have a fresh start, or we've got an error
                        if ($error) { ?>
                            <div class="alert alert-danger">
                                <p>Gewijzigd account kan niet opgeslagen worden vanwege (één van) deze
                                    redenen:</p>
                                <ul>
                                    <li>Naam voldoet niet aan de vereisten.</li>
                                    <li>Gewijzigd emailadres is reeds in gebruik of ongeldig.</li>
                                    </li>
                                </ul>
                            </div>
                        <?php }
                        // We have a fresh start, or we've got an error
                        if ($saveerror) { ?>
                            <div class="alert alert-danger">
                                <p>Technische fout bij het opslaan van het account.</p>
                                <p>Probeer het later nogmaals</p>
                            </div>
                        <?php }
                        ?>
                        <form action="edituser.php" method="post">
                            <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                            <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                            <div class="form-group">
                                <label for="username">Gebruikersnaam</label>
                                <input type="text" id="username"
                                       value="<?php echo $retrievedUser->get_username() ?>"
                                       disabled="disabled" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="name">Naam</label>
                                <input type="text" name="name" id="name" autocomplete="none"
                                       value="<?php echo $curfirstname ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email" autocomplete="none"
                                       value="<?php echo $curemail ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="rol">Selecteer rol</label>
                                <select class="form-control" name="rol" id="rol">
                                    <option value="none"<?php if ($currole === "Reguliere gebruiker") {
                                        echo " selected";
                                    } ?>>Reguliere gebruiker
                                    </option>
                                    <?php foreach ($roles as $rol) { ?>
                                        <option value="<?php echo $rol; ?>"<?php if ($rol == $currole) {
                                            echo " selected";
                                        } ?>><?php echo $rol ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php if($user->hasPermission(PERMISSION_RESET_PASSWORD)) { ?>
                                <div class="form-group">
                                    <label for="password">Wachtwoord</label>
                                    <input type="password" name="password" id="password" autocomplete="none"
                                           placeholder="Wachtwoord" class="form-control">
                                </div>
                            <?php } ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="changepwonl"
                                       id="changepwonl"<?php if ($curchangepwonl) {
                                    echo " checked";
                                } ?>>
                                <label class="form-check-label" for="changepwonl">Wijzig wachtwoord bij volgende
                                    aanmelding</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="archivedAccount"
                                       id="archivedAccount"<?php if ($curdisabled) {
                                    echo " checked";
                                } ?>>
                                <label class="form-check-label" for="archivedAccount">Account gearchiveerd</label>
                            </div>
                            <input type="submit" value="Sla het gewijzigde account op"
                                   class="btn btn-primary btn-block">
                            <?php if (!$freshStart && !$error) { ?>
                                <a href="selectuser.php" class="btn btn-success btn-block mt-2">Terug naar
                                    overzicht</a>
                            <?php } ?>
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

