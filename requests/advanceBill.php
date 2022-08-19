<?php
include_once __DIR__ . "/../config.php";
include_once __DIR__ . "/../classes/bill.php";
include_once __DIR__ . "/../classes/parliament.php";
include_once __DIR__ . "/../classes/house.php";
$channelID = $_GET['channel'];
$bill = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM bills WHERE channelID = {$channelID}"));
$house = new House($link, $bill['House']);
$parliament = $house->parliamentID;
$houses = json_decode(Parliament::getParliamentHouses($link, $parliament));
if($bill['Stage'] == 4) {
    mysqli_query($link, "UPDATE bills SET Stage = Stage + 1 WHERE channelID = {$channelID}");
    $otherHouse;
    foreach($houses as $h) {
        if($h != $house->ID) {
            $otherHouse = $h;
            break;
        }
    }
    mysqli_query($link, "UPDATE bills SET House = {$otherHouse} WHERE channelID = {$channelID}");
    $houseChannel = mysqli_fetch_array(mysqli_query($link, "SELECT channelID FROM parliamenthouse WHERE ID = {$otherHouse}"))[0];
    echo 0 . "," . $houseChannel;
} else if($bill['Stage'] == 10) {
    echo 2 . ",996438909972127874";
} else {
    mysqli_query($link, "UPDATE bills SET Stage = Stage + 1 WHERE channelID = {$channelID}");
    echo 1;
}
?>