<?php
include_once(dirname(__FILE__) . "/../includes/logger.php");
use Psr\Log\LogLevel;

Class SafePDO extends PDO {

    public static function exception_handler($exception) {
        global $log;
        // Output the exception details
        $log->log(LogLevel::CRITICAL, 'Uncaught exception: ' . $exception->getMessage());
    }

    public function __construct($dsn, $username='', $password='', $driver_options=array()) {

        // Temporarily change the PHP exception handler while we . . .
        set_exception_handler(array(__CLASS__, 'exception_handler'));

        // . . . create a PDO object
        parent::__construct($dsn, $username, $password, $driver_options);

        // Change the exception handler back to whatever it was before
        restore_exception_handler();
    }

}

$mysql_host = "localhost";
$mysql_database = "security";

$PDO_DSN = "mysql:host=$mysql_host;dbname=$mysql_database";

// Credentials for read access
$PDO_USER_RD = 'secread';
$PDO_PASS_RD = 'gW2EurIsQ6ESUnXH';

// Credentials for save access
$PDO_USER_WR = 'secsave';
$PDO_PASS_WR = '4aCZNGdqNqMA4Pee';

// Connect to the database with defined constants
$pdoread = new SafePDO($PDO_DSN, $PDO_USER_RD, $PDO_PASS_RD);
$pdosave = new SafePDO($PDO_DSN, $PDO_USER_WR, $PDO_PASS_WR);

