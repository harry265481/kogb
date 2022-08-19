<?php
class Army {
    public $ID;
    public $name;
    public $nation;
    public $location;
    public $leader;
    public $inGarrison;
    public $garrison;
    public $units = array();

    function __construct($link, $ID) {
        $sql = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM armies WHERE ID = {$ID}"));
        $this->ID = $sql['ID'];
        $this->name = $sql['name'];
        $this->nation = $sql['nation'];
        $this->leader = $sql['leader'];
        $this->inGarrison = $sql['inGarrison'];
        $this->garrison = $sql['Garrison'];
        $this->location = $sql['location'];
    }
    
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