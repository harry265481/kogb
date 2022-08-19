<?php 
include_once __DIR__ . "/../classes/nav.php";

$isadmin = false;
if($_SESSION["adminlevel"] > 0){
    $isadmin = true;
}
$nav = new NavBar();
$nav->addChild(new NavLink("home.php", "<i class=\"fa-solid fa-house\"></i>", "Home", "#sidebar"));
$nav->addChild(new NavLink("office.php", "<i class=\"fa-solid fa-inbox\"></i>", "Office", "#sidebar"));
$nav->addChild(new NavLink("pol-office.php", "<i class=\"fa-solid fa-building-flag\"></i>", "Election Office", "#sidebar"));
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
?>