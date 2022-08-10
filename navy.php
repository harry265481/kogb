<?php
include 'header/header.php';
include 'classes/ship.php';
include 'classes/fleet.php';
include 'classes/navyrank.php';
include 'classes/shipyards.php';
?>
<div class="page-header pt-3">
  <h2>Navy</h2>
</div>
<div class="row mt-3">
    <div class="col-lg-0 col-xl-2">
    </div>
    <div class="col-lg-12 col-xl-8">
        <nav>
            <div class="nav nav-tabs" id="nav-tabs" role="tablist">
                <button class="nav-link active" id="nav-ship-tab" data-bs-toggle="tab" data-bs-target="#nav-ship" type="button" role="tab">Ships</button>
                <button class="nav-link" id="nav-fleet-tab" data-bs-toggle="tab" data-bs-target="#nav-fleet" type="button" role="tab">Fleets</button>
                <button class="nav-link" id="nav-shipyard-tab" data-bs-toggle="tab" data-bs-target="#nav-shipyard" type="button" role="tab">Shipyards</button>
                <button class="nav-link" id="nav-ranks-tab" data-bs-toggle="tab" data-bs-target="#nav-ranks" type="button" role="tab">Ranks</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-ship" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Rate</th>
                            <th>Guns</th>
                            <th>Type</th>
                            <th>Approx. Max Crew</th>
                            <th>Fleet</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $ships = Ship::getAllShips($link);
                            $fleetNames = Fleet::getFleetNames($link);
                            $rate = 0;
                            $rateName = "";
                            $typeName = "";
                            foreach($ships as $ship) {
                                echo "<tr>";
                                echo "<td><i>{$ship['name']}</i>    </td>";
                                $guns = $ship['guns'];
                                foreach(Ship::$rates as $r => $n) {
                                    if($guns >= $n) {
                                        $rate = $r;
                                        $rateName = Ship::$rateWords[$r];
                                        break;
                                    }
                                }
                                echo "<td>{$rateName}</td>";
                                echo "<td>{$guns}</td>";
                                foreach(Ship::$type as $t => $n) {
                                    if($guns >= $n) {
                                        $typeName = Ship::$typeWords[$t];
                                        break;
                                    }
                                }
                                echo "<td>{$typeName}</td>";
                                $crew = round(Ship::$totalCrewPerGuns[$rate] * $guns);
                                echo "<td>{$crew}</td>";
                                $status = Ship::$statusWords[$ship['status']];
                                if($ship['fleet'] == "" || $ship['fleet'] == null || $ship['fleet'] == 0) {
                                    echo "<td></td>";
                                } else {
                                    $fleetName = Fleet::getFleetName($link, $ship['fleet']);
                                    echo "<td>{$fleetName}</td>";
                                }
                                echo "<td>{$status}</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-fleet" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Size</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(Fleet::getFleets($link) as $fleet) {
                            echo "<tr>";
                            echo "<td>{$fleet[0]}</td>";
                            echo "<td>{$fleet[1]}</td>";
                            $size = Fleet::getFleetSize($link, $fleet[0]);
                            echo "<td>{$size}</td>";
                            echo "<td><a class=\"btn btn-primary\" href=\"fleet.php?id={$fleet[0]}\">View</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-shipyard" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(Shipyard::getAllShipyards($link) as $s) {
                            echo "<tr>";
                            echo "<td>{$s[1]}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-ranks" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Appointer</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(NavyRank::getAllRanks($link) as $r) {
                            echo "<tr>";
                            echo "<td>{$r[1]}</td>";
                            echo "<td>{$r[2]}</td>";
                            echo "<td>{$r[3]}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-0 col-xl-2">
    </div>
</div>
<?php include_once "footer.php"; ?>