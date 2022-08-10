<?php
class Fleet {
    static function getFleetNames($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT name FROM fleets"));
    }

    static function getFleetName($link, $id) {
        $sql = mysqli_query($link, "SELECT name FROM fleets WHERE ID = {$id}");
        if(mysqli_num_rows($sql) > 0) {
            return mysqli_fetch_array($sql)[0];
        } else {
            return 0;
        }
    }

    static function getFleets($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM fleets"));
    }
    
    static function getFleetSize($link, $id) {
        return mysqli_num_rows(mysqli_query($link, "SELECT ID FROM ships WHERE fleet = {$id}"));
    }

    static function getFleet($link, $id) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM fleets WHERE ID = {$id}"));
    }
    
    static function getShipsInFleet($link, $id) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM ships WHERE fleet = {$id}"), MYSQLI_ASSOC);
    }
}
?>