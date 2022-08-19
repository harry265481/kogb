<?php
include_once __DIR__ . "/../config.php";
$house = $_GET['id'];
$pp = mysqli_fetch_array(mysqli_query($link, "SELECT presidingPosition FROM parliamenthouse WHERE ID = {$house}"))[0];
$holderID;
$hs = mysqli_query($link, "SELECT holderID FROM positions WHERE ID = {$pp}");
if(mysqli_num_rows($hs) > 0){
    $holderID = mysqli_fetch_array($hs)[0];
    $user = mysqli_fetch_array(mysqli_query($link, "SELECT User FROM people WHERE ID = {$holderID}"))[0];
    $discordID = mysqli_fetch_array(mysqli_query($link, "SELECT discordID FROM users WHERE ID = {$user}"))[0];
    header("Content-type: text/plain");
    echo $discordID;
} else {
    header("Content-type: text/plain");
    echo 0;
}
?>