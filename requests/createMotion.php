<?php
include_once __DIR__ . "/../config.php";
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$motion = $obj['motion'];
$channel = $obj['channel'];
$house = $obj['house'];
mysqli_query($link, "INSERT INTO motions (`motion`, `house`, `channel`) VAlUES ('{$motion}', '{$house}', '{$channel}')");
header("Content-type: text/plain");
echo mysqli_insert_id($link);
?>