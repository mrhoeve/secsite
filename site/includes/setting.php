<?php
include_once(dirname(__FILE__) . "/dbconnection.php");
include_once(dirname(__FILE__) . "/definitions.php");

function getSettingValue($key) {
    global $pdoread;

    debugToConsole("Requesting read of setting \"" . $key . "\"");

    // Check if we have a valid read connection
    if (!isset($pdoread)) {
        die('Failed to setup a database connection');
    }

    // Prepare our SQL
    if ($stmt = $pdoread->prepare('select value from setting where `key` = :keyname')) {
        $stmt->bindParam(':keyname', $key);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            debugToConsole("Value for \"$key\" found.");
            $result = $stmt->fetch();
            return $result['value'];
        }
        return "";
    } else {
        die('Internal error setting up the database connection');
    }
}