<?php 

include_once __DIR__ . "/province.php";

class Country {
    protected $ID = 0;
    protected $name = "";
    protected $provinces = array();

    function __construct($link, $ID) {
        $this->ID = $ID;
        $this->name = $this->getCountryName($link, $ID);
        $this->provinces = $this->getCountryProvinces($link, $ID);
    }

    protected function getCountryName($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM countries WHERE ID = {$this->ID}"))[0];
    }

    protected function getCountryProvinces($link, $ID) {
        $sProvinces = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM provinces WHERE Country = {$this->ID}"));
        $aProvinces = array();
        foreach($sProvinces as $fProvince) {
            array_push($aProvinces, new Province($link, $fProvince[0]));
        }
        return $aProvinces;
    }

    function getID() {
        return $this->ID;
    }

    function getName() {
        return $this->name;
    }

    function getProvinces() {
        return $this->provinces;
    }
}

?>