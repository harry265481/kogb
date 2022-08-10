<?php
class Army {
    public $ID;
    public $units = array();
    public $nation;
    public $location = array();
    public $leader;
    
    public static function getAllArmies($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM armies"));
    }
    
    public static function getArmyName($link, $id) {
        $sql = mysqli_query($link, "SELECT * FROM armies WHERE ID = {$id}");
        if(mysqli_num_rows($sql) > 0) {
            return mysqli_fetch_array($sql)[2];
        } else {
            return "";
        }
    }
}
?>