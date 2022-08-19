<?php
include_once 'header/header.php';
include_once 'classes/position.php';
include_once 'classes/unit.php';
include_once 'classes/discord.php';
include_once 'classes/army.php';
$nationID = $player->nation->ID;
$houseIDs = json_decode(mysqli_fetch_array(mysqli_query($link, "SELECT houseIDs FROM parliament INNER JOIN nations ON parliament.nationID = nations.ID WHERE nations.ID = {$nationID}"))[0]);
$members = array();
foreach($houseIDs as $h) {
    $membersSQL = mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM people WHERE House = {$h}"), MYSQLI_ASSOC);
    foreach($membersSQL as $m) {
        array_push($members, $m['ID']);
    }
}
$memberNames = array();
foreach($members as $m) {
    $memberNames[$m] = Person::getDisplayName($link, $m);
}

$manpower = number_format(mysqli_fetch_array(mysqli_query($link, "SELECT amount FROM manpowerreserves WHERE nation = {$nationID}"))[0]);

$regtypes;
if($player->canCreateUnits()) {
    $regsql = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM regtypes"), MYSQLI_ASSOC);
    foreach($regsql as $r) {
        $regtypes[$r['ID']] = array($r['name'], $r['costperman']);
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    //verify they can actually appoint this incase some sneaky 
    //bugger tries to edit some JS and appoint people he can't
    if(isset($_POST['appoint']) && $_POST['appoint'] == 1) {
        $valid = false;
        foreach($player->positions as $p) {
            $aPositions = Position::getPositionsAppointedBy($link, $p['ID']);
            foreach($aPositions as $ap) {
                if($ap['ID'] == $_POST['positionID']) { $valid = true;}
            }
        }
        if($valid == true) {
            //appoint
            mysqli_query($link, "UPDATE positions SET holderID = {$_POST['person']} WHERE ID = {$_POST['positionID']}");
            echo "<script>window.location = window.location.href;</script>";
        } else {
            echo "<b>You attempted to appoint someone your position can't appoint. If you believe this is in error, contact Earl of Berkeley on Discord</b>";
        }
    } else if(isset($_POST['kingspeech']) && $_POST['kingspeech'] == 1) {
        $text = trim($_POST['speech']);
        $url = "https://cdn.discordapp.com/avatars/{$player->discordID}/{$player->discordAvatar}.png";
        $name = Person::getDisplayName($link, $player->ID);
        $json = "{
            \"embeds\": [{
                \"title\": \"King's Speech\",
                \"description\": \"{$text}\",
                \"color\": \"6238132\",
                \"author\": {
                    \"name\": \"{$name}\"
                }
            }]
        }";
        //$embed = json_encode($array);
        if($player->canMakeKingsSpeech() && $player->nation->parliament->insession == false) {
            $channel = $player->nation->parliament->discordChannelID;
            $newMessage = Discord::MakeRequest("channels/{$channel}/messages", json_decode($json));
            if($player->canOpenParliament()) {
                $player->nation->parliament->openParliament($link);
            }
            echo "<script>window.location = window.location.href;</script>";
        }
    }
}
?>
<form action="office.php" method="post" name="appoint">
    <div class="modal fade text-dark" id="appointModal" tabindex="-1" aria-labelledby="appointModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointModalLabel">Appoint Person</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select class="form-select" name="person">
                    <?php 
                        foreach($memberNames as $id => $m) {
                            echo "<option value=\"{$id}\">{$m}</option>";
                        }
                    ?>
                </select>
            </div>
            <input type="hidden" id="positionID" name="positionID" value="0">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="appoint" value="1" class="btn btn-primary">Save changes</button>
            </div>
            </div>
        </div>
    </div>
</form>
<?php if($player->hasArmyPerms()): ?>
    <?php if($player->canCreateUnits()): ?>
        <form action="office.php" method="post" name="newArmy">
            <div class="modal fade text-dark" id="createUnitModal" tabindex="-1" aria-labelledby="createUnitModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="appointModalLabel">Create New Army</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="input-group mb-3">
                                <p>It is possible to create a unit with zero men. A "Regiment on paper"</p>
                                <select class="form-select" name="person" id="regTypeSelect" onchange="updateNewArmy()">
                                    <?php foreach($regtypes as $t => $r) { echo "<option cost=\"{$r[1]}\" id=\"regType{$t}\" value=\"{$t}\">{$r[0]}</option>"; } ?>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <input id="newRegSize" type="number" value="0" min="0" max="2000" onchange="updateNewArmy()">
                            </div>
                            <p id="pRegText"></p>
                            <script>
                                function updateNewArmy() {
                                    var regType = document.getElementById("regTypeSelect").value;
                                    var cost = document.getElementById("regType" + regType).getAttribute("cost");
                                    var name = document.getElementById("regType" + regType).innerHTML;
                                    var num = document.getElementById("newRegSize").value;
                                    var totalCost = Math.round(cost * num);
                                    document.getElementById("pRegText").innerHTML = "A new unit of " + name + " would cost £" + Intl.NumberFormat().format(totalCost) + " for a year";
                                }
                            </script>
                        </div>
                        <input type="hidden" id="positionID" name="positionID" value="0">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="newArmy" value="1" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif ?>
    <?php if($player->canSpendArmyMoney()): ?>
        <form action="office.php" method="post" name="recruitManpower">
            <div class="modal fade text-dark" id="recruitModal" tabindex="-1" aria-labelledby="recruitModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="appointModalLabel">Push recruiting</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Allocate a budget to push for recruits</p>
                            <div class="input-group mb-3">
                                <input id="budget" type="number" name="budget" value="0">
                            </div>
                        </div>
                        <input type="hidden" id="positionID" name="positionID" value="0">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="recruit" value="1" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif ?>
<?php endif ?>

<div class="page-header pt-3">
    <h2>Your office</h2>
    <h4>Your positions</h4>
    <p>
    <?php
        foreach($player->positions as $p) {
            echo "{$p['name']}<br>";
        }
    ?>
    </p>
</div>
<div class="row mt-3">
    <div class="col-sm-0 col-xxl-2"></div>
    <div class="col-sm-12 col-xxl-8">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link" id="nav-appoint-tab" data-bs-toggle="tab" data-bs-target="#appointTab">Appointments</button>
                <?php if($player->hasArmyPerms()): ?>
                    <button class="nav-link active" id="nav-army-tab" data-bs-toggle="tab" data-bs-target="#armyTab">Army</button>
                <?php endif ?>
                <?php if($player->hasOrdnancePerms()): ?>
                    <button class="nav-link" id="nav-navy-tab" data-bs-toggle="tab" data-bs-target="#ordnanceTab">Ordnance</button>
                <?php endif ?>
                <?php if($player->hasNavyPerms()): ?>
                    <button class="nav-link" id="nav-navy-tab" data-bs-toggle="tab" data-bs-target="#navyTab">Navy</button>
                <?php endif ?>
                <?php if($player->hasPeeragePerms()): ?>
                    <button class="nav-link" id="nav-navy-tab" data-bs-toggle="tab" data-bs-target="#peerageTab">Peerage</button>
                <?php endif ?>
                <?php if($player->hasParliamentPerms()): ?>
                    <button class="nav-link" id="nav-navy-tab" data-bs-toggle="tab" data-bs-target="#parliamentTab">Parliament</button>
                <?php endif ?>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane" id="appointTab" role="tabpanel" tabindex="0">
                <h6>Positions appointed by you</h6>
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Incumbent</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($player->positions as $p) {
                            $aPositions = Position::getPositionsAppointedBy($link, $p['ID']);
                            if($aPositions) {
                                echo "<tr class=\"table-secondary\"><td class=\"text-center\" colspan=\"3\"><b>Positions assigned as {$p['name']}</b><td class=\"text-center\"><button class=\"btn btn-primary\" onclick=\"hidePositions(this)\">Show/Hide</button></tr>";
                                foreach($aPositions as $ap) {
                                    echo "<tr style=\"display:none;\" class=\"pos{$p['ID']}\" id=\"{$ap['ID']}\">";
                                    echo    "<td>{$ap['name']}</td>";
                                    echo    "<td>{$ap['description']}</td>";
                                    $name = Position::getPositionHolderName($link, $ap['ID']);
                                    echo    "<td>{$name}</td>";
                                    echo    "<td><button type=\"button\" class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#appointModal\" onclick=\"getPosition(this)\">Appoint</button></td>";
                                    echo "</tr>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if($player->hasArmyPerms()): ?>
            <div class="tab-pane fade show active" id="armyTab" role="tabpanel" tabindex="0">
                <?php if($player->canCreateUnits()): ?>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createUnitModal">Create New Unit</button>
                <?php endif ?>
                <?php if($player->canSpendArmyMoney()): ?>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#recruitModal">Create New Unit</button>
                <?php endif ?>
                <?php echo "<p class=\"mb-\">Manpower: {$manpower}</p>"; ?>
                <form action="office.php" method="post">
                    <a href="#" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#armyList">Armies</a>
                    <a href="#" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#unitList">Units</a>
                    <hr>
                    <div class="collapse" id="armyList">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Leader</th>
                                    <th width="15%"></th>
                                </tr>
                            <tbody>
                                <?php
                                foreach(Army::getAllArmies($link) as $army) {
                                    echo "<tr>";
                                    echo    "<td>{$army[2]}</td>";
                                    echo    "<td>{$army[4]}</td>";
                                    echo    "<td><a class=\"btn btn-primary\" href=\"viewarmy.php?id={$army[0]}\">View</a></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="collapse" id="unitList">
                        <table class="table table-dark">
                            <thead>
                                <th>Type</th>
                                <th>Name</th>
                                <th width="90px">Size</th>
                                <th width="90px">Cost for 365 Days</th>
                                <th width="90px"></th>
                            </thead>
                            <tbody>
                                <?php
                                    $totalArmyCost = 0;
                                    $totalArmySize = 0;
                                    foreach($regtypes as $k => $r) {
                                        $totalTypeCost = 0;
                                        $totalTypeSize = 0;
                                        $units = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM units WHERE `type` = {$k}"), MYSQLI_ASSOC);
                                        $firstrow = true;
                                        foreach($units as $u) {
                                            echo "<tr>";
                                            if($firstrow == true) {
                                                $rows = count($units);
                                                $typeName = Unit::$types[$k];
                                                echo "<td class=\"align-middle\" rowspan=\"{$rows}\">{$typeName}</td>";
                                                $firstrow = false;
                                            }
                                            echo "<td>{$u['name']}</td>";
                                            echo "<td>{$u['size']}</td>";
                                            $uCost = $u['size'] * $r[1];
                                            $totalTypeSize += $u['size'];
                                            $totalTypeCost += $uCost;
                                            $totalArmySize += $u['size'];
                                            $totalArmyCost += $uCost;
                                            $uCost = number_format($uCost);
                                            echo "<td>{$uCost}</td>";
                                            if($player->canDisbandUnits()) {
                                                echo "<td><button type=\"submit\" name=\"disband\" class=\"btn btn-primary\" value=\"{$u['ID']}\">Disband</button></td>";
                                            } else {
                                                echo "<td></td>";
                                            }
                                            echo "</tr>";
                                        }
                                        $totalTypeCost = number_format($totalTypeCost);
                                        $totalTypeSize = number_format($totalTypeSize);
                                        echo "<tr class=\"table-light\"><td colspan=\"2\"><b>Total {$typeName}</b></td><td>{$totalTypeSize}</td><td>£{$totalTypeCost}</td><td></td></tr>";
                                    }
                                    $totalArmyCost = number_format($totalArmyCost);
                                    $totalArmySize = number_format($totalArmySize);
                                    echo "<tr class=\"table-light\"><td colspan=\"2\"><b>Total</b></td><td>{$totalArmySize}</td><td>£{$totalArmyCost}</td><td></td></tr>";
                                ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <?php endif ?>
            <?php if($player->hasOrdnancePerms()): ?>
            <div class="tab-pane" id="ordnanceTab" role="tabpanel" tabindex="0"></div>
            <?php endif ?>
            <?php if($player->hasNavyPerms()): ?>
            <div class="tab-pane" id="navyTab" role="tabpanel" tabindex="0"></div>
            <?php endif ?>
            <?php if($player->hasPeeragePerms()): ?>
            <div class="tab-pane" id="peerageTab" role="tabpanel" tabindex="0"></div>
            <?php endif ?>
            <?php if($player->hasParliamentPerms()): ?>
            <div class="tab-pane" id="parliamentTab" role="tabpanel" tabindex="0">
                <?php 
                    $nations = mysqli_fetch_all(mysqli_query($link, "SELECT ID, name FROM nations WHERE empire = {$player->nation->empire}"));
                    foreach($nations as $n) {
                        echo "<h3 class=\"border-bottom mt-3\">{$n[1]}</h3>";
                        $sql = mysqli_query($link, "SELECT `ID` FROM `parliament` WHERE `nationID` = {$n[0]} ORDER BY ID desc LIMIT 1");
                        if(mysqli_num_rows($sql) > 0) {
                            $parlid = mysqli_fetch_array($sql)[0];
                            $year = mysqli_fetch_array(mysqli_query($link, "SELECT `Year` FROM `elections` WHERE `parliamentID` = {$parlid} ORDER BY ID desc LIMIT 1"))[0];
                            echo "<p class=\"mb-1\">The last election for {$n[1]} was in: {$year}</p>";
                            if($player->nation->parliament->insession == false) {
                                echo "<p>Currently Prorogued</p>";
                            }
                            if($player->canProrogueParliament() && $player->nation->parliament->insession == true) {
                                echo "<form action=\"office.php\" method=\"post\">";
                                echo "<button name=\"open\" value=\"1\" class=\"btn btn-danger mt-1\">Prorogue Parliament</button>";
                                echo "</form>";
                            }
                            if($player->canMakeKingsSpeech() && $player->nation->parliament->insession == false) {
                                echo "<hr>";
                                echo "<p class=\"mb-0\">If you believe it will take you some time to write this speech, it is recommended to write it down somewhere else and then paste it here</p>";
                                echo "<button class=\"btn btn-success mt-1\" data-bs-toggle=\"collapse\" data-bs-target=\"#kingspeech\">Write King's Speech</button>";
                                echo "<div class=\"collapse\" id=\"kingspeech\">";
                                echo    "<form class=\"mt-3\" action=\"office.php\" method=\"post\">";
                                echo        "<textarea name=\"speech\" class=\"form-control\"></textarea>";
                                echo        "<button type=\"submit\" name=\"kingspeech\" value=\"1\" class=\"btn btn-primary mt-2\">Make speech to parliament</button>";
                                echo    "</form>";
                                echo "</div>";
                            }
                            echo "<hr>";
                        } else {
                            echo "<p class=\"mt-2\">Either {$n[1]} has not had an election, does not have a parliament or it has not been set up<br>Wait for it to be setup or contact Earl of Berkeley to see if it can set up</p>";
                        }
                    }
                ?>
            </div>
            <?php endif ?>
        </div>
    <div class="col-sm-0 col-xxl-2"></div>
</div>
<script>
function getPosition(element) {
    var posID = element.parentElement.parentElement.id;
    document.getElementById("positionID").value = posID;
}

function hidePositions(element) {
    var l = element.parentElement.parentElement.nextElementSibling.className;
    var x = document.getElementsByClassName(l);
    for(let i = 0; i < x.length; i++) {
        if(x[i].style.display === "none") {
            x[i].style.display = "table-row";
        } else {
            x[i].style.display = "none";
        }
    }
}
</script>
<?php include_once "footer.php"; ?>