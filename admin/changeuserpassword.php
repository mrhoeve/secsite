<?php
include_once(dirname(__FILE__) . "/encodeduser.php");

echo $retrievedUser->get_username();

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

$freshStart = true;
$error = false;
?>

<section id="setup2fa">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>2FA instellen</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$freshStart && !$error) { ?>
                            <p>2FA is succesvol toegevoegd.</p>
                            <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2">Terug naar
                                index</a>
                        <?php } else {
                            // We have a fresh start, or we've got an error
                            if ($error) { ?>
                                <p class="text-danger">Wachtwoordvaliatie of validatie van de ingevoerde 2FA code
                                    is mislukt.</p>
                            <?php } ?>
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
                                    <label for="password">Wachtwoord</label>
                                    <input type="password" name="password" id="password" autocomplete="none"
                                           placeholder="Wachtwoord" class="form-control">
                                </div>
                                <input type="submit" value="Edit user" class="btn btn-primary btn-block">
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

