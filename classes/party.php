<?php
class Party {
    public $ID;
    public $name;
    public $Color;
    public $housePosition;
    public $playerMembers = array();

    static $monarchyStances = array("Crown Supremacy", "Parliamentary Supremacy");
    static $tradeStances = array("Protectionism", "Free Trade");
    static $tariffStances = array("Pro-Import Tariffs", "Anti-Import Tariffs");
    static $religiousStances = array("Anglicanism", "Pluralism", "Catholicism");
    static $navyStances = array("Larger Navy", "Maintenance", "Smaller Navy");
    static $armyStances = array("Larger Army", "Maintenance", "Smaller Army");
    static $colonialStances = array("Expansion", "Maintenance");
    static $foreignStance = array("Interventionist", "See party description", "Isolationist");
    static $scotlandStances = array("Further Integration", "Status Quo", "Devolution");
    static $irelandStances = array("Integration", "Status Quo", "Constitutionalism");

    static function getPartyDetails($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM parties WHERE ID = {$ID}"));
    }

    static function getPartyPosition($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link,"SELECT `Position` FROM parties WHERE ID = {$ID}"))[0];
    }

    static function getAllParties($link, $parliament) {
        return mysqli_fetch_all(mysqli_query($link,"SELECT * FROM parties WHERE Parliament = {$parliament}"), MYSQLI_ASSOC);
    }

    static function getAllPartyMembers($link, $ID) {
        return mysqli_fetch_all(mysqli_query($link,"SELECT ID FROM people WHERE Party = {$ID}"), MYSQLI_ASSOC);
    }
}
?>