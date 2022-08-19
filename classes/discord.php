<?php
include_once "person.php";
include_once __DIR__ . "/../discordpayload.php";
class Discord {
    static function MakeRequest($endpoint, $data) {
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

    static function newGuildChannel($serverID, $content) {
        return Discord::MakeRequest("/guilds/{$serverID}/channels", $content);
    }
}

class DiscordEmbed {
    public $title;
    public $type = "rich";
    public $description;
    public $author;
    public $url;
    public $color;
    public $timestamp;

    public $top = "{ 
    \"embeds\": [{ 
        ";
    public $bottom = "}]}";
    public $text;

    public function __construct($content) {
        $text = $this->top;
        if(array_key_exists("title", $content)) {
            $this->title = $content["title"];
            $text .= "\"title\": \"{$this->title}\",
        ";
            reset($content);
        } else {
            reset($content);
        }

        if(array_key_exists("type", $content)) {
            $this->type = $content["type"];
            $text .= "\"type\": \"{$this->type}\",
        ";
            reset($content);
        } else {
            reset($content);
        }

        if(array_key_exists("description", $content)) {
            $this->description = $content["description"];
            $text .= "\"description\": \"{$this->description}\",
        ";
            reset($content);
        } else {
            reset($content);
        }

        if(array_key_exists("url", $content)) {
            $this->url = $content["url"];
            $text .= "\"url\": \"{$this->url}\",
        ";
            reset($content);
        } else {
            reset($content);
        }

        if(array_key_exists("color", $content)) {
            $this->color = $content["color"];
            $text .= "\"color\": {$this->color},
        ";
            reset($content);
        } else {
            reset($content);
        }

        if(array_key_exists("timestamp", $content)) {
            $this->timestamp = $content["timestamp"];
            $text .= "\"timestamp\": \"{$this->timestamp}\",
        ";
            reset($content);
        } else {
            reset($content);
        }

        if(array_key_exists("author", $content)) {
            $this->author = $content["author"];
            $last = array_key_last($this->author);
            $text .= "\"author\": {
            ";
            foreach($this->author as $e => $a) {
                if($e == $last) {
                    $text .= "\"{$e}\": \"{$a}\"
        ";
                } else {
                    $text .= "\"{$e}\": \"{$a}\",
            ";

                }
            }
            $text .= "}";
            reset($content);
        } else {
            reset($content);
        }
        if($text[-1] == ",") {
            $text = substr_replace($text, "", -1);
        }
        $text .= $this->bottom;
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }
}
?>