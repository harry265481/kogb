<?php
/*
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    include_once "config.php";
    $provinces = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM provinces"), MYSQLI_ASSOC);
    foreach($provinces as $p) {
        $pid = $p['ID'];
        $cap = $p['capitalists'];
        if($cap > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 9, {$cap})");
        }

        $off = $p['officers'];
        if($off > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 8, {$off})");
        }

        $clerks = $p['clerks'];
        if($clerks > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 7, {$clerks})");
        }

        $clergy = $p['clergymen'];
        if($clergy > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 6, {$clergy})");
        }

        $bureaucrats = $p['bureaucrats'];
        if($bureaucrats > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 5, {$bureaucrats})");
        }

        $art = $p['artisans'];
        if($art > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 4, {$art})");
        }

        $crafts = $p['craftsmen'];
        if($crafts > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 3, {$crafts})");
        }

        $soldiers = $p['soldiers'];
        if($soldiers > 0) {
            mysqli_query($link, "INSERT INTO pops (province, type, size) VALUES ({$pid}, 2, {$soldiers})");
        }

        $laborers = $p['laborers'];
        $wp = json_decode($p['percentWorkplaces']);
        foreach($wp as $w) {
            $workers = ($w[0] / 100) * $laborers;
            mysqli_query($link, "INSERT INTO pops (province, type, size, production) VALUES ({$pid}, 1, {$workers}, {$w[1]})");
        }
    }
*/
?>