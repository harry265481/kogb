<?php
define('DEBUG', FALSE);

include_once "defines.php";
include_once "classes/nation.php";
include_once "classes/resource.php";

function print_r2($val){
    echo '<pre>';
    print_r($val);
    echo  '</pre>';
}

function backTrace() {
    $e = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    echo "<br><b>Backtrace:</b><br>";
    foreach($e as $key => $i) {
        if($key > 0) {
            $path = basename($i['file']);
            echo "<b>{$i['function']}[{$i['line']}]</b> in {$path} <br>";
        }
    }
    echo "<br>";
}

function generatePatterns($link) {
    echo "<defs>";
    echo    "<pattern ";
    echo        "id=\"ToriesWhigs\" ";
    echo        "height=\"100\" ";
    echo        "width=\"6\" ";
    echo        "patternUnits=\"userSpaceOnUse\">";
    echo        "<rect ";
    echo            "style=\"fill:#3333CC\" ";
    echo            "id=\"tories\" ";
    echo            "width=\"3\" ";
    echo            "height=\"100\" ";
    echo            "x=\"0\" ";
    echo            "y=\"0\" />";
    echo        "<rect ";
    echo            "style=\"fill:#FF7F00\" ";
    echo            "id=\"whigs\" ";
    echo            "width=\"3\" ";
    echo            "height=\"100\" ";
    echo            "x=\"3\" ";
    echo            "y=\"0\" />";
    echo    "</pattern>";
    echo    "<pattern ";
    echo        "id=\"ToriesSpeaker\" ";
    echo        "height=\"100\" ";
    echo        "width=\"6\" ";
    echo        "patternUnits=\"userSpaceOnUse\">";
    echo        "<rect ";
    echo            "style=\"fill:#3333CC\" ";
    echo            "id=\"tories\" ";
    echo            "width=\"3\" ";
    echo            "height=\"100\" ";
    echo            "x=\"0\" ";
    echo            "y=\"0\" />";
    echo        "<rect ";
    echo            "style=\"fill:#000\" ";
    echo            "id=\"speaker\" ";
    echo            "width=\"3\" ";
    echo            "height=\"100\" ";
    echo            "x=\"3\" ";
    echo            "y=\"0\" />";
    echo    "</pattern>";
    echo    "<pattern ";
    echo        "id=\"WhigsSpeaker\" ";
    echo        "height=\"100\" ";
    echo        "width=\"6\" ";
    echo        "patternUnits=\"userSpaceOnUse\">";
    echo        "<rect ";
    echo            "style=\"fill:#000\" ";
    echo            "id=\"speaker\" ";
    echo            "width=\"3\" ";
    echo            "height=\"100\" ";
    echo            "x=\"0\" ";
    echo            "y=\"0\" />";
    echo        "<rect ";
    echo            "style=\"fill:#FF7F00\" ";
    echo            "id=\"whigs\" ";
    echo            "width=\"3\" ";
    echo            "height=\"100\" ";
    echo            "x=\"3\" ";
    echo            "y=\"0\" />";
    echo    "</pattern>";
    echo "</defs>";
};

function mysql_result($result, $number, $field=0) {
    mysqli_data_seek($result, $number);
    $row1 = mysqli_fetch_array($result);
    if($row1 != null) {
        return $row1[$field];
    } else {
        return null;
    }
}

function generateMap($link, $isInteractive = true) {
    $sqlget = "SELECT ID, name, shape, seats, seat1, seat2, seat3, seat4 FROM seats WHERE Parliament = 0";
    $sqldata2 = mysqli_query($link, $sqlget);
    echo "<svg width=\"100%\" id=\"map\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"225 0 605 1208.8\">";
    generatePatterns($link);
    $sqlpartyget = 'SELECT Color from parties';
    $colors = mysqli_query($link, $sqlpartyget);
    while($province = mysqli_fetch_array($sqldata2, MYSQLI_ASSOC)) {
        $color = "grey";
        if($province['seats'] == 1) {
            if(getMPPartyID($link, $province['seat1']) != NULL) {
                $color = mysql_result($colors, getMPPartyID($link, $province['seat1']), 'Color');
            }
        }
        if($province['seats'] == 2) {
            if(getMPPartyID($link, $province['seat1']) != NULL) {
                if(getMPPartyID($link, $province['seat1']) == getMPPartyID($link, $province['seat2'])) {
                    $color = mysql_result($colors, getMPPartyID($link, $province['seat1']), 'Color');
                }
                if(getMPPartyID($link, $province['seat1']) != getMPPartyID($link, $province['seat2'])) {
                    if(((getMPPartyID($link, $province['seat1']) == 1) && (getMPPartyID($link, $province['seat2']) == 2)) || ((getMPPartyID($link, $province['seat1']) == 2) && (getMPPartyID($link, $province['seat2']) == 1))) {
                        $color="url(#ToriesWhigs)";
                    }
                    if(((getMPPartyID($link, $province['seat1']) == 0) && (getMPPartyID($link, $province['seat2']) == 2)) || ((getMPPartyID($link, $province['seat1']) == 2) && (getMPPartyID($link, $province['seat2']) == 0))) {
                        $color="url(#ToriesSpeaker)";
                    }
                    if(((getMPPartyID($link, $province['seat1']) == 0) && (getMPPartyID($link, $province['seat2']) == 1)) || ((getMPPartyID($link, $province['seat1']) == 1) && (getMPPartyID($link, $province['seat2']) == 0))) {
                        $color="url(#WhigsSpeaker)";
                    }
                }
            }
        }
        if($isInteractive == true) {
            echo "<a href=seat.php?id=" . $province['ID'] . ">";
        }
        echo "<path data-tooltip-text=\"" . $province['name'] . "\" class=\"tooltip-trigger\" id=" . $province['ID'] . " " . $province['shape'] . "stroke=\"black\" style=\"fill:" . $color . "\" stroke-width=\"0.5\">";
        echo "</path>";
        if($isInteractive == true) {
        echo "</a>";
        }
    }
    if($isInteractive == true) {
    echo "
        <g id=\"tooltip\" visibility=\"hidden\">
            <rect id=\"ttrect\" width=\"80\" height=\"30\" fill=\"white\" rx=\"2\" ry=\"2\"/>
            <text id=\"tttext\" x=\"2\" y=\"26\" font-size=\"25px\">Tooltip</text>
        </g>
    ";
    }

    echo "
    <script type=\"text/javascript\"><![CDATA[
        (function() {
            var tooltip = document.getElementById('tooltip');
            var svg = document.getElementById('map');
            var triggers = document.getElementsByClassName('tooltip-trigger');
            var tooltipText = tooltip.getElementsByTagName('text')[0];
            var tooltiprect = tooltip.getElementsByTagName('rect')[0];
            for (var i = 0; i < triggers.length; i++) {
                triggers[i].addEventListener('mousemove', showTooltip);
                triggers[i].addEventListener('mouseout', hideTooltip);
            }

            function setStrokeWidth(width) {
                var list = document.getElementsByTagName('path');
                for(let item of list) {
                    item.setAttribute('stroke-width', width);
                }
            }

            function showTooltip(evt) {
                var CTM = svg.getScreenCTM();
                var x = (evt.clientX - CTM.e + 6) / CTM.a;
                var y = (evt.clientY - CTM.f + 20) / CTM.d;
                tooltip.setAttributeNS(null, \"transform\", \"translate(\" + x + \" \" + y + \")\");
                tooltip.setAttributeNS(null, \"visibility\", \"visible\");
                
                tooltipText.firstChild.data = evt.target.getAttributeNS(null, \"data-tooltip-text\");
                var bbox = tooltipText.getBBox();
                var twidth = bbox.width;
                tooltiprect.setAttribute('width', twidth + 6);
            }

            function hideTooltip() {
                tooltip.setAttributeNS(null, \"visibility\", \"hidden\");
            }
        })();
    ]]>
    </script>";
    echo "</svg>";
}

function generateIrishMap($link, $isInteractive = true) {
    $sqlget = 'SELECT ID, name, shape, seats, seat1, seat2, seat3, seat4 FROM seats WHERE Parliament = 1';
    $sqldata2 = mysqli_query($link, $sqlget);
    echo "<svg width=\"100%\" id=\"map\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"-200 0 360 450\">";
    generatePatterns($link);
    $sqlpartyget = 'SELECT Color from parties';
    $colors = mysqli_query($link, $sqlpartyget);
    while($province = mysqli_fetch_array($sqldata2, MYSQLI_ASSOC)) {
        $color = "grey";
        if($province['seats'] == 1) {
            if(getMPPartyID($link, $province['seat1']) != NULL) {
                $color = getMPColor($link, $province['seat1']);
            }
        }
        if($province['seats'] == 2) {
            if(getMPPartyID($link, $province['seat1']) != NULL) {
                if(getMPPartyID($link, $province['seat1']) == getMPPartyID($link, $province['seat2'])) {
                    $color = getMPColor($link, $province['seat1']);
                }
                if(getMPPartyID($link, $province['seat1']) != getMPPartyID($link, $province['seat2'])) {
                    if(((getMPPartyID($link, $province['seat1']) == 1) && (getMPPartyID($link, $province['seat2']) == 2)) || ((getMPPartyID($link, $province['seat1']) == 2) && (getMPPartyID($link, $province['seat2']) == 1))) {
                        $color="url(#ToriesWhigs)";
                    }
                    if(((getMPPartyID($link, $province['seat1']) == 0) && (getMPPartyID($link, $province['seat2']) == 2)) || ((getMPPartyID($link, $province['seat1']) == 2) && (getMPPartyID($link, $province['seat2']) == 0))) {
                        $color="url(#ToriesSpeaker)";
                    }
                    if(((getMPPartyID($link, $province['seat1']) == 0) && (getMPPartyID($link, $province['seat2']) == 1)) || ((getMPPartyID($link, $province['seat1']) == 1) && (getMPPartyID($link, $province['seat2']) == 0))) {
                        $color="url(#WhigsSpeaker)";
                    }
                }
            }
        }
        if($isInteractive == true) {
            echo "<a href=seat.php?id=" . $province['ID'] . ">";
        }
        echo "<path data-tooltip-text=\"" . $province['name'] . "\" class=\"tooltip-trigger\" id=" . $province['ID'] . " " . $province['shape'] . "stroke=\"black\" style=\"fill:" . $color . "\" stroke-width=\"0.5\">";
        echo "</path>";
        if($isInteractive == true) {
        echo "</a>";
        }
    }
    if($isInteractive == true) {
    echo "
        <g id=\"tooltip\" visibility=\"hidden\">
            <rect id=\"ttrect\" width=\"80\" height=\"30\" fill=\"white\" rx=\"2\" ry=\"2\"/>
            <text id=\"tttext\" x=\"2\" y=\"26\" font-size=\"25px\">Tooltip</text>
        </g>
    ";
    }

    echo "
        <script type=\"text/javascript\"><![CDATA[
            (function() {
                var tooltip = document.getElementById('tooltip');
                var svg = document.getElementById('map');
                var triggers = document.getElementsByClassName('tooltip-trigger');
                var tooltipText = tooltip.getElementsByTagName('text')[0];
                var tooltiprect = tooltip.getElementsByTagName('rect')[0];
                for (var i = 0; i < triggers.length; i++) {
                    triggers[i].addEventListener('mousemove', showTooltip);
                    triggers[i].addEventListener('mouseout', hideTooltip);
                }

                function setStrokeWidth(width) {
                    var list = document.getElementsByTagName('path');
                    for(let item of list) {
                        item.setAttribute('stroke-width', width);
                    }
                }

                function showTooltip(evt) {
                    var CTM = svg.getScreenCTM();
                    var x = (evt.clientX - CTM.e + 6) / CTM.a;
                    var y = (evt.clientY - CTM.f + 20) / CTM.d;
                    tooltip.setAttributeNS(null, \"transform\", \"translate(\" + x + \" \" + y + \")\");
                    tooltip.setAttributeNS(null, \"visibility\", \"visible\");
                    
                    tooltipText.firstChild.data = evt.target.getAttributeNS(null, \"data-tooltip-text\");
                    var bbox = tooltipText.getBBox();
                    var twidth = bbox.width;
                    tooltiprect.setAttribute('width', twidth + 6);
                }

                function hideTooltip() {
                    tooltip.setAttributeNS(null, \"visibility\", \"hidden\");
                }
            })();
        ]]>
        </script>";
    echo "</svg>";
}

//House
//0 = Commons
//1 = Lords
function generateSeating($link, $house, $parliament) {
    if($house == 0) {
        $sqlget = "SELECT ID, name, shape, seats, seat1, seat2, seat3, seat4 FROM seats WHERE Parliament = {$parliament}";
        $sqldata2 = mysqli_query($link, $sqlget);
        $seats = 0;
        $sqlgetp = 'SELECT * FROM parties';
        $sqldatap = mysqli_query($link, $sqlgetp);
        $parties = mysqli_num_rows($sqldatap);
        $seatsa = array();
        $seatsa = array_pad($seatsa, $parties, 0);
        $colorsa = array();
        $colorsa = array_pad($colorsa, $parties, 0);
        $wings = array();
        $wings = array_pad($seatsa, 4, 0);
        while($seat = mysqli_fetch_array($sqldata2, MYSQLI_ASSOC)) {
            $seats += $seat['seats'];
            for($i = 1; $i <= $seat['seats']; $i++) {
                $seatsa[getMPPartyID($link, $seat['seat' . $i])] += 1;
            }
        }
        foreach($seatsa as $key => $party) {
            $pos = mysql_result($sqldatap, intval($key), 'Position');
            $color = mysql_result($sqldatap, intval($key), 'Color');
            $wings[$pos] += $party;
            $colorsa[$key] = $color;
        }
        $wings[0] = 1;

        $wingrows = intval(ceil(sqrt(max(1, $wings[1], $wings[2])/20.0))*2);
        $wingcols = intval(ceil(max($wings[1], $wings[2]) / floatval($wingrows)));
        $centercols = intval(ceil(sqrt($wings[3]/4.0)));
        $centerrows = 0;
        if($centercols > 0) {
            $centerrows = ceil(floatval($wings[3] / $centercols));
        }

        //Speaker + speaker gap + the biggest wing
        $totalcols = 1 + 1 + max($wings[1], $wings[2]);

        if($centercols > 0) {
            $totalcols += $centercols + 1;
        }

        //if cross bench is 'taller' than the wings, use that
        //else, the biggest wing plus a 2 row gap
        $totalrows = max($wings[1] + $wings[2] + 2, $centerrows);

        //Now how big is each square
        $blocksize = 350 / max($totalcols, $totalrows);

        $svgHeight = $blocksize*$totalcols+10;
        $svgWidth = $blocksize*$totalrows+10;

        $poslist = array(array(), array(), array(), array());

        //svgheight / 2 - block size * (1 - spacingBetweenBlocks) / 2
        $centertop = $svgHeight / 2 - $blocksize * (1 - 0.1) / 2;
        $centertop += $blocksize / 2;

        //speaker
        for($i = 0; $i < $wings[0]; $i++) {
            array_push($poslist[0], array(5.0 + $blocksize * ($i + 0.1 / 2), $centertop));
        }

        //center
        for($i = 0; $i < $wings[3]; $i++) {
            $thiscol = intval(min($centerrows, $wings[3] - $i * $centerrows));
            for($j = 0; $j < $thiscol; $j++) {
                array_push($poslist[3], array($svgWidth - 5.0 - ($centercols - $i - 0.1 / 2) * $blocksize, (($svgHeight - $thiscol * $blocksize) / 2) + $blocksize * ($j + 0.1 / 2)));
            }
        }

        //Left
        for($i = 0; $i < $wingcols; $i++) {
            for($j = 0; $j < $wingrows; $j++) {
                array_push($poslist[2], array(5 + (1 + $i + 0.1 / 2) * $blocksize, $centertop - (1.5 + $j) * $blocksize));
            }
        }
        
        //Right
        for($i = 0; $i < $wingcols; $i++) {
            for($j = 0; $j < $wingrows; $j++) {
                array_push($poslist[1], array(5 + (1 + $i + 0.1 / 2) * $blocksize, $centertop + (1.5 + $j) * $blocksize));
            }
        }

        echo "Seats: " . $seats . "<br><br>";
        echo "Speaker: 1<br>";
        echo "Whigs: " . $seatsa[1] . "<br>";
        echo "Tories: " . $seatsa[2] . "<br><br>";
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewbox=\"5 134 30 18\">";
        echo "<g id=\"diagram\">";
        echo    "<g id=\"head\" style=\"fill:#000\">";
        echo        "<rect x=\"" . $poslist[0][0][0] . "\" y=\"" . $poslist[0][0][1] . "\" width=\"" . ($blocksize * 0.9) . "\" height=\"" . ($blocksize * 0.9) . "\" />";
        echo    "</g>";
        for($i = 1; $i < 4; $i++) {
            $style;
            if($i < 3) {
                $style = "style=\"fill:" . $colorsa[$i] . "\"";
            }
            $counter = 0;
        echo    "<g id=\"" . $wings[$i] . "\">";
            for($j = 0; $j < count($poslist[$i]); $j++) {
                if($counter < $seatsa[$i]) {
        echo        "<rect " . $style . " x=\"" . $poslist[$i][$j][0] . "\" y=\"" . $poslist[$i][$j][1] . "\" width=\"" . ($blocksize * 0.9) . "\" height=\"" . ($blocksize * 0.9) . "\" />";
                }
            
            $counter++;
            }
        echo    "</g>";
        }
        echo    "</g>";
        echo "</svg>";
    } else if($house == 1) {
        $sqlget = 'SELECT Party FROM people WHERE HoL = 1';
        $sqldata2 = mysqli_query($link, $sqlget);
        $members = mysqli_num_rows($sqldata2);

        $sqlgetp = 'SELECT * FROM parties';
        $sqldatap = mysqli_query($link, $sqlgetp);
        $parties = mysqli_num_rows($sqldatap);

        $seatsa = array();
        $seatsa = array_pad($seatsa, $parties, 0);

        $colorsa = array("#000", "#24135F", "#E4003B", "#808080");
        //$colorsa = array_pad($colorsa, $parties, 0);

        $wings = array();
        $wings = array_pad($seatsa, 4, 0);

        while($lord = mysqli_fetch_array($sqldata2, MYSQLI_ASSOC)) {
            $seatsa[$lord['Party']] += 1;
            $wings[$lord['Party']] += 1;
        }
        /*
        foreach($seatsa as $key => $party) {
            $pos = mysql_result($sqldatap, intval($key), 'Position');
            $color = mysql_result($sqldatap, intval($key), 'Color');
            $wings[$pos] += $party;
        }*/
        $wings[0] = 1;

        $wingrows = intval(ceil(sqrt(max(1, $wings[1], $wings[2])/20.0))*2);
        $wingcols = intval(ceil(max($wings[1], $wings[2]) / floatval($wingrows)));
        $centercols = intval(ceil(sqrt($wings[3]/4.0)));
        $centerrows = 0;
        if($centercols > 0) {
            $centerrows = ceil(floatval($wings[3] / $centercols));
        }

        //Speaker + speaker gap + the biggest wing
        $totalcols = 1 + 1 + max($wings[1], $wings[2]);

        if($centercols > 0) {
            $totalcols += $centercols + 1;
        }

        //if cross bench is 'taller' than the wings, use that
        //else, the biggest wing plus a 2 row gap
        $totalrows = max($wings[1] + $wings[2] + 2, $centerrows);

        //Now how big is each square
        $blocksize = 350 / max($totalcols, $totalrows);

        $svgHeight = $blocksize*$totalcols+10;
        $svgWidth = $blocksize*$totalrows+10;

        $poslist = array(array(), array(), array(), array());

        //svgheight / 2 - block size * (1 - spacingBetweenBlocks) / 2
        $centertop = $svgHeight / 2 - $blocksize * (1 - 0.1) / 2;
        $centertop += $blocksize / 2;

        //speaker
        for($i = 0; $i < $wings[0]; $i++) {
            array_push($poslist[0], array(5.0 + $blocksize * ($i + 0.1 / 2), $centertop));
        }

        //center
        for($i = 0; $i < $wings[3]; $i++) {
            $thiscol = intval(min($centerrows, $wings[3] - $i * $centerrows));
            for($j = 0; $j < $thiscol; $j++) {
                array_push($poslist[3], array($svgWidth - 5.0 - ($centercols - $i - 0.1 / 2) * $blocksize, (($svgHeight - $thiscol * $blocksize) / 2) + $blocksize * ($j + 0.1 / 2)));
            }
        }

        //Left
        for($i = 0; $i < $wingcols; $i++) {
            for($j = 0; $j < $wingrows; $j++) {
                array_push($poslist[2], array(5 + (1 + $i + 0.1 / 2) * $blocksize, $centertop - (1.5 + $j) * $blocksize));
            }
        }
        
        //Right
        for($i = 0; $i < $wingcols; $i++) {
            for($j = 0; $j < $wingrows; $j++) {
                array_push($poslist[1], array(5 + (1 + $i + 0.1 / 2) * $blocksize, $centertop + (1.5 + $j) * $blocksize));
            }
        }

        echo "Seats: " . $members . "<br><br>";
        echo "Speaker: 1<br>";
        echo "Government: " . $seatsa[1] . "<br>";
        echo "Opposition: " . $seatsa[2] . "<br><br>";
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewbox=\"0 0 1000 600\">";
        echo "<g id=\"diagram\">";
        echo    "<g id=\"head\" style=\"fill:#000\">";
        echo        "<rect x=\"" . $poslist[0][0][0] . "\" y=\"" . $poslist[0][0][1] . "\" width=\"" . ($blocksize * 0.9) . "\" height=\"" . ($blocksize * 0.9) . "\" />";
        echo    "</g>";
        for($i = 1; $i < 4; $i++) {
            $style;
            if($i < 3) {
                $style = "style=\"fill:" . $colorsa[$i] . "\"";
            }
            $counter = 0;
        echo    "<g id=\"" . $wings[$i] . "\">";
            for($j = 0; $j < count($poslist[$i]); $j++) {
                if($counter < $seatsa[$i]) {
        echo        "<rect " . $style . " x=\"" . $poslist[$i][$j][0] . "\" y=\"" . $poslist[$i][$j][1] . "\" width=\"" . ($blocksize * 0.9) . "\" height=\"" . ($blocksize * 0.9) . "\" />";
                }
            
            $counter++;
            }
        echo    "</g>";
        }
        echo    "</g>";
        echo "</svg>";
    }
}

//LOTS TO DO HERE
//very powerful
//only use if you're certain of an election
function processElection($link, $year) {
    if (!mysqli_query($link, "INSERT INTO elections (`Year`) VALUES ('{$year}')")) {
        echo("Error description: " . mysqli_error($link));
    }
    $electionID = mysqli_insert_id($link);
    $sqlSeats = mysqli_query($link, "SELECT * FROM seats");

    //Get the seat data
    while($seat = mysqli_fetch_array($sqlSeats, MYSQLI_ASSOC)) {
        echo "Processing " . $seat['Name'] . "<br>";
        $voters = $seat['voters'];
        $price = $seat['Price'];
        $seats = $seat['seats'];
        $seatID = $seat['ID'];
        $franchise = $seat['FranchiseType'];
        $mps = array();

        //Get the MPs running in the current seat
        $sqlMPs = mysqli_query($link, "SELECT ID, employerID, seatID, purse FROM EmployedMPs WHERE seatID = " . $seatID);
        $numcands = mysqli_num_rows($sqlMPs);
        //Are there more MPs than there are seats?
        //Is there a contest at all
        if($numcands > $seats) {
            //There are more MPs than seats
            //Is this NPC replacement or NPC contesting
            if($numcands > ($seats * 2)) {
                echo "Contest<br>";
                processContest($link, $electionID, $seatID, $seats, $franchise, $price, $voters, $sqlMPs);
            } else {
                echo "NPC replacement or contest<br>";
                //Probably NPC replacement, but lets check a few things
                //Is this a burgage seat?
                if($franchise == 3) {
                    processBurgageElection($link, $electionID, $seatID, $seats, $sqlMPs);
                } else {
                    //Get the previous MPs, which should all be NPCs
                    $previous = getMPsInSeat($link, $seatID);
                    $resultingMPs = $previous;
                    $playerMPs = getPlayerMPsInSeat($link, $seatID);
                    $parray = array();
                    $narray = array();
                    foreach($playerMPs as $p) {
                        array_push($parray, getMPPartyID($link, $p));
                    }
                    
                    foreach($previous as $key => $p) {
                        array_push($narray, getMPPartyID($link, $p));
                    }

                    if(areAllMPsNPCInSeat($link, $seatID)) {
                        echo "All MPs are NPCs <br>";
                        $a = array_count_values(getPartyOfMPsInSeat($link, $seatID)); // NPC parties and how many MPs
                        $b = array_count_values($parray); //Player parties and amount of MPs
                        if($a == $b) {
                            //If the 'glove fits'
                            echo "'Glove Fits'<br>";
                            echo "Finished contest <br><br>";
                            insertSeatResults($link, $electionID, $seatID, $seats, $playerMPs);
                        } else {
                            //If they aren't exact
                            echo "Glove doesn't fit<br>";
                            $c = array_key_first($a); // party of the previous
                            $d = array_key_first($b); // party of the new
                            //only returns true if everyone present, previous or new, is of the same party
                            if(count($a) == 1 && count($b) == 1 && $c == $d) {
                                echo "Everyone is of the same party<br>";
                                $cpmp = count($playerMPs);
                                for($i = 0; $i < $cpmp; $i++) {
                                    $resultingMPs[$i] == $playerMPs[$i];
                                }
                                echo "Finished contest <br><br>";
                                insertSeatResults($link, $electionID, $seatID, $seats, $resultingMPs);
                            } else {
                                //if there's more than 1 NPC party
                                //and there's more than 1 player party
                                $room = false;
                                //Check if there is room for all
                                foreach($a as $key => $freq) {
                                    if(!array_key_exists($key, $b) || $freq < $b[$key]) {
                                        processContest($link, $electionID, $seatID, $seats, $franchise, $price, $voters, $sqlMPs);
                                        break;
                                    } else {
                                        $room = true;
                                        break;
                                    }
                                }
                                if($room == true) {
                                    echo "There was room<br>";
                                    foreach($playerMPs as $p) {
                                        foreach($resultingMPs as $key=> $r) {
                                            //if the current prev was an NPC
                                            if(isControlledByNPC($link, $resultingMPs[$key]) && getMPPartyID($link, $p) == getMPPartyID($link, $r)) {
                                                $resultingMPs[$key] = $p;
                                            }
                                        }
                                    }
                                    echo "Finished contest <br><br>";
                                    insertSeatResults($link, $electionID, $seatID, $seats, $resultingMPs);
                                }
                            }
                        }
                    } else {
                        //Not all of the current MPs are controlled by NPCs but at least 1 is
                        echo "Not all MPs are NPCs <br>";
                        foreach($playerMPs as $p) {
                            if(in_array(getMPPartyID($link, $p), $narray)) {
                                foreach($resultingMPs as $key => $r) {
                                    //is the current seat NPC
                                    if(isControlledByNPC($link, $resultingMPs[$key])) {
                                        //replace them
                                        $resultingMPs[$key] = $p;
                                    }
                                }
                            } else {
                                processContest($link, $electionID, $seatID, $seats, $franchise, $price, $voters, $sqlMPs);
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            //if there is not a contest
            //Use last elections results
            //Because technically, there is no players and no one contesting
            echo "No contest<br><br>";
            useLastElectionResults($link, $seats, $electionID, $seatID);
        }
    }
    echo "<br>Election complete";
}

/**
 * processContest
 *
 * @param  mysqli_connection $link
 * @param  int $electionID
 * @param  int $seatID
 * @param  int $numseats
 * @param  int $franchise
 * @return void
 */
function processContest($link, $electionID, $seatID, $numseats, $franchise, $price, $voters, $sqlMPs) {
    echo "Contest detected<br>";
    switch ($franchise) {
        case 0:
            processFreeholderElection($link, $electionID, $seatID, $numseats, $price, $voters, $sqlMPs);
            break;
        case 1:
            processCorporationElection($link, $electionID, $seatID, $numseats, $sqlMPs);
            break;
        case 2:
            processScotAndLotElection($link, $electionID, $seatID, $numseats, $price, $voters, $sqlMPs);
            break;
        case 3:
            processBurgageElection($link, $electionID, $seatID, $numseats, $sqlMPs);
            break;
        case 4:
            processUniversityElection($link, $sqlMPs);
            break;
        case 5:
            processFreeholderElection($link, $electionID, $seatID, $numseats, $price, $voters, $sqlMPs);
            break;
    }
}

function processCorporationElection($link, $electionID, $seatID, $seats, $sqlMPs) {
    $mpsinseats = mysqli_fetch_all($sqlMPs, MYSQLI_ASSOC);
    $mps = array();
    $moneyinseat = 0;
    //if there is a contest
    foreach($mpsinseats as $mp) {
        $moneyinseat += $mp['purse'];
        if($mp['purse'] > 0) {
            array_push($mps, array(intval($mp['ID']), intval($mp['purse'])));
        }
    }
    //Is there money?
    if($moneyinseat > 0) {
        //sort them
        usort($mps, 'sortMPs');
        $winners = array();
        for($i = 0; $i < $seats; $i++) {
            array_push($winners, $mps[$i][0]);
        }
        echo "Finished contest<br><br>";
        insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
        return;
    } else {
        echo "No Money<br>";
        //use last results if there is none
        echo "Finished contest<br><br>";
        useLastElectionResults($link, $seats, $electionID, $seatID);
        return;
    }
}

function processFreeholderElection($link, $electionID, $seatID, $seats, $price, $voters, $sqlMPs) {
    $mpsinseats = mysqli_fetch_all($sqlMPs, MYSQLI_ASSOC);
    $mps = array();
    $moneyinseat = 0;
    //if there is a contest
    foreach($mpsinseats as $mp) {
        $moneyinseat += $mp['purse'];
    }
    if($moneyinseat > 0) {
        //if there is money
        if(($moneyinseat / $price) <= $voters ) {
            //if bought voters is less than voters
            //get bought votes, give NPCs the remainder based on previous seats
            $boughtvotes = 0;
            //put the bought votes in the array
            foreach($mpsinseats as $mp) {
                //if they're not NPC Whig or NPC Tory, or the Speaker seat
                if(!isNPC($link, $mp['employerID'])) {
                    $votes = intval($mp['purse'] / $price);
                    $boughtvotes += $votes;
                    array_push($mps, array(intval($mp['ID']), $votes));
                }
            }
            //after the players/non-NPCs have been processed
            //do the NPCs
            $remaining = $voters - $boughtvotes;
            $votespernpc = $remaining / $seats;
            foreach($mpsinseats as $mp) {
                //NPCs only or the speaker
                if(isNPC($link, $mp['employerID'])) {
                    array_push($mps, array(intval($mp['ID']), $votespernpc));
                }
            }
            //Sort by most votes
            usort($mps, 'sortMPs');
            $winners = array();
            foreach($mps as $mp) {
                array_push($winners, $mp[0]);
            }
            insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
        } else {
            //if bought voters is greater than voters
            //give MPs votes proportional to their money spent
            foreach($mpsinseats as $mp) {
                $percentage = $mp['purse'] / $moneyinseat;
                $mpv = intval($percentage * $voters);
                array_push($mps, array(intval($mp['ID']), $mpv));
            }
            usort($mps, 'sortMPs');
            $winners = array();
            foreach($mps as $mp) {
                array_push($winners, $mp[0]);
            }
            insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
            drainAllMoneyFromMPsArr($link, $winners);
        }
    } else {
        //if there is no money, take the previous seats
        echo "Finished contest<br><br>";
        useLastElectionResults($link, $seats, $electionID, $seatID);
    }
}

//$sqlMPs = mysqli_result of the MPs running in the seat
function processBurgageElection($link, $electionID, $seatID, $seats, $sqlMPs) {
    //It is a burgage
    $playermps = array();
    
    //Get all the player MPs and put their employers into an array
    while($mp = mysqli_fetch_array($sqlMPs, MYSQLI_ASSOC)) {
        if($mp['employerID'] != 1 && $mp['employerID'] != 2) {
            array_push($playermps, $mp['employerID']);
        }
    }

    //Make sure we're using unique employers
    $playermps = array_unique($playermps);
    $burgageowningplayers = array();

    //Get the burgages in the seat owned by the MPs, if any
    foreach($playermps as $pmp) {
        $burgages = mysqli_query($link, "SELECT Count FROM burgageHolders WHERE seatID = {$seatID} AND holderID = {$pmp} AND Count > 0");
        $count = mysqli_fetch_array($burgages)[0];
        if(mysqli_num_rows($burgages) > 0) {
            array_push($burgageowningplayers, array($pmp, $count));
        }
    }

    //If the players running own burgages
    if(!empty($burgageowningplayers)) {
        //Get the total amount of burgages in the seat
        $sql = "SELECT Count FROM burgages WHERE ID = {$seatID}";
        $totalb = mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC)['Count'];
        $foundmajor = false; //flag
        $major; //ID of the biggest burgage owner
        if(count($burgageowningplayers) > 1) {
            usort($burgageowningplayers, 'sortMPs');
        }

        //does one of them own over 50%?
        foreach($burgageowningplayers as $bmp) {
            if($foundmajor == false) {
                if($bmp[1] > ($totalb / 2)) {
                    //Found em
                    $foundmajor = true;
                    $major = $bmp[0];
                }
            } else {
                //Found em, so break out of the for
                break;
            }
        }

        //If there is someone with over 50%
        if($foundmajor) {
            //Get their MPs at that seat
            $sql = "SELECT ID FROM EmployedMPs WHERE employerID = {$major} AND seatID = {$seatID}";
            $mpsmaj = mysqli_query($link, $sql);
            $nummps = mysqli_num_rows($mpsmaj);
            //If there are less MPs than seats, make an MP to stand for them
            if($seats > $nummps) {
                $mpstomake = $seats - $nummps;
                for($i = 1; $i <= $mpstomake; $i++) {
                    $sql = "INSERT INTO EmployedMPs (employerID, seatID) VALUES ({$major}, {$seatID})";
                    mysqli_query($link, $sql);
                }
            }
            insertSeatResults($link, $electionID, $seatID, $seats, getMPIDsofPlayerAtSeat($link, $major, $seatID));
            return;
        } else {
            //If there is not a majority
            //Depends on if there's 1 player or more
            if(count($burgageowningplayers) > 1) {
                //If there is more than 1, split between the 2 biggest
                $biggest = array();
                //Get the first mp at the seat belonging to each player
                for($i = 0; $i <= 1; $i++) {
                    array_push($biggest, getnthMPIDsofPlayerAtSeat($link, $burgageowningplayers[$i], $seatID, $i));
                }
                insertSeatResults($link, $electionID, $seatID, $seats, $biggest);
                return;
            }
        }
    } else {
        //They don't own burgages
        echo "Finished contest<br><br>";
        useLastElectionResults($link, $seats, $electionID, $seatID);
        return;
    }
}

function processScotAndLotElection($link, $electionID, $seatID, $seats, $price, $voters, $sqlMPs) {
    $mpsinseats = mysqli_fetch_all($sqlMPs, MYSQLI_ASSOC);
    $mps = array();
    $moneyinseat = 0;
    //if there is a contest
    foreach($mpsinseats as $mp) {
        $moneyinseat += $mp['purse'];
        if($mp['purse'] > 0) {
            array_push($mps, array($mp['ID'], $mp['purse']));
        }
    }
    if(count(getPlayerMPsInSeat($link, $seatID)) > 0) {
        if(($moneyinseat / $price) <= $voters ) {
            //if bought voters is less than voters
            //get bought votes, give NPCs the remainder based on previous seats
            $boughtvotes = 0;
            //put the bought votes in the array
            foreach($mpsinseats as $mp) {
                //if they're not NPC Whig or NPC Tory, or the Speaker seat
                if(!isControlledByNPC($link, $mp['ID'])) {
                    $votes = intval($mp['purse'] / $price);
                    $boughtvotes += $votes;
                    array_push($mps, array(intval($mp['ID']), $votes));
                }
            }
            //after the players/non-NPCs have been processed
            //do the NPCs
            $remaining = $voters - $boughtvotes;
            $votespernpc = $remaining / $seats;
            while($mp = mysqli_fetch_array($sqlMPs, MYSQLI_ASSOC)) {
                //NPCs only or the speaker
                if(($mp['employerID'] == 1) || ($mp['employerID'] != 2) || ($mp['employerID'] != 0)) {
                    array_push($mps, array(intval($mp['ID']), $votespernpc));
                }
            }

            //Sort by most votes
            usort($mps, 'sortMPs');
            $winners = array();
            foreach($mps as $mp) {
                array_push($winners, $mp[0]);
            }
            echo "Finished contest<br><br>";
            insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
            return;
        } else {
            //if bought voters is greater than voters
            //give MPs votes proportional to their money spent
            while($mp = mysqli_fetch_array($sqlMPs, MYSQLI_ASSOC)) {
                $mpv = intval($mp['purse'] / $price);
                array_push($mps, array(intval($mp['ID']), $mpv));
            }
            usort($mps, 'sortMPs');
            $winners = array();
            foreach($mps as $mp) {
                array_push($winners, $mp[0]);
            }
            echo "Finished contest<br><br>";
            insertSeatResults($link, $electionID, $seatID, $seats, $winners, $mps);
            drainAllMoneyFromMPsArr($link, $winners);
            return;
        }
    } else {
        echo "Finished contest<br><br>";
        useLastElectionResults($link, $seats, $electionID, $seatID);
        return;
    }
}

/**
 * insertSeatResults
 *
 * @param  mysqli_conn $link
 * @param  int $electionID
 * @param  int $seatID
 * @param  int $numseats
 * @param  array $winners
 * @param  array $voters = null
 * @return void
 */
function insertSeatResults($link, $electionID, $seatID, $numseats, array $winners, array $voters = null) {
    $updatestring = "";
    $insertvalues = "";
    $insertcolumns = "";
    $vi;
    for($i = 0; $i < $numseats; $i++) {
        $j = $i + 1;
        if($i == $numseats - 1) {
            $updatestring .= ("seat{$j} = {$winners[$i]} ");
            $insertcolumns .= ("Seat{$j} ");
            $insertvalues .= ("{$winners[$i]} ");
        } else {
            $updatestring .= ("seat{$j} = {$winners[$i]}, ");
            $insertcolumns .= ("Seat{$j}, ");
            $insertvalues .= ("{$winners[$i]}, ");
        }
    }
    $sql = "UPDATE seats SET {$updatestring} WHERE ID = {$seatID}";
    if (!mysqli_query($link, $sql)) {
        echo $sql . "<br>";
        echo("Error description: " . mysqli_error($link) . " <br>");
    }
    if(isset($voters)) {
        $vi = json_encode($voters);
        $sql = "INSERT INTO ElectionResults (ElectionID, SeatID, {$insertcolumns}, voters) VALUES ({$electionID}, {$seatID}, {$insertvalues}, \"{$vi}\")";
    } else {
        $sql = "INSERT INTO ElectionResults (ElectionID, SeatID, {$insertcolumns}) VALUES ({$electionID}, {$seatID}, {$insertvalues})";
    }
    if (!mysqli_query($link, $sql)) {
        echo $sql . "<br>";
        echo("Error description: " . mysqli_error($link) . " <br>");
    }
}

function drainAllMoneyFromMPsArr($link, $mps) {
    foreach($mps as $mp) {
        $sql = "UPDATE EmployedMPs SET purse = 0 WHERE ID = {$mp}";
        mysqli_query($link, $sql);
    }
}

function drainAllMoneyFromMPs($link,...$mps) {
    foreach($mps as $mp) {
        $sql = "UPDATE EmployedMPs SET purse = 0 WHERE ID = {$mp}";
        mysqli_query($link, $sql);
    }
}

function drainAllMoneyFromMP($link, $mps) {
    $sql = "UPDATE EmployedMPs SET purse = 0 WHERE ID = {$mp}";
    return mysqli_query($link, $sql);
}

function useLastElectionResults($link, $numseats, $electionID, $seatID) {
    $sql = "SELECT seat1, seat2, seat3, seat4 FROM seats WHERE ID = {$seatID}";
    $last = mysqli_query($link, $sql);
    $last = mysqli_fetch_array($last, MYSQLI_NUM);
    $insertvalues = "";
    $insertcolumns = "";
    for($i = 0; $i <= $numseats - 1; $i++) {
        $j = $i + 1;
        if($i == ($numseats - 1)) {
            $insertcolumns .= ("Seat{$j}");
            $insertvalues .= ("{$last[$i]}");
        } else {
            $insertcolumns .= ("Seat{$j}, ");
            $insertvalues .= ("{$last[$i]}, ");
        }
    }
    $sql = "INSERT INTO ElectionResults (ElectionID, SeatID, {$insertcolumns}) VALUES ({$electionID}, {$seatID}, {$insertvalues})";
    if (!mysqli_query($link, $sql)) {
        echo $sql . "<br>";
        echo("Error description: " . mysqli_error($link) . " <br>");
    }
}

//Returns an array of MP IDs at a seat given a players ID and the seatID
function getMPIDsofPlayerAtSeat($link, $playerID, $seatID) {
    $sql = "SELECT ID FROM EmployedMPs WHERE employerID = {$playerID} AND seatID = {$seatID} ORDER BY ID";
    $mps = mysqli_query($link, $sql);
    $return = array();
    while($mp = mysqli_fetch_array($mps)){
        array_push($return, $mp[0]);
    }
    return $return;
}

function getnthMPIDsofPlayerAtSeat($link, $playerID, $seatID, $n) {
    $sql = "SELECT ID FROM EmployedMPs WHERE employerID = {$playerID} AND seatID = {$seatID} ORDER BY ID";
    $mps = mysqli_query($link, $sql);
    return mysqli_fetch_array($mps)[$n][0];
}

//returns an array of IDs of MPs currently occupying seats
function getMPsInSeat($link, $seatID) {
    $seats = intval(mysqli_fetch_array(mysqli_query($link, "SELECT seats FROM seats WHERE ID = {$seatID}"), MYSQLI_ASSOC)['seats']);
    $selectcolumns = "";
    for($i = 1; $i <= $seats; $i++) {
        if($i == ($seats)) {
            $selectcolumns .= ("seat{$i}");
        } else {
            $selectcolumns .= ("seat{$i}, ");
        }
    }
    $sql = "SELECT {$selectcolumns} FROM seats WHERE ID = {$seatID}";
    return mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_NUM);
}

//returns an array of parties
function getPartyOfMPsInSeat($link, $seatID) {
    $mps = getMPsInSeat($link, $seatID);
    $array = array();
    foreach($mps as $mp) {
        array_push($array, getMPPartyID($link, $mp));
    }
    return $array;
}

//returns array of MP IDs controlled by players in seat
function getPlayerMPsInSeat($link, $seatID) {
    $mps = mysqli_fetch_all(mysqli_query($link, "SELECT ID, employerID FROM EmployedMPs WHERE seatID = {$seatID}"), MYSQLI_BOTH);
    $playermps = array();
    foreach($mps as $mp) {
        if(!isNPC($link, $mp['employerID'])) {
            array_push($playermps, $mp['ID']);
        }
    }
    return $playermps;
}

//returns array of MP IDs controlled by npcs in seat
function getNPCMPsInSeat($link, $seatID) {
    $sql = "SELECT ID, employerID FROM EmployedMPs WHERE seatID = {$seatID}";
    $mps = mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_BOTH);
    $npcsmps = array();
    foreach($mps as $mp) {
        if(isNPC($link, $mp['ID'])) {
            array_push($npcsmps, $mp['ID']);
        }
    }
    return $npcsmps;
}

//returns true if all the current MPs are NPCs
function areAllMPsNPCInSeat($link, $seatID) {
    $a = false;
    $arr = getMPsInSeat($link, $seatID);
    foreach($arr as $b) {
        if(isControlledByNPC($link, $b)) {$a = true;}
    }
    return $a;
}

//Returns the ID of the MPs employer
function getMPEmployer($link, $ID) {
    return (int) mysqli_fetch_array(mysqli_query($link, "SELECT employerID FROM employedmps WHERE ID = {$ID}"))[0];
}

//returns true if the MP is controlled by an NPC
function isControlledByNPC($link, $ID) {
    return (bool) isNPC($link, getMPEmployer($link, $ID));
}

//returns true or false if the 'person' is an NPC
function isNPC($link, $ID) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT isNPC FROM people WHERE ID = {$ID}"))[0];
}

function getMPEmployerName($link, $ID) {
    $eid = intval(getMPEmployer($link, $ID));
    $name = mysqli_fetch_array(mysqli_query($link, "SELECT FirstName, LastName FROM people WHERE ID = {$eid}"));
    $name = $name[0] . " " . $name[1];
    return $name;
}

function getElectionYear($link, $ID) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT Year FROM elections WHERE ID = {$ID}"))['Year'];
}

function getElectionResultsTable($link, $seatID) {
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
                            <th colspan=\"2\">Interest</th>
                            <th>Votes</th>
                        </tr>
                    </thead>
                    <tbody>";
                for ($i = 1; $i <= $seats; $i++) { 
                    $ID = $row['Seat'.$i];
                    $color = getMPColor($link, $ID);
                    $name = getMPEmployerName($link, $ID);
                    $v = "";
                    if($row['voters'] != null) {
                        $v = $voters[$i-1][1];
                    }
                    echo "<tr>";
                    echo "<td>{$ID}</td>";
                    echo "<td width=\"15px\" style=\"background-color: {$color}\"></td>";
                    echo "<td><b>{$name}</b></td>";
                    echo "<td><b>{$v}</b></td>";
                    echo "</tr>";
                }
                if($row['voters'] != null) {
                    if(count($voters) > $seats) {
                        for($i = $seats + 1; $i <= (count($voters)); $i++) {
                            $color = getMPColor($link, $voters[$i-1][0]);
                            $name = getMPEmployerName($link, $voters[$i-1][0]);
                            $ID = $voters[$i-1][0];
                            $v = $voters[$i-1][1];
                    echo "<tr>";
                    echo "<td>{$ID}</td>";
                    echo "<td width=\"15px\" style=\"background-color: {$color}\"></td>";
                    echo "<td>{$name}</td>";
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

function getUsername($link, $ID) {
    $data = mysqli_fetch_array(mysqli_query($link, "SELECT username, discordUser FROM users WHERE id = {$ID}"));
    if(isset($data[1]) && $data[1] != null) {
        return $data[1];
    } else {
        return $data[0];
    }
}

function getTimeStuff($link) {
    $sql = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `years` ORDER BY Year DESC LIMIT 1"));
    return $sql;
}

function getName($link, $ID) {
    $data = mysqli_fetch_array(mysqli_query($link, "SELECT FirstName, LastName FROM people WHERE ID = {$ID}"));
    return $data[0] . " " . $data[1];
}

/*********************/
/* Economy functions */
/*********************/
function generateEconMap($link, $isInteractive = true) {
    $sqlget = "SELECT ID, name, shape, Terrain FROM provinces ORDER BY ID asc";
    $sqldata2 = mysqli_query($link, $sqlget);
    echo "<svg width=\"100%\" id=\"map\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"170 0 720 1250\">";
    while($province = mysqli_fetch_array($sqldata2, MYSQLI_ASSOC)) {
        $color = "#95CBA4";
        $color = getProvinceTerrainColor($link, $province['Terrain']);
        if($isInteractive == true) {
            echo "<a href=province.php?id=" . $province['ID'] . ">";
        }
        echo "<path data-tooltip-text=\"" . $province['name'] . "\" class=\"tooltip-trigger\" id=" . $province['ID'] . " " . $province['shape'] . "stroke=\"black\" style=\"fill:" . $color . "\" stroke-width=\"0.5\">";
        echo "</path>";
        if($isInteractive == true) {
        echo "</a>";
        }
    }
    if($isInteractive == true) {
    echo "
        <g id=\"tooltip\" visibility=\"hidden\">
            <rect id=\"ttrect\" width=\"80\" height=\"30\" fill=\"white\" rx=\"2\" ry=\"2\"/>
            <text id=\"tttext\" x=\"2\" y=\"26\" font-size=\"25px\">Tooltip</text>
        </g>
    ";
    }

    echo "
    <script type=\"text/javascript\"><![CDATA[
        (function() {
            var tooltip = document.getElementById('tooltip');
            var svg = document.getElementById('map');
            var triggers = document.getElementsByClassName('tooltip-trigger');
            var tooltipText = tooltip.getElementsByTagName('text')[0];
            var tooltiprect = tooltip.getElementsByTagName('rect')[0];
            for (var i = 0; i < triggers.length; i++) {
                triggers[i].addEventListener('mousemove', showTooltip);
                triggers[i].addEventListener('mouseout', hideTooltip);
            }

            function setStrokeWidth(width) {
                var list = document.getElementsByTagName('path');
                for(let item of list) {
                    item.setAttribute('stroke-width', width);
                }
            }

            function showTooltip(evt) {
                var CTM = svg.getScreenCTM();
                var x = (evt.clientX - CTM.e + 6) / CTM.a;
                var y = (evt.clientY - CTM.f + 20) / CTM.d;
                tooltip.setAttributeNS(null, \"transform\", \"translate(\" + x + \" \" + y + \")\");
                tooltip.setAttributeNS(null, \"visibility\", \"visible\");
                
                tooltipText.firstChild.data = evt.target.getAttributeNS(null, \"data-tooltip-text\");
                var bbox = tooltipText.getBBox();
                var twidth = bbox.width;
                tooltiprect.setAttribute('width', twidth + 6);
            }

            function hideTooltip() {
                tooltip.setAttributeNS(null, \"visibility\", \"hidden\");
            }
        })();
    ]]>
    </script>";
    echo "</svg>";
}

/********************/
/* Province Getters */
/********************/
function getProvinceTerrainColor($link, $terrain) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT color FROM terraintypes WHERE ID = {$terrain}"))[0];
}

function getProvinceBaseSize($link, $province, $rtsm) {
    $lpopsd = mysqli_fetch_all(mysqli_query($link, "SELECT size FROM pops WHERE province = {$province} AND type = 1"));
    $tworkers = 0;
    foreach($lpopsd as $lpop) {
        $tworkers += $lpop['0'];
    }
    $bsize = floor(1.5 * ceil($tworkers / (MAX_WORKERS * $rtsm)));
    return $bsize;
}

/********************/
/* Resource Getters */
/********************/
function getResourceName($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM resources WHERE ID = {$id}"))[0];
}

function getResourceOutput($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT outputAmount FROM resources WHERE ID = {$id}"))[0];
}

function getResourceType($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM resources WHERE ID = {$id}"))[0];
}

function getResourcePrice($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT currentPrice FROM resources WHERE ID = {$id}"))[0];
}

function getResourcebasePrice($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT basePrice FROM resources WHERE ID = {$id}"))[0];
}

function getResourceTerrainSizeModifiers($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT `farmSize`, `mineSize`, `timberSize`, `tropicalSize` FROM terraintypes WHERE ID = {$id}"), MYSQLI_NUM);
}

//Alias of above
function getRTSMs($link, $id) {
    return getResourceTerrainSizeModifiers($link, $id);
}

function getResourceTerrainEfficiencyModifiers($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT `farmEfficiency`, `mineEfficiency`, `timberEfficiency`, `tropicalEfficiency` FROM terraintypes WHERE ID = {$id}"), MYSQLI_NUM);
}

//Alias of above
function getRTEMs($link, $id) {
    return getResourceTerrainEfficiencyModifiers($link, $id);
}

function getResourceCategory($link, $resource) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT category FROM resources WHERE ID = {$resource}"), MYSQLI_NUM)[0];
}

/********************/
/* Resource Setters */
/********************/
function setResourcePrice($link, $price, $id) {
    mysqli_query($link, "UPDATE resourves SET currentPrice = {$price} WHERE ID = {$id}");
}

function adjustPrices($link, $leftovers) {
    foreach($leftovers as $key => $l) {
        $bp = getResourcebasePrice($link, $key);
        $cp = getResourcePrice($link, $key);
        if($l < 0) {
            if($cp < ($bp * PRICE_MAXIMUM_MULTIPLIER)) {
                $np = $cp + BASE_PRICE_CHANGE;
                setResourcePrice($link, $np, $key);

                //Check if the price has some how gone over the maximum, set it back to the maximum
            } else if($cp > ($bp * PRICE_MAXIMUM_MULTIPLIER)) {
                setResourcePrice($link, $bp * PRICE_MAXIMUM_MULTIPLIER, $key);
            }
        } else if($l > 0) {
            if($cp > ($bp * PRICE_MINIMUM_MULITPLIER)) {
                $np = $cp - BASE_PRICE_CHANGE;
                setResourcePrice($link, $np, $key);

                //Check if the price has some how gone over the maximum, set it back to the maximum
            } else if($cp < ($bp * PRICE_MINIMUM_MULITPLIER)) {
                setResourcePrice($link, $bp * PRICE_MINIMUM_MULITPLIER, $key);
            }
        }
    }
}

/********************/
/* Terrain Getters */
/********************/
function getTerrainName($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM terraintypes WHERE ID = {$id}"), MYSQLI_BOTH)[0];
}

function getTerrainModifiers($link, $id) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT * FROM terraintypes WHERE ID = {$id}"), MYSQLI_BOTH);
}

/***************/
/* Pop Getters */
/***************/
function getPopNeeds($link, $popID, $ntype = 0) {
    $pop = mysqli_fetch_array(mysqli_query($link, "SELECT type, size FROM pops WHERE ID = {$popID}"));
    $totalneeds = array();
    $popneeds = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM poptypes WHERE ID = {$pop['type']}"), MYSQLI_BOTH);
    $rsc = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM resources"));
    foreach($rsc as $r) {
        $totalneeds[$r[0]] = 0;
    }
    $life = json_decode($popneeds[2]);
    $everyday = json_decode($popneeds[3]);
    $luxury = json_decode($popneeds[4]);
    if($ntype == 0 || $ntype == 1) {
        foreach($life as $l) {
            $y = 1 * $l[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
            $totalneeds[$l[0]] += $y;
        }
    }

    if($ntype == 0 || $ntype == 2 || $ntype == 4) {
        foreach($everyday as $e) {
            $y = 1 * $e[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
            $totalneeds[$e[0]] += $y;
        }
    }

    if($ntype == 0 || $ntype == 3 || $ntype == 4) {
        foreach($luxury as $l) {
            $y = 1 * $l[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
            $totalneeds[$l[0]] += $y;
        }
    }
    return $totalneeds;
}

function getPopIcon($type) {
    switch ($type) {
        case 1:
            return '<i class="fa-solid fa-person-digging"></i>';
            break;
        case 2:
            return '<i class="fa-solid fa-person-military-rifle"></i>';
            break;
        case 3:
            return '<i class="fa-solid fa-hammer"></i>';
            break;
        case 4:
            return '<i class="fa-solid fa-pen-ruler"></i>';
            break;
        case 5:
            return '<i class="fa-solid fa-file-pen"></i>';
            break;
        case 6:
            return '<i class="fa-solid fa-graduation-cap"></i>';
            break;
        case 7:
            return '<i class="fa-solid fa-user-tie"></i>';
            break;
        case 8:
            return '<i class="fa-solid fa-medal"></i>';
            break;
        case 9:
            return '<i class="fa-solid fa-person-walking-with-cane"></i>';
            break;
    }
}

function getPopName($link, $type) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM poptypes WHERE ID = {$type}"))[0];
}

function getPOPOutput($link, $pop) {
    //$workers = $popdata['size'];

    //$terrain = mysqli_fetch_array(mysqli_query($link, "SELECT Terrain FROM provinces WHERE ID = {$popdata['province']}"))[0];

    $rtsms = getRTSMs($link, $terrain);
    $rtems = getRTEMs($link, $terrain);

    //Output Amount
    $oa = getResourceOutput($link, $popdata['type']);

    //Resource Type
    $rt = getResourceType($link, $popdata['type']);

    //Resource Terrain Size Modifier
    //rt = column of size mod depending on type - 1 because arrays start at 0
    $rtsm = $rtsms[$rt - 1];

    //Resource Terrain Efficiency Modifier
    //rt = column of eff mod depending on type - 1 because arrays start at 0
    $rtem = $rtems[$rt - 1];

    //Province Size
    $bsize = getProvinceBaseSize($link, $popdata['province'], $rtsm);

    //Base Production = (Province Size) * Terrain Modifiers * (Output Amount)
    $bp = $bsize * $rtsm * $oa;
    $tp = ($workers / (MAX_WORKERS * $bsize));
    $oe = 1 + ($bcrats / $workers) + $rtem;
    $output = $bp * $tp * $oe;
    return $output;
}

function getPOPSavings($link, $pop) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT money FROM pops WHERE ID = {$pop}"))[0];
}

/***************/
/* Pop Setters */
/***************/
function payPOP($link, $amount, $popID) {
    mysqli_query($link, "UPDATE pops SET `money` = money + {$amount} WHERE ID = {$popID}");
}

/**********************/
/* Production Getters */
/**********************/

function getProvResourceOutput($link, $id, $type, $workers, $bcrats, $terrain) {
    $rtsms = getRTSMs($link, $terrain);
    $rtems = getRTEMs($link, $terrain);
    //Output Amount
    $oa = getResourceOutput($link, $type);

    //Resource Type
    $rt = getResourceType($link, $type);

    //Resource Terrain Size Modifier
    //rt = column of size mod depending on type - 1 because arrays start at 0
    $rtsm = $rtsms[$rt - 1];

    //Resource Terrain Efficiency Modifier
    //rt = column of eff mod depending on type - 1 because arrays start at 0
    $rtem = $rtems[$rt - 1];

    //Province Size = [[total laborers in province / (maxWorker * terrain modifier for that resource)] rounded up * 1.5] rounded down
    //$bsize = floor(1.5 * ceil($tworkers / (MAX_WORKERS * $rtsm)));
    $bsize = getProvinceBaseSize($link, $id, $rtsm);

    //Base Production = (Province Size) * Terrain Modifiers * (Output Amount)
    $bp = $bsize * $rtsm * $oa;
    $tp = ($workers / (MAX_WORKERS * $bsize));
    $oe = 1 + ($bcrats / $workers) + $rtem;
    $output = $bp * $tp * $oe;
    return $output;
}

function getCountryResourceOutput($link, $country) {
    $totaloutput = array();
    $rsc = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM resources"));
    foreach($rsc as $r) {
        $totaloutput[$r[0]] = 0;
    }
    $provinces = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM provinces WHERE Country = {$country}"), MYSQLI_ASSOC);
    foreach($provinces as $province) {
        $terrain = $province['Terrain'];
        $rtsms = getRTSMs($link, $terrain);
        $rtems = getRTEMs($link, $terrain);
        $pops = mysqli_fetch_all(mysqli_query($link, "SELECT type, size, production FROM pops WHERE province = {$province['ID']}"), MYSQLI_ASSOC);
        $bcrats = 0;
        $tworkers = 0;

        //Get bureaucrats
        foreach($pops as $p) {
            $tworkers += $p['size'];
            if($p['type'] == 5) {
                $bcrats += $p['size'];
            }
        }

        //Now determine how much they make
        foreach($pops as $pop) {
            if($pop['production'] != null) {
                if(getResourceCategory($link, $pop['production']) == 1) {
                    $oa = getResourceOutput($link, $pop['production']);
                    $rt = getResourceType($link, $pop['production']);
                    $rtsm = $rtsms[$rt - 1];
                    $rtem = $rtems[$rt - 1];
                    $bsize = getProvinceBaseSize($link, $province['ID'], $rtsm);
                    $bp = $bsize * $rtsm * $oa;
                    $tp = ($pop['size'] / (MAX_WORKERS * $bsize));
                    $oe = 1 + ($bcrats / $pop['size']) + $rtem;
                    $output = $bp * $tp * $oe;
                    $totaloutput[$pop['production']] += $output;
                }
            }
        }
    }
    return $totaloutput;
}

function getNationResourceOutput($link, $nationID) {
    $population = 0;
    $totaloutput = array();
    $rsc = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM resources"));
    foreach($rsc as $r) {
        $totaloutput[$r[0]] = 0;
    }
    $countries = mysqli_fetch_all(mysqli_query($link, "SELECT ID, name FROM countries WHERE nationID = {$nationID}"));
    foreach($countries as $country) {
        $provinces = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM provinces WHERE Country={$country[0]}"), MYSQLI_ASSOC);
        foreach($provinces as $province) {
            $terrain = $province['Terrain'];
            $rtsms = getRTSMs($link, $terrain);
            $rtems = getRTEMs($link, $terrain);
            $pops = mysqli_fetch_all(mysqli_query($link, "SELECT type, size, production FROM pops WHERE province = {$province['ID']}"), MYSQLI_ASSOC);
            $bcrats = 0;
            $tworkers = 0;

            //Get bureaucrats
            foreach($pops as $p) {
                $tworkers += $p['size'];
                if($p['type'] == 5) {
                    $bcrats += $p['size'];
                }
            }

            //Now determine how much they make
            foreach($pops as $pop) {
                $population += $pop['size'];
                if($pop['production'] != null) {
                    if(getResourceCategory($link, $pop['production']) == 1) {
                        $oa = getResourceOutput($link, $pop['production']);
                        $rt = getResourceType($link, $pop['production']);
                        $rtsm = $rtsms[$rt - 1];
                        $rtem = $rtems[$rt - 1];
                        $bsize = getProvinceBaseSize($link, $province['ID'], $rtsm);
                        $bp = $bsize * $rtsm * $oa;
                        $tp = ($pop['size'] / (MAX_WORKERS * $bsize));
                        $oe = 1 + ($bcrats / $pop['size']) + $rtem;
                        $output = $bp * $tp * $oe;
                        $totaloutput[$pop['production']] += $output;
                    }
                }
            }
        }
    }
    return $totaloutput;
}

function displayCountryProduction($link, $country) {
    echo "<div class=\"row\">";
    echo "<div class=\"col-md-6 border border-white\">";
    $output = getCountryResourceOutput($link, $country);
    $num = 0;
    foreach($output as $o) {
        if($o > 0) {
            $num++;
        }
    }
    $half = $num  / 2;
    $i = 1;
    $broken = false;
    foreach($output as $n => $o) {
        if($o > 0) {
            $name = getResourceName($link, $n);
            echo "<img width=\"20px\" src=\"assets/icons/{$name}.svg\"/> " . round($o, 2) . " {$name} <br>\n";
            $i++;
        }
        if($i > $half && $broken == false) {
            echo "</div>";
            echo '<div class="col-md-6 border border-white">';
            $broken = true;
        }
    }
    echo "</div>";
    echo "</div>";
}

/*****************/
/* Needs Getters */
/*****************/
function getCountryNeeds($link, $country) {
    $totalneeds = array();
    $popneeds = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM poptypes "));
    $rsc = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM resources"));
    foreach($rsc as $r) {
        $totalneeds[$r[0]] = 0;
    }
    $provinces = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM provinces WHERE Country = {$country}"), MYSQLI_ASSOC);
    foreach($provinces as $province) {
        $pops = mysqli_fetch_all(mysqli_query($link, "SELECT type, size, production FROM pops WHERE province = {$province['ID']}"), MYSQLI_ASSOC);
        foreach($pops as $pop) {
            $pn = $popneeds[$pop['type'] - 1];
            //foreach($pn as $n) {
                $life = json_decode($pn[2]);
                $everyday = json_decode($pn[3]);
                $luxury = json_decode($pn[4]);
                foreach($life as $l) {
                    $y = 1 * $l[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
                    $totalneeds[$l[0]] += $y;
                }
                
                foreach($everyday as $e) {
                    $y = 1 * $e[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
                    $totalneeds[$e[0]] += $y;
                }
                
                foreach($luxury as $l) {
                    $y = 1 * $l[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
                    $totalneeds[$l[0]] += $y;
                }
            //}
        }
    }
    return $totalneeds;
}

function getNationNeeds($link, $nationID, $nType = 0) {
    $totalneeds = array();
    $popneeds = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM poptypes "));
    $rsc = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM resources"));
    foreach($rsc as $r) {
        $totalneeds[$r[0]] = 0;
    }
    
    $countries = mysqli_fetch_all(mysqli_query($link, "SELECT ID, name FROM countries WHERE nationID = {$nationID}"));
    foreach($countries as $country) {
        $provinces = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM provinces WHERE Country={$country[0]}"), MYSQLI_ASSOC);
        foreach($provinces as $province) {
            $pops = mysqli_fetch_all(mysqli_query($link, "SELECT type, size, production FROM pops WHERE province = {$province['ID']}"), MYSQLI_ASSOC);
            foreach($pops as $pop) {
                $pn = $popneeds[$pop['type'] - 1];
                $life = json_decode($pn[2]);
                $everyday = json_decode($pn[3]);
                $luxury = json_decode($pn[4]);
                if($nType == 0 || $nType == 1) {
                    foreach($life as $l) {
                        $y = 1 * $l[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
                        $totalneeds[$l[0]] += $y;
                    }
                }
                
                if($nType == 0 || $nType == 2 || $nType == 4) {
                    foreach($everyday as $e) {
                        $y = 1 * $e[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
                        $totalneeds[$e[0]] += $y;
                    }
                }
                if($nType == 0 || $nType == 3 || $nType == 4) {
                    foreach($luxury as $l) {
                        $y = 1 * $l[1] * BASE_POP_DEMAND * $pop['size'] / NEEDS_POP;
                        $totalneeds[$l[0]] += $y;
                    }
                }
            }
        }
    }
    return $totalneeds;
}

function displayCountryNeeds($link, $country) {
    echo "<div class=\"row\">\n";
    echo "<div class=\"col-md-6 border border-white\">\n";
    $needs = getCountryNeeds($link, $country);
    $num = 0;
    foreach($needs as $n) {
        if($n > 0) {
            $num++;
        }
    }
    $half = $num  / 2;
    $i = 1;
    $broken = false;
    foreach($needs as $n => $o) {
        if($o > 0) {
            $name = getResourceName($link, $n);
            echo "<img width=\"20px\" src=\"assets/icons/{$name}.svg\"/> " . round($o, 2) . " {$name} <br>\n";
            $i++;
        }
        if($i > $half && $broken == false) {
            echo "</div>";
            echo '<div class="col-md-6 border border-white">';
            $broken = true;
        }
    }
    echo "</div>";
    echo "</div>";
}

/*************/
/* Day Cycle */
/*************/

//Simulate 1 day
function runDay($link) {
    $globalMarket = new Market($link);
    $nations = mysqli_fetch_all(mysqli_query($link, "SELECT ID, name FROM nations"));
    $aNations = array();
    foreach($nations as $nation) {
        $oNation = new Nation($link, $nation[0]);
        $aNations[$oNation->getID()] = $oNation;
    }

    //RGO Gather Raw
    performResourceGathering($link, $aNations);

    echo "Processing artisans <br>\n";
    processArtisanProduction($link, $aNations);

    //Artisans produce stuff
    /*
    foreach($aNations as $nation) {
        print_r2($nation->getmarket());
    }
    */

    //Feed needs from common market
    feedNeeds($link, $aNations);
    echo "Feeding needs<br>\n";
    foreach($aNations as $nation) {
        print_r2($nation->getmarket());
    }

    determineArtisanChange($link, $aNations);
    /*
    //Export excess
    foreach($aNations as $nation) {
        $nation->exportExcessLifeNeeds($globalMarket);
    }
    
    */
    

    
    //Import additional life needs if available
    //TODO implement import tarriffs
    //importLifeNeeds($aNations, $globalMarket);

    //Adjust prices
    //adjustPrices($link, $leftovers);

    //produce manufactured items
    //'feed' everyday and luxury items
    //decideArtisanOutput($link);
}

//Meant to be run by runDay
function performResourceGathering($link, $aNations) {
    foreach($aNations as $nation) {
        $aCountries = $nation->getCountries();
        foreach($aCountries as $country) {
            $aProvinces = $country->getProvinces();
            foreach($aProvinces as $province) {
                $aPops = $province->getPopsOfType(1);
                foreach($aPops as $pop) {
                    $output = $pop->getPopRGOOutput($link);
                    $RGOIncome = $output * Resource::getResourcePrice($link, $pop->getProduction());
                    $oi = 0;
                    if($province->hasOwners()) {
                        $oi = $RGOIncome * 2 * ($province->getNOwners() / $province->getNWorkers());
                        $owners = $province->getOwners();
                        foreach($owners as $owner) {
                            if($owner[2] == $pop->getProduction())
                            $income = $oi * $owner[1];
                            if($income > 0) {
                                //payPlayer($link, $income, $owner[0]);
                            }
                        }
                    }
                    $popIncome = $RGOIncome - $oi;
                    $nation->addToMarket($pop->getProduction(), $output);
                    //$pop->payPOP($link, $popIncome);
                }
            }
        }
    }
}

//Attempt to feed the people from the common market
function feedNeeds($link, $aNations) {
    foreach($aNations as $nation) {
        $aCountries = $nation->getCountries();
        foreach($aCountries as $country) {
            $aProvinces = $country->getProvinces();
            foreach($aProvinces as $province) {
                $aPops = $province->getPops();
                foreach($aPops as $pop) {
                    $lifeneeds = $pop->getLifeNeeds();
                    foreach($lifeneeds as $idResource => $amountResource) {
                        if($amountResource > 0) {
                            $price = Resource::getResourcePrice($link, $idResource);
                            $balance = $pop->getMoney();
                            $avail = $nation->marketRequest($idResource, $amountResource);
                            if($avail) {
                                $cost = $avail * $price;
                                $pop->payPop($link, 0 - $cost);
                                $pop->feedLifeNeed(intval($idResource), $avail);
                            }
                        }
                    }
                    $everydayneeds = $pop->getEverydayNeeds();
                    foreach($everydayneeds as $idResource => $amountResource) {
                        if($amountResource > 0) {
                            $price = Resource::getResourcePrice($link, $idResource);
                            $balance = $pop->getMoney();
                            $avail = $nation->marketRequest($idResource, $amountResource);
                            if($avail) {
                                $cost = $avail * $price;
                                $pop->payPop($link, 0 - $cost);
                                $pop->feedEverydayNeed(intval($idResource), $avail);
                            }
                        }
                    }
                    $luxuryneeds = $pop->getLuxuryNeeds();
                    foreach($luxuryneeds as $idResource => $amountResource) {
                        if($amountResource > 0) {
                            $price = Resource::getResourcePrice($link, $idResource);
                            $balance = $pop->getMoney();
                            $avail = $nation->marketRequest($idResource, $amountResource);
                            if($avail) {
                                $cost = $avail * $price;
                                $pop->payPop($link, 0 - $cost);
                                $pop->feedLuxuryNeed(intval($idResource), $avail);
                            }
                        }
                    }
                }
            }
        }
    }
}

function importLifeNeeds($aNations, &$globalMarket) {
    foreach($aNations as $nation) {
        $aCountries = $nation->getCountries();
        foreach($aCountries as $country) {
            $aProvinces = $country->getProvinces();
            foreach($aProvinces as $province) {
                $aPops = $province->getPopsOfType(1);
                foreach($aPops as $pop) {
                    $lifeneeds = $pop->getLifeNeeds();
                    foreach($lifeneeds as $idResource => $amountResource) {
                        if($amountResource > 0) {
                            $price = Resource::getResourcePrice($link, $idResource);
                            $balance = $pop->getMoney();
                            $avail = $nation->marketRequest($idResource, $amountResource);
                            if($avail) {
                                $cost = $avail * $price;
                                $pop->payPop($link, 0 - $cost);
                                $pop->feedLifeNeed($idResource, $avail);
                            }
                        }
                    }
                }
            }
        }
    }
}

function processArtisanProduction($link, $aNations) {
    foreach($aNations as $nation) {
        $aCountries = $nation->getCountries();
        foreach($aCountries as $country) {
            $aProvinces = $country->getProvinces();
            foreach($aProvinces as $province) {
                $aPops = $province->getPopsOfType(4);
                foreach($aPops as $pop) {
                    $prod = $pop->getProduction();
                    if($prod != null) {
                        $recipe = Resource::getResourceRecipe($link, $prod);
                        $inventory = array();
                        $totalprice = 0;
                        $width = $pop->getSize() / ARTISAN_WIDTH;
                        foreach($recipe as $i => $a) {
                            $price = Resource::getResourcebasePrice($link, $i);
                            $totalprice = $price * $a;
                        }
                        //Work out how many they can make given their money and pop size
                        $bal = $pop->getMoney();
                        $factor = 1;
                        $factor = ($bal / $totalprice) * $width;
                        if($factor > $width) {
                            $factor = $width;
                        }
                        $avail = $nation->marketRecipeRequest($recipe, $factor);
                        $finalprice = 0;
                        foreach($avail as $i => $a) {
                            $finalprice += $a * Resource::getResourcebasePrice($link, $i);
                        }
                        $pop->payPop($link, 0 - $finalprice);
                        if (count($avail) > 0) {
                            $g = array_key_first($avail);
                            $finalfactor = $avail[$g] / $recipe[$g];

                            $bp = Resource::getResourceOutput($link, $prod);
                            $actualOutput = $finalfactor * $bp * ($pop->getSize() / 10000);

                            $p = Resource::getResourcePrice($link, $prod);
                            $profit = $actualOutput * $p;
                            $pop->payPop($link, $profit);
                        }
                    } else {
                        echo "Pop makes nothing<br>\n";
                    }
                }
            }
        }
    }
}

function determineArtisanChange($link, $aNations) {
    foreach($aNations as $nation) {
        $aCountries = $nation->getCountries();
        foreach($aCountries as $country) {
            $aProvinces = $country->getProvinces();
            foreach($aProvinces as $province) {
                $aPops = $province->getPopsOfType(4);
                foreach($aPops as $pop) {
                    $popmarket = &$nation->getMarket();
                    $currentR = $pop->getProduction();
                    $needsChange = false;
                    $foundApplicableChange = false;
                    //Is there a 'negative' supply of their current production?
                    if($popmarket[$currentR] < 0) {
                        $needsChange = true;
                        //If yes, determine the cause
                        //That is, find the ingredient that is lacking
                        
                        //Current Recipe
                        $recipe = Resource::getResourceRecipe($link, $currentR);

                        //Determine the most 'in demand'
                        $demandofrecipe = array();
                        foreach($recipe as $i => $a) {
                            if($popmarket[$currentR] < 0) {
                                $demandofrecipe[$i] = $popmarket[$i];
                            }
                        }
                        asort($demandofrecipe);
                        $percentage = 0.15;
                        if($pop->getMoney() == 0) {
                            $percentage = 0.2;
                        } else {
                            $percentage = 0.1;
                        }
                        foreach($demandofrecipe as $i => $a) {
                            if(!(Resource::getResourceCategory($link, $i) == 1)) {
                                $pop->changeProduction($link, $percentage, $i);
                                $foundApplicableChange = true;
                                break;
                            } else {
                                $foundApplicableChange = false;
                            }
                        }

                        //If the ingredients aren't viable, check for what's in most demand and make that
                        //if that thing's ingredients are also in need, the system will naturally want them to make the next thing down
                        if(!$foundApplicableChange) {
                            $mStockpile = $popmarket->stockpile;
                            asort($mStockpile);
                            $sKeys = array_keys($mStockpile);
                            $newID = 0;
                            foreach($sKeys as $k) {
                                if($k != $currentR && !(Resource::getResourceCategory($link, $i) == 1)) {      
                                    $percentage = 0.15;
                                    if($pop->getMoney() == 0) {
                                        $percentage = 0.2;
                                    } else {
                                        $percentage = 0.1;
                                    }
                                    $pop->changeProduction($link, $percentage, $k);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function payPlayer($link, $amount, $playerID) {}

?> 
