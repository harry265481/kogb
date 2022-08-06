<?php

include_once __DIR__ . "/resource.php";
include_once(__DIR__ . '/../defines.php');

class Pop {
    protected $ID = 0;
    protected $province = 0;
    protected ?Province $provinceRef;
    protected int $type = 0;
    protected $size = 0;
    protected $production = NULL;
    protected $money = 0;
    protected $lifeneedsmet = array();
    protected $everydayneedsmet = array();
    protected $luxuryneedsmet = array();
    protected $lifeneeds = array();
    protected $everydayneeds = array();
    protected $luxuryneeds = array();
    protected $profit = 0;

    function __construct($link, $ID) {
        $this->ID = $ID;
        $this->getPopInfo($link);
        $this->getPopNeeds($link);
    }

    protected function getPopNeeds($link) {
        $needs = mysqli_fetch_array(mysqli_query($link, "SELECT lifeneeds, everydayneeds, luxuryneeds FROM poptypes WHERE ID = {$this->type}"), MYSQLI_NUM);
        $this->lifeneeds = $this->getPopLifeNeeds(json_decode($needs[0]));
        $this->everydayneeds = $this->getPopEverydayNeeds(json_decode($needs[1]));
        $this->luxuryneeds = $this->getPopLuxuryNeeds(json_decode($needs[2]));
    }

    protected function getPopLifeNeeds($needs) {
        $popNeeds = array();
        foreach($needs as $n) {
            $popNeeds[intval($n[0])] = 0;
            $this->lifeneedsmet[intval($n[0])] = 0;
            $y = 1 * $n[1] * BASE_POP_DEMAND * ($this->size / NEEDS_POP);
            $popNeeds[intval($n[0])] += $y;
        }
        return $popNeeds;
    }

    protected function getPopEverydayNeeds($needs) {
        $popNeeds = array();
        foreach($needs as $n) {
            $popNeeds[$n[0]] = 0;
            $this->everydayneedsmet[$n[0]] = 0;
            $y = 1 * $n[1] * BASE_POP_DEMAND * ($this->size / NEEDS_POP);
            $popNeeds[$n[0]] += $y;
        }
        return $popNeeds;
    }

    protected function getPopLuxuryNeeds($needs) {
        $popNeeds = array();
        foreach($needs as $n) {
            $popNeeds[$n[0]] = 0;
            $this->luxuryneedsmet[$n[0]] = 0;
            $y = 1 * $n[1] * BASE_POP_DEMAND * ($this->size / NEEDS_POP);
            $popNeeds[$n[0]] += $y;
        }
        return $popNeeds;
    }

    protected function getPopInfo($link) {
        $info = mysqli_fetch_array(mysqli_query($link, "SELECT province, `type`, size, production, money, lifeneedsmet, everydayneedsmet, luxuryneedsmet FROM pops WHERE ID = {$this->ID}"), MYSQLI_ASSOC);
        $this->province = $info['province'];
        $this->type = $info['type'];
        $this->size = $info['size'];
        $this->production = $info['production'];
        if($info['money'] > 0) {
            $this->money = $info['money'];
        } else {
            $this->money = 0;
        }
    }

    function setProvinceRef(&$province) {
        $this->provinceRef = $province;
    }

    function getID() {
        return $this->ID;
    }

    function getProvince() {
        return $this->province;
    }

    function getType() {
        return $this->type;
    }

    function getSize() {
        return $this->size;
    }

    function getProduction() {
        return $this->production;
    }

    function getPopRGOOutput($link) {
        $bcrats = Province::getNumPopsOfTypeInProvince($link, $this->ID, 5);
        $workers = $this->size;
        $oa = Resource::getResourceOutput($link, $this->production);
        $rt = Resource::getResourceType($link, $this->production);
        $rtem = $this->provinceRef->getTerrain()->getResourceTerrainEfficiencyModifiersByType($rt);
        $rtsm = $this->provinceRef->getTerrain()->getResourceTerrainSizeModifiersByType($rt);
        $baseSize = $this->provinceRef->getBaseSize($rt);

        $bp = $baseSize * $rtsm * $oa;
        $tp = ($workers / (MAX_WORKERS * $baseSize));
        $oe = 1 + ($bcrats / $workers) + $rtem;
        $output = $bp * $tp * $oe;
        return $output;
    }

    function payPop($link, $amount, $sql = true) {
        if($amount > 0) {
            $this->money += $amount;
            if($sql) {
                mysqli_query($link, "UPDATE pops SET `money` = money + {$amount} WHERE ID = {$this->ID}");
            }
        } else {
            if($this->money <= 0) {
                $this->money = 0;
                if($sql) {
                    mysqli_query($link, "UPDATE pops SET `money` = 0 WHERE ID = {$this->ID}");
                }
            } else {
                $this->money += $amount;
                if($sql) {
                    mysqli_query($link, "UPDATE pops SET `money` = money + {$amount} WHERE ID = {$this->ID}");
                }
            }
        }
    }

    function getMoney() {
        return $this->money;
    }
    
    function getLifeNeeds() {
        return $this->lifeneeds;
    }

    function getEverydayNeeds() {
        return $this->everydayneeds;
    }

    function getLuxuryNeeds() {
        return $this->luxuryneeds;
    }

    function feedLifeNeed($which, $amount) {
        $f = $this->lifeneeds[$which] / $amount;
        $this->lifeneedsmet[$which] += $f;
    }

    function feedEverydayNeed($which, $amount) {
        $f = $this->everydayneeds[$which] / $amount;
        $this->everydayneeds[$which] += $f;
    }

    function feedLuxuryNeed($which, $amount) {
        $f = $this->luxuryneeds[$which] / $amount;
        $this->luxuryneeds[$which] += $f;
    }

    function changePopulation($link, $amount, $money = 0) {
        $this->size += $amount;
        mysqli_query($link, "UPDATE `pops` SET `size`={$this->size} WHERE ID = {$this->ID}");
    }

    //Percentage should be a decimal between 0 and 1
    function changeProduction($link, $percentage, $newID) {
        $movingPop = round($this->size * $percentage);
        
        //They take their share of money too
        $movingmoney = $this->money * $percentage;

        //Check if a population already makes that resource
        $ePop = mysqli_query($link, "SELECT ID FROM pops WHERE province = {$this->province} AND production = {$newID}");
        if(mysqli_num_rows($ePop) > 0) {
            //Assign percentage of previous pop to the new one
            $this->changePopulation($link, 0 - $movingPop);
            $this->payPop($link, 0 - $movingmoney);
        } else {
            //if it does not, make one
            //$this->changePopulation($link, $movingPop);
            //$this->payPop($link, 0 - $movingmoney);
            mysqli_query($link, "INSERT INTO `pops` (`province`, `type`, `size`, `production`, `money`) VALUES ('{$this->province}', '4', '{$movingPop}', '{$newID}', '{$movingmoney}')");
        }
    }
}
?>