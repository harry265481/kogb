<?php
include_once 'header/header.php';
include_once 'classes/army.php';
$armyID = $_GET['id'];
$army = new Army($link, $armyID);

?>
<head>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />
</head>
<h3 class="mt-3"><?php echo $army->name ?></h3>
<button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#mapcontainer">Open Map</button>
<hr>
<div class="collapse" id="mapcontainer">
    <div id="army-map" style='width: 100%; height: 800px;'></div>
</div>
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
        if($army->inGarrison == true) {
            echo "const armymarker{$army->ID} = new mapboxgl.Marker().setLngLat({$army->location}).setPopup(new mapboxgl.Popup().setHTML(\"<h4 style='color: black'>{$army->name}</h4>\")).addTo(armymap);\n";
        } else {
            $i = Garrison::getGarrison($link, $army->garrison);
            echo "const armymarker{$army->ID} = new mapboxgl.Marker().setLngLat({$i[2]}).setPopup(new mapboxgl.Popup().setHTML(\"<h4 style='color: black'>{$army->name}</h4><br><h4 style='color: black'>at {$i[1]}</h4>\")).addTo(armymap);\n";
        }
    ?>
</script>
<?php include_once "footer.php"; ?>