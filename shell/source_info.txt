$ diff <(curl -s https://raw.githubusercontent.com/adamziaja/php/master/shell/source_linux.php) <(curl -s https://raw.githubusercontent.com/adamziaja/php/master/shell/source_win.php)
30c30
<             highlight_string(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $_GET['s'])) . PHP_EOL;
---
>             highlight_string(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\' . $_GET['s'])) . PHP_EOL;
41c41
<             foreach (new DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . '/' . $dir) as $file) {
---
>             foreach (new DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . '\\' . $dir) as $file) {
55c55
<                 echo '<a class="dir" href="?d=' . $dir . '/' . $file . '">' . $file . '</a><br>' . PHP_EOL;
---
>                 echo '<a class="dir" href="?d=' . $dir . '\\' . $file . '">' . $file . '</a><br>' . PHP_EOL;
60c60
<                     echo '<a class="file" href="?s=' . $dir . '/' . $file . '">' . $file . '</a><br>' . PHP_EOL;
---
>                     echo '<a class="file" href="?s=' . $dir . '\\' . $file . '">' . $file . '</a><br>' . PHP_EOL;
62c62
<                     echo '<a href="?s=' . $dir . '/' . $file . '">' . $file . '</a><br>' . PHP_EOL;
---
>                     echo '<a href="?s=' . $dir . '\\' . $file . '">' . $file . '</a><br>' . PHP_EOL;
