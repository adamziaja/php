<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <meta http-equiv="expires" content="Mon, 26 Jul 1997 05:00:00 GMT">
        <meta http-equiv="pragma" content="no-cache">
        <meta name="description" content="XXXXX">
        <meta name="keywords" content="XXXXX">
        <meta name="author" content="XXXXX">
        <title>XXXXX</title>
        <style type="text/css">
            body {
                /* margin-top: 0;
                margin-left: 0; */
                background-color: #191919;
                /* background-color: black; */
            }
            body, p {
                font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
                color: #E0E4CC;
                /* text-align: justify; */
                /* -webkit-text-shadow: 1px 1px 1px #444444;
                -moz-text-shadow: 1px 1px 1px #444444;
                text-shadow: 1px 1px 1px #444444; */
            }
            a, a:visited {
                color: #69D2E7;
                text-decoration: none;
            }
            a:hover {
                color: #A7DBD8;
            }
            img {
                border: 0;
                vertical-align: top;
            }
            td {
                padding: 10px;
                vertical-align: top;
            }
            td.internal {
                padding: 1px;
                padding-top: 5px;
                vertical-align: top;
            }
            td.internal2 {
                padding-top: 5px;
                padding-left: 0px;
                padding-right: 15px;
                padding-bottom: 0px;
                vertical-align: top;
            }
            sup {
                vertical-align: top;
            }
            strong {
                color: #F38630;
                font-weight: normal;
            }
            .headline {
                color: #ECD078;
                font-weight: bold;
            }
            .quote {
                color: #E9C92D;
                font: 400 20px/30px 'Pacifico';
                font-style: italic;
                text-indent: -6px;
                -webkit-text-shadow: 0 3px 0 black;
                -moz-text-shadow: 0 3px 0 black;
                text-shadow: 0 3px 0 black;
            }
            .info, .success, .warning, .error {
                border: 1px solid;
                padding:15px 15px 15px 15px;
                background-repeat: no-repeat;
                background-position: 10px center;
            }
            .info, .info a {
                color: #00529B;
                background-color: #BDE5F8;
            }
            .success, .success a {
                color: #4F8A10;
                background-color: #DFF2BF;
            }
            .warning, .warning a {
                color: #9F6000;
                background-color: #FEEFB3;
            }
            .error, .error a {
                color: #D8000C;
                background-color: #FFBABA;
            }
            .border {
                border: 2px solid black;
            }
            del { 
                background: url('img/del.png') left center repeat-x; text-decoration: none;
            }
            hr {
                width: 150px;
                margin-left: 0;
                text-align: left;
            }
            span {
                border-bottom: 1px dotted #E0E4CC;
            }
        </style>
    </head>
    <body><?php
        /* (C) 2015 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com */

        $mysqli = new mysqli('localhost', 'XXXXX', 'XXXXX', 'XXXXX');

        if ($mysqli->connect_error) {
//            die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        echo PHP_EOL . "<p>&hellip;<span title='21 dni'>ostatnie</span> <span title='powyżej średniej z retweetów dla danego profilu na twitterze'>najważniejsze</span> <i>ćwierknięcia</i> z wybranych portali poświęconych bezpieczeństwu IT</p>" . PHP_EOL . "<pre>" . PHP_EOL;

        $query = "SELECT screen_name,ROUND(AVG(retweet_count)) FROM twitter GROUP BY screen_name ORDER BY ROUND(AVG(retweet_count)) DESC";

        if (!$result = $mysqli->query($query)) {
//            echo "Error: ($mysqli->errno) $mysqli->error" . PHP_EOL;
        }

        while ($row = $result->fetch_assoc()) {
            //echo $row['screen_name'] . " " . $row['ROUND(AVG(retweet_count))'] . PHP_EOL;

            $query2 = "SELECT * FROM twitter WHERE screen_name = '" . $row['screen_name'] . "' AND retweet_count >= " . $row['ROUND(AVG(retweet_count))'] . " ORDER BY created_at DESC";

            if (!$result2 = $mysqli->query($query2)) {
//                echo "Error: ($mysqli->errno) $mysqli->error" . PHP_EOL;
            }

            while ($row2 = $result2->fetch_assoc()) {
                //echo $row2['screen_name'] . " " . $row2['id'] . PHP_EOL;
                if ($row2['created_at'] >= strtotime("-21 day")) {
                    $id = str_replace($row2['id'], "<a href=\"https://twitter.com/" . $row2['screen_name'] . "/status/" . $row2['id'] . "\" target=\"_blank\">" . $row2['id'] . "</a>", $row2['id']);
                    $screen_name = str_replace($row2['screen_name'], "<a href=\"https://twitter.com/" . $row2['screen_name'] . "\" target=\"_blank\">" . $row2['screen_name'] . "</a>", $row2['screen_name']);
                    echo date('Y-m-d H:i:s', $row2['created_at']) . " &mdash; " . $screen_name . " &mdash; " . $id . " &mdash; &#9851; " . $row2['retweet_count'] . " &mdash; &#10084; " . $row2['favorite_count'] . " &mdash; " . $row2['text'] . PHP_EOL;
                }
            }
            echo PHP_EOL;
        }

        echo "</pre>" . PHP_EOL;
        ?>
    </body>
</html>
