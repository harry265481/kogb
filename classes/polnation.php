<?php

class PolNation {
    public $abbrev;
    public $name;
    public $parties = array();
    public $hasParliament = false;
    public $parliament;

    function __construct($link, $ID, $withParties = false) {
        $data = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM nations WHERE ID = {$ID}"));
        $this->abbrev = $data['abbrev'];
        $this->name = $data['name'];
    }
}

?>