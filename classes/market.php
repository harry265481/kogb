<?php
include_once "resource.php";
include_once(__DIR__ . '/../functions.php');

class Market implements ArrayAccess {

    public $stockpile = array();

    function __construct($link) {
        $rIDs = Resource::getAllResourceIDs($link);

        foreach($rIDs as $r) {
            $stockpile[intval($r[0])] = 0;
        }
        print_r2($stockpile);
    }

    function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->stockpile[] = $value;
        } else {
            $this->stockpile[$offset] = $value;
        }
    }

    function offsetExists($offset) {
        return isset($this->stockpile[$offset]);
    }

    function offsetUnset($offset) {
        unset($this->stockpile[$offset]);
    }

    function offsetGet($offset) {
        return isset($this->stockpile[$offset]) ? $this->stockpile[$offset] : null;
    }

    function takeInExcess($excess) {
        foreach($excess as $r => $e) {
            if(!isset($this->stockpile[$r])){
                $this->stockpile[$r] = 0;
            }
            $this->stockpile[$r] += $e;
        }
    }
}
?>