<?php
include 'header/header.php';
include 'classes/unit.php';
include 'classes/army.php';
include 'classes/armyrank.php';
include 'classes/garrisons.php';
?>
<head>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />
</head>
<div class="page-header pt-3">
  <h2>Army</h2>
</div>
<div class="row mt-3">
    <div class="col-sm-0 col-xl-1 col-xxl-2">
    </div>
    <div class="col-sm-12 col-xl-10 col-xxl-8">
        <nav>
            <div class="nav nav-tabs" id="nav-tabs" role="tablist">
                <button class="nav-link active" id="nav-units-tab" data-bs-toggle="tab" data-bs-target="#nav-units" type="button" role="tab">Units</button>
                <button class="nav-link" id="nav-armies-tab" data-bs-toggle="tab" data-bs-target="#nav-armies" type="button" role="tab">Armies</button>
                <button class="nav-link" id="nav-installations-tab" data-bs-toggle="tab" data-bs-target="#nav-installations" type="button" role="tab">Installations</button>
                <button class="nav-link" id="nav-armyranks-tab" data-bs-toggle="tab" data-bs-target="#nav-armyranks" type="button" role="tab">Ranks</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-units" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="d-none d-md-table-cell">Type</th>
                            <th>Size</th>
                            <th class="d-none d-md-table-cell">Army</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(Unit::getAllUnits($link) as $unit) {
                            echo "<tr>";
                            echo "<td>{$unit[1]}</td>";
                            $type = Unit::$types[$unit[2]];
                            echo "<td class=\"d-none d-md-table-cell\">{$type}</td>";
                            echo "<td>{$unit[3]}</td>";
                            $armyName = Army::getArmyName($link, $unit[4]);
                            echo "<td class=\"d-none d-md-table-cell\">{$armyName}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-armies" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Leader</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(Army::getAllArmies($link) as $army) {
                            echo "<tr>";
                            echo "<td>{$army[2]}</td>";
                            echo "<td>{$army[3]}</td>";
                            echo "<td>{$army[4]}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div id="army-map" style='width: 100%; height: 800px;'></div>
                <script>
                    mapboxgl.accessToken = 'pk.eyJ1IjoiaGFycnkyNjU0OCIsImEiOiJjbDZqd3VxZXEzbml5M2RwM2swcDE1OXZ1In0.gHBBefRRGqmBg2Izg8sDtA';
                    const armymap = new mapboxgl.Map({
                        container: 'army-map', // container ID
                        style: 'mapbox://styles/harry26548/cl6ixru2f000014m5x2no85sb', // style URL
                        center: [0.0, 51.5], // starting position [lng, lat]
                        zoom: 6, // starting zoom
                        projection: 'equirectangular' // display the map as a 3D globe
                    });
                    <?php 
                        foreach(Army::getAllArmies($link) as $a) {
                            if($a[6] == 0) {
                                echo "const armymarker{$a[0]} = new mapboxgl.Marker().setLngLat({$a[3]}).setPopup(new mapboxgl.Popup().setHTML(\"<h4 style='color: black'>{$a[2]}</h4>\")).addTo(armymap);\n";
                            } else {
                                $i = Garrison::getGarrison($link, $a[6]);
                                echo "const armymarker{$a[0]} = new mapboxgl.Marker().setLngLat({$i[2]}).setPopup(new mapboxgl.Popup().setHTML(\"<h4 style='color: black'>{$a[2]}</h4><br><h4 style='color: black'>at {$i[1]}</h4>\")).addTo(armymap);\n";
                            }
                        }
                    ?>
                </script>
            </div>
            <div class="tab-pane fade" id="nav-installations" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(Garrison::getAllGarrisons($link) as $g) {
                            echo "<tr>";
                            echo "<td>{$g[1]}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-armyranks" role="tabpanel" tabindex="0">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center align-middle">Name</th>
                            <th colspan="3" class="text-center">Annual Pay</th>
                        </tr>
                        <tr>
                            <th class="text-center">Horse</th>
                            <th class="text-center">Dragoons</th>
                            <th class="text-center">Foot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(ArmyRank::getAllRanks($link) as $r) {
                            echo "<tr>";
                            echo "<td>{$r[1]}</td>";
                            if($r[2] == 0) {
                                echo "<td></td>";
                            } else {
                                echo "<td>£{$r[2]}</td>";
                            }
                            if($r[3] == 0) {
                                echo "<td></td>";
                            } else {
                                echo "<td>£{$r[3]}</td>";
                            }
                            if($r[4] == 0) {
                                echo "<td></td>";
                            } else {
                                echo "<td>£{$r[4]}</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-0 col-xl-1 col-xxl-2">
    </div>
</div>
<?php include_once "footer.php"; ?>