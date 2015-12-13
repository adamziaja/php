<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>?</title>
    </head>
    <body>
        <?php
        /* (C) 2015 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com */

//CREATE TABLE twitter (
//id BIGINT UNSIGNED NOT NULL UNIQUE PRIMARY KEY,
//screen_name TEXT NOT NULL,
//created_at INT(4) UNSIGNED NOT NULL,
//text TEXT NOT NULL,
//retweet_count SMALLINT UNSIGNED,
//favorite_count SMALLINT UNSIGNED
//);

        $mysqli = new mysqli('localhost', 'XXXXX', 'XXXXX', 'XXXXX');

        if ($mysqli->connect_error) {
            die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        $CONSUMER_KEY = "XXXXX"; // Consumer Key (API Key)
        $CONSUMER_SECRET = "XXXXX"; // Consumer Secret (API Secret)

        require "twitteroauth/autoload.php"; // https://twitteroauth.com

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token, $access_token_secret);

        foreach (array("niebezpiecznik", "zaufana3strona", "sekurak") as $screen_name) {
            // https://dev.twitter.com/rest/reference/get/statuses/user_timeline
            $statuses = $connection->get("statuses/user_timeline", array("screen_name" => "$screen_name", "include_rts" => "1", "count" => "200"));

            echo "<pre>";
            foreach ($statuses as $status) {
                if (is_null($status->retweeted_status) && !(substr($status->text, 0, 1) === "@")) { // && $status->retweet_count > 0 && strtotime($status->created_at) >= strtotime("-14 day")
                    //$user = $status->user->id;
                    $id = $status->id;
                    //$created_at = date('Y-m-d H:i:s', strtotime($status->created_at));
                    $created_at = strtotime($status->created_at);
                    $text = $status->text;
                    $text = trim(preg_replace('/\s+/', ' ', $text));
                    $retweet_count = $status->retweet_count;
                    $favorite_count = $status->favorite_count;

                    foreach ($status->entities->urls as $url) {
                        $text = str_replace($url->url, "<a href=\"$url->expanded_url\" target=\"_blank\">$url->expanded_url</a>", $text);
                        //$text = str_replace($url->url, $url->expanded_url, $text);
                    }

                    foreach ($status->entities->media as $media) {
                        $text = str_replace($media->url, "<a href=\"$media->media_url\" target=\"_blank\"><img src=\"img/camera.gif\" alt=\"$media->media_url\"></a>", $text);
                        //$text = str_replace($media->url, "", $text);
                    }

                    /* mysqli */
                    $query = "REPLACE INTO twitter (id, screen_name, created_at, text, retweet_count, favorite_count) VALUES(?, ?, ?, ?, ?, ?)";

                    if ($statement = $mysqli->prepare($query)) {
                        //bind parameters for markers, where (s = string, i = integer, d = double,  b = blob)
                        $statement->bind_param('isisii', $id, $screen_name, $created_at, $text, $retweet_count, $favorite_count);
                    } else {
                        echo "Error: $mysqli->error" . PHP_EOL;
                    }

                    if (!$statement->execute()) {
                        echo "Error: ($mysqli->errno) $mysqli->error" . PHP_EOL;
                    }

                    /* display */
                    $id = str_replace($id, "<a href=\"https://twitter.com/$screen_name/status/$id\" target=\"_blank\">$id</a>", $id);
                    echo "$created_at &mdash; $id &mdash; &#9851; $retweet_count &mdash; &#10084; $favorite_count &mdash; $text" . PHP_EOL;
                }
            }
            echo "</pre>";
        }

        $statement->close();
        ?>
    </body>
</html>
