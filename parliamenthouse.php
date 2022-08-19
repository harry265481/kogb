<?php
include_once 'header/header.php';
include_once 'classes/house.php';
include_once 'classes/party.php';
include_once 'classes/mp.php';
include_once 'classes/bill.php';
$houseID = $_GET['id'];
$house = new House($link, $houseID);
/*
echo "<pre>";
print_r($house);
echo "</pre>";
*/
$member = false;
if($player->house == $house->ID) {
  $member = true;
}

$po = false;
if($player->ID == $house->presidingOfficer) {
  $po = true;
}

$bills = Bill::getHouseBills($link, $house->ID);

if($house->type == 1) {
    $sqlget = "SELECT ID, Name, County, Country, Franchise, voters, seats, seat1, seat2, seat3, seat4 FROM seats WHERE Parliament = {$houseID}";
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
                  <th>Stage</th>
                  <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(count($bills) > 0) {
                  foreach($bills as $bill) {
                    if($bill->Stage > 0) {
                      echo  "<tr>";
                      echo    "<td nowrap>{$bill->shortTitle}</td>";
                      $name = Person::getDisplayName($link, $bill->author);
                      echo    "<td>{$name}</td>";
                      $stage = Bill::$stages[$bill->Stage];
                      echo    "<td>{$stage}</td>";
                      echo    "<td class=\"text-end\"><a class=\"btn btn-primary\" href=\"bill.php?id={$bill->ID}\">View</a></td>";
                      echo "</tr>";
                    }
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
    <div class="col-sm-0 col-md-1 col-xl-3"></div>
    <div class="col-sm-12 col-md-10 col-xl-6">
      <table class="table table-dark">
        <thead>
          <th colspan="2">Name</th>
          <th width="60px">#</th>
          <th>Side</th>
          <th></th>
        </thead>
        <tbody>
          <?php
            if($house->type == 0){
              $count = array();
              foreach($house->members as $member) {
                if(!isset($count[$member->party])) {
                  $count[$member->party] = 0;
                }
                $count[$member->party] += 1;
              }
              arsort($count);
              foreach($count as $party => $amount) {
                $partydetails = Party::getPartyDetails($link, $party);
                $positionText = Party::$sides[$partydetails[5]];
                echo "<tr>
                  <td width=\"15px\" style=\"background-color: {$partydetails[2]}\"></td>
                  <td>{$partydetails[1]}</td>
                  <td>{$amount}</td>
                  <td>{$positionText}</td>
                  <td><a class=\"btn btn-primary\" href=\"faction.php?id={$partydetails[0]}\">View</a></td>
                </tr>";
              }
            } else if($house->type == 1) {
              $count = array();
              foreach($house->MPs as $mp) {
                if(!isset($count[$mp->partyID])) {
                  $count[$mp->partyID] = 0;
                }
                $count[$mp->partyID] += 1;
              }
              arsort($count);
              foreach($count as $party => $amount) {
                $partydetails = Party::getPartyDetails($link, $party);
                $positionText = Party::$sides[$partydetails[5]];
                echo "
                <tr>
                  <td width=\"15px\" style=\"background-color: {$partydetails[2]}\"></td>
                  <td>{$partydetails[1]}</td>
                  <td>{$amount}</td>
                  <td>{$positionText}</td>
                  <td class=\"text-end\"><a class=\"btn btn-primary\" href=\"faction.php?id={$partydetails[0]}\">View</a></td>
                </tr>";
              }
            }
          ?>
        </tbody>
      </table>
    </div>
    <div class="col-sm-0 col-md-1 col-xl-3"></div>

</div>
<hr>
<div class="row">
  <div class="col-sm-0 col-xl-1"></div>
  <div class="col-sm-12 col-xl-10">
    <?php if($house->type == 1): ?>
    <a href="#" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#memberslist">Show Seats</a>
    <?php else: ?>
    <a href="#" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#memberslist">Show Members</a>
    <?php endif ?>
    <hr>
    <div class="collapse" id="memberslist">
      <?php if($house->type == 1): ?>
        <button type="button" class="btn btn-primary mb-2" onclick="hideColumn('county')">Show/Hide Counties</button>
        <button type="button" class="btn btn-primary mb-2" onclick="hideColumn('country')">Show/Hide Countries</button>
        <div style="max-width:30%" class="col-md-4 col-sm-3 input-group mb-2">
          <div class="input-group-prepend">
            <span class="input-group-text">Search</span>
          </div>
          <input type="text" oninput="searchRows(this.value)" style="width: 30% !important" class="form-control" placeholder="Seat Name...">
        </div>
        <div class="input-group mb-3">
          <select oninput="showCounty(this.value)" class="custom-select">
            <option selected>All</option>
            <?php
            $sqlcounties = mysqli_query($link, "SELECT Name FROM Counties");
            while($county = mysqli_fetch_array($sqlcounties, MYSQLI_ASSOC)) {
              echo "<option value={$county['Name']}>{$county['Name']}</option>";
            }
            ?>
          </select>
          <div class="input-group-append">
            <label class="input-group-text" for="inputGroupSelect02">County(s)</label>
          </div>
        </div>
      <?php endif ?>
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
                  echo "<tr>";
                  echo "<td>{$m->fullname}</td>";
                  echo "<td>{$m->title}</td>";
                  $position = Party::getPartyPosition($link, $m->party);
                  $side = Party::$sides[$position];
                  echo "<td>{$side}</td>";
                  echo "</tr>";

              }
          } else if($house->type == 1) {
            $sqlgetPartyNames = mysqli_fetch_all(mysqli_query($link, 'SELECT Name, Color FROM parties'), MYSQLI_ASSOC);
            foreach ($sqldata as $row) {
              for($i = 1; $i <= 4; $i++) {
                ${'seat' . $i} = "N/A";
                ${'seat' . $i . 'c'} = "#ccc";
              }
              for($i = 1; $i <= $row['seats']; $i++) {
                ${'seat' . $i . 'c'} = MP::getMPColor($link, $row['seat1']);
                ${'seat' . $i} = MP::getMPPartyName($link, $row['seat1']);
              }
            
              //Print it all as a row
              echo "<tr>";
              echo "<td>{$row['Name']}</td>";
              echo "<td class=\"county\" style=\"display: none\">{$row['County']}</td>";
              echo "<td class=\"country\" style=\"display: none\">{$row['Country']}</td>";
              for($i = 1; $i <= 4; $i++) {
                echo "<td style=\"background-color:${'seat' . $i . 'c'} !important; width:15px\"></td>";
                echo "<td>{$seat1}</td>";
              }
              echo "<td><a class='btn btn-primary' href='seat.php?id={$row['ID']}'>View</a></td>";
              echo "</tr>";
            }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-sm-0 col-xl-1"></div>
</div>
<?php if($member) {
  echo "
  <div class=\"text-center\">
    <a href=\"newbill.php?id={$houseID}\" class=\"btn btn-primary\">New Bill</a>
  </div>";
}

if($po) {
  echo "<hr>
    <div class=\"row mt-3\">
    <h5 class=\"text-center\">Bills to be put to the floor</h5>
    <div class=\"col-sm-0 col-md-1 col-xl-3\"></div>
    <div class=\"col-sm-12 col-md-10 col-xl-6\">
        <table class=\"table table-dark\">
            <thead>
                <tr>
                  <th>Title</th>
                  <th>Author</th>
                  <th>Stage</th>
                  <th></th>
                  <th></th>
                </tr>
            </thead>
            <tbody>";
    if(count($bills) > 0) {
      foreach($bills as $bill) {
        if($bill->Stage == 0) {
          echo  "<tr>";
          echo    "<td nowrap>{$bill->shortTitle}</td>";
          $name = Person::getDisplayName($link, $bill->author);
          echo    "<td>{$name}</td>";
          $stage = Bill::$stages[$bill->Stage];
          echo    "<td>{$stage}</td>";
          echo    "<td><a class=\"btn btn-primary\" href=\"bill.php?id={$bill->ID}\">View</a></td>";
          echo "</tr>";
        }
      }
    }
    echo "
      </tbody>
    </table>
  </div>
  <div class=\"col-sm-0 col-md-1 col-xl-3\"></div>";
}

include_once "footer.php"; ?>