<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <script type="text/javascript" src="http://tablesorter.com/jquery-latest.js"></script>
        <script type="text/javascript" src="http://tablesorter.com/__jquery.tablesorter.js"></script>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                $("#myTable").tablesorter();
            }
            );
        </script>
        <style type="text/css">
            body {
                font-family: Verdana, Geneva, sans-serif;
                font-size: 11px;
                color: black;
            }
            table {
                border-collapse: collapse;
            }
            table, td, th {
                border: 1px solid black;
                text-align: right;
            }
        </style>
    </head>
    <body>
        <?php
        // (C) 2014 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com

        $apikey = '?locale=pl_PL&apikey=XXXXXXXXXX'; // https://dev.battle.net/apps/mykeys

        $array = json_decode(file_get_contents("https://eu.api.battle.net/d3/profile/adam-21870/$apikey"), true);

        echo $array['battleTag'] . PHP_EOL;

        $i = NULL;
        echo '<table id="myTable" class="tablesorter">';
        foreach ($array['heroes'] as $player) {
            $hero = json_decode(file_get_contents('https://eu.api.battle.net/d3/profile/adam-21870/hero/' . $player['id'] . $apikey), true);
            $stats = $hero['stats'];
            array_unshift($stats, $player['name'] . '&nbsp;' . $player['class'] . '&nbsp;' . $hero['level'] . '&nbsp;(' . $hero['paragonLevel'] . ')<br>' . gmdate("Y-m-d H:i:s", $player['last-updated']));
            //var_dump($stats);
            if (is_null($i)) {
                echo '<thead><tr>';
                foreach ($stats as $key => $value) {
                    echo '<th>' . $key . '</th>';
                }
                echo '</tr></thead><tbody>';
            }
            echo '<tr>';
            foreach ($stats as $value) {
                echo '<td>' . $value . '</td>';
            }
            echo '</tr>';
            echo PHP_EOL;
            $i++;
        }
        echo '</tbody></table>';
        ?>
    </body>
</html>
