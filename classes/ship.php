<?php
class Ship {
    public $ID;
    public $status;
    public $guns;

    public static $type = array(50, 28, 20, 16, 12, 4);
    public static $typeWords = array("Ship of the Line", "Frigate", "Sloop-of-War", "Brig, Cutter, or Schooner");
    public static $rates = array(100, 80, 64, 50, 32, 20, 4);
    public static $totalCrewPerGuns = array(8.5, 8.75, 7.8125, 6.4, 6.25, 7, 5.625, 5);
    public static $rateWords = array("First Rate", "Second Rate", "Third Rate", "Fourth Rate", "Fifth Rate", "Sixth Rate", "Unrated");
    public static $statusWords = array("In Ordinary", "Sea Service", "Extraordinary Repair");
    
    public static function getAllShips($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM ships"), MYSQLI_ASSOC);
    }

}
?>