<?php
include_once __DIR__ . "/../config.php";
$house = $_GET['id'];
header("Content-type: text/plain");
echo mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM parliamenthouse WHERE ID = {$house}"))[0];
?>