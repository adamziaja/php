<?php
/*
(C) 2017 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

1. visit url
2. grab all links
3. check for first link that have regexp value
4. forward to a:href url
*/
ini_set('user_agent', 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)');
$str = file_get_contents('http://***URL***');
$DOM = new DOMDocument;
@$DOM->loadHTML($str);
$items = $DOM->getElementsByTagName('a');
for ($i = 0; $i < $items->length; $i++) {
    if (preg_match('/***A-VALUE***/i', $items->item($i)->nodeValue)) {
        $url = $items->item($i)->getAttribute('href');
        header("Location: $url", true, 302);
        echo "<a href='$url'>" . $url . '</a>' . PHP_EOL;
    }
}
?>
