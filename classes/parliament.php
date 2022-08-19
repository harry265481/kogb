<?php
class Parliament {
    public $ID;
    public $houses = array();
    public $nationID;
    public $discordChannelID;
    public $insession;

    function __construct($link, $ID) {
        $data = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `parliament` WHERE ID = {$ID}"));
        $this->ID = $data['ID'];
        $this->houses = json_decode($data['houseIDs']);
        $this->nationID = $data['nationID'];
        $this->insession = $data['inSession'];
        $this->discordChannelID = $data['monarchspeechchannel'];
    }

    function openParliament($link) {
        mysqli_fetch_array(mysqli_query($link, "UPDATE parliament SET `inSession` = 1 WHERE ID = {$nationID}"))[0];
        $this->insession = 1;
    }

    static function getParliamentHouses($link, $id) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT houseIDs FROM parliament WHERE ID = {$id}"))[0];
    }
}
?>