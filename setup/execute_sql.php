<?php
/*
 Dit PHP-script maakt de tabellen aan die gebruikt worden voor deze opdracht.
 Let op! Het gegenereerde SQL-script verwijderd alle aanwezige tabellen!
 SQL script gebaseerd op https://w3programmings.com/how-to-execute-sql-file-directly-in-php/
 Het is wel hevig aangepast naar de specifieke omstandigheden van deze opdracht.
*/

# Include de te gebruiken database connectie en credentials
# Zorg er wel voor dat de credentials geldig zijn en de juiste rechten hebben op de te gebruiken database
include_once "../site/includes/dbconnection.php";

$pdoadmin = new SafePDO($PDO_DSN, "root", "M@rt1n@1801");

# Lees het bestand in die gegenereerd is via MySQL Workbench
$query = file_get_contents("create_db.sql");
# Voer dit bestand in zijn geheel uit op de database
$stmt = $pdoadmin->prepare($query);
if ($stmt->execute()) {
    echo "Tabellen zijn succesvol aangemaakt\n\nDe aanwezige wachtwoorden worden direct versleuteld...\n";

    $stmt = $pdoadmin->prepare("select username, password from user");
    $stmt->execute();
    $allUsers = $stmt->fetchAll();
    $stmt = $pdoadmin->prepare("update user set password = :password where username = :username");
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

