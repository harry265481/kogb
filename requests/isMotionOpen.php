<?php
include_once __DIR__ . "/../config.php";
$motionID = $_GET['id'];
echo mysqli_fetch_array(mysqli_query($link, "SELECT `open` FROM motions WHERE ID = {$motionID}"))[0];
?>