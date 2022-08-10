<?php
class ArmyRank {
    static function getAllRanks($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM armyranks ORDER BY ID desc"));
    }
}
?>