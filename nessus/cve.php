<!DOCTYPE html>
<html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#hide").click(function() {
                    $("ul").hide();
                });
                $("#show").click(function() {
                    $("ul").show();
                });
            });
        </script>
        <style type="text/css">
            * {
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: black;
            }
            a:link, a:visited, a:active {
                color: black;
                text-decoration: none;
                border-bottom: 1px solid red;
            }
        </style>
    </head>
    <body>
        <?php
        /*
         * CVE list to unique list with details (via vFeed https://github.com/toolswatch/vFeed)
         * by Adam Ziaja 2014
         */

        ini_set('max_execution_time', 300);

        if ((!empty($_POST)) || (!empty($_GET))) {
            if ($_POST['cves']) {
                $query = $_POST['cves'];
                } elseif ($_GET['cve']) {
                $query = $_GET['cve'];
            } else {
                die('No POST/GET?' . PHP_EOL);
            }
            $db = new SQLite3('vFeed/vfeed.db'); // https://github.com/toolswatch/vFeed
            //var_dump($_POST['cves']);
            preg_match_all('/CVE-\d\d\d\d-\d*/i', $query, $matches);
            $cves = array_unique($matches[0]);
            echo '<ol>' . PHP_EOL;
            foreach ($cves as $cve) {
                echo '<li><a href="#' . $cve . '">' . $cve . '</a></li>';
            }
            echo '</ol>' . PHP_EOL;
            echo '<textarea name="cves" rows="10" cols="100">' . PHP_EOL;
            $dradis_cve = NULL;
            foreach ($cves as $cve) {
                $dradis_cve .= "$cve, ";
            }
            echo substr($dradis_cve, 0, -2);
            echo '</textarea><br>' . PHP_EOL;
            echo '<button id="hide">Hide</button> <button id="show">Show</button>';
            foreach ($cves as $cve) {
                $cve = strtoupper($cve);
                echo '<hr><p><a href="https://web.nvd.nist.gov/view/vuln/detail?vulnId=' . $cve . '" target="_bank" id="' . $cve . '">' . $cve . '</a>' . PHP_EOL;
                $cve_results = $db->query("SELECT summary FROM nvd_db WHERE cveid = '$cve'");
                while ($cve_row = $cve_results->fetchArray()) {
                    echo '<div style="text-align:justify; width: 900px">' . $cve_row['summary'] . '</div>' . PHP_EOL;
                }
                /*
                echo '<ul><p>Nessus script name:</p>' . PHP_EOL;
                $cve_results = $db->query("SELECT nessus_script_name FROM map_cve_nessus WHERE cveid = '$cve'");
                while ($cve_row = $cve_results->fetchArray()) {
                    echo '<li><div style="text-align:justify; width: 900px">' . $cve_row['nessus_script_name'] . '</div></li>' . PHP_EOL;
                }
                echo '</ul>' . PHP_EOL;
                */
                echo '<ul><p>References:</p>' . PHP_EOL;
                $cve_results = $db->query("SELECT refname FROM cve_reference WHERE cveid = '$cve'");
                while ($cve_row = $cve_results->fetchArray()) {
                    echo '<li><a href="' . $cve_row['refname'] . '" target="_bank">' . $cve_row['refname'] . '</a></li>' . PHP_EOL;
                }
                echo '</ul></p>' . PHP_EOL;
            }
        } else {
            echo '<p>CVE list:</p>' . PHP_EOL;
            echo '<form method="post" action="?"><textarea name="cves" rows="30" cols="100"></textarea><p><input type="submit" name="submit" value="Submit"></p></form>' . PHP_EOL;
        }
        ?>
    </body>
</html>
