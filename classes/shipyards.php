<?php
class Shipyard {
    public static function getAllShipyards($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM shipyards"));
    }
}
?>