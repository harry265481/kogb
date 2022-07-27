<?php
include_once "token.php";
function MakeRequest($endpoint, $data) {
    # Set endpoint
    $url = "https://discord.com/api/".$endpoint."";

    # Encode data, as Discord requires you to send json data.
    $data = json_encode($data);

    # Initialize new curl request
    $ch = curl_init();

    # Set headers, data etc..
    $botToken = constant('BOT_TOKEN');
    curl_setopt_array($ch, array(
        CURLOPT_URL            => $url, 
        CURLOPT_HTTPHEADER     => array(
            'Authorization: Bot ' . $botToken,
            "Content-Type: application/json",
            "Accept: application/json"
        ),
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_VERBOSE        => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POSTFIELDS => $data,
    ));

    $request = curl_exec($ch);
    curl_close($ch);
    return json_decode($request, true);
}

function MakeDeleteRequest($endpoint) {
    # Set endpoint
    $url = "https://discord.com/api/".$endpoint."";

    # Encode data, as Discord requires you to send json data.
    $data = json_encode($data);

    # Initialize new curl request
    $ch = curl_init();

    # Set headers, data etc..
    $botToken = constant('BOT_TOKEN');
    curl_setopt_array($ch, array(
        CURLOPT_URL            => $url, 
        CURLOPT_HTTPHEADER     => array(
            'Authorization: Bot ' . $botToken,
            "Content-Type: application/json",
            "Accept: application/json"
        ),
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_VERBOSE        => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => "DELETE";
    ));

    $request = curl_exec($ch);
    curl_close($ch);
    return json_decode($request, true);
}

include_once "../config.php";
//House of Lords
$sql = "SELECT User, Party FROM people WHERE HoL = 1";
$users = mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_ASSOC);
$admin = array();
$cross = array();
$opp = array();

function getPartyPosition($link, $partyID) {
    return mysqli_fetch_array(mysqli_query($link, "SELECT Position FROM parties WHERE ID = {$partyID}"))[0];
}

foreach($users as $user) {
    $pos = getPartyPosition($link, $user['Party']);
    $discID = mysqli_fetch_array(mysqli_query($link, "SELECT discordID FROM users WHERE id = {$user['User']}"))[0];
    if($discID != null) {
        if($pos == 1) {
            array_push($admin, $discID);
        } else if($pos == 2) {
            array_push($opp, $discID);
        } else if($pos == 3) {
            array_push($cross, $discID);
        }
    }
}

$adminstring = $crossstring = $oppstring = "";

if(count($admin) > 0) {
    foreach($admin as $a) {
        $adminstring .= "<@" . $a . ">\\r";
    }
} else {
    $adminstring = "None";
}

if(count($cross) > 0) {
    foreach($cross as $c) {
        $crossstring .= "<@" . $c . ">\\r";
    }
} else {
    $crossstring = "None";
}

if(count($opp) > 0) {
    foreach($opp as $o) {   
        $oppstring .= "<@" . $o . ">\\r";
    }
} else {
    $oppstring = "None";
}

$json = '{
    "embeds": [{
      "title": "House of Lords Members",
      "color": 9902642,
      "footer": {
        "text": "This message will update daily"
      },
      "fields": [
        {
          "name": "Administration",
          "value": "' . $adminstring . '"
        },
        {
          "name": "Opposition",
          "value": "' . $oppstring . '"
        },
        {
          "name": "Crossbench",
          "value": "' . $crossstring . '"
        }
      ]
    }]
  }';
  $json = json_decode($json);
  echo "<pre>";
  print_r($newMessage = MakeRequest("channels/996621415207944212/messages", $json));
  echo "</pre>";
  //check if a message already exists, if so, delete it
  $prev = mysqli_query($link, "SELECT messageID FROM embeds WHERE channel = \"{$newMessage['channel_id']}\"");
  if($prev) {
    print_r($delete = MakeDeleteRequest("channels/{$newMessage['channel_id']}/messages/{$prev['messageID']}"));
  }
  //add the message to the database
  mysqli_query($link, "INSERT INTO `embeds` (`messageID`, `channel`) VALUES (\"{$newMessage['id']}\", \"{$newMessage['channel_id']}\"");
  ?>