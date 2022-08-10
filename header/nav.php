<?php 
include_once __DIR__ . "/../classes/nav.php";

$isadmin = false;
if($_SESSION["adminlevel"] > 0){
    $isadmin = true;
}
$nav = new NavBar();
$nav->addChild(new NavLink("home.php", "<i class=\"fa-solid fa-house\"></i>", "Home", "#sidebar"));
$nav->addChild(new NavLink("pol-office.php", "<i class=\"fa-solid fa-building-flag\"></i>", "Political Office", "#sidebar"));
$countryDropdown = new NavDropdown("country", "<img height=\"16px\" src=\"assets/icons/flags/{$player->nation->abbrev}.svg\"/>", $player->nation->name, "#sidebar");
$countryDropdown->addChild(new NavLink("nation.php?id=1", "<i class=\"fa-solid fa-flag\"></i>", "Details", "#government-dropdown"));

$governmentDropdown = new NavDropdown("government", "<i class=\"fa-solid fa-landmark-dome\"></i>", "Government", "#country-dropdown");
$governmentDropdown->addChild(new NavLink("government.php", "<i class=\"fa-solid fa-users\"></i>", "Cabinet", "#government-dropdown"));
$governmentDropdown->addChild(new NavLink("parliamenthouse.php?id=1", "<i class=\"fa-solid fa-landmark\"></i>", "House of Lords", "#government-dropdown"));
$governmentDropdown->addChild(new NavLink("parliamenthouse.php?id=2", "<i class=\"fa-solid fa-archway\"></i>", "House of Commons", "#government-dropdown"));
$countryDropdown->addChild($governmentDropdown);

$militaryDropdown = new NavDropdown("military", "<i class=\"fa-solid fa-user-shield\"></i>", "Military", "#country-dropdown");
$militaryDropdown->addChild(new NavLink("army.php", "<i class=\"fa-solid fa-shield\"></i>", "Army", "#military-dropdown"));
$militaryDropdown->addChild(new NavLink("navy.php", "<i class=\"fa-solid fa-anchor\"></i>", "Navy", "#military-dropdown"));
$countryDropdown->addChild($militaryDropdown);

$countryDropdown->addChild(new NavLink("factions.php?id=1", "<i class=\"fa-solid fa-user-group\"></i>", "Factions", "#country-dropdown"));
$nav->addChild($countryDropdown);

if($isadmin) {
    $nav->addChild(new NavLink("pending.php", "<i class=\"fa-solid fa-clipboard\"></i>", "Pending Characters", "#sidebar"));
}

$nav->print();
/*
<a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar" data-bs-target="#econ-dropdown" data-bs-toggle="collapse"><i class="fa-solid fa-sterling-sign"></i> <span>Economy</span></a>
<div id="econ-dropdown" class="collapse">
    <div id="econ-nav" class="list-group border-0 rounded-0 text-sm-smart">
        <a href="econmap.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#econ-dropdown"><i class="fa-solid fa-map"></i> <span>Map</span></a>
        <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#econ-dropdown" data-bs-target="#prod-dropdown" data-bs-toggle="collapse"><i class="fa-solid fa-industry"></i> <span>Production</span></a>
        <div id="prod-dropdown" class="collapse">
            <div id="prod-nav" class="list-group border-0 rounded-0 text-sm-smart">
                <a href="econcountry.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#prod-dropdown"><i class="fa-solid fa-chart-pie"></i> <span>Stats</span></a>
                <a href="recipes.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#prod-dropdown"> <span>Recipes</span></a>
            </div>
        </div>
    </div>
</div>
*/
?>