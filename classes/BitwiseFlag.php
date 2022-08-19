<?php
abstract class PositionBitwiseFlag {
    protected $armyFlags;
    protected $ordnanceFlags;
    protected $navyFlags;
    protected $peerageFlags;
    protected $parliamentFlags;

    protected function isArmyFlagSet($flag) {
        return (($this->armyFlags & $flag) == $flag);
    }

    protected function setArmyFlag($flag, $value) {
        if($value) {
            $this->armyFlags |= $flag;
        } else {
            $this->armyFlags &= ~$flag;
        }
    }

    protected function isOrdnanceFlagSet($flag) {
        return (($this->ordnanceFlags & $flag) == $flag);
    }

    protected function setOrdnanceFlag($flag, $value) {
        if($value) {
            $this->ordnanceFlags |= $flag;
        } else {
            $this->ordnanceFlags &= ~$flag;
        }
    }

    protected function isNavyFlagSet($flag) {
        return (($this->navyFlags & $flag) == $flag);
    }

    protected function setNavyFlag($flag, $value) {
        if($value) {
            $this->navyFlags |= $flag;
        } else {
            $this->navyFlags &= ~$flag;
        }
    }

    protected function isPeerageFlagSet($flag) {
        return (($this->peerageFlags & $flag) == $flag);
    }

    protected function setPeerageFlag($flag, $value) {
        if($value) {
            $this->peerageFlags |= $flag;
        } else {
            $this->peerageFlags &= ~$flag;
        }
    }

    protected function isParliamentFlagSet($flag) {
        return (($this->parliamentFlags & $flag) == $flag);
    }

    protected function setParliamentFlag($flag, $value) {
        if($value) {
            $this->parliamentFlags |= $flag;
        } else {
            $this->parliamentFlags &= ~$flag;
        }
    }
}
?>