<?php
class MP {
    public $ID;
    public $purse = 0;
    public $seatname;

    static function getAllMPsEmployedBy($link, $ID) {
        $q = mysqli_query($link, "SELECT * FROM EmployedMPs WHERE employerID = {$ID}");
        if(mysqli_num_rows($q) > 0) {
            return mysqli_fetch_all($q);
        } else {
            return false;
        }
    }

    static function getAllMPsInSeat($link, $ID) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM EmployedMPs WHERE seatID = {$ID}"), MYSQLI_ASSOC);
    }
}
?>