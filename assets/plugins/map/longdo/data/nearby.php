<?php
/**
 *-----------------------------------------------------------------------
 * EDITOR 	: Apiwat Rodchuajeen (P'JANE)
 * CONTACT 	: -
 * DATE 	: 14-02-2018
 *-----------------------------------------------------------------------
 */
error_reporting(E_ERROR);
include('function.php');
$config=parse_ini_file('../config.ini',TRUE);
$req=$_REQUEST;
$url=$config['longdo']['poiService']."?span=0.01&zoom=15&lon=".$req['lon']."&lat=".$req['lat']."&limit=".$config['longdo']['limit']."&locale=".$config['longdo']['locale']."&key=".$config['longdo']['key'];
$result=curlService($url,'','GET');
$result=json_decode($result,TRUE);
foreach($result['data'] as $key=>$value){
	foreach($value['tag'] as $k=>$y){
  		if($y=='ตำรวจ'||$y=='police')
    		$res[$value['id']]=$value;
 	}
}
foreach($res as $key=>$value)
 	$resx[]=$value;
$result['data']=$resx;
print_r(json_encode($result));
?>