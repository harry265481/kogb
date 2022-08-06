<?php

class Terrain {
    protected $ID = 0;
    protected $name = "";
    protected $color = "";
    protected $farmSize = 1;
    protected $farmEfficiency = 1;
    protected $mineSize = 1;
    protected $mineEfficiency = 1;
    protected $timberSize = 1;
    protected $timberEfficiency = 1;
    protected $tropicalSize = 1;
    protected $tropicalEfficiency = 1;

    function __construct($link, $ID){
        $this->ID = $ID;
        $this->getTerrainInfo($link);
    }

    function getTerrainInfo($link) {
        $sInfo = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM terraintypes WHERE ID = {$this->ID}"), MYSQLI_ASSOC);
        $this->name = $sInfo['name'];
        $this->color = $sInfo['color'];
        $this->farmSize = $sInfo['farmSize'];
        $this->farmEfficiency = $sInfo['farmEfficiency'];
        $this->mineSize = $sInfo['mineSize'];
        $this->mineEfficiency = $sInfo['mineEfficiency'];
        $this->timberSize = $sInfo['timberSize'];
        $this->timberEfficiency = $sInfo['timberEfficiency'];
        $this->tropicalSize = $sInfo['tropicalSize'];
        $this->tropicalEfficiency = $sInfo['tropicalEfficiency'];
    }

    //Should be made deprecated soon
    function getResourceTerrainSizeModifiers() {
        return [$this->farmSize, $this->mineSize, $this->timberSize, $this->tropicalSize];
    }

    //Should be made deprecated soon
    function getResourceTerrainEfficiencyModifiers() {
        return [$this->farmEfficiency, $this->mineEfficiency, $this->timberEfficiency, $this->tropicalEfficiency];
    }


    function getResourceTerrainSizeModifiersByType($type) {
        switch ($type) {
            case 1:
                return $this->farmSize;
                break;
            case 2:
                return $this->mineSize;
                break;
            case 3:
                return $this->timberSize;
                break;
            case 4:
                return $this->tropicalSize;
                break;
        }
        return 0;
    }

    function getResourceTerrainEfficiencyModifiersByType($type) {
        switch ($type) {
            case 1:
                return $this->farmEfficiency;
                break;
            case 2:
                return $this->mineEfficiency;
                break;
            case 3:
                return $this->timberEfficiency;
                break;
            case 4:
                return $this->tropicalEfficiency;
                break;
        }
    return 0;
    }
}

?>