<pre>
<?php
$dbname = "XXXXX";
$mysql_user = "XXXXX";
$mysql_password = "XXXXX";
$mysql_host = "XXXXX";

$g_link = mysql_connect("$mysql_host", "$mysql_user", "$mysql_password") or die('mysql_connect');

mysql_select_db("$dbname", $g_link) or die('mysql_select_db');

$result = mysql_query("XXXXX");

if (!$result) {
    echo 'MySQL Error: ' . mysql_error();
    exit;
} else {
    echo 'OK';
}

mysql_free_result($result);
?>
</pre>
