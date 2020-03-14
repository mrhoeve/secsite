<?php
/*
 File intended to setup sql tables from sql file
 Run this only once since it drops the tables!!
 File based upon https://w3programmings.com/how-to-execute-sql-file-directly-in-php/
*/

# Setup correct parameters
$mysql_host = "localhost";
$mysql_database = "security";
$mysql_user = "root";
$mysql_password = "M@rt1n@1801";

# MySQL with PDO_MYSQL
$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
$query = file_get_contents("create_db.sql");
$stmt = $db->prepare($query);
if ($stmt->execute())
    echo "Success";
else
    echo "Fail";
