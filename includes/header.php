<?php
if(!defined('LEVEL')) {
    die("LEVEL not defined");
}
include_once(dirname(__FILE__) . "/dbconnection.php");
include_once(dirname(__FILE__) . "/User.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css"
          integrity="sha384-REHJTs1r2ErKBuJB0fCK99gCYsVjwxHrSU0N7I1zl9vZbggVJXRMsv/sLlOAGb4M" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo LEVEL ?>css/style.css">
    <title>SomeSite</title>
</head>

<body data-target="#main-nav" id="home">
<?php
$user = new User();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $user->isEmpty()) {
    $user = UserHelper::validateUserAndTimestamp(unserialize($_SESSION['user']));
}
?>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark" id="main-nav">
    <div class="container">
        <a href="<?php echo LEVEL ?>index.php" class="navbar-brand">SomeSite</a>
        <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="<?php echo LEVEL ?>index.php" class="nav-link">Home</a>
                </li>
                <?php if (!$user->isEmpty() && ($user->hasPermission(PERMISSION_RESET_TOTP))) { ?>
                    <li class="nav-item">
                        <a href="#explore-head-section" class="nav-link">OTP Admin</a>
                    </li>
                <?php } ?>
                <?php if (!$user->isEmpty() && ($user->hasPermission(PERMISSION_RESET_PASSWORD) || $user->hasPermission(PERMISSION_CREATE_ACCOUNT) || $user->hasPermission(PERMISSION_READ_ACCOUNT) || $user->hasPermission(PERMISSION_UPDATE_ACCOUNT) || $user->hasPermission(PERMISSION_DELETE_ACCOUNT) || $user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT))) { ?>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            Accounts
                        </a>
                        <div class="dropdown-menu">
                            <?php if($user->hasPermission(PERMISSION_RESET_PASSWORD)) { ?> <a href="#" class="dropdown-item">Reset wachtwoord</a><?php } ?>
                            <?php if($user->hasPermission(PERMISSION_READ_ACCOUNT)) { ?> <a href="<?php echo LEVEL ?>admin/selectuser.php" class="dropdown-item">Bekijk accounts</a><?php } ?>
                            <?php if($user->hasPermission(PERMISSION_CREATE_ACCOUNT)) { ?> <a href="#" class="dropdown-item">Creëer account</a><?php } ?>
                            <?php if($user->hasPermission(PERMISSION_UPDATE_ACCOUNT)) { ?> <a href="#" class="dropdown-item">Update account</a><?php } ?>
                            <?php if($user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT)) { ?> <a href="#" class="dropdown-item">Archiveer account</a><?php } ?>
                            <?php if($user->hasPermission(PERMISSION_DELETE_ACCOUNT)) { ?> <a href="#" class="dropdown-item">Verwijder account</a><?php } ?>
                        </div>
                    </li>
                <?php } ?>
                <?php if (!$user->isEmpty()) { ?>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">User: <?php echo $user->get_firstName() ?></a>
                        <div class="dropdown-menu">
                            <?php if(!$user->has2fa()) { ?> <a href="<?php echo LEVEL ?>user/setup2fa.php" class="dropdown-item">2FA instellen</a><?php } ?>
                            <?php if($user->has2fa()) { ?> <a href="<?php echo LEVEL ?>user/remove2fa.php" class="dropdown-item">2FA verwijderen</a><?php } ?>
                            <a href="<?php echo LEVEL ?>user/logout.php" class="dropdown-item">Uitloggen</a>
                        </div>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a href="<?php echo LEVEL ?>user/register.php" class="nav-link">Maak een account aan</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo LEVEL ?>user/login.php" class="nav-link">Login</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
