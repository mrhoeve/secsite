<?php
include_once(dirname(__FILE__) . "/../includes/definitions.php");
setLevelToRoot("..");
include_once(dirname(__FILE__) . "/../includes/header.php");

$encodedUser = isset($_POST['seluser']) ? $_POST['seluser'] : "";
$checkcode = isset($_POST['checkcode']) ? $_POST['checkcode'] : "";
$retrievedUser = new User();
if(!empty($encodedUser) && !empty($checkcode)) {
    $retrievedUser = UserHelper::checkCodeAndGetUser($encodedUser, $checkcode);
}

echo $retrievedUser->get_username();