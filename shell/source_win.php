<!DOCTYPE html>
<html>
    <head>
        <style type="text/css">
            body {
                font-size: 12px;
                font-family: Verdana;
            }
            a:link,
            a:visited,
            a:hover,
            a:active { 
                color: blue;
                font-weight: normal;
            }
            .dir {
                background-color: #99CCFF;
            }
            .file {
                background-color: #FFCC66;
            }
        </style>
    </head> 
    <body><?php
        // (C) 2015 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

        $back = '<p><a href="javascript:history.back()">&laquo; back</a></p>' . PHP_EOL;
        echo $back;
        if (isset($_GET['s'])) {
            highlight_string(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\' . $_GET['s'])) . PHP_EOL;
        } else {
            if (isset($_GET['d'])) {
                $dir = $_GET['d'];
            } else {
                $dir = NULL;
            }

            $filestack = array();
            $dirstack = array();

            foreach (new DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . '\\' . $dir) as $file) {
                if (!$file->isDot()) {
                    if ($file->isFile()) {
                        array_push($filestack, $file->getFilename());
                    } elseif ($file->isDir()) {
                        array_push($dirstack, $file->getFilename());
                    }
                }
            }

            sort($filestack);
            sort($dirstack);

            foreach ($dirstack as $file) {
                echo '<a class="dir" href="?d=' . $dir . '\\' . $file . '">' . $file . '</a><br>' . PHP_EOL;
            }

            foreach ($filestack as $file) {
                if (strpos($file, '.php')) {
                    echo '<a class="file" href="?s=' . $dir . '\\' . $file . '">' . $file . '</a><br>' . PHP_EOL;
                } else {
                    echo '<a href="?s=' . $dir . '\\' . $file . '">' . $file . '</a><br>' . PHP_EOL;
                }
            }
        }
        echo $back;
        ?>
    </body>
</html>
