<?php
include_once __DIR__ . "/../config.php";
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$motionID = $obj['motionid'];
$message = $obj['messageid'];
mysqli_query($link, "UPDATE motions SET `message` = {$message} WHERE ID = {$motionID}");
?>