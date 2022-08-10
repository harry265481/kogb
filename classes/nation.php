<?php 

include_once __DIR__ . "/country.php";
include_once __DIR__ . "/market.php";

class Nation {
    public $ID = 0;
    public $name = "";
    public $abbrev = "";
    public $countries = array();
    public $headOfGovPos = 0;
    public $empire = 0;
    public $parliamentIDs;
    public Market $commonMarket;

    static function getNationDetails($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM nations WHERE ID = {$ID}"));
    }

    function __construct($link, $ID) {
        $this->ID = $ID;
        $this->name = $this->getNationName($link);
        $this->abbrev = $this->getNationAbbrev($link);
        $this->countries = $this->getNationCountries($link);
        $this->empire = $this->getNationEmpire($link);
        $this->headOfGovPos = $this->getNationGovPos($link);
        $this->commonMarket = new Market($link);
        $this->parliamentIDs = $this->getNationParliament($link);
    }

    private function getNationName($link) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM nations WHERE ID = {$this->ID}"))[0];
    }

    private function getNationAbbrev($link) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT abbrev FROM nations WHERE ID = {$this->ID}"))[0];
    }

    private function getNationEmpire($link) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT empire FROM nations WHERE ID = {$this->ID}"))[0];
    }

    private function getNationGovPos($link) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT headOfGovPos FROM nations WHERE ID = {$this->ID}"))[0];
    }

    private function getNationParliament($link) {
        $sql = mysqli_query($link, "SELECT houseIDs FROM parliament WHERE nationID = {$this->ID}");
        if(mysqli_num_rows($sql)) {
            return mysqli_fetch_array($sql)[0];
        } else {
            return false;
        }
    }

    private function getNationCountries($link) {
        $sCountries = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM countries WHERE nationID = {$this->ID}"));
        $aCountries = array();
        foreach($sCountries as $fCountry) {
            $oCountry = new Country($link, $fCountry[0]);
            $aCountries[$oCountry->ID] = $oCountry;
        }
        return $aCountries;
    }

    function getPopulation() {
        $population = 0;
        foreach($this->countries as $c) {
            $population += $c->getPopulation();
        }
        return $population;
    }

    function getID() {
        return $this->ID;
    }

    function getName() {
        return $this->name;
    }

    function getCountries() {
        return $this->countries;
    }

    function &getMarket() {
        return $this->commonMarket;
    }

    function addToMarket($resource, $amount) {
        $this->commonMarket[$resource] += $amount;
    }

    function takeFromMarket($resource, $amount) {
        if($this->commonMarket[$resource] == 0 || !isset($this->commonMarket[$resource])) {
            $this->commonMarket[$resource] = 0 - $amount;
        } else {
            $this->commonMarket[$resource] -= $amount;
        }   
    }

    function marketRequest($which, $amount){
        if($this->commonMarket[$which] >= $amount) {
            $this->takeFromMarket($which, $amount);
            //$this->commonMarket[$which] -= $amount;
            return $amount;
        } else if($this->commonMarket[$which] > 0) {
            $avail = $this->commonMarket[$which];
            $this->takeFromMarket($which, $amount);
            //$this->commonMarket[$which] -= $amount;
            return $avail;
        } else {
            $this->commonMarket[$which] -= $amount;
            return false;
        }
    }

    function marketRecipeRequest($recipe, $amount){
        $delivery = array();
        foreach($recipe as $i => $a) {
            $requestA = $a * $amount;
            //Check if there's enough to fullfill the request
            if($this->commonMarket->stockpile[$i] >= $requestA) {
                //There is
                $enough = false;
                $this->takeFromMarket($i, $requestA);
                $delivery[$i] = $requestA;
                //return $amount;
            } else if($this->commonMarket->stockpile[$i] > 0) {
                //There's not but there's some
                $avail = $this->commonMarket->stockpile[$i];
                $this->takeFromMarket($i, $requestA);
                $delivery[$i] = $this->avail;
            } else {
                $this->takeFromMarket($i, $requestA);
            }
        }
        $dFactor = 0;
        foreach($delivery as $i => $r) {
            $e = $r / $recipe[$i];
            if($dFactor == 0 || $dFactor > $e) {
                $dFactor = $e;
            }
        }
        foreach($delivery as $i => $r) {
            $delivery[$i] = $recipe[$i] * $dFactor;
        }
        return $delivery;
    }

    function exportExcess(&$globalMarket) {
        $exports = array();
        foreach($this->commonMarket->stockpile as $resource => $amount) {
            $exports[$resource] = $amount;
            $this->commonMarket->stockpile[$resource] = 0;
        }
        $globalMarket->takeInExcess($exports);
    }

    function exportExcessLifeNeeds(&$globalMarket) {
        $exports = array();
        foreach($this->commonMarket->stockpile as $resource => $amount) {
            if($resource)
            $exports[$resource] = $amount;
            $this->commonMarket->stockpile[$resource] = 0;
        }
        $globalMarket->takeInExcess($exports);
    }
}
?>