<?php
include_once __DIR__ . "/polnation.php";
include_once __DIR__ . "/position.php";
include_once __DIR__ . "/BitwiseFlag.php";

class Person extends PositionBitwiseFlag {
    public $ID;
    public bool $approved = false;
    public $firstname;
    public $lastname;
    public $fullname;
    public $birthyear;
    public $party;
    public $title;
    public $userID;
    public $purse;
    public $purseString;
    public $house;
    public $positions = array();
    public PolNation $nation;

    const FLAG_ARMY_CREATE_UNITS = 1;
    const FLAG_ARMY_DISBAND_UNITS = 2;
    const FLAG_ARMY_MOVE_UNITS = 4;
    const FLAG_ARMY_CREATE_ARMIES = 8;
    const FLAG_ARMY_DISBAND_ARMIES = 16;
    const FLAG_ARMY_MERGE_ARMIES = 32;
    const FLAG_ARMY_MOVE_ARMIES = 64;
    const FLAG_ARMY_LEADERS = 128;
    const FLAG_ARMY_MONEY = 256;

    const FLAG_ORDNANCE_CREATE_UNITS = 1;
    const FLAG_ORDNANCE_DISBAND_UNITS = 2;
    const FLAG_ORDNANCE_ASSIGN_UNITS = 4;
    const FLAG_ORDNANCE_BUILD_FORTS = 8;
    const FLAG_ORDNANCE_DISMANTLE_FORTS = 16;
    const FLAG_ORDNANCE_MONEY = 32;
    const FLAG_ORDNANCE_CONSTRUCT_FOUNDRIES = 64;
    const FLAG_ORDNANCE_DECONSTRUCT_FOUNDRIES = 128;
    const FLAG_ORDNANCE_CONSTRUCT_LABORATORIES = 256;
    const FLAG_ORDNANCE_DECONSTRUCT_LABORATORIES = 512;

    const FLAG_NAVY_MONEY = 1;
    const FLAG_NAVY_MOVE_FLEETS = 2;
    const FLAG_NAVY_CREATE_FLEETS = 4;
    const FLAG_NAVY_DISBAND_FLEETS = 8;
    const FLAG_NAVY_ORDER_SHIPS = 16;
    const FLAG_NAVY_SCRAP_SHIPS = 32;
    const FLAG_NAVY_PURCHASE_SUPPLIES = 64;
    const FLAG_NAVY_LEADERS = 128;
    const FLAG_NAVY_PAY_CREW = 256;

    const FLAG_PEERAGE_CREATE = 1;
    const FLAG_PEERAGE_NOMINATE = 2;
    
    const FLAG_PARLIAMENT_OPEN = 1;
    const FLAG_PARLIAMENT_PROROGUE = 2;
    const FLAG_PARLIAMENT_SPEECH = 4;

    function __construct($link, $ID, &$valid) {
        $this->ID = $ID;
        if($this->getPlayerData($link, $ID)) {
            $valid = true;
        } else {
            $valid = false;
        }
    }

    private function getPlayerData($link, $ID) {
        $this->approved = true;
        $sChar = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM people WHERE ID = {$ID}"));
        $this->userID = $sChar["User"];
        $sUser = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM users WHERE ID = {$this->userID}"));
        $this->firstname = $sChar["FirstName"];
        $this->lastname = $sChar["LastName"];
        $this->fullname = $this->firstname . " " . $this->lastname;
        $this->birthyear = $sChar["BirthYear"];
        $this->party = $sChar["Party"];
        $this->title = $sChar["NobleTitle"];
        $this->house = $sChar["House"];
        $this->userID = $sChar["User"];
        $this->discordID = $sUser["discordID"];
        $this->discordAvatar = $sUser["discordAvatar"];
        $this->purse = $sChar["purse"];
        $this->purseString = number_format(floatval($this->purse));
        $this->house = $sChar["House"];
        $this->positions = Position::getPositionsOfPerson($link, $ID);
        if($this->positions) {
            foreach($this->positions as $p) {
                $this->armyFlags = $this->armyFlags | $p['armyPermissions'];
                $this->ordnanceFlags = $this->ordnanceFlags | $p['ordnancePermissions'];
                $this->navyFlags = $this->navyFlags | $p['navyPermissions'];
                $this->peerageFlags = $this->peerageFlags | $p['peeragePermissions'];
                $this->parliamentFlags = $this->parliamentFlags | $p['parliamentPermissions'];
            }
        }

        //TODO Make this work for more than just britain
        //TODO Make this not load for every time this class is created
        $this->nation = new PolNation($link, 1);
        return true;
    }

    public function hasArmyPerms() {
        return ($this->armyFlags != 0) ? true : false;
    }
    public function canCreateUnits() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_CREATE_UNITS);
    }    
    public function canDisbandUnits() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_DISBAND_UNITS);
    }
    public function canMoveUnits() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_MOVE_UNITS);
    }
    public function canCreateArmies() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_CREATE_ARMIES);
    }
    public function canDisbandArmies() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_DISBAND_ARMIES);
    }
    public function canMergeArmies() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_MERGE_ARMIES);
    }
    public function canMoveArmies() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_MOVE_ARMIES);
    }
    public function canAssignArmyLeaders() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_LEADERS);
    }
    public function canSpendArmyMoney() {
        return $this->isArmyFlagSet(self::FLAG_ARMY_MONEY);
    }
    public function hasOrdnancePerms() {
        return ($this->ordnanceFlags != 0) ? true : false;
    }
    public function canCreateOrdnanceUnits() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_CREATE_UNITS);
    }
    public function canDisbandOrdnanceUnits() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_DISBAND_UNITS);
    }
    public function canAssignOrdnanceUnits() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_ASSIGN_UNITS);
    }
    public function canBuildForts() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_BUILD_FORTS);
    }
    public function canDismantleForts() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_DISMANTLE_FORTS);
    }
    public function canSpendOrdnanceMoney() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_MONEY);
    }
    public function canConstructFoundries() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_CONSTRUCT_FOUNDRIES);
    }
    public function canDeconstructFoundries() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_DECONSTRUCT_FOUNDRIES);
    }
    public function canConstructLaboratories() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_CONSTRUCT_LABORATORIES);
    }
    public function canDeconstructLaboratories() {
        return $this->isOrdnanceFlagSet(self::FLAG_ORDNANCE_DECONSTRUCT_LABORATORIES);
    }
    public function hasNavyPerms() {
        return ($this->navyFlags != 0) ? true : false;
    }
    public function canSpendNavyMoney() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_MONEY);
    }
    public function canMoveFleets() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_MOVE_FLEETS);
    }
    public function canCreateFleets() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_CREATE_FLEETS);
    }
    public function canDisbandFleets() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_DISBAND_FLEETS);
    }
    public function canOrderShips() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_ORDER_SHIPS);
    }
    public function canScrapShips() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_SCRAP_SHIPS);
    }
    public function canPurchaseSupplies() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_PURCHASE_SUPPLIES);
    }
    public function canAssignNavyLeaders() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_LEADERS);
    }
    public function canPayNavyCrew() {
        return $this->isNavyFlagSet(self::FLAG_NAVY_PAY_CREW);
    }
    public function hasPeeragePerms() {
        return ($this->peerageFlags != 0) ? true : false;
    }
    public function canCreatePeerage() {
        return $this->isPeerageFlagSet(self::FLAG_PEERAGE_CREATE);
    }
    public function canNominatePeerage() {
        return $this->isPeerageFlagSet(self::FLAG_PEERAGE_NOMINATE);
    }
    public function hasParliamentPerms() {
        return ($this->parliamentFlags != 0) ? true : false;
    }
    public function canOpenParliament() {
        return $this->isParliamentFlagSet(self::FLAG_PARLIAMENT_OPEN);
    }
    public function canProrogueParliament() {
        return $this->isParliamentFlagSet(self::FLAG_PARLIAMENT_PROROGUE);
    }
    public function canMakeKingsSpeech() {
        return $this->isParliamentFlagSet(self::FLAG_PARLIAMENT_SPEECH);
    }

    /*********/
    /*Statics*/
    /*********/
    static function fromID($link, $ID, &$valid) {
        return new static($link, $ID, $valid);
    }

    static function fromUserID($link, $ID, &$valid) {
        $ID = mysqli_fetch_assoc(mysqli_query($link, "SELECT ID FROM people WHERE User = {$ID}"))['ID'];
        return new static($link, $ID, $valid);
    }

    static function fromDiscordID($link, $ID, &$valid) {
        $uid = mysqli_fetch_assoc(mysqli_query($link, "SELECT ID FROM users WHERE discordID = {$ID}"))['ID'];
        $id = mysqli_fetch_assoc(mysqli_query($link, "SELECT ID FROM people WHERE User = {$uid}"))['ID'];
        return new static($link, $id, $valid);
    }

    static function getPlayerName($link, $id) {
        $name = mysqli_fetch_array(mysqli_query($link, "SELECT FirstName, LastName FROM people WHERE ID = {$id}"));
        return $name['FirstName'] . " " . $name['LastName'];
    }

    static function getDisplayName($link, $id) {
        $name = mysqli_fetch_array(mysqli_query($link, "SELECT FirstName, LastName, NobleTitle FROM people WHERE ID = {$id}"));
        if(isset($name['NobleTitle']) && $name['NobleTitle'] != "") {
            return "The " . $name['NobleTitle'];
        } else {
            return $name['FirstName'] . " " . $name['LastName'];
        }
    }

    static function getUserID($link, $id) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT User FROM people WHERE ID = {$id}"))[0];
    }

    static function isNPC($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT isNPC FROM people WHERE ID = {$ID}"))[0];
    }

}

?>