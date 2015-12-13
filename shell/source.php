<pre>
<?php
if(strlen($_GET['a'])>1){
  $path = NULL;
	echo htmlspecialchars(file_get_contents($path.$_GET['a'])) . PHP_EOL;
} ?>
</pre>
