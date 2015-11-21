<!DOCTYPE html>
<html>
    <head>
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
    </head>
    <body>
        <?php
        /*
         * nessus (ssl findings) to dradis parser
         * by Adam Ziaja 2014-2015
         */

        //error_reporting(-1);
        //ini_set('display_errors', 'On');
        //date_default_timezone_set('Europe/London');

        if (empty($_GET['i']) && empty($_GET['x'])) {
            foreach (scandir('/tmp', 1) as $file) {
                if (preg_match('/\.nessus$/i', $file)) {
                    $file = str_replace('.nessus', '', $file);
                    echo '<p><a href="?x=' . $file . '">' . $file . '</a></p>' . PHP_EOL;
                }
            }
        }

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

                $sslmulti = array();

                //echo '<table cellpadding="5">' . PHP_EOL;
                //echo '<tr><th>#</th><th>Risk</th><th>IP</th><th>Port</th><th>Vulnerability</th><th>CVSS</th><th>CIA</th></tr>' . PHP_EOL;
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

                    //echo '<tr><td  class="risk_' . $info['risk_factor'] . '">' . $n . '</td><td  class="risk_' . $info['risk_factor'] . '">' . $info['risk_factor'] . '</td><td  class="risk_' . $info['risk_factor'] . '">' . $info['ip'] . '</td><td  class="risk_' . $info['risk_factor'] . '">' . $info['port'] . '/' . $info['protocol'] . '/' . $info['svc_name'] . '</td><td  class="risk_' . $info['risk_factor'] . '"><a href="?i=' . $info['plugin_name'] . '&x=' . $xml . '">' . $info['plugin_name'] . '</a></td><td  class="risk_' . $info['risk_factor'] . '"><a href="' . 'http://nvd.nist.gov/cvss.cfm?calculator&version=2&vector=(' . $info['cvss_vector'] . ')' . '" target="_blank">' . $info['cvss_base_score'] . '</a>' . $cia . '</td></tr>' . PHP_EOL;

                    if (preg_match('/^ssl|^tls/i', $info['plugin_name'])) {
                        $plugin_name = $info['plugin_name'];
                        if ($plugin_name == "SSL Version 2 and 3 Protocol Detection") {
                            $plugin_name = "SSL Protocol Detection";
                            //var_dump($plugin_name);
                        }
                        if ($info['cvss_base_score'] > 0) {
                            if (htmlspecialchars($_GET['y']) == 1) {
                                array_push($sslmulti, array($plugin_name, $info['port'] . '/' . $info['protocol']));
                            } else {
                                array_push($sslmulti, array($plugin_name, $info['ip'] . ':' . $info['port']));
                            }
                        }
                    }
                }
                //echo '</table>' . PHP_EOL;

                if (count($sslmulti) >= 0) {

                    $ssldescinfo = array(
                        "SSL Self-Signed Certificate" => "The X.509 certificate chain for this service is not signed by a recognized certificate authority.",
                        "SSL Certificate Cannot Be Trusted" => "The X.509 certificate chain for this service is not signed by a recognized certificate authority.",
                        "SSL Protocol Detection" => "The remote service accepts connections encrypted using SSL, which have several cryptographic flaws.",
                        "SSL Certificate with Wrong Hostname" => "The commonName (CN) of the SSL certificate presented on this service is for a different machine.",
                        "TLS Padding Oracle Information Disclosure Vulnerability (TLS POODLE)" => "The remote host is affected by a man-in-the-middle (MitM) information disclosure vulnerability known as POODLE.",
                        "SSLv3 Padding Oracle On Downgraded Legacy Encryption Vulnerability (POODLE)" => "The remote host is affected by a man-in-the-middle (MitM) information disclosure vulnerability known as POODLE.",
                        "SSL RC4 Cipher Suites Supported" => "The RC4 cipher is flawed in its generation of a pseudo-random stream of bytes so that a wide variety of small biases are introduced into the stream, decreasing its randomness.",
                    );

                    $sslrecominfo = array(
                        "SSL Self-Signed Certificate" => "Purchase or generate a proper certificate for this service.",
                        "SSL Protocol Detection" => "Disable SSL. It is recommended to use TLS 1.2 instead.",
                        "TLS Padding Oracle Information Disclosure Vulnerability (TLS POODLE)" => "TLS POODLE - Contact the vendor for an update.",
                        "SSL RC4 Cipher Suites Supported" => "Reconfigure the affected application, if possible, to avoid use of RC4 ciphers.",
                    );

                    $sslrefinfo = array(
                        "SSL Certificate Cannot Be Trusted" => "https://www.ssllabs.com/downloads/SSL_TLS_Deployment_Best_Practices.pdf",
                        "SSL Protocol Detection" => "http://www.schneier.com/paper-ssl.pdf\nhttps://www.pcisecuritystandards.org/pdfs/15_02_12_PCI_SSC_Bulletin_on_DSS_revisions_SSL_update.pdf",
                        "TLS Padding Oracle Information Disclosure Vulnerability (TLS POODLE)" => "https://www.imperialviolet.org/2014/12/08/poodleagain.html",
                        "SSLv3 Padding Oracle On Downgraded Legacy Encryption Vulnerability (POODLE)" => "https://www.openssl.org/~bodo/ssl-poodle.pdf",
                        "SSL RC4 Cipher Suites Supported" => "http://www.imperva.com/docs/HII_Attacking_SSL_when_using_RC4.pdf",
                    );

                    //echo '<p>SSL Multiple Vulnerabilities: <!--<button id="hide">Hide</button> --><button id="show">Show</button><p>';
                    echo '<p><strong>' . htmlspecialchars($_GET['x']) . '</strong></p>';
                    if (is_numeric($_GET['y'])) {
                        if ($_GET['y'] == 1) {
                            echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '&y=0">few ip</a></p>';
                        } else {
                            echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '&y=1">one ip</a></p>';
                        }
                    } else {
                        echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '&y=0">few ip</a>; <a href="' . $_SERVER['REQUEST_URI'] . '&y=1">one ip</a></p>';
                    }
                    //print_r($_SERVER);
                    echo '<p><textarea rows="62" cols="180" readonly="readonly">' . PHP_EOL;
                    //echo '<pre>';
                    //var_dump($sslmulti);
                    $ssllast = NULL;
                    $ssldesc = NULL;
                    $sslaff = NULL;
                    foreach ($sslmulti as &$ssl) {
                        //var_dump($ssl);
                        if ($ssllast == $ssl[0]) {
                            if (htmlspecialchars($_GET['y']) == 1) {
                                $sslaff .= $ssl[1] . ' ';
                            } else {
                                $sslaff .= $ssl[1] . PHP_EOL;
                            }
                        } else {
                            if (is_null($ssllast)) {
                                $sslaff .= "#* " . $ssl[0] . PHP_EOL;
                            } else {
                                $sslaff .= PHP_EOL . "#* " . $ssl[0] . PHP_EOL;
                            }
                            if ($ssldescinfo[$ssl[0]]) {
                                $ssldesc .= "#* " . $ssl[0] . " - " . $ssldescinfo[$ssl[0]] . PHP_EOL;
                            } else {
                                $ssldesc .= "#* " . $ssl[0] . " - *TODO* *TODO* *TODO* *TODO* *TODO*" . PHP_EOL;
                            }
                            if ($sslrecominfo[$ssl[0]]) {
                                $sslrecom .= "#* " . $sslrecominfo[$ssl[0]] . PHP_EOL;
                            }
                            if ($sslrefinfo[$ssl[0]]) {
                                $sslref .= $sslrefinfo[$ssl[0]] . PHP_EOL;
                            }
                            if (htmlspecialchars($_GET['y']) == 1) {
                                $sslaff .= $ssl[1] . ' ';
                            } else {
                                $sslaff .= $ssl[1] . PHP_EOL;
                            }
                        }
                        $ssllast = $ssl[0];
                    }
                    //var_dump($ssldesc);
                    $sslfullinfo = NULL;
                    $sslfullinfo .= '#[Title]#' . PHP_EOL;
                    $sslfullinfo .= 'SSL Multiple Vulnerabilities' . PHP_EOL . PHP_EOL;

                    $sslfullinfo .= '#[Tsaid]#' . PHP_EOL;
                    preg_match('/^[0-9]*/', htmlspecialchars($_GET['x']), $matches);
                    $sslfullinfo .= $matches[0] . '-XXXXX' . PHP_EOL . PHP_EOL;

                    $sslfullinfo .= '#[CVSSv2]#' . PHP_EOL;
                    $sslfullinfo .= '"6.4":http://nvd.nist.gov/cvss.cfm?calculator&version=2&vector=(AV:N/AC:L/Au:N/C:P/I:P/A:N)' . PHP_EOL . PHP_EOL;

                    $sslfullinfo .= '#[Risk]#' . PHP_EOL;
                    $sslfullinfo .= 'Medium' . PHP_EOL . PHP_EOL;

                    $sslfullinfo .= '#[Category]#' . PHP_EOL;
                    $sslfullinfo .= 'XXXXX' . PHP_EOL . PHP_EOL;

                    $sslfullinfo .= '#[AttackScenario]#' . PHP_EOL;
                    $sslfullinfo .= 'An attacker may be able to perform a man-in-the-midle attack and decrypt communications between clients and affected service.' . PHP_EOL . PHP_EOL;

                    $sslfullinfo .= '#[Description]#' . PHP_EOL;
                    $sslfullinfo .= $ssldesc . PHP_EOL;

                    $sslfullinfo .= '#[Recommendation]#' . PHP_EOL;
                    $sslfullinfo .= $sslrecom . PHP_EOL;

                    $sslfullinfo .= '#[AffectedHosts]#' . PHP_EOL;
                    $sslfullinfo .= $sslaff . PHP_EOL . PHP_EOL;

                    $sslfullinfo .= '#[References]#' . PHP_EOL;
                    $sslfullinfo .= $sslref . PHP_EOL;

                    $sslfullinfo .= '#[Evidences]#' . PHP_EOL . PHP_EOL;

                    echo $sslfullinfo;
                    //echo '</pre>';
                    echo '</textarea></p>' . PHP_EOL;
                }
            }
        } elseif (!empty($_GET['x'])) {
            if (!file_exists('/tmp/' . $xml . '.nessus')) {
                die('There is no file <b>' . htmlspecialchars($xml) . '</b>.');
            }
        }
        ?>
    </body>
</html>
