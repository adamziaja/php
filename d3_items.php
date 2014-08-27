#!/usr/bin/php
<?php

// (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

$array = json_decode(file_get_contents('https://eu.battle.net/api/d3/profile/adam-21870/hero/46580866'), true);

//var_dump($array['items']);
foreach ($array['items'] as $items) {
    //var_dump($items['tooltipParams']);
    $item = json_decode(file_get_contents('https://eu.battle.net/api/d3/data/' . $items['tooltipParams']), true);
    //var_dump($item);
    echo '#' . $item['name'] . PHP_EOL;
    //var_dump($item['attributesRaw']);
    foreach ($item['attributesRaw'] as $name => $attribute) {
        echo $name . ' ';
        if ($attribute['min'] == $attribute['max']) {
            if (preg_match('/Percent/', $name)) {
                echo ($attribute['max'] * 100) . '%' . PHP_EOL;
            } else {
                echo $attribute['max'] . PHP_EOL;
            }
        } else {
            echo $attribute['min'] . '-' . $attribute['max'] . PHP_EOL;
        }
    }
    echo PHP_EOL;
}

?>
