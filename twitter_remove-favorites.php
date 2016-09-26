<?php

/* (C) 2016 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$CONSUMER_KEY = "XXXXX";
$CONSUMER_SECRET = "XXXXX";
$ACCESS_TOKEN = "XXXXX";
$ACCESS_TOKEN_SECRET = "XXXXX";

require "twitteroauth/autoload.php"; // https://twitteroauth.com

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $ACCESS_TOKEN, $ACCESS_TOKEN_SECRET);

again:

$i = NULL;
var_dump($i);

$statuses = $connection->get("favorites/list", ["screen_name" => "adamziaja", "count" => 100]);
//var_dump($statuses);
foreach ($statuses as $status) {
	//if (strtotime($status->created_at) <= strtotime("-14 day")) {
		$i++;
		var_dump($i);
		var_dump($status->id);
		//var_dump($status->text);
		$response = $connection->post("favorites/destroy", ["id" => $status->id]);
		//var_dump($response);
	//}
}

var_dump($i);
if (!is_null($i)) {
	goto again; // https://xkcd.com/292/
}

?>
