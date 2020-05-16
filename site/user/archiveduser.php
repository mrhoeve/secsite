<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
setLevelToRoot("..");
include_once(dirname(__FILE__) . "/../includes/header.php");
?>

    <section id="archivedusersection">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4>Account gearchiveerd</h4>
                        </div>
                        <div class="card-body">
                            <p class="alert alert-danger" id="messageAccountArchived">Uw account is gearchiveerd.<br/>Neem contact op met de beheerder...</p>
                            <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2" id="backToIndex">Terug naar
                                index</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
include_once(dirname(__FILE__) . "/../includes/footer.php");
