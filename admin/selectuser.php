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

    $listUsers = UserHelper::loadAllUsers();

    ?>


    <section id="login">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <table id="dtUserTable" class="table table-striped table-bordered table-sm">
                        <thead>
                        <tr>
                            <th class="th-sm">
                            </th>
                            <th class="th-sm" colspan="4">Gebruikersinformatie
                            </th>
                            <th class="th-sm text-center" colspan="5">Acties
                            </th>
                        </tr>
                        <tr>
                            <th class="th-sm text-center">B
                            </th>
                            <th class="th-sm">Username
                            </th>
                            <th class="th-sm">Naam
                            </th>
                            <th class="th-sm">Emailadres
                            </th>
                            <th class="th-sm">Rol
                            </th>
                            <th class="th-sm text-center">E
                            </th>
                            <th class="th-sm text-center">C
                            </th>
                            <th class="th-sm text-center">2
                            </th>
                            <th class="th-sm text-center">A
                            </th>
                            <th class="th-sm text-center">D
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($listUsers as $user) {
                            $selUser = getUser($user);
                            $encodedUser = base64_encode(serialize($selUser));
                            $checkcode = UserHelper::calculateCheckcode($encodedUser);
                            ?>
                            <tr>
                                <td class="text-center"><?php if ($selUser->isDisabled()) echo "<i class=\"fas fa-ban\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Gebruiker is gearchiveerd\"></i>"; else echo " "; ?></td>
                                <td><?php echo $selUser->get_username() ?></td>
                                <td><?php echo $selUser->get_firstName() ?></td>
                                <td><?php echo $selUser->get_email() ?></td>
                                <td><?php echo $selUser->get_role() ?></td>
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
                                </td>
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
                                </td>
                                <td class="text-center">
                                    <form action="edituser.php" method="post">
                                        <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                        <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                        <button type="submit" name="submit" value="submit"
                                                class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                data-placement="bottom" title="Reset 2FA"><i class="fas fa-qrcode"></i>
                                        </button>
                                    </form>
                                </td>
                                <?php if (!$selUser->isDisabled()) { ?>
                                    <td class="text-center">
                                        <form action="edituser.php" method="post">
                                            <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                            <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                            <button type="submit" name="submit" value="submit"
                                                    class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                    data-placement="bottom" title="Archiveer gebruiker"><i
                                                        class="fas fa-user-slash"></i>
                                            </button>
                                        </form>
                                    </td>
                                <?php } else { ?>
                                    <td class="text-center">
                                        <button type="submit" name="submit" value="submit"
                                                class="btn btn-sm btn-secondary" disabled><i
                                                    class="fas fa-user-slash"></i>
                                        </button>
                                    </td>
                                <?php } ?>
                                <td class="text-center">
                                    <form action="edituser.php" method="post">
                                        <input type="hidden" name="seluser" value="<?php echo $encodedUser; ?>">
                                        <input type="hidden" name="checkcode" value="<?php echo $checkcode; ?>">
                                        <button type="submit" name="submit" value="submit"
                                                class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                data-placement="bottom" title="Verwijder gebruiker"><i
                                                    class="fas fa-user-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>
                            </th>
                            <th>Username
                            </th>
                            <th>Naam
                            </th>
                            <th>Emailadres
                            </th>
                            <th>Rol
                            </th>
                            <th>E
                            </th>
                            <th>C
                            </th>
                            <th>2
                            </th>
                            <th>A
                            </th>
                            <th>D
                            </th>
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
    if (!$freshStart && !$error) { ?>

        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"
                crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"
                crossorigin="anonymous"></script>

        <!-- Use of datatables: https://datatables.net/examples/api/ -->
        <script type="text/javascript">
            $('[data-toggle="tooltip"]').tooltip();
            $('#dtUserTable').dataTable({
                "order": [[1, "asc"]],
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
                },
                "columnDefs": [{
                    "targets": [0, 5, 6, 7, 8, 9],
                    "orderable": false,
                    "searchable": false
                }]
            });
        </script>
        <?php
    }
}
include_once(dirname(__FILE__) . "/../includes/footer.php");
?>
