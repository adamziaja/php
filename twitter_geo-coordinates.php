<?php

/*
 * (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com
 * 
 * Collecting GPS positions from the user's Twitter profile.
 * Example of use:
 * php twitter_geo-coordinates.php username > /tmp/test.html && firefox /tmp/test.html
 */

if (count($argv) > 1) {
    $screen_name = $argv[1];
} else {
    die('You must define a username in the first argument!' . PHP_EOL);
}

date_default_timezone_set('Europe/Warsaw');

// https://github.com/abraham/twitteroauth
require_once('twitteroauth/twitteroauth/twitteroauth.php');

// Twitter API
$consumerkey = "XXXXXXXXXX";
$consumersecret = "XXXXXXXXXX";
$accesstoken = "XXXXXXXXXX";
$accesstokensecret = "XXXXXXXXXX";

function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
    $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
    return $connection;
}

$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$screen_name&include_rts=false&include_entities=true&count=200");

if (!isset($tweets->errors) && !isset($tweets->error)) {
    echo '<!DOCTYPE html><html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"></head><body style="font: 12px Verdana, Geneva, sans-serif">' . PHP_EOL;
    foreach ($tweets as $tweet) {
        //var_dump($tweet);
        //die();
        if (isset($tweet->geo)) {
            echo '<p>' . PHP_EOL . date('Y-m-d H:i:s', strtotime($tweet->created_at)) . ' (' . date_default_timezone_get() . ')<br>' . PHP_EOL;
            echo '<a href="https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet->id_str . '" target="_blank">' . $tweet->user->screen_name . '</a><br>' . PHP_EOL;
            echo '<img src="http://maps.googleapis.com/maps/api/staticmap?center=' . $tweet->geo->coordinates[0] . ',' . $tweet->geo->coordinates[1] . '&zoom=13&size=600x300&maptype=roadmap&markers=color:red%7C' . $tweet->geo->coordinates[0] . ',' . $tweet->geo->coordinates[1] . '" alt="' . $tweet->geo->coordinates[0] . ',' . $tweet->geo->coordinates[1] . '"><br>' . PHP_EOL;
            echo '<a href="https://maps.google.pl/maps?q=' . $tweet->geo->coordinates[0] . ',' . $tweet->geo->coordinates[1] . '" target="_blank">' . $tweet->place->full_name . '</a><br>' . PHP_EOL;
            echo $tweet->text . PHP_EOL . '</p>' . PHP_EOL . '<hr style="width: 600px; text-align: left; margin-left: 0">' . PHP_EOL;
        }
    }
    echo '</body></html>' . PHP_EOL;
} else {
    $errors = NULL;
    if (isset($tweets->error)) {
        $errors .= $tweets->error . PHP_EOL;
    }
    if (isset($tweets->errors)) {
        foreach ($tweets->errors as $error) {
            $errors .= $error->message . PHP_EOL;
        }
    }
    die($errors);
}
?>
