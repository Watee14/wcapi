<?php
/**
 *-----------------------------------------------------------------------
 * EDITOR 	: Apiwat Rodchuajeen (P'JANE)
 * CONTACT 	: -
 * DATE 	: 14-02-2018
 *-----------------------------------------------------------------------
 */
include('function.php');
$config=parse_ini_file('../config.ini',TRUE);
$req=$_REQUEST;
//print_r($config);
$url=$config['longdo']['getAddress']."?lat=".$req['lat']."&lon=".$req['lon']."&lang=th&key=".$config['longdo']['key'];
$result=curlService($url,'','GET');
print_r($result);



?>