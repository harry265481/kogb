<?php
include_once 'header/header.php';
include_once 'classes/fleet.php';
include_once 'classes/ship.php';
$fleetID = $_GET['id'];
$fleet = Fleet::getFleet($link, $fleetID);
?>
<div class="page-header pt-3">
  <h2><?php echo $fleet['name'] ?></h2>
</div>
<div class="row mt-3">
    <div class="col-sm-0 col-xl-3">
    </div>
    <div class="col-sm-12 col-xl-6">  
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Rate</th>
                    <th>Guns</th>
                    <th>Type</th>
                    <th>Approx. Max Crew</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $ships = Fleet::getShipsInFleet($link, $fleetID);
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
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <div id="fleet-map" style='width: 100%; height: 800px;'></div>
        <script>
            mapboxgl.accessToken = 'pk.eyJ1IjoiaGFycnkyNjU0OCIsImEiOiJjbDZqd3VxZXEzbml5M2RwM2swcDE1OXZ1In0.gHBBefRRGqmBg2Izg8sDtA';
            const fleetmap = new mapboxgl.Map({
                container: 'fleet-map', // container ID
                style: 'mapbox://styles/harry26548/cl6ixru2f000014m5x2no85sb', // style URL
                center: <?php echo $fleet['location'] ?>, // starting position [lng, lat]
                zoom: 6, // starting zoom
                projection: 'equirectangular' // display the map as a 3D globe
            });
            <?php echo "const fleetmarker = new mapboxgl.Marker().setLngLat({$fleet['location']}).setPopup(new mapboxgl.Popup().setHTML(\"<h4 style='color: black'>{$fleet['location']}</h4>\")).addTo(fleetmap);\n";?>
        </script>
    </div>
    <div class="col-sm-0 col-xl-3">
    </div>
</div>
<?php include_once "footer.php"; ?>