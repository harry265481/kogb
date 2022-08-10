<?php
class Empire {
    static function getEmpireDetails($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM empire WHERE ID = {$ID}"));
    }
}
?>