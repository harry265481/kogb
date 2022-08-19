<?php
class University {
    public $ID;
    public $name;
    public $royalPrerogative;
    public $hasConstituency;
    public $seatID;
    public $ChancellorPosID;
    public $electors = array();
    public $parties = array();

    function __construct($link, $ID) {
        $sql = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM university WHERE ID = {$ID}"), MYSQLI_ASSOC);
        $this->ID = $sql['ID'];
        $this->name = $sql['name'];
        $this->royalPrerogative = (bool) $sql['royalPrerogative'];
        $this->hasConstituency = (bool) $sql['hasConstituency'];
        if($this->hasConstituency) {
            $this->seatID = $sql['seatID'];
            $this->electors = json_decode($sql['electors']);
            $this->parties = json_decode($sql['parties']);
        }
        $this->ChancellorPosID;
    }

    static function fromSeatID($link, $ID) {
        return new static($link, mysqli_fetch_array(mysqli_query($link, "SELECT ID FROM university WHERE seatID = {$ID}"))[0]);
    }

    static function fromID($link, $ID) {
        return new static($link, $ID);
    }
}
?>