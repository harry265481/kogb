<?php
class Constituency {
    public $ID;
    public $name;
    public $member1;
    public $member2;
    public $member3;
    public $member4;

    function __construct($link, $ID) {

    }

    static function getAllIDName($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT ID, Name FROM `seats` ORDER BY `seats`.`Name` ASC"));
    }
    
    static function getConstituencyName($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT Name FROM `seats` WHERE ID = {$ID}"))[0];
    }
}
?>