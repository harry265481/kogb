<?php
include_once __DIR__ . "/polnation.php";
include_once __DIR__ . "/position.php";

class Person {
    public $ID;
    public bool $approved = false;
    public $firstname;
    public $lastname;
    public $fullname;
    public $birthyear;
    public $party;
    public $title;
    public $purse;
    public $purseString;
    public $house;
    public $positions = array();
    public PolNation $nation;

    function __construct($link, $ID, &$valid) {
        $this->ID = $ID;
        if($this->getPlayerData($link, $ID)) {
            $valid = true;
        } else {
            $valid = false;
        }
    }

    static function fromID($link, $ID, &$valid) {
        return new static($link, $ID, $valid);
    }

    static function fromUserID($link, $ID, &$valid) {
        $ID = mysqli_fetch_assoc(mysqli_query($link, "SELECT ID FROM people WHERE User = {$ID}"))['ID'];
        return new static($link, $ID, $valid);
    }

    private function getPlayerData($link, $ID) {
        $this->approved = true;
        $sChar = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM people WHERE ID = {$ID}"));
        $this->firstname = $sChar["FirstName"];
        $this->lastname = $sChar["LastName"];
        $this->fullname = $this->firstname . " " . $this->lastname;
        $this->birthyear = $sChar["BirthYear"];
        $this->party = $sChar["Party"];
        $this->title = $sChar["NobleTitle"];
        $this->purse = $sChar["purse"];
        $this->purseString = number_format(floatval($this->purse));
        $this->house = $sChar["House"];
        $this->positions = Position::getPositionsOfPerson($link, $ID);

        //TODO Make this work for more than just britain
        //TODO Make this not load for every time this class is created
        $this->nation = new PolNation($link, 1);
        return true;
    }

    static function getPlayerName($link, $id) {
        $name = mysqli_fetch_array(mysqli_query($link, "SELECT FirstName, LastName FROM people WHERE ID = {$id}"));
        return $name['FirstName'] . " " . $name['LastName'];
    }

    static function getDisplayName($link, $id) {
        $name = mysqli_fetch_array(mysqli_query($link, "SELECT FirstName, LastName, NobleTitle FROM people WHERE ID = {$id}"));
        if(isset($name['NobleTitle']) && $name['NobleTitle'] != "") {
            return "The " . $name['NobleTitle'];
        } else {
            return $name['FirstName'] . " " . $name['LastName'];
        }
    }

}

?>