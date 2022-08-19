<?php
    include_once "header/header.php";
    include_once "classes/bill.php";
    include_once "classes/person.php";
    include_once "classes/house.php";
    include_once "classes/parliament.php";
    $billID = $_GET['id'];
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        Bill::putBillToFloor($link, $billID);
    }
    $bill = Bill::getBill($link, $_GET['id']);
    $houseID = $bill['House'];
    $house = new House($link, $houseID);
    $member = false;
    if($player->house == $houseID) {
      $member = true;
    }

    $po = false;
    if($player->ID == $house->presidingOfficer) {
      $po = true;
    }
    ?>
<head><style>@font-face{font-family: newspaper; src: url("assets/OldNewspaperTypes.ttf")} .text {font-family: newspaper} .drop-letter {float:left; font-size:250%; line-height:80%;} </style></head>
<div class="row mt-3 text-center">
    <div class="col">
        <h5 class="text">Introduced by <?php echo Person::getDisplayName($link, $bill['author']) ?></h5>
        <hr>
        <h6><?php echo Bill::$stages[$bill['Stage']]; ?></h6>
    </div>
</div>
<div class="row mt-1 text-center">
    <div class="col-sm-0 col-lg-2 col-xl-3 col-xxl-3"></div>
    <div class="col-sm-12 col-lg-8 col-xl-6 col-xxl-6 text-dark">
        <button class="btn btn-primary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#billtext">View Text</button>
        <div class="collapse" id="billtext" style="background-color: #E2CBA3">
            <h2 class="text"><?php echo $bill['shortTitle']?></h2>
            <hr>
            <h6 class="text"><?php echo stripslashes($bill['longTitle']);?></h6>
            <hr>
            <p class="text"><?php echo stripslashes($bill['text']);?></p>
        </div>
    </div>
    <div class="col-sm-0 col-lg-2 col-xl-3 col-xxl-3"></div>
</div>
<div class="row mt-1">
    <div class="col-sm-0 col-lg-1"></div>
    <div class="col-sm-12 col-lg-5 bg-light text-dark">
        <?php
            $house->parliamentID;
            $parliament = json_decode(Parliament::getParliamentHouses($link, $house->parliamentID));
            $color = array($parliament[0] => "danger", $parliament[1] =>  "success", $parliament[1] + 1 => "warning");
            $icon = array($parliament[0] => "lords-small", $parliament[1] => "commons-small", $parliament[1] + 1 => "royal-small");
            $origin = House::getHouseName($link, $bill['Origin']);
            $cHouse = $bill['Origin'];
            $otherid = ($cHouse == 1) ? 2 : 1;
            $other = House::getHouseName($link, $otherid);
            echo "<p class=\"border-bottom border-{$color[$cHouse]}\" style=\"font-weight: 500; font-size: 20px\"><img src=\"assets/icons/house/{$icon[$cHouse]}.svg\" width=\"23px\">Bill started in the {$origin}</p>";
            foreach(Bill::$stages as $k => $s) {
                if($k == 5) {
                    $cHouse = ($cHouse == $parliament[0]) ? $parliament[1] : $parliament[0];
                    echo "</div>";
                    echo "<div class=\"col-sm-12 col-lg-5 bg-light text-dark\">";
                    echo "<p class=\"border-bottom border-{$color[$cHouse]}\" style=\"font-weight: 500; font-size: 20px\"><img src=\"assets/icons/house/{$icon[$cHouse]}.svg\" width=\"23px\">Bill in the {$other}</p>";
                }
                
                if($k == 10) {
                    $cHouse = $parliament[1] + 1;
                    echo "</div>";
                    echo "<div class=\"col-sm-0 col-lg-1\"></div>";
                    echo "<div class=\"col-sm-0 col-lg-1\"></div>";
                    echo "<div class=\"col-sm-12 col-lg-10 bg-light text-dark\">";
                    echo "<p class=\"border-bottom border-{$color[$cHouse]}\" style=\"font-weight: 500; font-size: 20px\"><img src=\"assets/icons/house/{$icon[$cHouse]}.svg\" width=\"23px\">Final Stages</p>";
                }

                echo "<div class=\"text-{$color[$cHouse]} my-1\">";
                if($bill['Stage'] < $k) {
                    echo'
                        <span class="fa-stack">
                            <i class="fa-regular fa-circle fa-stack-2x"></i>
                        </span>';
                } else if($bill['Stage'] == $k) {
                    echo'
                        <span class="fa-stack">
                            <i class="fa-regular fa-circle fa-stack-2x"></i>
                            <i class="fa-regular fa-hourglass fa-stack-1x"></i>
                        </span>';
                }else if($bill['Stage'] > $k) {
                    echo'
                        <span class="fa-stack">
                            <i class="fa-regular fa-circle fa-stack-2x"></i>
                            <i class="fa-regular fa-check fa-stack-1x"></i>
                        </span>';
                }
                echo Bill::$stages[$k];
                echo "</div>";
            }
        ?>
    </div>
    <div class="col-sm-0 col-lg-1"></div>
</div>

<?php
if($po && $bill['Stage'] == 0) {
    echo "<form action=\"bill.php?id={$billID}\" method=\"post\">";
        echo "<div class=\"row text-center mt-1\">
            <div class=\"col-sm-0 col-lg-1\"></div>
            <div class=\"col-sm-12 col-lg-10\">";
            echo "<button class=\"btn btn-primary\" type=\"submit\">Put bill to house floor</button>";
        echo "</div>
            <div class=\"col-sm-0 col-lg-1\"></div>
        </div>";
    echo "</form>";
}
?>
<?php include_once "footer.php" ?>