<?php
include_once __DIR__ . "/../newchannel.php";
include_once __DIR__ . "/discord.php";
include_once __DIR__ . "/house.php";
include_once __DIR__ . "/person.php";
class Bill {
    public $ID;
    public $shortTitle;
    public $longTitle;
    public $author;
    public $Stage;
    public $text;
    public $origin; 

    static $stages = array("First Reading", "Second Reading", "Committee Stage", "Report Stage", "Third Reading", "First Reading", "Second Reading", "Committee Stage", "Report Stage", "Third Reading", "Consideration of amendments", "Royal Assent");

    function __construct($link, $id) {
        $bill = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM bills WHERE ID = {$id}"));
        $this->ID = $bill['ID'];
        $this->longTitle = $bill['longTitle'];
        $this->shortTitle = $bill['shortTitle'];
        $this->author = $bill['author'];
        $this->Stage = $bill['Stage'];
        $this->text = $bill['text'];
        $this->origin = $bill['Origin'];
    }

    static function getHouseBills($link, $houseID) {
        $array = array();
        $bills = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM bills WHERE House = {$houseID}"));
        foreach($bills as $bill) {
            array_push($array, new static($link, $bill[0]));
        }
        return $array;
    }

    static function getBill($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM bills WHERE ID = {$ID}"));
    }

    static function getBillVotingRecord($link, $ID) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM votingRecord WHERE billID = {$ID}"), MYSQLI_ASSOC);
    }

    static function setBillChannelID($link, $ID, $channel) {
        return mysqli_query($link, "UPDATE bills SET channelID = {$channel} WHERE ID = {$ID}");
    }

    static function insertNewBill($link, $shortTitle, $longTitle, $text, $author, $house) {
        $sql = "INSERT INTO `bills` (`shortTitle`, `longTitle`, `author`, `House`, `Origin`, `text`) VALUES (?,?,?,?,?,?)";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssiiis", $param_st, $param_lt, $param_a, $param_h, $param_o, $param_t);
            $param_st = mysqli_real_escape_string($link, $shortTitle);
            $param_lt = mysqli_real_escape_string($link, $longTitle);
            $param_a = mysqli_real_escape_string($link, $author);
            $param_h = mysqli_real_escape_string($link, $house);
            $param_o = mysqli_real_escape_string($link, $house);
            $param_t = mysqli_real_escape_string($link, $text);
            mysqli_stmt_execute($stmt);
        }
    }

    static function putBillToFloor($link, $ID) {
        $iBill = Bill::getBill($link, $ID);
        $shortTitle = stripslashes($iBill['shortTitle']);
        $longTitle = stripslashes($iBill['longTitle']);

        mysqli_query($link, "UPDATE bills SET stage = 1 WHERE ID = {$ID}");

        $channelID = House::getHouseChannelID($link, $iBill['House']);
        $color = House::getHouseColor($link, $iBill['House']);
        $userID = Person::getUserID($link, $iBill['author']);
        $user = mysqli_fetch_array(mysqli_query($link, "SELECT discordID, discordAvatar, discordUser FROM users WHERE ID = {$userID}"));
        $newChannel = Discord::newGuildChannel("996436329837629460", array("name" => $shortTitle, "type" => 0, "topic" => $longTitle, "parent_id" => $channelID));
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/bill.php?id={$ID}";
$json = "{
    \"embeds\": [{
        \"title\": \"{$shortTitle}\",
        \"description\": \"{$longTitle}\",
        \"url\": \"{$url}\",
        \"timestamp\": \"{$iBill['created_at']}\",
        \"color\": {$color},
        \"author\": {
            \"name\": \"{$user[2]}\",
            \"icon_url\": \"https://cdn.discordapp.com/avatars/{$user[0]}/{$user[1]}.png\"
        }
    }]
}";
        $channelID = $newChannel['id'];
        Bill::setBillChannelID($link, $ID, $channelID);
        $newMessage = Discord::MakeRequest("channels/{$channelID}/messages", json_decode($json));
$json2 = "{
    \"content\": \"This bill has been given it's first reading and it has been printed. The author of the bill, <@{$user[0]}>, is now invited to make opening arguments for this bill. The debate should be kept to the overall intent of the bill.\"
}";
        $newMessage = Discord::MakeRequest("channels/{$channelID}/messages", json_decode($json2));
    }
}
?>