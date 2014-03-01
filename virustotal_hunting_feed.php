#!/usr/bin/php
<?php

// VirusTotal intelligence hunting feed parser
// (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

$file = file_get_contents('https://www.virustotal.com/intelligence/hunting/notifications-feed/?key=YOUR_API_KEY');
$array = json_decode($file, true);

foreach ($array['notifications'] as $notification) {
    if ($notification['positives'] > 0){
        unset($notification['total']);
        foreach ($notification['scans'] as $scanner => $scan) {
            if (is_null($scan)) {
                unset($notification['scans']["$scanner"]);
            }
        }
        $json = json_encode($notification);
        echo $json . "\n";
    }
}

?>
