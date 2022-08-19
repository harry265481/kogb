<?php
include_once __DIR__ . "/person.php";

class Position {
    public $ID;
    public $name;

    static $termDesc = array("At His Majesty's Pleasure", "Life", "\"Good Behaviour\"", );

    public static function getPositionHolder($link) {}

    public static function getPositionName($link, $id) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM positions WHERE ID = {$id}"))[0];
    }

    public static function getPositionHolderName($link, $id) {
        $sql = mysqli_fetch_array(mysqli_query($link, "SELECT holderID FROM positions WHERE ID = {$id}"))[0];
        if($sql == null || $sql == "") {
            return "<i>Vacant</i>";
        } else {
            return Person::getDisplayName($link, $sql);
        }
    }
    
    public static function getPositionsAppointedBy($link, $id) {
        $sql = mysqli_query($link, "SELECT * FROM positions WHERE appointer = {$id}");
        if(mysqli_num_rows($sql) > 0) {
            return mysqli_fetch_all($sql, MYSQLI_ASSOC);
        } else {
            return false;
        }
    }

    public static function getPositionsOfPerson($link, $ID) {
        $sql = mysqli_query($link, "SELECT * FROM positions WHERE holderID = {$ID}");
        if(mysqli_num_rows($sql) > 0) {
            return mysqli_fetch_all($sql, MYSQLI_ASSOC);
        } else {
            return false;
        }

    }
}
?>