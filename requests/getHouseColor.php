<?php
include_once __DIR__ . "/../config.php";
$house = $_GET['id'];
echo mysqli_fetch_array(mysqli_query($link, "SELECT colorINT FROM parliamenthouse WHERE ID = {$house}"))[0];
?>