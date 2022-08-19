<?php
include_once __DIR__ . "/../config.php";
$category = $_GET['channel'];
echo mysqli_fetch_array(mysqli_query($link, "SELECT ID FROM parliamenthouse WHERE channelID = {$category}"))[0];
?>