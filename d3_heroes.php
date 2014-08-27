#!/usr/bin/php
<?php

// (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

$file  = file_get_contents('https://eu.battle.net/api/d3/profile/adam-21870/');
$array = json_decode($file, true);

echo $array['battleTag'] . ' ' . $array['seasonalProfiles'][0]['paragonLevel'] . 'lvl' . PHP_EOL;

$profile = 'https://eu.battle.net/d3/pl/profile/' . str_replace('#', '-', $array['battleTag']) . '/';
echo $profile . PHP_EOL . PHP_EOL;

foreach ($array['heroes'] as $hero) {
    if ($array['lastHeroPlayed'] == $hero['id']) {
        echo '@ ';
    }
    echo $profile . 'hero/' . $hero['id'] . PHP_EOL;
    echo $hero['name'] . ' ' . $hero['class'] . ' ' . $hero['level'] . 'lvl' . PHP_EOL;
    echo gmdate("Y-m-d H:i:s", $hero['last-updated']) . PHP_EOL;
    echo PHP_EOL;
}

?>
