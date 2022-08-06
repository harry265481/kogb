<?php
class Bill {
    public $ID;
    public $longTitle;
    public $author;
    public $Stage;
    public $text;
    public $HoCyays;
    public $HoCnays;
    public $HoLyays;
    public $HoLnays;

    static $stages = array("First Reading", "Second Reading", "Committee Stage", "Report Stage", "Third Reading");

    function __construct($link, $id) {
        $bill = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM bills WHERE ID = {$id}"));
        $this->ID = $bill['ID'];
        $this->longTitle = $bill['longTitle'];
        $this->author = $bill['author'];
        $this->Stage = $bill['Stage'];
        $this->text = $bill['text'];
        $this->HoCyays = $bill['HoCyays'];
        $this->HoCnays = $bill['HoCnays'];
        $this->HoLyays = $bill['HoLyays'];
        $this->HoLnays = $bill['HoLnays'];
    }

    static function getHouseBills($link, $houseID) {
        $array = array();
        $bills = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM bills WHERE House = {$houseID}"));
        foreach($bills as $bill) {
            array_push($array, new static($link, $bill));
        }
        return $array;
    }
}
?>