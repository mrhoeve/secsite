<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
setLevelToRoot("..");
include_once(dirname(__FILE__) . "/../user/User.php");

$user = null;
if (isset($_POST['username']) || isset($_POST['password'])) {
    UserHelper::authenticateAndLoginUser($_POST['username'], $_POST['password']);
}
header('Location: ..\index.php');