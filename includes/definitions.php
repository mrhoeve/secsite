<?php

function debugToConsole($output)
{
    if (DEVELOPMENT_DEBUG) {
        // Always pass the output through a json_encode to ensure proper sanitation
        $outputSanitized = json_encode($output);
        // But we want to remove the first and last quotes, so let's check if there's a string to work with
        if(strlen($outputSanitized) > 2) {
            $outputSanitized = substr($outputSanitized,1,strlen($outputSanitized)-2);
            $consoleLog = "<script>console.log('[DEVDEBUG] - " . $outputSanitized . "' );</script>";
            echo $consoleLog;
        }
    }
}


define('DEVELOPMENT_DEBUG', true);

define('PERMISSION_CREATE_ACCOUNT', 'PERMISSION_CREATE_ACCOUNT');
define('PERMISSION_READ_ACCOUNT', 'PERMISSION_READ_ACCOUNT');
define('PERMISSION_UPDATE_ACCOUNT', 'PERMISSION_UPDATE_ACCOUNT');
define('PERMISSION_DELETE_ACCOUNT', 'PERMISSION_DELETE_ACCOUNT');
define('PERMISSION_ARCHIVE_ACCOUNT', 'PERMISSION_ARCHIVE_ACCOUNT');
define('PERMISSION_RESET_PASSWORD', 'PERMISSION_RESET_PASSWORD');
define('PERMISSION_RESET_TOTP', 'PERMISSION_RESET_TOTP');


define('SESSION_LOGGEDIN', 'loggedin');
define('SESSION_USERNAME', 'user');



//b.	‘Administrator’ role may create, read, update, delete and archive accounts.
//c.	‘Helpdesk’ role may update TOTP secret key (i.e. reset) and/or password
