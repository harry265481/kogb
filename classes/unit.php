<?php
class Unit {
    public $ID;
    public $size;
    public $type;
    public $location = array();

    public static $types = array("Horse Guards", "Horse Grenadier Guards", "Dragoon Guards", "Dragoons", "Foot Guards", "Foot");
    public static $costperman = array(95.85, 61.4, 50.5, 27.8, 18.6);
    
    public static function getAllUnits($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM units"));
    }
}
?>