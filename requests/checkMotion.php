<?php
include_once __DIR__ . "/../config.php";
$motionID = $_GET['id'];
$channel = $_GET['channel'];
$votes = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM motionvoting WHERE motionID = {$motionID}"), MYSQLI_ASSOC);
$channelID = mysqli_fetch_array(mysqli_query($link, "SELECT channel FROM motions WHERE ID = {$motionID}"))[0];
$finalvotes = array();
foreach ($votes as $v) {
    $finalvotes[$v['personID']] = $v['vote'];
}
$yays = 0;
$nays = 0;
foreach($finalvotes as $fv) {
    if($fv == 0) {
        $nays++;
    } else if($fv == 1) {
        $yays++;
    }
}
header("Content-type: text/plain");
if($yays > $nays && $channel == $channelID) {
    echo true;
} else {
    echo false;
}
?>