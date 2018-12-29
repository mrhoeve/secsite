<?php
/**
 * Created by IntelliJ IDEA.
 * User: mrhoe
 * Date: 28-12-2018
 * Time: 14:12
 */

$wachtwoord = '$2y$10$mwI9w92f5Cez1d7C0iy7Ju9pQruBJiYmb9G3IKcWh4DyuIzwyFeKa';
echo password_hash("M@rt1n@1801", PASSWORD_BCRYPT);
if(password_verify("Welkom@01", $wachtwoord ) == true) {
    echo "De wachtwoorden zijn gelijk";
} else {
    echo "Verschillende wachtwoorden!";
}
