<?php
class Garrison {
    static function getAllGarrisons($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM barracks"));
    }

    static function getGarrison($link, $id) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM barracks WHERE ID = {$id}"));
    }
}
?>