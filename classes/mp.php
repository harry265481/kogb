<?php
include_once __DIR__ . "/person.php";
include_once __DIR__ . "/parliament.php";
class MP {
    public $ID;
    public $name;
    public $employerID;
    public $partyID;
    public $seatID;
    public $purse = 0;
    public $isPlayer;

    function __construct($link, $ID) {
        $mp = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM employedmps WHERE ID = {$ID}"), MYSQLI_ASSOC);
        $this->ID = $mp['ID'];
        $this->name = $mp['Name'];
        $this->employerID = $mp['employerID'];
        $this->partyID = MP::getMPPartyID($link, $mp['ID']);
        $this->purse = $mp['purse'];
        $this->seatID = $mp['seatID'];
        $this->isPlayer = $mp['isPlayer'];
    }

    function isControlledByNPC($link) {
        return Person::isNPC($link, $this->employerID);
    }

    static function getAllMPsEmployedBy($link, $ID) {
        $q = mysqli_query($link, "SELECT * FROM employedmps WHERE employerID = {$ID}");
        if(mysqli_num_rows($q) > 0) {
            return mysqli_fetch_all($q);
        } else {
            return false;
        }
    }
    static function getAllMPsEmployedIn($link, $ID, $house) {
        $seats = mysqli_fetch_all(mysqli_query($link, "SELECT seat1, seat2, seat3, seat4 FROM seats WHERE Parliament = {$house}"), MYSQLI_ASSOC);
        $mps = array();
        foreach($seats as $s) {
            $s1 = MP::getMPEmployer($link, $s['seat1']);
            if($ID == $s1) {
                array_push($mps, new MP($link, $s['seat1']));
            }
            if(isset($s['seat2'])) {
                $s2 = MP::getMPEmployer($link, $s['seat2']);
                if($ID == $s2) {
                    array_push($mps, new MP($link, $s['seat1']));
                }
            }
            if(isset($s['seat3'])) {
                $s3 = MP::getMPEmployer($link, $s['seat3']);
                if($ID == $s3) {
                    array_push($mps, new MP($link, $s['seat1']));
                }
            }
            if(isset($s['seat4'])) {
                $s4 = MP::getMPEmployer($link, $s['seat4']);
                if($ID == $s4) {
                    array_push($mps, new MP($link, $s['seat1']));
                }
            }
        }
        return $mps;
    }

    static function getAllMPsInSeat($link, $ID) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM employedmps WHERE seatID = {$ID}"), MYSQLI_ASSOC);
    }
    
    static function getMPColor($link, $mpid) {
        $employer = mysqli_fetch_array(mysqli_query($link, "SELECT employerID FROM employedmps WHERE ID = {$mpid}"))[0];
        $party = mysqli_fetch_array(mysqli_query($link, "SELECT Party FROM people WHERE ID = {$employer}"))[0];
        $color = mysqli_fetch_array(mysqli_query($link, "SELECT Color FROM parties WHERE ID = {$party}"))[0];
        return $color;
    }

    static function getMPEmployerName($link, $ID) {
        $eid = intval(MP::getMPEmployer($link, $ID));
        $name = mysqli_fetch_array(mysqli_query($link, "SELECT FirstName, LastName FROM people WHERE ID = {$eid}"));
        $name = $name[0] . " " . $name[1];
        return $name;
    }

    static function getMPEmployer($link, $ID) {
        return (int) mysqli_fetch_array(mysqli_query($link, "SELECT employerID FROM employedmps WHERE ID = {$ID}"))[0];
    }

    static function getMPName($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM employedmps WHERE ID = {$ID}"))[0];
    }

    static function getMPPartyID($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT Party FROM people LEFT JOIN employedmps ON people.ID = employedmps.employerID WHERE employedmps.ID = {$ID}"))[0];
    }

    static function getMPPartyName($link, $mpid) {
        $employer = mysqli_fetch_array(mysqli_query($link, "SELECT employerID FROM EmployedMPs WHERE ID = {$mpid}"))[0];
        $party = mysqli_fetch_array(mysqli_query($link, "SELECT Party FROM people WHERE ID = {$employer}"))[0];
        $name = mysqli_fetch_array(mysqli_query($link, "SELECT Name FROM parties WHERE ID = {$party}"))[0];
        return $name;
    }
}
?>