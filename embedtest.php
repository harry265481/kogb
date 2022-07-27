<?php

include_once "discordpayload.php";
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

//$newDM = MakeRequest('users/@me/channels', array("recipient_id" => $discordid));

$json = '{
    "embeds": [{
      "title": "House of Lords Members",
      "color": 9902642,
      "fields": [
        {
          "name": "Earls",
          "value": "<@879823038303047771>"
        }
      ]
    }]
  }';

  $json = json_decode($json);
  echo "<pre>";
  print_r($json);
  echo "</pre>";
  echo "<hr>";
  echo "<pre>";
  print_r($newMessage = MakeRequest("channels/1000857627682685038/messages", $json));
  echo "</pre>";
?>