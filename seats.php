<?php
session_start();
include 'config.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$sqlget = 'SELECT ID, Name, County, Country, Franchise, voters, seats, seat1, seat2, seat3, seat4 FROM seats ORDER BY Name asc WHERE Parliament = 0';
$sqldata = mysqli_query($link, $sqlget) or die('Connection could not be established');

include 'functions.php';
include 'header/header.php';
?>
  <script>
    function hideColumn(column) {
      var x = document.getElementsByClassName(column);
      for(let i = 0; i < x.length; i++) {
        if(x[i].style.display === "none") {
          x[i].style.display = "table-cell";
        } else {
          x[i].style.display = "none";
        }
      }
    }

    function searchRows(input){ 
      var x = document.querySelectorAll('table tbody tr td:first-of-type');
      for(let i = 0; i < x.length; i++) {
        if(x[i].innerText.toLowerCase().startsWith(input.toLowerCase())) {
          x[i].parentElement.style.display = "table-row";
        } else {
          x[i].parentElement.style.display = "none";
        }
      }
    }
    function showCounty(input){ 
      var x = document.querySelectorAll('table tbody tr td:nth-of-type(2)');
      for(let i = 0; i < x.length; i++) {
        if(input != "All") {
          if(x[i].innerText.toLowerCase().startsWith(input.toLowerCase())) {
            x[i].parentElement.style.display = "table-row";
          } else {
            x[i].parentElement.style.display = "none";
          }
        } else {
          x[i].parentElement.style.display = "table-row";
        }
      }
    }
  </script>
  <div class="page-header pt-3">
    <h2>Seats</h2>
  </div>
  <p class="page-header"></p>
  <button type="button" class="btn btn-primary" onclick="hideColumn('county')">Show/Hide Counties</button>
  <button type="button" class="btn btn-primary" onclick="hideColumn('country')">Show/Hide Countries</button>
  <button type="button" class="btn btn-primary" onclick="hideColumn('franchise')">Show/Hide Franchise</button>
  <div style="max-width:30%" class="col-md-4 col-sm-3 input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">Search</span>
    </div>
      <input type="text" oninput="searchRows(this.value)" style="width: 30% !important" class="form-control" placeholder="Seat Name...">
  </div>
  <div class="input-group mb-3">
    <select oninput="showCounty(this.value)" class="custom-select">
      <option selected>All</option>
      <?php $sqlcounties = mysqli_query($link, "SELECT Name FROM Counties");
      while($county = mysqli_fetch_array($sqlcounties, MYSQLI_ASSOC)) {
        echo '<option value="' . $county['Name'] . '">' . $county['Name'] . '</option>';
      }
      ?>
    </select>
    <div class="input-group-append">
      <label class="input-group-text" for="inputGroupSelect02">County(s)</label>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-dark">
      <thead>
        <tr>
          <th>Name</th>
          <th class="county">County</th>
          <th class="country">Country</th>
          <th>Voters</th>
          <th style="white-space: nowrap;" class="franchise">Franchise</th>
          <th style="white-space: nowrap;" colspan="2">Seat 1</th>
          <th style="white-space: nowrap;" colspan="2">Seat 2</th>
          <th style="white-space: nowrap;" colspan="2">Seat 3</th>
          <th style="white-space: nowrap;" colspan="2">Seat 4</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
<?php
$sqlgetPartyNames = mysqli_query($link, 'SELECT Name, Color FROM parties');
while ($row = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {
  $seat1 = "N/A";
  $seat1c = "#ccc";
  $seat2 = "N/A";
  $seat2c = "#ccc";
  $seat3 = "N/A";
  $seat3c = "#ccc";
  $seat4 = "N/A";
  $seat4c = "#ccc";
  if($row['seats'] == 1) {
    $seat1c = getMPColor($link, $row['seat1']);
    $seat1 = getMPPartyName($link, $row['seat1']);
  } else if($row['seats'] == 2) {
    $seat1c = getMPColor($link, $row['seat1']);
    $seat1 = getMPPartyName($link, $row['seat1']);
    $seat2c = getMPColor($link, $row['seat2']);
    $seat2 = getMPPartyName($link, $row['seat2']);
  } else if($row['seats'] == 4) {
    $seat1c = getMPColor($link, $row['seat1']);
    $seat1 = getMPPartyName($link, $row['seat1']);
    $seat2c = getMPColor($link, $row['seat2']);
    $seat2 = getMPPartyName($link, $row['seat2']);
    $seat3c = getMPColor($link, $row['seat3']);
    $seat3 = getMPPartyName($link, $row['seat3']);
    $seat4c = getMPColor($link, $row['seat4']);
    $seat4 = getMPPartyName($link, $row['seat4']);
  }

    //Print it all as a row
    echo '<tr>';
    echo '<td>' . $row['Name'] .' </td>';
    echo '<td class="county">' . $row['County'] .' </td>';
    echo '<td class="country">' . $row['Country'] .' </td>';
    echo '<td>' . $row['voters'] .' </td>';
    echo '<td class="franchise">' . $row['Franchise'] .' </td>';
    echo '<td width="15px" style="background-color:' . $seat1c . ' !important"></td>';
    echo '<td>' . $seat1 . ' </td>';
    echo '<td width="15px" style="background-color:' . $seat2c . ' !important"></td>';
    echo '<td>' . $seat2 . ' </td>';
    echo '<td width="15px" style="background-color:' . $seat3c . ' !important"></td>';
    echo '<td>' . $seat3 . ' </td>';
    echo '<td width="15px" style="background-color:' . $seat4c . ' !important"></td>';
    echo '<td>' . $seat4 . ' </td>';
    echo '<form action=seat.php method=get>';
    echo '<td>'."<input class='btn btn-primary btn-outline' type=submit name=view value=View".' </td>';
    echo "<td style='display:none;'>".'<input type=hidden name=id value='.$row['ID'].' </td>';
    echo '</form>';
    echo '</tr>';
}

echo '</table></div>';
?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
<?php include_once "footer.php"; ?>