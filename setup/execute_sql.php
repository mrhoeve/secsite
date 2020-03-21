<?php
/*
 Dit PHP-script maakt de tabellen aan die gebruikt worden voor deze opdracht.
 Let op! Het gegenereerde SQL-script verwijderd alle aanwezige tabellen!
 SQL script gebaseerd op https://w3programmings.com/how-to-execute-sql-file-directly-in-php/
 Het is wel hevig aangepast naar de specifieke omstandigheden van deze opdracht.
*/

# Setup correct parameters
$mysql_host = "localhost";
$mysql_database = "security";
$mysql_user = "root";
$mysql_password = "M@rt1n@1801";

# MySQL with PDO_MYSQL
$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
# Lees het bestand in die gegenereerd is via MySQL Workbench
$query = file_get_contents("create_db.sql");
# Voer dit bestand in zijn geheel uit op de database
$stmt = $db->prepare($query);
if ($stmt->execute()) {
    echo "Tabellen zijn succesvol aangemaakt\n\nDe aanwezige wachtwoorden worden direct versleuteld...\n";

    $stmt = $db->prepare("select username, password from $mysql_database.user");
    $stmt->execute();
    $allUsers = $stmt->fetchAll();
    $stmt = $db->prepare("update $mysql_database.user set password = :password where username = :username");
    foreach ($allUsers as $user) {
        echo "Wachtwoord voor gebruiker {$user['username']} wordt geüpdated\n";
        $stmt->bindValue(":username", $user['username']);
        $stmt->bindValue(":password", password_hash($user['password'], PASSWORD_BCRYPT));
        $stmt->execute();
    }

    echo "\nOpdracht voltooid";

} else {
    echo "Tabellen zijn NIET succesvol aangemaakt...";
}

