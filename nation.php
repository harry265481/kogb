<?php
include_once "header/header.php";
include_once "classes/nation.php";
include_once "classes/house.php";
$nationID = $_GET['id'];
$nation = new Nation($link, $nationID);
?>
<head>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />
</head>
<div class="row mt-3 text-center">
    <div class="col-sm-0 col-md-1 col-lg-2 col-xl-3">
    </div>
    <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">  
        <?php echo "<img class=\"mt-1\" width=\"20%\" src=\"assets/icons/coa/{$nation->abbrev}.svg\" />" ?>
        <h1><?php echo $nation->name ?></h1>
        <?php 
            echo "<img class=\"mt-4\" width=\"30%\" src=\"assets/icons/flags/{$nation->abbrev}.svg\" />";
            echo "<p class=\"mt-3\">Population: " . number_format($nation->getPopulation()) . "</p>";

            echo "<hr>";

            echo "<h4>Head of State</h4>";
            $hos = mysqli_fetch_array(mysqli_query($link, "SELECT monarch FROM empire WHERE ID = {$nation->empire}"))[0];
            echo "<h6>{$hos}</h6>";

            echo "<hr>";

            echo "<h4>Head of Government</h4>";
            $hogPos = Position::getPositionName($link, $nation->headOfGovPos);
            echo "<h5>{$hogPos}</h5>";
            $hog = Position::getPositionHolderName($link, $nation->headOfGovPos);
            echo "<h6>{$hog}</h6>";

            echo "<hr>";
            
            echo "<a class=\"btn btn-primary\" href=\"government.php\">Cabinet</a>";
            echo "<hr>";
            echo "<div class=\"mt-3\">";
            $parliament = json_decode($nation->parliamentIDs);
            if($parliament != false) {
                foreach($parliament as $h) {
                    $name = House::getHouseName($link, $h);
                    echo "<div class=\"mt-2\">";
                    echo "<a class=\"btn btn-primary\" href=\"parliamenthouse?id={$h}\">{$name}</a>";
                    echo "</div>";
                }
            }
            echo "</div>";
        ?>
    </div>
    <div class="col-sm-0 col-md-1 col-lg-2 col-xl-3">
    </div>
</div>
<div class="row mt-3 text-center">
    <div class="col-sm-0 col-xl-1">
    </div>
    <div class="col-sm-12 col-xl-10">
        <div id="map" style='width: 100%; height: 800px;'></div>
        <script>
            mapboxgl.accessToken = 'pk.eyJ1IjoiaGFycnkyNjU0OCIsImEiOiJjbDZqd3VxZXEzbml5M2RwM2swcDE1OXZ1In0.gHBBefRRGqmBg2Izg8sDtA';
            const map = new mapboxgl.Map({
                container: 'map', // container ID
                //style: 'mapbox://styles/mapbox/light-v10', // style URL
                style: 'mapbox://styles/harry26548/cl6n4y4ln005g15np0hgp2mk0', // style URL
                center: [0.0, 51.5], // starting position [lng, lat]
                zoom: 6, // starting zoom
                projection: 'equirectangular', // display the map as a 3D globe
            });
            map.on('load', () => {
            <?php
                foreach($nation->countries as $c) {
                    foreach($c->provinces as $p) {
                        //echo "\"id\": \"{$p->name}layer\", \"source\": \"{$p->name}\", \"type\": \"fill\", \"paint\": { \"fill-color\": \"#00ff00\"},\n";
                        $filename = "NAME_" . $p->name . ".geojson";
                        echo "map.addSource('{$p->name}', {
                            'type': 'geojson',
                            'data': 'assets/geojson/{$filename}' 
                        });
                        map.addLayer({ 'id': '{$p->name}',
                            'type': 'fill',
                            'source': '{$p->name}',
                            'layout': {},
                            'paint': {
                                'fill-color': '#0080ff',
                                'fill-opacity': 0.5
                            }
                        });
                        map.addLayer({ 'id': '{$p->name}outline',
                            'type': 'line',
                            'source': '{$p->name}',
                            'paint': {
                                'line-color': '#000',
                                'line-width': 1
                            }
                        });";
                    }
                }
                ?>
            })
        </script>
    </div>
    <div class="col-sm-0 col-xl-1">
    </div>
</div>
<?php include_once "footer.php" ?>