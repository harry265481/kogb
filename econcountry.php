<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
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
<h1 class="mt-3" >Production</h1>
<h3 class="mt-3">Britain</h3>
<div class="row mb-3">
    <div class="col-lg-3 col-md-6 border border-white">
        <?php 
            $output = getNationResourceOutput($link, 1);
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
                    echo '<div class="col-lg-3 col-md-6 border border-white">';
                    $broken = true;
                }
            }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12 border border-white">
        <ul class="nav nav-tabs" id="countryTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="england-tab" data-bs-toggle="tab" data-bs-target="#england-tab-pane" type="button" role="tab">England</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="wales-tab" data-bs-toggle="tab" data-bs-target="#wales-tab-pane" type="button" role="tab">Wales</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="scotland-tab" data-bs-toggle="tab" data-bs-target="#scotland-tab-pane" type="button" role="tab">Scotland</button></li>
        </ul>
        <div class="tab-content" id="countryTabContent">
            <div class="tab-pane show active" id="england-tab-pane" role="tabpanel" aria-labelledby="england-tab" tabindex="0">
                <?php 
                    displayCountryProduction($link, 1)
                ?>
            </div>
            <div class="tab-pane" id="wales-tab-pane" role="tabpanel" aria-labelledby="wales-tab" tabindex="0">
                <?php 
                    displayCountryProduction($link, 2)
                ?>
            </div>
            <div class="tab-pane" id="scotland-tab-pane" role="tabpanel" aria-labelledby="scotland-tab" tabindex="0">
                <?php 
                    displayCountryProduction($link, 3)
                ?>
            </div>
        </div>
    </div>
</div>
<h1 class="mt-3" >Demand</h1>
<div class="row">
    <div class="col-lg-6 col-md-12 border border-white">
        <ul class="nav nav-tabs" id="countryNeedsTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="englandneeds-tab" data-bs-toggle="tab" data-bs-target="#englandneeds-tab-pane" type="button" role="tab">England</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="walesneeds-tab" data-bs-toggle="tab" data-bs-target="#walesneeds-tab-pane" type="button" role="tab">Wales</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="scotlandneeds-tab" data-bs-toggle="tab" data-bs-target="#scotlandneeds-tab-pane" type="button" role="tab">Scotland</button></li>
        </ul>
        <div class="tab-content" id="countryNeedsTabContent">
            <div class="tab-pane show active" id="englandneeds-tab-pane" role="tabpanel" aria-labelledby="englandneeds-tab" tabindex="0">
                <?php 
                    displayCountryNeeds($link, 1)
                ?>
            </div>
            <div class="tab-pane" id="walesneeds-tab-pane" role="tabpanel" aria-labelledby="walesneeds-tab" tabindex="0">
                <?php 
                    displayCountryNeeds($link, 2)
                ?>
            </div>
            <div class="tab-pane" id="scotlandneeds-tab-pane" role="tabpanel" aria-labelledby="scotlandneeds-tab" tabindex="0">
                <?php 
                    displayCountryNeeds($link, 3)
                ?>
            </div>
        </div>
    </div>
</div>
<?php include_once "footer.php"; ?>