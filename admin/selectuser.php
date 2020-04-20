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

    // Strategie om verder te gaan. De data moet als base64encoded string naar de volgende pagina,
    // inclusief een hashcode van md5(serialized_user_met_salt). Dan zitten we redelijk safe als iemand al doorheeft dat er een base64encoded string in zit ;-)

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
                            <th class="th-sm">Username
                            </th>
                            <th class="th-sm">Naam
                            </th>
                            <th class="th-sm">Emailadres
                            </th>
                            <th class="th-sm">Rol
                            </th>
                            <th class="th-sm">Actie
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($listUsers as $user) {
                            $selUser = getUser($user);
                            ?>
                            <tr>
                                <td><?php if($selUser->isDisabled()) echo "<i class=\"fas fa-ban\"></i>"; else echo " "; ?></td>
                                <td><?php echo $selUser->get_username() ?></td>
                                <td><?php echo $selUser->get_firstName() ?></td>
                                <td><?php echo $selUser->get_email() ?></td>
                                <td><?php echo $selUser->get_role() ?></td>
                                <td><i class="fas fa-user-edit"></i> <i class="fas fa-key"></i> <i class="fas fa-user-slash"></i> <i class="fas fa-user-times"> <i class="fas fa-user-plus"></i></i></td>
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
                            <th>Actie
                            </th>
                        </tr>

                        </tfoot>
                    </table>
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
            $('#dtUserTable').dataTable({
                "order": [[ 1, "asc"]],
                "language": {
                    "lengthMenu": "Toon _MENU_ records per pagina",
                    "zeroRecords": "Geen resultaten gevonden",
                    "info": "Pagina _PAGE_ van _PAGES_ wordt getoond",
                    "infoEmpty": "Geen records aanwezig",
                    "infoFiltered": "(gefilterd van totaal _MAX_ records)"
                }
            });
        </script>
        <?php
    }
}
include_once(dirname(__FILE__) . "/../includes/footer.php");
?>
