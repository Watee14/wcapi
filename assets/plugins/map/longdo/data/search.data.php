<?php
/**
 *-----------------------------------------------------------------------
 * EDITOR 	: Apiwat Rodchuajeen (P'JANE)
 * CONTACT 	: -
 * DATE 	: 22-03-2018
 *-----------------------------------------------------------------------
 */
if(isset($_REQUEST['q'])){
	include('function.php');
	$config=parse_ini_file('../config.ini',TRUE);
	$req=$_REQUEST;
	$url=$config['longdo']['poiSearch']."?keyword=".$req['q'];
	$result=curlService($url,'','GET');
	print_r($result);
}