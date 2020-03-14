<?php
    define('PERMISSION_CREATE_ACCOUNT', 'PERMISSION_CREATE_ACCOUNT');
    define('PERMISSION_READ_ACCOUNT', 'PERMISSION_READ_ACCOUNT');
    define('PERMISSION_UPDATE_ACCOUNT', 'PERMISSION_UPDATE_ACCOUNT');
    define('PERMISSION_DELETE_ACCOUNT', 'PERMISSION_DELETE_ACCOUNT');
    define('PERMISSION_ARCHIVE_ACCOUNT', 'PERMISSION_ARCHIVE_ACCOUNT');
    define('PERMISSION_RESET_PASSWORD', 'PERMISSION_RESET_PASSWORD');
    define('PERMISSION_RESET_TOTP', 'PERMISSION_RESET_TOTP');



//b.	‘Administrator’ role may create, read, update, delete and archive accounts.
//c.	‘Helpdesk’ role may update TOTP secret key (i.e. reset) and/or password
