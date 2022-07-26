<?php
session_start();
include 'config.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

include 'functions.php';
include 'header/header.php';
$groups = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM posgroups"), MYSQLI_ASSOC);
$positions = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM positions"), MYSQLI_ASSOC);
?>
<h1 class="h1 mt-3">Government</h1>
<?php
  foreach($groups as $group) {
    $name = $group['name'];
    echo "<p class=\"h2 mt-3\">{$name}</p>\n";
    echo "<table class=\"table caption-top table-dark\">
            <thead>
              <tr>
                <th>Name</th>
                <th>Incumbent</th>";
              if($group['ID'] > 1) {
              echo "<th>Appointer</th>";
              }
              echo "<th>Annual Income</th>";
            echo "</tr>
            </thead>
            <tbody>";
      foreach($positions as $pos) {
        if(in_array($group['ID'], json_decode($pos['posgroup']))) {
          $pname = $pos['name'];
          echo "<tr>";
          echo  "<td>{$pname}</td>";        
          if($pos['holderID'] != null) {
            echo '<td>' . getName($link, $pos['holderID']) .' </td>';
          } else {
            echo '<td><i>Vacant</i></td>';
          }
          if($pos['ID'] > 0 && $pos['ID'] != 66 && $pos['appointer'] > -1) {
          $appointer = $positions[$pos['appointer']]['name'];
          echo "<td>{$appointer}</td>";
          } else if($pos['appointer'] == -1) {
            echo "<td>The House of Commons</td>";
          }
          $pay = $pos['pay'];
          echo "<td>{$pay}</td>";
          echo "</tr>";
        }
      }  
    echo '</tbody>';
    echo '</table>';
    }
?>
<?php include_once "footer.php"; ?>