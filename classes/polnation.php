<?php
include_once __DIR__ . "/parliament.php";
class PolNation {
    public $ID;
    public $abbrev;
    public $name;
    public $empire;
    public $parties = array();
    public $hasParliament = false;
    public $parliament;

    function __construct($link, $ID, $withParties = false) {
        $data = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM nations WHERE ID = {$ID}"));
        $this->ID = $data['ID'];
        $this->abbrev = $data['abbrev'];
        $this->name = $data['name'];
        $this->empire = $data['empire'];
        $this->parliament = new Parliament($link, mysqli_fetch_array(mysqli_query($link, "SELECT ID FROM parliament WHERE nationID = {$ID}"))[0]);
    }
}

?>