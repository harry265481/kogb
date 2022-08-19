<?php
include_once __DIR__ . "/person.php";
include_once __DIR__ . "/mp.php";
include_once __DIR__ . "/constituency.php";
class House {
    public $ID;
    public $members = array();
    public $position = 1;
    public $type = 0;
    public $name = "";
    public $parliamentID = 1;
    public $presidingPosition;
    public $presidingPositionName;
    public $presidingOfficer;
    public $presidingOfficerName;
    public $MPs = array();

    function __construct($link, $ID) {
        $sHouse = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM parliamenthouse WHERE ID = {$ID}"));
        $this->ID = $ID;
        $this->type = $sHouse['Type'];
        $this->name = $sHouse['Name'];
        $this->parliamentID = $sHouse['parliamentID'];

        $this->presidingPosition = $sHouse['presidingPosition'];
        $this->presidingPositionName = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM positions WHERE ID = {$this->presidingPosition}"))[0];

        $this->presidingOfficer = mysqli_fetch_array(mysqli_query($link, "SELECT holderID FROM positions WHERE ID = {$this->presidingPosition}"))[0];
        $sOfficerName = mysqli_fetch_array(mysqli_query($link, "SELECT holderID FROM positions WHERE ID = {$this->presidingPosition}"))[0];
        if($sOfficerName == "" || $sOfficerName == NULL) {
            $this->presidingOfficerName = "<i>Vacant</i>";
        } else {
            $this->presidingOfficerName = Person::getDisplayName($link, $sOfficerName);
        }

        if($this->type == 0) {
            $members = mysqli_fetch_all(mysqli_query($link,"SELECT ID FROM people WHERE House = {$this->ID}"));
            foreach($members as $m) {
                $valid = false;
                $p = Person::fromID($link, $m[0], $valid);
                if($valid) {
                    array_push($this->members, $p);
                }
            }
        } else if($this->type == 1) {
            $data = mysqli_fetch_all(mysqli_query($link, "SELECT ID, seatID FROM employedmps"));
            foreach($data as $mp) {
                if(Constituency::getConstituencyParliament($link, $mp[1]) == $this->ID) {
                    array_push($this->MPs, new MP($link, $mp[0]));
                }
            }
        }
    }

    function getParliamentName($link) {
        $nationID = mysqli_fetch_array(mysqli_query($link,"SELECT nationID FROM parliament WHERE ID = {$this->parliamentID}"))[0];
        return "Parliament of " . mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM nations WHERE ID =  {$nationID}"))[0];
    }

    static function getHouseName($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM parliamenthouse WHERE ID = {$ID}"))[0];
    }

    static function getHouseColor($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link,"SELECT `colorINT` FROM parliamenthouse WHERE ID = {$ID}"))[0];
    }

    static function getHouseChannelID($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link,"SELECT `channelID` FROM parliamenthouse WHERE ID = {$ID}"))[0];
    }

    static function getHouseType($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link,"SELECT `Type` FROM parliamenthouse WHERE ID = {$ID}"))[0];
    }
}
?>