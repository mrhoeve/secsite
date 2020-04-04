<?php
if(!defined('LEVEL')) {
    die("LEVEL not defined");
}
include_once(dirname(__FILE__) . "./dbconnection.php");
include_once(dirname(__FILE__) . "/../user/User.php");
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
    <link rel="stylesheet" href="<?php echo LEVEL ?>css/style.css">
    <title>SomeSite</title>
</head>

<body data-target="#main-nav" id="home">
<?php
$user = null;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $user === null) {
    $user = UserHelper::validateUserAndTimestamp(unserialize($_SESSION['user']));
}
?>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top" id="main-nav">
    <div class="container">
        <a href="index.html" class="navbar-brand">SomeSite</a>
        <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="#home" class="nav-link">Home</a>
                </li>
                <?php if ($user != null && ($user->hasPermission(PERMISSION_RESET_TOTP))) { ?>
                    <li class="nav-item">
                        <a href="#explore-head-section" class="nav-link">OTP Admin</a>
                    </li>
                <?php } ?>
                <?php if ($user != null && ($user->hasPermission(PERMISSION_RESET_PASSWORD) || $user->hasPermission(PERMISSION_CREATE_ACCOUNT) || $user->hasPermission(PERMISSION_READ_ACCOUNT) || $user->hasPermission(PERMISSION_UPDATE_ACCOUNT) || $user->hasPermission(PERMISSION_DELETE_ACCOUNT) || $user->hasPermission(PERMISSION_ARCHIVE_ACCOUNT))) { ?>
                    <li class="nav-item">
                        <a href="#create-head-section" class="nav-link">Accounts</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a href="#share-head-section" class="nav-link">Share</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

