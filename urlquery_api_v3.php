#!/usr/bin/php
<?php

/*
 * example of use URLQuery API v3
 * (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com
 */

$context = array(
    'http' => array(
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'method' => 'POST',
        'content' => '{"method": "report_list", "key": "_YOUR-API-KEY_"}', // change me
    ),
);
$file = file_get_contents('https://_API-URL_/v3/json', false, stream_context_create($context)); // change me ("Please do not share this URL unless needed.")
$array = json_decode($file, true);

if ($array['_status_']['status'] == 'ok') {
    foreach ($array['reports'] as $report) {
        if ($report['url']['ip']['cc'] == 'PL' || preg_match('#paypal#i', $report['url']['addr'])) { // change me
            //echo $report['report_id'] . PHP_EOL;
            var_dump($report);
        }
    }
}
?>
