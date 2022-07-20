<?php
include 'config.php';
include 'functions.php';
include 'header/header.php';
?>
<head>
    <style>
        svg {
            border:solid 1px black;
            background-color:#165ec9;
        }
        path:hover {
            fill:#ccc !important;
        }
    </style>
</head>
<h1 style = "margin-top: 70px">Map</h1>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '225 0 605 1208.8');
    getElementById('ttrect').setAttribute('height', '30');
    getElementById('tttext').setAttribute('font-size', '25');
    getElementById('tttext').setAttribute('y', '26');
    setStrokeWidth(0.5);
    ">Reset View</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '300 1050 150 150');
    getElementById('ttrect').setAttribute('height', '10');
    getElementById('tttext').setAttribute('font-size', '8');
    getElementById('tttext').setAttribute('y', '8');
    setStrokeWidth(0.2);
    ">Cornwall</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '660 1000 50 50');
    getElementById('ttrect').setAttribute('height', '6');
    getElementById('tttext').setAttribute('font-size', '5');
    getElementById('tttext').setAttribute('y', '5');
    setStrokeWidth(0.1);
    ">Middlesex</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '230 240 380 440');
    getElementById('ttrect').setAttribute('height', '12');
    getElementById('tttext').setAttribute('font-size', '10');
    getElementById('tttext').setAttribute('y', '8');
    setStrokeWidth(0.3);
    ">Scotland</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '320 810 220 240');
    getElementById('ttrect').setAttribute('height', '12');
    getElementById('tttext').setAttribute('font-size', '10');
    getElementById('tttext').setAttribute('y', '8');
    setStrokeWidth(0.3);
    ">Wales</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '480 950 200 200');
    getElementById('ttrect').setAttribute('height', '12');
    getElementById('tttext').setAttribute('font-size', '10');
    getElementById('tttext').setAttribute('y', '8');
    setStrokeWidth(0.3);
    ">South West</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '620 920 200 200');
    getElementById('ttrect').setAttribute('height', '12');
    getElementById('tttext').setAttribute('font-size', '10');
    getElementById('tttext').setAttribute('y', '8');
    setStrokeWidth(0.3);
    ">South East</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '646 836 200 200');
    getElementById('ttrect').setAttribute('height', '12');
    getElementById('tttext').setAttribute('font-size', '10');
    getElementById('tttext').setAttribute('y', '8');
    setStrokeWidth(0.3);
    ">East</button>
<button type="button" class="btn btn-primary" onclick="
    getElementById('map').setAttribute('viewBox', '500 752 200 200');
    getElementById('ttrect').setAttribute('height', '12');
    getElementById('tttext').setAttribute('font-size', '10');
    getElementById('tttext').setAttribute('y', '8');
    setStrokeWidth(0.3);
    ">Midlands</button><br>
<?php
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); generateMap($link); 
?>