<?php
include_once(dirname(__FILE__) . "/dbconnection.php");
include_once(dirname(__FILE__) . "/definitions.php");
use Psr\Log\LogLevel;


function getSettingValue($key) {
    global $pdoread;
    global $log;

    $log->log(LogLevel::INFO, "Requesting read of setting \"" . $key . "\"");

    // Check if we have a valid read connection
    if (!isset($pdoread)) {
        $log->log(LogLevel::CRITICAL, 'Failed to setup a database connection');
    }

    // Prepare our SQL
    if ($stmt = $pdoread->prepare('select value from setting where `key` = :keyname')) {
        $stmt->bindParam(':keyname', $key);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            $log->log(LogLevel::INFO, "Value for \"$key\" found.");
            $result = $stmt->fetch();
            return $result['value'];
        }
        return "";
    } else {
        $log->log(LogLevel::CRITICAL, 'Internal error setting up the database connection');
    }
}