<?php
include_once 'header/header.php';
include_once 'classes/house.php';
include_once 'classes/party.php';
include_once 'classes/bill.php';
$houseID = $_GET['id'];
$house = new House($link, $houseID);

$member = false;
if($player->house == $house->ID) {
  $member = true;
}

$bills = Bill::getHouseBills($link, $house->ID);

if($house->type == 1) {
    $sqlget = 'SELECT ID, Name, County, Country, Franchise, voters, seats, seat1, seat2, seat3, seat4 FROM seats WHERE Parliament = 0';
    $sqldata = mysqli_query($link, $sqlget);
    echo '
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
        var x = document.querySelectorAll(\'table tbody tr td:first-of-type\');
        for(let i = 0; i < x.length; i++) {
          if(x[i].innerText.toLowerCase().startsWith(input.toLowerCase())) {
            x[i].parentElement.style.display = "table-row";
          } else {
            x[i].parentElement.style.display = "none";
          }
        }
      }
      function showCounty(input){ 
        var x = document.querySelectorAll(\'table tbody tr td:nth-of-type(2)\');
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
    </script>';
}
?>
<div class="row mt-3">
  <h2 class="text-center"><?php echo $house->getParliamentName($link); ?></h2>
  <div class="text-center">
    <img style="width: 50%" src="<?php echo "assets/img/" . $house->name . ".svg"; ?>" >
  </div>
</div>
<div class="row mt-3">
  <div class="text-center">
    <h6><?php echo $house->presidingPositionName; ?></h6>
    <p><?php echo $house->presidingOfficerName; ?></p>
  </div>
</div>
<div class="row">
    <div class="col-sm-0 col-md-1 col-xl-3">
    </div>
    <div class="col-sm-12 col-md-10 col-xl-6">
        <table class="table table-dark">
            <thead>
                <tr>
                  <th>Title</th>
                  <th>Author</th>
                  <th>Voting</th>
                  <th>Stage</th>
                  <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(count($bills) > 0) {
                  foreach($bills as $bill) {
                    echo  "<tr>";
                    echo    "<td nowrap>{$bill->shortTitle}</td>";
                    $name = Person::getDisplayName($link, $bill->author);
                    echo    "<td>{$name}</td>";
                    if($house->type == 0) {
                      echo  "<td>{$bill->HoLyays} - {$bill->HoLyays}</td>";
                    } else if($house->type == 1) {
                      echo  "<td>{$bill->HoCyays} - {$bill->HoCyays}</td>";
                    }
                    $stage = Bill::$stages[$bill->Stage];
                    echo    "<td>{$stage}</td>";
                    echo    "<td><a class=\"btn btn-primary\" href=\"bill.php?id={$bill->ID}\">View</a></td>";
                    echo "</tr>";
                  }
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="col-sm-0 col-md-1 col-xl-3">
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-0 col-xl-1">
    </div>
    <div class="col-sm-12 col-xl-10">
      <?php
        if($house->type == 1) {
          echo '
              <button type="button" class="btn btn-primary mb-2" onclick="hideColumn(\'county\')">Show/Hide Counties</button>
              <button type="button" class="btn btn-primary mb-2" onclick="hideColumn(\'country\')">Show/Hide Countries</button>
              <div style="max-width:30%" class="col-md-4 col-sm-3 input-group mb-2">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Search</span>
                  </div>
                  <input type="text" oninput="searchRows(this.value)" style="width: 30% !important" class="form-control" placeholder="Seat Name...">
              </div>
              <div class="input-group mb-3">
                  <select oninput="showCounty(this.value)" class="custom-select">
                      <option selected>All</option>';
                      $sqlcounties = mysqli_query($link, "SELECT Name FROM Counties");
                      while($county = mysqli_fetch_array($sqlcounties, MYSQLI_ASSOC)) {
                          echo '<option value="' . $county['Name'] . '">' . $county['Name'] . '</option>';
                      }
                      
                  echo
                '</select>
                  <div class="input-group-append">
                      <label class="input-group-text" for="inputGroupSelect02">County(s)</label>
                  </div>
              </div>';
        }
      ?>
        <table class="table table-dark">
            <thead>
                <tr>
                    <?php
                    if($house->type == 0) {
                        echo "
                        <th>Name</th>
                        <th>Title(s)</th>
                        <th>Side</th>";
                    } else if($house->type == 1) {
                        echo '
                        <th>Name</th>
                        <th class="county" style="display: none">County</th>
                        <th class="country" style="display: none">Country</th>
                        <th style="white-space: nowrap;" colspan="2">Seat 1</th>
                        <th style="white-space: nowrap;" colspan="2">Seat 2</th>
                        <th style="white-space: nowrap;" colspan="2">Seat 3</th>
                        <th style="white-space: nowrap;" colspan="2">Seat 4</th>
                        <th></th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if($house->type == 0) {
                    foreach($house->members as $m) {
                        echo '<tr>';
                        echo "<td>{$m->fullname}</td>";
                        echo "<td>{$m->title}</td>";
                        $position = Party::getPartyPosition($link, $m->party);

                        if($position == 0) {
                            $side = "Speaker";
                        } else if($position == 1) {
                            $side = "Government";
                        } else if($position == 2) {
                            $side = "Opposition";
                        } else if($position == 3) {
                            $side = "Crossbench";
                        }
                        echo '<td>' . $side .'</td>';
                        echo '</tr>';

                    }
                } else if($house->type == 1) {
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
                        echo '<td class="county" style="display: none">' . $row['County'] .' </td>';
                        echo '<td class="country" style="display: none">' . $row['Country'] .' </td>';
                        echo '<td style="background-color:' . $seat1c . ' !important; width:15px"></td>';
                        echo '<td>' . $seat1 . ' </td>';
                        echo '<td style="background-color:' . $seat2c . ' !important; width:15px"></td>';
                        echo '<td>' . $seat2 . ' </td>';
                        echo '<td style="background-color:' . $seat3c . ' !important; width:15px"></td>';
                        echo '<td>' . $seat3 . ' </td>';
                        echo '<td style="background-color:' . $seat4c . ' !important; width:15px"></td>';
                        echo '<td>' . $seat4 . ' </td>';
                        echo '<form action=seat.php method=get>';
                        echo    '<td>'."<input class='btn btn-primary btn-outline' type=submit name=view value=View".' </td>';
                        echo    "<td style='display:none;'>".'<input type=hidden name=id value='.$row['ID'].' </td>';
                        echo '</form>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="col-sm-0 col-xl-1">
    </div>
</div>
<?php if($member) {
  echo "
  <div class=\"text-center\">
    <a href=\"newbill.php?id={$houseID}\" class=\"btn btn-primary\">New Bill</a>
  </div>";
}
include_once "footer.php"; ?>