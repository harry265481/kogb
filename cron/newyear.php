<?php
include_once "../config.php";
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$last = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM years ORDER BY Year DESC LIMIT 1"));
$lastyear = $last[0];
$start = new DateTime($last[1]);
$weeks = $last[2];
$diff = $start->diff(new DateTime());

$newdt = date('Y-m-d H:00:00');

if(($diff->d) >= ($weeks * 7)) {
    $nyear = $lastyear + 1;
    $sql = "INSERT INTO `years` (`Year`, `yearstart`, `weeks`) VALUES ({$nyear}, '{$newdt}', {$weeks})";
    mysqli_query($link, $sql);
}
?>