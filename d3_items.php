#!/usr/bin/php
<?php

// (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

function msort($array, $key, $sort_flags = SORT_REGULAR) // (C) http://blog.jachim.be/2009/09/php-msort-multidimensional-array-sort/comment-page-1/
{
    if (is_array($array) && count($array) > 0) {
        if (!empty($key)) {
            $mapping = array();
            foreach ($array as $k => $v) {
                $sort_key = '';
                if (!is_array($key)) {
                    $sort_key = $v[$key];
                } else {
                    foreach ($key as $key_key) {
                        $sort_key .= $v[$key_key];
                    }
                    $sort_flags = SORT_STRING;
                }
                $mapping[$k] = $sort_key;
            }
            asort($mapping, $sort_flags);
            $sorted = array();
            foreach ($mapping as $k => $v) {
                $sorted[] = $array[$k];
            }
            return $sorted;
        }
    }
    return $array;
}

$array = json_decode(file_get_contents('https://eu.battle.net/api/d3/profile/adam-21870/hero/46580866'), true);

$stack = array();

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
                $data = ($attribute['max'] * 100) . '%';
            } else {
                echo $attribute['max'] . PHP_EOL;
                $data = $attribute['max'];
            }
        } else {
            echo $attribute['min'] . '-' . $attribute['max'] . PHP_EOL;
            $data = $attribute['min'] . '-' . $attribute['max'];
        }
        array_push($stack, array(
            $item['name'],
            $name,
            $data
        ));
    }
    
    foreach ($item['gems'] as $gem) {
        echo '@' . $gem['item']['id'] . PHP_EOL;
        foreach ($gem['attributesRaw'] as $name => $attribute) {
            echo $name . ' ';
            if ($attribute['min'] == $attribute['max']) {
                echo $attribute['max'] . PHP_EOL;
                $data = $attribute['max'];
            } else {
                echo $attribute['min'] . '-' . $attribute['max'] . PHP_EOL;
                $data = $attribute['min'] . '-' . $attribute['max'];
            }
        }
        array_push($stack, array(
            $gem['item']['id'],
            $name,
            $data
        ));
    }
    
    echo PHP_EOL;
}

$stack = msort($stack, 1);

var_dump($stack);

?>
