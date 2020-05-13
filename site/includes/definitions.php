<?php
session_start();

include_once(dirname(__FILE__) . "/logger.php");
use Psr\Log\LogLevel;

define('DEVELOPMENT_DEBUG', true);

define('ROLE_ADMINISTRATOR', 'Administrator');
define('PERMISSION_CREATE_ACCOUNT', 'PERMISSION_CREATE_ACCOUNT');
define('PERMISSION_READ_ACCOUNT', 'PERMISSION_READ_ACCOUNT');
define('PERMISSION_UPDATE_ACCOUNT', 'PERMISSION_UPDATE_ACCOUNT');
define('PERMISSION_DELETE_ACCOUNT', 'PERMISSION_DELETE_ACCOUNT');
define('PERMISSION_ARCHIVE_ACCOUNT', 'PERMISSION_ARCHIVE_ACCOUNT');
define('PERMISSION_RESET_PASSWORD', 'PERMISSION_RESET_PASSWORD');
define('PERMISSION_RESET_TOTP', 'PERMISSION_RESET_TOTP');


define('SESSION_LOGGEDIN', 'loggedin');
define('SESSION_USER', 'user');

function setLevelToRoot($level)
{
    if (strlen($level) > 0 && substr($level, -1) != "/" && substr($level, -1) != "\\") {
        $level = $level . "/";
    }
    define('LEVEL', $level);
}

function loadProgressCss()
{
    define('LOAD_PROGRESS_CSS', true);
}

function changeOwnPassword()
{
    define('CHANGE_OWN_PASSWORD', true);
}

//b.	‘Administrator’ role may create, read, update, delete and archive accounts.
//c.	‘Helpdesk’ role may update TOTP secret key (i.e. reset) and/or password
