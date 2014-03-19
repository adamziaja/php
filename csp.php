<?php
header("Content-Security-Policy-Report-Only: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; connect-src 'self'; font-src 'self'; object-src 'self'; media-src 'self'; frame-src 'self'; sandbox; report-uri /csp.php");
?>

csp.php:
<?php
// content security policy report-uri
// (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

//error_reporting(0);
$json = json_encode(json_decode(file_get_contents('php://input')), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL; // PHP 5.4+
$csp = $_SERVER['HTTP_USER_AGENT'] . ';' . $_SERVER['REMOTE_ADDR'] . ';' . $_SERVER['REMOTE_HOST'] . PHP_EOL . $json . PHP_EOL;
file_put_contents('csp.txt', $csp, FILE_APPEND | LOCK_EX);
?>
