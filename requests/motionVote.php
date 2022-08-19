<?php
include_once __DIR__ . "/../config.php";
include_once __DIR__ . "/../classes/person.php";
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$motion = $obj['motion'];
$person = $obj['person'];
$vote = $obj['vote'];
$valid = false;
$personObj = Person::fromDiscordID($link, $person, $valid);
$personID = $personObj->ID;
mysqli_query($link, "INSERT INTO motionvoting (`personID`,`motionID`,`vote`) VALUES ('{$personID}', '{$motion}', {$vote})");
?>