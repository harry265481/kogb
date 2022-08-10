<?php
include_once __DIR__ . "/pop.php";
include_once __DIR__ . "/terrain.php";
include_once __DIR__ . "/../defines.php";

class Province {
    public $ID = 0;
    public $name = "";
    public Terrain $terrain;
    public $country = 0;
    public $pops = array();
    public $aOwners = array();
    public $nOwners = 0;
    public $hasOwners = false;
    public $nRGOWorkers = 0;
    public $population = 0;

    function __construct($link, $ID) {
        $this->ID = $ID;
        $this->name = $this->getProvinceName($link);
        $this->country = $this->getProvinceCountry($link);
        $this->terrain = $this->getProvinceTerrain($link);
        $this->pops = $this->getProvincePops($link);
        $this->aOwners = $this->getProvinceOwners($link);
        $this->nRGOWorkers = $this->getProvinceRGOWorkers();
        $this->population = $this->getProvincePopulation();
    }

    private function getProvinceName($link) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM provinces WHERE ID = {$this->ID}"))[0];
    }

    private function getProvinceCountry($link) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT Country FROM provinces WHERE ID = {$this->ID}"))[0];
    }

    private function getProvinceTerrain($link) {
        $sTerrain = mysqli_fetch_array(mysqli_query($link, "SELECT Terrain FROM provinces WHERE ID = {$this->ID}"))[0];
        return new Terrain($link, $sTerrain[0]);
    }

    private function getProvinceOwners($link) {
        $sOwners = mysqli_query($link, "SELECT ownerID, percentage, good FROM rgoownership WHERE provinceID = {$this->ID} AND percentage > 0");
        $aOwners = array();
        $ownercount = 0;
        if($sOwners) {
            $this->hasowners = true;
            $owners = mysqli_fetch_all($sOwners);
            $this->nOwners = count($owners);
            foreach($owners as $owner) {
                array_push($aOwners, array($owner[0], $owner[1], $owner[2]));
            }
        }
        return $aOwners;
    }

    private function getProvincePops($link) {
        $sPops = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM pops WHERE province = {$this->ID}"));
        $aPops = array();
        foreach($sPops as $fPop) {
            $oPop = new Pop($link, $fPop[0]);
            $oPop->setProvinceRef($this);
            $aPops[$oPop->getID()] = $oPop;
        }
        return $aPops;
    }

    private function getProvinceRGOWorkers() {
        $n = 0;
        foreach($this->pops as $pop) {
            if($pop->getType() == 1) {
                $n += $pop->getSize();
            }
        }
        return $n;
    }

    private function getProvincePopulation() {
        $population = 0;
        foreach($this->pops as $pop) {
            $population += $pop->getSize();
        }
        return $population * 4;
    }

    function getName() {
        return $this->name;
    } 

    function getCountry() {
        return $this->country;
    }

    function getTerrain() {
        return $this->terrain;
    }

    function getPops() {
        return $this->pops;
    }
    
    function getOwners(){
        return $this->aOwners;
    }
    
    function getNOwners(){
        return $this->nOwners;
    }
    
    function hasOwners(){
        return $this->hasOwners;
    }
    
    function getNWorkers(){
        return $this->nWorkers;
    }

    function getPopsOfType($type) {
        $rPops = array();
        foreach($this->pops as $pop) {
            if($pop->getType() == $type) {
                array_push($rPops, $pop);
            }
        }
        return $rPops;
    }

    function getNumPopsOfType($type) {
        $nPops = 0;
        foreach($this->pops as $pop) {
            if($pop->type == $type) {
                $nPops += $pop->size;
            }
        }
        return $nPops;
    }

    function getRGOWorkers() {
        return $this->nRGOWorkers;
    }

    function getPopulation() {
        return $this->population;
    }

    function getBaseSize($resourceType) {
        $rtsm = $this->terrain->getResourceTerrainSizeModifiersByType($resourceType);
        $bsize = floor(1.5 * ceil($this->nRGOWorkers / (MAX_WORKERS * $rtsm)));
        return $bsize;
    }

    static function getNumPopsOfTypeInProvince($link, $ID, $type) {
        $sPops = mysqli_query($link, "SELECT size FROM pops WHERE `type` = {$type} AND province = {$ID}");
        $nPops = 0;
        if(mysqli_num_rows($sPops) > 0) {
            $aPops = mysqli_fetch_array($sPops);
            foreach($sPops as $pop) {
                $nPops += $pop['size'];
            }
        }
        return $nPops;
    }
}
?>