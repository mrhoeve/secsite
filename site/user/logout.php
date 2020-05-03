<?php
include_once(dirname(__FILE__) . "/../includes/user.php");
UserHelper::deleteSession();
header('Location: ../index.php');
