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

    /*.risk_None, .risk_Low, .risk_Medium, .risk_High, .risk_Critical {
        padding: 5px;
        margin-bottom: 1px;
    }*/

    /* ############### */
    .risk_None {
        background-color: #357abd;
        color: white;
    }
    .risk_None a:link, .risk_None a:visited, .risk_None a:active {
        color: white !important;
    }
    /* ############### */

    /* ############### */
    .risk_Low {
        background-color: #4cae4c;
        color: white;
    }
    .risk_Low a:link, .risk_Low a:visited, .risk_Low a:active {
        color: white !important;
    }
    /* ############### */

    .risk_Medium {
        background-color: #fdc431;
    }

    .risk_High {
        background-color: #ee9336;
    }

    /* ############### */
    .risk_Critical {
        background-color: #d43f3a;
        color: white;
    }
    .risk_Critical a:link, .risk_Critical a:visited, .risk_Critical a:active {
        color: white !important;
    }
    /* ############### */
</style>

<form action="?" method="post" enctype="multipart/form-data">
    Select *.nessus to upload: <input type="file" name="fileToUpload" id="fileToUpload"> <input type="submit" value="Upload" name="submit">
</form>
<?php
/*
 * nessus to dradis parser
 * by Adam Ziaja 2014
 */

//error_reporting(-1);
//ini_set('display_errors', 'On');
//date_default_timezone_set('Europe/Warsaw');

if (empty($_GET['i']) && empty($_GET['x'])) {
    foreach (scandir('/tmp', 1) as $file) {
        if (preg_match('/\.nessus$/i', $file)) {
            $file = str_replace('.nessus', '', $file);
            echo '<p><a href="?x=' . $file . '">' . $file . '</a></p>' . PHP_EOL;
        }
    }
}

/*
 * upload: poczatek
 */

if (isset($_POST["submit"])) {
    echo '<pre>';
    var_dump($_FILES["fileToUpload"]);
    echo '</pre>';
    $target_dir = "/tmp/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

// Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

// Check file size
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

// Allow certain file formats
    if ($imageFileType != "nessus") {
        echo "Sorry, only NESSUS files are allowed.";
        $uploadOk = 0;
    }

// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            $xml = explode('.nessus', basename($_FILES["fileToUpload"]["name"]))[0];
            echo '<p><a href="?x=' . $xml . '">' . $xml . '</a></p>';
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

/*
 * upload: koniec
 */
if (!empty($_GET['x'])) {
    $xml = $_GET['x'];
    $xml = str_replace('.nessus', '', $xml);
}

if (!empty($_GET['x']) && file_exists('/tmp/' . $xml . '.nessus')) {
    $nessus = simplexml_load_file('/tmp/' . $xml . '.nessus') or die(); // *.nessus
    $affected = '#[AffectedHosts]#' . PHP_EOL;
    $stack = array();
    $n = NULL;

    foreach ($nessus->Report[0]->ReportHost as $host) {
        foreach ($host->ReportItem as $bug) {
            if ($_GET['i'] == (string) $bug->plugin_name) {
                $output = NULL;

                $output .= '#[Title]#' . PHP_EOL;
                $output .= (string) $bug->plugin_name . PHP_EOL . PHP_EOL;

                $output .= '#[Tsaid]#' . PHP_EOL;
                preg_match('/^[0-9]*/', htmlspecialchars($_GET['x']), $matches);
                $output .= $matches[0] . '-XXXXX' . PHP_EOL . PHP_EOL;

                $output .= '#[CVSSv2]#' . PHP_EOL;
                if ((string) $bug->risk_factor != 'None') {
                    $calc_cvss = 'http://nvd.nist.gov/cvss.cfm?calculator&version=2&vector=(' . str_replace('CVSS2#', '', (string) $bug->cvss_vector) . ')';
                    $output .= '"' . (string) $bug->cvss_base_score . '":' . $calc_cvss . PHP_EOL . PHP_EOL;
                } else {
                    $output .= PHP_EOL . PHP_EOL;
                }

                $output .= '#[Risk]#' . PHP_EOL;
                $output .= (string) $bug->risk_factor . PHP_EOL . PHP_EOL;

                $output .= '#[Category]#' . PHP_EOL;
                $output .= 'XXXXX' . PHP_EOL . PHP_EOL;

                $output .= '#[AttackScenario]#' . PHP_EOL;
                $output .= PHP_EOL . PHP_EOL;

                $output .= '#[Description]#' . PHP_EOL;
                $output .= (string) $bug->description . PHP_EOL . PHP_EOL;

                $output .= '#[Recommendation]#' . PHP_EOL;
                $output .= (string) $bug->solution . PHP_EOL . PHP_EOL;

                $output .= '#[AffectedHosts]#' . PHP_EOL;
                $output .= (string) $host['name'] . ' (' . (string) $bug->attributes()->port . '/' . (string) $bug->attributes()->protocol . '/' . (string) $bug->attributes()->svc_name . ')' . PHP_EOL . PHP_EOL;

                $output .= '#[References]#' . PHP_EOL;
                $output .= (string) $bug->see_also . PHP_EOL . PHP_EOL;

                $output .= '#[Evidences]#' . PHP_EOL;
                $output .= (string) $bug->plugin_output . PHP_EOL . PHP_EOL;

                $affected_host = (string) $host['name'] . ' (' . (string) $bug->attributes()->port . '/' . (string) $bug->attributes()->protocol . '/' . (string) $bug->attributes()->svc_name . ')';
                $affected .= $affected_host . PHP_EOL;
                $n++;
                echo '<p>#' . $n . ' &mdash; ' . $affected_host . ' <a href="' . $calc_cvss . '" target="_blank">' . (string) $bug->cvss_vector . '</a></p>' . PHP_EOL;
                if (preg_match('/nessus/i', $output) || preg_match('/this script/i', $output) || preg_match('/plugin/i', $output)) { // alert
                    echo '<p><textarea rows="20" cols="150" style="background-color: #FFCCFF" readonly="readonly">' . PHP_EOL;
                } else {
                    echo '<p><textarea rows="20" cols="150" readonly="readonly">' . PHP_EOL;
                }
                //var_dump($bug);
                echo $output;
                echo '</textarea></p>' . PHP_EOL;

                preg_match_all('/CVE-\d\d\d\d-\d*/i', $bug->description . $bug->solution . $bug->see_also, $matches);
                $cves = array_unique($matches[0]);
                echo '<p><ol>';
                $cvelist = NULL;
                foreach ($cves as $cvecheck) {
                    echo '<li><a href="/cve.php?cve=' . $cvecheck . '" target="_blank">' . $cvecheck . '</a></li>' . PHP_EOL;
                    $cvelist .= $cvecheck . ',';
                }
                $cvelist = trim($cvelist, ',');
                echo '</ol></p>';
                echo '<p><a href="/cve.php?cve=' . $cvelist . '" target="_blank">All CVE\'s</a></p>' . PHP_EOL;

                echo '<p>(same as above)<br><textarea rows="7" cols="150" onclick="this.focus();this.select()" readonly="readonly">' . str_replace(',', ', ', $cvelist) . '</textarea></p>';

                echo '<p><button onclick="window.history.back()">Go Back</button></p>';
            } elseif (empty($_GET[i])) {
                $array = array("risk_factor" => (string) $bug->risk_factor,
                    "ip" => (string) $host['name'],
                    "port" => (int) $bug->attributes()->port,
                    "protocol" => (string) $bug->attributes()->protocol,
                    "svc_name" => (string) $bug->attributes()->svc_name,
                    "plugin_name" => (string) $bug->plugin_name,
                    "cvss_base_score" => (float) $bug->cvss_base_score,
                    "cvss_vector" => str_replace('CVSS2#', '', (string) $bug->cvss_vector)
                );
                array_push($stack, $array);
            }
        }
    }

    if (!empty($affected_host)) {
        echo '<p><textarea rows="20" cols="150" style="background-color: #E0E0E0" readonly="readonly" onclick="this.focus();this.select()">' . $affected . '</textarea></p>' . PHP_EOL;
    } else {
        // http://stackoverflow.com/a/17325584
        foreach ($stack as $row) {
            foreach ($row as $key => $value) {
                ${$key}[] = $value;
            }
        }
        //array_multisort($cvss_base_score, SORT_DESC, $ip, SORT_DESC, $stack);
        array_multisort($cvss_base_score, SORT_DESC, $plugin_name, SORT_DESC, $stack);

        echo '<table cellpadding="5">' . PHP_EOL;
        echo '<tr><th>#</th><th>Risk</th><th>IP</th><th>Port</th><th>Vulnerability</th><th>CVSS</th><th>CIA</th></tr>' . PHP_EOL;
        foreach ($stack as $info) {
            $n++;

            $cia = '</td><td  class="risk_' . $info['risk_factor'] . '">';
            $cvss = explode('/', $info['cvss_vector']);
            //var_dump($cvss);
            $cia_c = $cvss[3];
            if ((explode(':', $cia_c)[1] == 'P') || (explode(':', $cia_c)[1] == 'C')) {
                $cia .= 'C';
            }
            $cia_i = $cvss[4];
            if ((explode(':', $cia_i)[1] == 'P') || (explode(':', $cia_i)[1] == 'C')) {
                $cia .= 'I';
            }
            $cia_a = $cvss[5];
            if ((explode(':', $cia_a)[1] == 'P') || (explode(':', $cia_a)[1] == 'C')) {
                $cia .= 'A';
            }
            if ($cia == '</td><td  class="risk_' . $info['risk_factor'] . '">') {
                $cia = NULL;
            }

            //echo '<div class="risk_' . $info['risk_factor'] . '">#' . $n . ' &mdash; ' . $info['ip'] . ' (' . $info['port'] . '/' . $info['protocol'] . '/' . $info['svc_name'] . ') &mdash; <a href="?i=' . $info['plugin_name'] . '&x=' . $xml . '">' . $info['plugin_name'] . '</a> &mdash; <a href="' . 'http://nvd.nist.gov/cvss.cfm?calculator&version=2&vector=(' . $info['cvss_vector'] . ')' . '" target="_blank">CVSS ' . $info['cvss_base_score'] . '</a>' . $cia . '</div>' . PHP_EOL;
            echo '<tr><td  class="risk_' . $info['risk_factor'] . '">' . $n . '</td><td  class="risk_' . $info['risk_factor'] . '">' . $info['risk_factor'] . '</td><td  class="risk_' . $info['risk_factor'] . '">' . $info['ip'] . '</td><td  class="risk_' . $info['risk_factor'] . '">' . $info['port'] . '/' . $info['protocol'] . '/' . $info['svc_name'] . '</td><td  class="risk_' . $info['risk_factor'] . '"><a href="?i=' . $info['plugin_name'] . '&x=' . $xml . '">' . $info['plugin_name'] . '</a></td><td  class="risk_' . $info['risk_factor'] . '"><a href="' . 'http://nvd.nist.gov/cvss.cfm?calculator&version=2&vector=(' . $info['cvss_vector'] . ')' . '" target="_blank">' . $info['cvss_base_score'] . '</a>' . $cia . '</td></tr>' . PHP_EOL;
        }
        echo '</table>' . PHP_EOL;
        echo '<p><a href="ssl.php?x='. htmlspecialchars($_GET['x']) .'" target="_blank">SSL Multiple Vulnerabilities</a></p>' . PHP_EOL;
    }
} elseif (!empty($_GET['x'])) {
    if (!file_exists('/tmp/' . $xml . '.nessus')) {
        die('There is no file <b>' . htmlspecialchars($xml) . '</b>.');
    }
}
?>
