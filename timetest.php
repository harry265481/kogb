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
    document.getElementById('date').innerHTML = ordinal_suffix_of(timestamp.getUTCDate()) + ", " + month(timestamp.getUTCMonth()) + ", " + timestamp.getUTCFullYear();
    setTimeout(startTime, 1000);
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

function month(i) {
    let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[i];
}
</script>

</body>
</html>
