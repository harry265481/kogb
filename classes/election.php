<?php
include_once __DIR__ . "/constituency.php";
class Election {
    public $year;
    public $constituencies = array();
    public $results = array();
    public $electionID = 0;

    function __construct($link, $year, $parliament) {
        $this->electionID = 19;
        if($this->electionID != false) {
            $seats = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM seats WHERE Parliament = {$parliament}"), MYSQLI_ASSOC);
            foreach($seats as $seat) {
                array_push($this->constituencies, new Constituency($link, $seat['ID']));
            }
        }
        foreach($this->constituencies as $c) {
            echo "Processing {$c->name} <br>";
            $c->processElection($link);
            echo "Inserted results <br><br>";
            //$this->insertSeatResults($link, $c);
        }
    }

    function insertElectionyear($link, $year) {
        if (!mysqli_query($link, "INSERT INTO elections (`Year`, `parliamentID`) VALUES ('{$year}', {$parliament})")) {
            echo("Error description: " . mysqli_error($link));
            return false;
        } else {
            return mysqli_insert_id($link);
        }
    }

    function insertSeatResults($link, Constituency $c) {
        $updateString = "";
        $insertValues = "";
        $insertColumns = "";
        $vi;
        for($i = 0; $i < $c->seats; $i++) {
            $j = $i + 1;
            if($i == $c->seats - 1) {
                $updateString .= ("seat{$j} = {$c->winners[$i]} ");
                $insertcolumns .= ("Seat{$j} ");
                $insertvalues .= ("{$c->winners[$i]} ");
            } else {
                $updatestring .= ("seat{$j} = {$c->winners[$i]}, ");
                $insertcolumns .= ("Seat{$j}, ");
                $insertvalues .= ("{$c->winners[$i]}, ");
            }
        }
        $sql = "UPDATE seats SET {$updateString} WHERE ID = {$c->ID}";
        if (!mysqli_query($link, $sql)) {
            echo $sql . "<br>";
            echo("Error description: " . mysqli_error($link) . " <br>");
        }
        if(isset($c->votes)) {
            $vi = json_encode($c->voters);
            $sql = "INSERT INTO ElectionResults (ElectionID, SeatID, {$insertcolumns}, voters) VALUES ({$this->electionID}, {$c->ID}, {$insertvalues}, \"{$vi}\")";
        } else {
            $sql = "INSERT INTO ElectionResults (ElectionID, SeatID, {$insertcolumns}) VALUES ({$this->electionID}, {$c->ID}, {$insertvalues})";
        }
        if (!mysqli_query($link, $sql)) {
            echo $sql . "<br>";
            echo("Error description: " . mysqli_error($link) . " <br>");
        }
    }
}
?>