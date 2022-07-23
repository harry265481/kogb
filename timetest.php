<?php
include_once "config.php";
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
include_once "functions.php";
$data = getTimeStuff($link);
$year = $data[0];
$time = $data[1];
$irlweek = 604800 * 1000;
$irlyear = 31557600 * 1000;
$icyear = $irlweek * $data[2];
$a = $irlyear / $icyear;

echo '<pre>' . print_r(get_defined_vars(), true) . '</pre>';
?>
<!DOCTYPE html>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
    <style>
        #date {
            font-family:'Open Sans', Arial, sans-serif;
            text-align:center;
            margin: 0 0 10px 0;
        }
        #time {
            font-family:'Open Sans', Arial, sans-serif;
            text-align:center;
            font-size:3em;
            font-weight:200;
            color:#000;
        }

        #time span{
            border-radius: 5px;
            padding: 1px 4px 3px 4px;
        }

        #hour, #minute{
            color: #fff;
            background: #000;
            background: -webkit-linear-gradient(top, #555, black);
        }

        #second{
            color: #333;
            background: #ddd;
            background: -webkit-linear-gradient(top, #eee, #ccc);
        }
    </style>
</head>

<body onload="startTime()">
    <div id="date"></div>
    <div id="time">
        <span id="hour"></span>
        :
        <span id="minute"></span>
        :
        <span id="second"></span>
    </div>
    <span id="ct"></span><br>
    <span id="start"></span><br>
    <span id="now"></span><br>

<script>
function startTime() {
    var startDate = new Date('<?php echo $time ?> GMT-0400');
    var year = startDate.getFullYear();
    var now = new Date();

    //RL ms since time start x a for dilation
    var ct = (now - startDate) * <?php echo $a ?>;
    //ct = Math.floor(ct);
    var timestamp = new Date(ct);
    timestamp.setFullYear(<?php echo $year ?>);
    //Time started in 1750 so add 1750
    var year = <?php echo $year ?>;
    document.getElementById('start').innerHTML = year;
    document.getElementById('now').innerHTML = timestamp;
/*
    //current year = start year + [(time since) / (year in ms)]
    var y = Math.floor(year + (ct / 31557600000));

    //current month = remainder of [(current time) / (4 weeks in ms)]
    var mo = Math.floor((ct / 2419200066.234088) % 12); 
    mo = mo + 1;

    //current day = {[current month * (4 weeks in ms)] - current time} / (division to convert to hours)
    var d = Math.floor(((mo * 2419200066.234088) - ct) / 1000 / 60 / 60 / 24); 
    //add one to offset from month starting at 0 to 1
    d = d + 1;

    //current hour
    //Mod of current time and length of a day in ms
    //That is to say, ms since day start
    var ch = ct % 86400000;

    //convert ms to hours
    ch = ch / 1000 / 60 / 60;
    var h = Math.floor(ch);
    if(h < 10) {
        h = "0" + h;
    }

    //current minute
    //Mod of current time and length of a day in ms
    //That is to say, ms since day start
    var cm = ct % 3600000;

    //convert ms to hours
    cm = cm / 1000 / 60;
    var mi = Math.floor(cm);
    if(mi < 10) {
        mi = "0" + mi;
    }

    //current minute
    //Mod of current time and length of a day in ms
    //That is to say, ms since day start
    var cs = ct % 60000;

    //convert ms to hours
    cs = cs / 1000;
    var s = Math.floor(cs);
    if(s < 10) {
        s = "0" + s;
    }
  document.getElementById('date').innerHTML = ordinal_suffix_of(d) + " day, " + ordinal_suffix_of(mo) + " moon, " + y;
  document.getElementById('hour').innerHTML = h;
  document.getElementById('minute').innerHTML = mi;
  document.getElementById('second').innerHTML = s;
  document.getElementById('ct').innerHTML = ct;
  document.getElementById('start').innerHTML = startDate;
  document.getElementById('now').innerHTML = now;
*/
  
  setTimeout(startTime, 1);
}

function ordinal_suffix_of(i) {
    var j = i % 10,
        k = i % 100;
    if (j == 1 && k != 11) {
        return i + "st";
    }
    if (j == 2 && k != 12) {
        return i + "nd";
    }
    if (j == 3 && k != 13) {
        return i + "rd";
    }
    return i + "th";
}

function checkTime(i) {
  if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
  return i;
}
</script>

</body>
</html>
