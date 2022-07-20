<?php

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
    $sqlget = 'SELECT ID, name, shape, seats, seat1, seat2, seat3, seat4 FROM seats';
    $sqldata2 = mysqli_query($link, $sqlget);
    echo "<svg height=\"800px\" id=\"map\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"225 0 605 1208.8\">";
    generatePatterns($link);
    $sqlpartyget = 'SELECT Color from parties';
    $colors = mysqli_query($link, $sqlpartyget);
    while($province = mysqli_fetch_array($sqldata2, MYSQLI_ASSOC)) {
        $color = "grey";
        if($province['seats'] == 1) {
            if($province['seat1'] != NULL) {
                $color = mysql_result($colors, $province['seat1'], 'Color');
            }
        }
        if($province['seats'] == 2) {
            if($province['seat1'] != NULL) {
                if($province['seat1'] == $province['seat2']) {
                    $color = mysql_result($colors, $province['seat1'], 'Color');
                }
                if($province['seat1'] != $province['seat2']) {
                    if((($province['seat1'] == 1) && ($province['seat2'] == 2)) || (($province['seat1'] == 2) && ($province['seat2'] == 1))) {
                        $color="url(#ToriesWhigs)";
                    }
                    if((($province['seat1'] == 0) && ($province['seat2'] == 2)) || (($province['seat1'] == 2) && ($province['seat2'] == 0))) {
                        $color="url(#ToriesSpeaker)";
                    }
                    if((($province['seat1'] == 0) && ($province['seat2'] == 1)) || (($province['seat1'] == 1) && ($province['seat2'] == 0))) {
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

function generateSeating($link) {
    $sqlget = 'SELECT ID, name, shape, seats, seat1, seat2, seat3, seat4 FROM seats';
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
            $seatsa[$seat['seat' . $i]] += 1;
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
}

//LOTS TO DO HERE
function processElection($link, $year) {
    /*
    $sqlNewElection = mysqli_query($link, "INSERT INTO Elections VALUES `year`=" . $year);
    $electionID = mysqli_insert_id($link)
    $sqlSeats = mysqli_query($link, "SELECT * seats");

    while($seat = mysqli_fetch_array($sqlSeats, MYSQLI_ASSOC)) {

        //Burgage Elections
        if($seat['FrachiseType'] == 3) {
            $sqlBurgageCount= mysqli_fetch_array(mysqli_query($link, "SELECT Count FROM burgages WHERE ID = " . $seat['ID']), MYSQLI_ASSOC);
            $burgageCount = $sqlBurgageCount['Count'];
            
            $sqlBurgageCount= mysqli_query($link, "SELECT holderID, Count FROM burgageHolders WHERE seatID = " . $seat['ID']);
            $burgerOwners = array();
            while($burgers = mysqli_fetch_array($sqlBurgageCount, MYSQLI_ASSOC)) {
                array_push($burgerOwners, array($burgers['holderID'], $burgers['Count']));
            }
            $biggestBurgerArray = array(0, 0);
            for($i = 0; $i < count($burgerOwners); $i++) {
                if($burgerOwners[$i][1] > $biggestBurgerArray[1]) {
                    $biggestBurgerArray[0] = $burgerOwners[$i][0];
                    $biggestBurgerArray[1] = $burgerOwners[$i][1];
                }
            }
            $sqlPerson = mysqli_fetch_array(mysqli_query($link, "SELECT party FROM People WHERE ID = " . $biggestBurgerArray[0]), MYSQLI_ASSOC);
            if($seats['Seats'] == 1) {
                $sqlInsertRecord = mysqli_query($link, "INSERT INTO ElectionResults(ElectionID, SeatID, Seat1) VALUES (" . $electionID . ", " . $seat['ID'] . ", " . $sqlPerson['party'] . ")");
            } else ($seats['Seats'] == 2) {
                $sqlInsertRecord = mysqli_query($link, "INSERT INTO ElectionResults(ElectionID, SeatID, Seat1, Seat2) VALUES (" . $electionID . ", " . $seat['ID'] . ", " . $sqlPerson['party'] . ", " . $sqlPerson['party'] . ")");
            } else ($seats['Seats'] == 4) {
                $sqlInsertRecord = mysqli_query($link, "INSERT INTO ElectionResults(ElectionID, SeatID, Seat1, Seat2, Seat3, Seat4) VALUES (" . $electionID . ", " . $seat['ID'] . ", " . $sqlPerson['party'] . ", " . $sqlPerson['party'] . ", " . $sqlPerson['party'] . ", " . $sqlPerson['party'] . ")");
            } 
        }
        //Corporation

        //Freemen

        //Freeholder

        //Other elections
    }
    */
}
?> 