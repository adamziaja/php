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
$last  = NULL;
$sum   = NULL;

foreach ($array['items'] as $items) {
    $item = json_decode(file_get_contents('https://eu.battle.net/api/d3/data/' . $items['tooltipParams']), true);
    foreach ($item['attributesRaw'] as $name => $attribute) {
        if ($attribute['min'] == $attribute['max']) {
            $data = $attribute['max'];
        } else {
            $data = $attribute['min'] . '-' . $attribute['max'];
        }
        array_push($stack, array(
            $item['name'],
            $name,
            $data
        ));
    }
    
    foreach ($item['gems'] as $gem) {
        foreach ($gem['attributesRaw'] as $name => $attribute) {
            if ($attribute['min'] == $attribute['max']) {
                $data = $attribute['max'];
            } else {
                $data = $attribute['min'] . '-' . $attribute['max'];
            }
        }
        array_push($stack, array(
            $gem['item']['id'],
            $name,
            $data
        ));
    }
}

$stack = msort($stack, array(
    1,
    0
));

foreach ($stack as $item) {
    if ($item[1] != $last && !is_null($sum)) {
        if (preg_match('/Percent/', $last) || preg_match('/Chance/', $last) || preg_match('/Gold_Find/', $last)) {
            echo ($sum * 100) . '%' . PHP_EOL;
        } else {
            echo $sum . PHP_EOL;
        }
        echo PHP_EOL;
    }
    
    if ($item[1] != $last) {
        echo '#' . $item[1] . PHP_EOL;
    }
    
    if (preg_match('/Percent/', $item[1]) || preg_match('/Chance/', $item[1]) || preg_match('/Gold_Find/', $item[1])) {
        echo ($item[2] * 100) . '% ' . $item[0] . PHP_EOL;
    } else {
        echo $item[2] . ' ' . $item[0] . PHP_EOL;
    }
    
    if ($item[1] != $last) {
        $sum = NULL;
    }
    
    $sum  = $sum + $item[2];
    $last = $item[1];
}

if (preg_match('/Percent/', $last) || preg_match('/Chance/', $last) || preg_match('/Gold_Find/', $last)) {
    echo ($sum * 100) . '%' . PHP_EOL;
} else {
    echo $sum . PHP_EOL;
}

?>
