<?php
class NavyRank {
    static function getAllRanks($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM navyranks ORDER BY ID desc"));
    }
}
?>