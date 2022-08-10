<?php
class Bill {
    public $ID;
    public $shortTitle;
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
        $this->shortTitle = $bill['shortTitle'];
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
            array_push($array, new static($link, $bill[0]));
        }
        return $array;
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
            echo "<script>parent.self.location=\"parliamenthouse.php?id={$house}\"</script>";
        }
    }
}
?>