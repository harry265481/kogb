<?php
include_once __DIR__ . "/mp.php";
include_once __DIR__ . "/university.php";
class Constituency {
    public $ID;
    public $name;
    public $Franchise;
    public $Price;
    public $seats;
    public $members = array();
    public $candidates = array();
    public $winners = array();
    public $voters;
    public $votes = null;

    //University only
    public University $university;

    static $franchiseTypeName = array("Freeholders", "Corporation", "Scot and Lot", "Burgages", "University", "Freemen");

    function __construct($link, $ID) {
        $seat = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM seats WHERE ID = {$ID}"), MYSQLI_ASSOC);
        $this->ID = $seat['ID'];
        $this->name = $seat['Name'];
        $this->Franchise = $seat['FranchiseType'];
        if($this->Franchise == 4) {
            $this->university = University::fromSeatID($link, $this->ID);
        }
        $this->Price = $seat['Price'];
        $this->voters = $seat['voters'];
        $this->seats = $seat['seats'];
        for($i = 1; $i <= $seat['seats']; $i++) {
            $members[$i] = $seat['seat' . $i];
        }
        $cands = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM employedmps WHERE seatID = {$this->ID}"));
        /*
        echo "<pre>";
        print_r($cands);
        echo "</pre>";
        */
        foreach($cands as $cand) {
            array_push($this->candidates, new MP($link, $cand[0]));
        }
    }

    function processElection($link) {
        //All university elections are processed the same way
        if($this->Franchise == 4) {
            $this->processUniversity($link);
        } else if(count($this->candidates) > $this->seats) {
            if(count($this->candidates) > ($this->seats * 2)) {
                echo "Contest <br>";
                $this->processContest($link);
            } else {
                echo "NPC replacement, NPC contest<br>";
                //is this a burgage?
                if($this->Franchise == 3) {
                    $this->processBurgage($link);
                } else {
                    //Get the previous MPs, which should all be NPCs
                    $playerMPs = $this->getPlayerMPsInSeat($link);
                    $this->winners = $this->members;
                    $parray = array();
                    $narray = array();
                    foreach($playerMPs as $p) {
                        array_push($parray, MP::getMPPartyID($link, $p->ID));
                    }

                    foreach($this->members as $p) {
                        array_push($narray, MP::getMPPartyID($link, $p->ID));
                    }

                    if($this->areAllMPsNPCInSeat($link)) {
                        $a = array_count_values($this->getPartyOfMPsInSeat($link));
                        $b = array_count_values($parray);
                        if($a == $b) {
                            echo "'Glove Fits'<br>";
                            echo "Finished contest <br><br>";
                            $this->winners = $playerMPs;
                        } else {
                            //If they aren't exact
                            $c = array_key_first($a);
                            $d = array_key_first($b);
                            //only returns true if everyone present, previous or new, is of the same party
                            if(count($a) == 1 && count($b) == 1 && $c == $d) {
                                echo "Everyone is of the same party<br>";
                                for($i = 0; $i < count($playerMPs); $i++) {
                                    $this->winners[$i] == $playerMPs[$i];
                                }
                                echo "Finished contest <br><br>";
                            } else {
                                //if there's more than 1 NPC party
                                //and there's more than 1 player party
                                $room = false;
                                //Check if there is room for all
                                foreach($a as $key => $freq) {
                                    if(!array_key_exists($key, $b) || $freq < $b[$key]) {
                                        processContest($link);
                                        break;
                                    } else {
                                        $room = true;
                                        break;
                                    }
                                }
                                if($room == true) {
                                    echo "There was room<br>";
                                    foreach($playerMPs as $p) {
                                        foreach($this->winners as $key=> $r) {
                                            //if the current prev was an NPC
                                            if($r->isControlledByNPC($link) && $p->getMPPartyID($link) == $r->getMPPartyID($link)) {
                                                $this->winners[$key] = $p;
                                            }
                                        }
                                    }
                                    echo "Finished contest <br><br>";
                                }
                            }
                        }
                    } else {
                        //Not all of the current MPs are controlled by NPCs but at least 1 is
                        echo "Not all MPs are NPCs <br>";
                        foreach($playerMPs as $p) {
                            if(in_array($p->getMPPartyID($link), $narray)) {
                                foreach($this->winners as $key => $r) {
                                    //is the current seat NPC
                                    if($r->isControlledByNPC($link)) {
                                        //replace them
                                        $this->winners[$key] = $p;
                                    }
                                }
                            } else {
                                processContest($link);
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            echo "No Contest<br>";
            $this->winners = $this->members;
        }
    }

    private function processContest($link) {
        echo "Contest in {$this->name} <br>";
        switch ($this->Franchise) {
            case 0:
                $this->processFreeholder($link);
                break;
            case 1:
                $this->processCorporation($link);
                break;
            case 2:
                $this->processScotAndLotElection($link);
                break;
            case 3:
                $this->processBurgage($link);
                break;
            case 4:
                $this->processUniversity($link);
                break;
            case 5:
                $this->processFreeholder($link);
                break;
        }
    }

    private function processFreeholder($link) {
        echo "Freeholder contest in {$this->name} <br>";
        $mps = array();
        $moneyinseat = 0;
        //if there is a contest
        foreach($this->candidates as $mp) {
            $moneyinseat += $mp->purse;
        }
        if($moneyinseat > 0) {
            //if there is money
            if(($moneyinseat / $this->Price) <= $voters ) {
                //if bought voters is less than voters
                //get bought votes, give NPCs the remainder based on previous seats
                $boughtvotes = 0;
                //put the bought votes in the array
                foreach($this->candidates as $mp) {
                    //if they're not NPC Whig or NPC Tory, or the Speaker seat
                    if(!$mp->isControlledByNPC($link)) {
                        $votes = intval($mp->purse / $price);
                        $boughtvotes += $votes;
                        array_push($mps, array($mp, $votes));
                    }
                }

                //after the players/non-NPCs have been processed
                //do the NPCs
                $remaining = $voters - $boughtvotes;
                $votespernpc = $remaining / $this->seats;
                foreach($this->candidates as $mp) {
                    //NPCs only or the speaker
                    if($mp->isControlledByNPC($link)) {
                        array_push($mps, array($mp, $votespernpc));
                    }
                }
                //Sort by most votes
                usort($mps, [Constituency::class , 'sortMPs']);
                $winners = array();
                for($i = 0; $i < $this->seats; $i++) {
                    $winners[$i] = $mp[$i];
                }
            } else {
                //if bought voters is greater than voters
                //give MPs votes proportional to their money spent
                foreach($this->candidates as $mp) {
                    $percentage = $mp->purse / $moneyinseat;
                    $mpv = intval($percentage * $voters);
                    array_push($mps, array($mp, $mpv));
                }
                usort($mps, [Constituency::class , 'sortMPs']);
                $winners = array();
                for($i = 0; $i < $this->seats; $i++) {
                    $winners[$i] = $mps[$i];
                }
                //insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
                //drainAllMoneyFromMPsArr($link, $winners);
            echo "Finished contest<br><br>";
            }
        } else {
            //if there is no money, take the previous seats
            echo "Finished contest<br><br>";
            //useLastElectionResults($link, $seats, $electionID, $seatID);
        }
    }

    private function processCorporation($link) {
        echo "Corporation contest in {$this->name} <br>";
        $mps = array();
        $moneyinseat = 0;
        //if there is a contest
        foreach($this->candidates as $mp) {
            $moneyinseat += $mp->purse;
            if($mp->purse > 0) {
                array_push($mps, array($mp, $mp->purse));
            }
        }
        //Is there money?
        if($moneyinseat > 0) {
            //sort them
            usort($mps, [Constituency::class , 'sortMPs']);
            for($i = 1; $i < $this->seats; $i++) {
                if($mp->isControlledByNPC($link)) {
                    array_push($mps, array($mp, $mps[$i]));
                }
                $this->winners[$i] = $mps[$i];
            }
            echo "Finished contest<br><br>";
            //insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
            return;
        } else {
            echo "No Money<br>";
            //use last results if there is none
            echo "Finished contest<br><br>";
            //useLastElectionResults($link, $seats, $electionID, $seatID);
            return;
        }
    }

    private function processScotAndLotElection($link) {
        echo "Scot and Lot contest in {$this->name} <br>";
        $mps = array();
        $moneyinseat = 0;
        //if there is a contest
        foreach($this->candidates as $mp) {
            $moneyinseat += $mp->purse;
            if($mp->purse > 0) {
                array_push($mps, array($mp, $mp->purse));
            }
        }
        if(count(getPlayerMPsInSeat($link, $seatID)) > 0) {
            if(($moneyinseat / $this->price) <= $this->voters ) {
                //if bought voters is less than voters
                //get bought votes, give NPCs the remainder based on previous seats
                $boughtvotes = 0;
                //put the bought votes in the array
                foreach($this->candidates as $mp) {
                    //if they're not NPC Whig or NPC Tory, or the Speaker seat
                    if(!$mp->isControlledByNPC($link)) {
                        $votes = intval($mp->purse / $price);
                        $boughtvotes += $votes;
                        array_push($mps, array($mp, $votes));
                    }
                }
                //after the players/non-NPCs have been processed
                //do the NPCs
                $remaining = $voters - $boughtvotes;
                $votespernpc = $remaining / $seats;
                //while($mp = mysqli_fetch_array($sqlMPs, MYSQLI_ASSOC)) {
                foreach($this->candidates as $mp) {
                    //NPCs only or the speaker
                    if(($mp->employerID == 1) || ($mp->employerID != 2) || ($mp->employerID != 0)) {
                        array_push($mps, array($mp, $votespernpc));
                    }
                }
    
                //Sort by most votes
                usort($mps, [Constituency::class , 'sortMPs']);
                for($i = 0; $i < $seats; $i++) {
                    $this->winners[$i] = $mps[$i];
                }
                echo "Finished contest<br><br>";
                //insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
                return;
            } else {
                //if bought voters is greater than voters
                //give MPs votes proportional to their money spent
                while($mp = mysqli_fetch_array($sqlMPs, MYSQLI_ASSOC)) {
                    $mpv = intval($mp['purse'] / $price);
                    array_push($mps, array($mp, $mpv));
                }
                usort($mps, [Constituency::class , 'sortMPs']);
                for($i = 0; $i < $seats; $i++) {
                    $this->winners[$i] = $mps[$i];
                }
                echo "Finished contest<br><br>";
                //insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
                //drainAllMoneyFromMPsArr($link, $winners);
                return;
            }
        } else {
            //echo "Finished contest<br><br>";
            //useLastElectionResults($link, $seats, $electionID, $seatID);
            return;
        }

    }

    private function processBurgage($link) {
        echo "Burgage contest in {$this->name} <br>";
        //It is a burgage
        $playermps = array();
        
        //Get all the player MPs and put their employers into an array
        foreach($this->candidates as $mp) {
            if($mp->employerID != 1 && $mp->employerID != 2) {
                array_push($playermps, $mp->employerID);
            }
        }
    
        //Make sure we're using unique employers
        $playermps = array_unique($playermps);
        $burgageowningplayers = array();
    
        //Get the burgages in the seat owned by the MPs, if any
        foreach($playermps as $pmp) {
            $burgages = mysqli_query($link, "SELECT Count FROM burgageHolders WHERE seatID = {$this->ID} AND holderID = {$pmp} AND Count > 0");
            $count = mysqli_fetch_array($burgages)[0];
            if(mysqli_num_rows($burgages) > 0) {
                array_push($burgageowningplayers, array($pmp, $count));
            }
        }
    
        //If the players running own burgages
        if(!empty($burgageowningplayers)) {
            //Get the total amount of burgages in the seat
            $sql = "SELECT Count FROM burgages WHERE ID = {$this->ID}";
            $totalb = mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC)['Count'];
            $foundmajor = false; //flag
            $major; //ID of the biggest burgage owner
            if(count($burgageowningplayers) > 1) {
                usort($burgageowningplayers, [Constituency::class , 'sortMPs']);
            }
    
            //does one of them own over 50%?
            foreach($burgageowningplayers as $bmp) {
                if($foundmajor == false) {
                    if($bmp[1] > ($totalb / 2)) {
                        //Found em
                        $foundmajor = true;
                        $major = $bmp[0];
                        break;
                    }
                }
            }
    
            //If there is someone with over 50%
            if($foundmajor) {
                //Get their MPs at that seat
                $sql = "SELECT ID FROM EmployedMPs WHERE employerID = {$major} AND seatID = {$this->ID}";
                $mpsmaj = mysqli_query($link, $sql);
                $nummps = mysqli_num_rows($mpsmaj);
                //If there are less MPs than seats, make an MP to stand for them
                if($this->seats > $nummps) {
                    $mpstomake = $this->seats - $nummps;
                    for($i = 1; $i <= $mpstomake; $i++) {
                        $sql = "INSERT INTO EmployedMPs (`employerID`, `seatID`) VALUES ({$major}, {$this->ID})";
                        mysqli_query($link, $sql);
                    }
                }
                //insertSeatResults($link, $electionID, $seatID, $seats, getMPIDsofPlayerAtSeat($link, $major, $seatID));
                return;
            } else {
                //If there is not a majority
                //Depends on if there's 1 player or more
                if(count($burgageowningplayers) > 1) {
                    //If there is more than 1, split between the 2 biggest
                    $biggest = array();
                    //Get the first mp at the seat belonging to each player
                    for($i = 0; $i <= 1; $i++) {
                        array_push($biggest, Constituency::getnthMPIDsofPlayerAtSeat($link, $burgageowningplayers[$i], $this->ID, $i));
                    }
                    //insertSeatResults($link, $electionID, $seatID, $seats, $biggest);
                    return;
                }
            }
        } else {
            //They don't own burgages
            echo "Finished contest<br><br>";
            //useLastElectionResults($link, $seats, $electionID, $seatID);
            return;
        }

    }

    private function processUniversity($link) {
        echo "University contest in {$this->name} <br>";
        //get the year of the last election

        //as a decimal
        if($this->university->royalPrerogative == true) {
            $sql = "SELECT `Year` FROM `elections` INNER JOIN `electionresults` ON elections.ID = electionResults.ElectionID WHERE electionresults.SeatID = {$this->ID} ORDER BY ElectionID desc LIMIT 1";
            $lastYear = mysqli_fetch_array(mysqli_query($link, $sql))[0];
            
            //the current year should have already been inserted into 
            //the election table so we grab the year from there
            $currentYear = mysqli_fetch_array(mysqli_query($link, "SELECT `Year` FROM `elections` ORDER BY ID desc LIMIT 1"))[0];
            $percentage = ($currentYear - $lastYear) / 100;
            //Cambridge
            $parray = array();
            $marray = array();
            $earray = array();

            //Kill off the needed percentage and get an array of the electors properly
            foreach($this->university->electors as $k => $e) {
                $this->university->electors[$k][1] = ceil((1 - $percentage) * $e[1]);
                $earray[$e[1]] = $e[0];
            }

            //Get all the parties with candidates at the seat
            foreach($this->candidates as $cand) {
                if(!isset($parray[MP::getMPPartyID($link, $cand->ID)])) {
                    $parray[MP::getMPPartyID($link, $cand->ID)] = 0;
                }

                if(!isset($marray[MP::getMPPartyID($link, $cand->ID)])) {
                    $marray[MP::getMPPartyID($link, $cand->ID)] = 0;
                }
                $parray[MP::getMPPartyID($link, $cand->ID)] += 1;
                $marray[MP::getMPPartyID($link, $cand->ID)] += $cand->purse;
            }

            $votes = array();
            foreach($this->candidates as $cand) {
                $i = MP::getMPPartyID($link, $cand->ID);
                $votes[$cand->ID] = ceil(($cand->purse / $marray[$i]) * $earray[$i]) * 2;
            }
            arsort($votes);
            $results = array();
            $rVotes = array();
            foreach($votes as $c => $v) {
                if($v > 0) {
                    array_push($results, $c);
                    array_push($rVotes, array($c, $v));
                }
            }
            for($i = 1; $i < $this->seats; $i++) {
                $this->winners[$i] = $results[$i - 1];
            }
            $this->votes = $rVotes;
        } else {
            $winParty1 = array_rand($this->university->parties);
            $winParty2 = array_rand($this->university->parties);

            $winCands1 = array();
            $winCands2 = array();
            foreach($this->candidates as $c) {
                if($winParty1 != $winParty2) {
                    if(MP::getMPPartyID($link, $c->ID) == ($this->university->parties[$winParty1])) {
                        array_push($winCands1, $c->ID);
                    } else if(MP::getMPPartyID($link, $c->ID) == ($this->university->parties[$winParty2])) {
                        array_push($winCands2, $c->ID);
                    }
                } else {
                    array_push($winCands1, $c->ID);
                    array_push($winCands2, $c->ID);
                }
            }
            $wink1 = array_rand($winCands1);
            unset($winCands2[$wink1]);
            $wink2 = array_rand($winCands2);
            
            $this->winners[0] = $winCands1[$wink1];
            $this->winners[1] = $winCands2[$wink2];
        }
    }

    function getPlayerMPsInSeat($link) {
        $playerMPs = array();
        foreach($this->candidates as $c) {
            if(!$c->isControlledByNPC($link)) {
                array_push($playerMPs, $c);
            }
        }
        return $playerMPs;
    }

    function getPartyOfMPsInSeat($link) {
        $a = array();
        foreach($this->members as $m) {
            array_push($array, MP::getMPPartyID($link, $m->ID));
        }
        return $a;
    }

    //returns true if all the current MPs are NPCs
    function areAllMPsNPCInSeat($link) {
        foreach($this->members as $m) {
            if($m->isControlledByNPC($link)) { return false;}
        }
        return true;
    }
    
    static function getnthMPIDsofPlayerAtSeat($link, $playerID, $seatID, $n) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT ID FROM EmployedMPs WHERE employerID = {$playerID} AND seatID = {$seatID} ORDER BY ID"))[$n][0];
    }

    //usort callback for 2d array where it sorts by the second column
    static function sortMPs($a, $b) {
        if($a[1] == $b[1]) return 0;
        return ($a[1] < $b[1]) ? 1: -1;
    }

    static function getAllIDName($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT ID, Name FROM `seats` ORDER BY `seats`.`Name` ASC"));
    }
    
    static function getConstituencyName($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT Name FROM `seats` WHERE ID = {$ID}"))[0];
    }
    
    static function getConstituency($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `seats` WHERE ID = {$ID}"));
    }
    
    static function getConstituencyParliament($link, $ID) {
        return mysqli_fetch_array(mysqli_query($link, "SELECT Parliament FROM `seats` WHERE ID = {$ID}"))[0];
    }

    static function getElectionResultsTable($link, $seatID) {
        $sql = "SELECT ElectionID, Seat1, Seat2, Seat3, Seat4, voters FROM electionresults WHERE SeatID = {$seatID}";
        $rows = mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_ASSOC);
        $seats = mysqli_fetch_array(mysqli_query($link, "SELECT seats FROM seats WHERE ID = {$seatID}"))['seats'];
        foreach($rows as $row) {
            $voters;
            if($row['voters'] != null) {
                $voters = json_decode($row['voters']);
            }
    
            $year = getElectionYear($link, $row['ElectionID']);
            echo "
            <div class=\"row\">
                <div class=\"table-responsive col-md-8\">
                    <h3>{$year}</h3>
                    <table class=\"table table-dark\">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th colspan=\"2\">Interest</th>
                                <th>Votes</th>
                            </tr>
                        </thead>
                        <tbody>";
            for ($i = 1; $i <= $seats; $i++) { 
                $ID = $row['Seat'.$i];
                $color = MP::getMPColor($link, $ID);
                $name = MP::getMPName($link, $ID);
                $ename = MP::getMPEmployerName($link, $ID);
                $v = "";
                if($row['voters'] != null) {
                    $v = $voters[$i-1][1];
                }
                echo "<tr>";
                echo "<td>{$ID}</td>";
                echo "<td><b>{$name}</b></td>";
                echo "<td width=\"15px\" style=\"background-color: {$color}\"></td>";
                echo "<td><b>{$ename}</b></td>";
                echo "<td><b>{$v}</b></td>";
                echo "</tr>";
            }
            if($row['voters'] != null) {
                if(count($voters) > $seats) {
                    for($i = $seats + 1; $i <= (count($voters)); $i++) {
                        $color = MP::getMPColor($link, $voters[$i-1][0]);
                        $ename = MP::getMPEmployerName($link, $voters[$i-1][0]);
                        $ID = $voters[$i-1][0];
                        $v = $voters[$i-1][1];
                        echo "<tr>";
                        echo "<td>{$ID}</td>";
                        echo "<td><b>{$name}</b></td>";
                        echo "<td width=\"15px\" style=\"background-color: {$color}\"></td>";
                        echo "<td>{$ename}</td>";
                        echo "<td>{$v}</td>";
                        echo "</tr>";
                    }
                }
            }
            echo
            "</tbody>
            </table>
            </div>
            </div>";
        }
    }
}
?>