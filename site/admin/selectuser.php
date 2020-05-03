<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
setLevelToRoot("..");
include_once(dirname(__FILE__) . "/../includes/header.php");

function userHasAuthorisation()
{
    return true;
}

function getUser($mixedUser): User
{
    return unserialize($mixedUser);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false || !userHasAuthorisation()) {
    header('Location: ..\index.php');
} else {

    $aantalActiekolommen = 0;
    if ($user->hasPermission(PERMISSION_UPDATE_ACCOUNT)) {
        $aantalActiekolommen++;
    }
    if ($user->hasPermission(PERMISSION_RESET_PASSWORD)) {
        $aantalActiekolommen++;
    }
    if ($user->hasPermission(PERMISSION_RESET_TOTP)) {
        $aantalActiekolommen++;
    }
    if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) {
        $aantalActiekolommen++;
    }
    if ($user->hasPermission(PERMISSION_DELETE_ACCOUNT)) {
        $aantalActiekolommen++;
    }

    $listUsers = UserHelper::loadAllUsers();

    ?>


    <section id="selectuser">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <table id="dtUserTable" class="table table-striped table-bordered table-sm">
                        <thead>
                        <tr>
                            <?php if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?>
                                <th class="th-sm">
                                </th><?php } ?>
                            <th class="th-sm"<?php if ($user->hasPermission(PERMISSION_READ_ACCOUNT)) echo ' colspan="4"' ?>>
                                Gebruikersinformatie
                            </th>
                            <?php if ($aantalActiekolommen > 0) { ?>
                                <th class="th-sm text-center" colspan="5">Acties
                                </th><?php } ?>
                        </tr>
                        <tr>
                            <?php if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?>
                                <th class="th-sm text-center">B
                                </th><?php } ?>
                            <th class="th-sm">Username
                            </th>
                            <?php if ($user->hasPermission(PERMISSION_READ_ACCOUNT)) { ?>
                                <th class="th-sm">Naam
                                </th>
                                <th class="th-sm">Emailadres
                                </th>
                                <th class="th-sm">Rol
                                </th><?php }
                            if ($aantalActiekolommen > 0) {
                                if ($user->hasPermission(PERMISSION_UPDATE_ACCOUNT)) { ?>
                                    <th class="th-sm text-center">E
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_RESET_PASSWORD)) { ?>
                                    <th class="th-sm text-center">C
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_RESET_TOTP)) { ?>
                                    <th class="th-sm text-center">2
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?>
                                    <th class="th-sm text-center">A
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_DELETE_ACCOUNT)) { ?>
                                    <th class="th-sm text-center">D
                                    </th><?php }
                            } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($listUsers as $listUser) {
                            $selUser = getUser($listUser);
                            $encodedUser = base64_encode(serialize($selUser));
                            $checkcode = UserHelper::calculateCheckcode($encodedUser);
                            ?>
                            <tr>
                                <?php if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?>
                                    <td class="text-center"><?php if ($selUser->isDisabled()) echo "<i class=\"fas fa-ban\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Gebruiker is gearchiveerd\"></i>"; else echo " "; ?></td><?php } ?>
                                <td><?php echo $selUser->get_username() ?></td>
                                <?php if ($user->hasPermission(PERMISSION_READ_ACCOUNT)) { ?>
                                    <td><?php echo $selUser->get_firstName() ?></td>
                                    <td><?php echo $selUser->get_email() ?></td>
                                    <td><?php echo $selUser->get_role() ?></td><?php }
                                if ($user->hasPermission(PERMISSION_UPDATE_ACCOUNT)) { ?>
                                    <td class="text-center">
                                    <form action="edituser.php" method="post">
                                        <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                        <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                        <button type="submit" name="submit" value="submit"
                                                class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                data-placement="bottom" title="Bewerk deze gebruiker"><i
                                                    class="fas fa-user-edit"></i>
                                        </button>
                                    </form>
                                    </td><?php }
                                if ($user->hasPermission(PERMISSION_RESET_PASSWORD)) { ?>
                                    <td class="text-center">
                                    <form action="changeuserpassword.php" method="post">
                                        <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                        <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                        <button type="submit" name="submit" value="submit"
                                                class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                data-placement="bottom" title="Wijzig wachtwoord"><i
                                                    class="fas fa-key"></i>
                                        </button>
                                    </form>
                                    </td><?php }
                                if ($user->hasPermission(PERMISSION_RESET_TOTP)) {
                                    if ($selUser->has2fa()) { ?>
                                        <td class="text-center">
                                            <form action="twofactorauthenticator.php" method="post">
                                                <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                                <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                                <button type="submit" name="submit" value="submit"
                                                        class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                        data-placement="bottom" title="Reset 2FA"><i
                                                            class="fas fa-qrcode"></i>
                                                </button>
                                            </form>
                                        </td>
                                    <?php } else { ?>
                                        <td class="text-center">
                                            <button type="submit" name="submit" value="submit"
                                                    class="btn btn-sm btn-secondary" disabled><i
                                                        class="fas fa-qrcode"></i>
                                            </button>
                                        </td>
                                    <?php }
                                }
                                if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?>
                                    <td class="text-center">
                                    <form action="archiveuser.php" method="post">
                                        <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                        <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                        <button type="submit" name="submit" value="submit"
                                                class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                data-placement="bottom" title="Archiveer gebruiker"><i
                                                    class="fas fa-user-slash"></i>
                                        </button>
                                    </form>
                                    </td><?php }
                                if ($user->hasPermission(PERMISSION_DELETE_ACCOUNT)) { ?>
                                    <td class="text-center">
                                    <form action="deleteuser.php" method="post">
                                        <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                        <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                        <button type="submit" name="submit" value="submit"
                                                class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                data-placement="bottom" title="Verwijder gebruiker"><i
                                                    class="fas fa-user-times"></i>
                                        </button>
                                    </form>
                                    </td><?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <?php if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?>
                                <th>
                                </th><?php } ?>
                            <th>Username
                            </th>
                            <?php if ($user->hasPermission(PERMISSION_READ_ACCOUNT)) { ?>
                                <th>Naam
                                </th>
                                <th>Emailadres
                                </th>
                                <th>Rol
                                </th><?php }
                            if ($aantalActiekolommen > 0) {
                                if ($user->hasPermission(PERMISSION_UPDATE_ACCOUNT)) { ?>
                                    <th class="text-center">E
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_RESET_PASSWORD)) { ?>
                                    <th class="text-center">C
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_RESET_TOTP)) { ?>
                                    <th class="text-center">2
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?>
                                    <th class="text-center">A
                                    </th><?php }
                                if ($user->hasPermission(PERMISSION_DELETE_ACCOUNT)) { ?>
                                    <th class="text-center">D
                                    </th><?php }
                            } ?>
                        </tr>
                        </tfoot>
                    </table>
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
    </section>

    <?php
    include_once(dirname(__FILE__) . "/../includes/jsscripts.php");
    $usercolumn = $user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT) ? 1 : 0;

    $columnDefs = '';
    if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT) || $aantalActiekolommen > 0) {
        $basecolumn = 1;
        $columnDefs = ",
                \"columnDefs\": [{
                    \"targets\": [";
        if ($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) {
            $columnDefs .= "0" . ($aantalActiekolommen > 0 ? ", " : "");
            $basecolumn = 2;
        }
        if ($user->hasPermission(PERMISSION_READ_ACCOUNT)) {
            $basecolumn += 3;
        }
        for ($teller = 0; $teller < $aantalActiekolommen; $teller++) {
            $columnDefs .= $basecolumn + $teller;
            if ($teller + 1 != $aantalActiekolommen) {
                $columnDefs .= ", ";
            }
        }
        $columnDefs .= "],
                    \"orderable\": false,
                    \"searchable\": false
                }";

    }
    ?>

    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"
            crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"
            crossorigin="anonymous"></script>

    <!-- Use of datatables: https://datatables.net/examples/api/ -->
    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip();
        $('#dtUserTable').dataTable({
            "order": [[<?php echo $usercolumn; ?>, "asc"]],
            "language": {
                "sProcessing": "Bezig...",
                "sLengthMenu": "_MENU_ resultaten weergeven",
                "sZeroRecords": "Geen resultaten gevonden",
                "sInfo": "_START_ tot _END_ van _TOTAL_ resultaten",
                "sInfoEmpty": "Geen resultaten om weer te geven",
                "sInfoFiltered": " (gefilterd uit _MAX_ resultaten)",
                "sInfoPostFix": "",
                "sSearch": "Zoeken:",
                "sEmptyTable": "Geen resultaten aanwezig in de tabel",
                "sInfoThousands": ".",
                "sLoadingRecords": "Een moment geduld aub - bezig met laden...",
                "oPaginate": {
                    "sFirst": "Eerste",
                    "sLast": "Laatste",
                    "sNext": "Volgende",
                    "sPrevious": "Vorige"
                },
                "oAria": {
                    "sSortAscending": ": activeer om kolom oplopend te sorteren",
                    "sSortDescending": ": activeer om kolom aflopend te sorteren"
                }
            }<?php echo $columnDefs; ?>]
        })
        ;
    </script>
    <?php
}
include_once(dirname(__FILE__) . "/../includes/footer.php");
?>
