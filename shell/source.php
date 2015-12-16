<?php
if(isset($_GET['a'])){
    echo highlight_string(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$_GET['a'])) . PHP_EOL;
}
?>
