<?php
include_once __DIR__ . "/../config.php";
include_once __DIR__ . "/../classes/person.php";
include_once __DIR__ . "/../classes/mp.php";
$uid = $_GET['id'];
$house = $_GET['house'];
$valid = false;
$person = Person::fromDiscordID($link, $uid, $valid);

//are they a player?
$psql = mysqli_query($link, "SELECT house FROM people WHERE ID = {$person->ID}");
if(mysqli_num_rows($psql) > 0) {
    $p = mysqli_fetch_array($psql)[0];
    if($p == $house) {
        echo 2;
    } else {
        $mps = MP::getAllMPsEmployedIn($link, $person->ID, $house);
        if(count($mps) > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
} else {
    echo 0;
}
?>