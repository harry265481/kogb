<?php
include_once __DIR__ . "/../config.php";
include_once __DIR__ . "/../classes/house.php";
include_once __DIR__ . "/../classes/mp.php";
$motionID = $_GET['id'];
$houseID = $_GET['house'];
$votes = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM motionvoting WHERE motionID = {$motionID}"), MYSQLI_ASSOC);
mysqli_query($link, "UPDATE motions SET `open` = 0 WHERE ID = {$motionID}");
$finalvotes = array();
foreach ($votes as $v) {
    $finalvotes[$v['personID']] = $v['vote'];
}
$yays = 0;
$nays = 0;
$houseType = House::getHouseType($link, $houseID);
foreach($finalvotes as $p => $fv) {
    if($fv == 0) {
        if($houseType == 0) {
            $nays++;
        } else if($houseType == 1) {
            $nays += count(MP::getAllMPsEmployedIn($link, $p, $houseID));
        }
    } else if($fv == 1) {
        if($houseType == 0) {
            $yays++;
        } else if($houseType == 1) {
            $yays += count(MP::getAllMPsEmployedIn($link, $p, $houseID));
        }
    }
}
header("Content-type: text/plain");
echo $yays . "," . $nays;
?>