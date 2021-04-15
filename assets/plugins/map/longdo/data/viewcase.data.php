<?
$color[1] = '#ff4d4d';
$color[2] = '#e6e600';
$color[3] = '#00b300';
$color[4] = '#1affa3';
$color[5] = '#1a75ff';
$color[6] = '#ff99ff';
$color[7] = '#ff33cc';
$color[8] = '#800000';
$color[9] = '#cc6600';

 
$data['status'] = "0" ;
$data['type'] = "case" ;
$data['icon'] = "marker.png" ;
$data['color'] = $color[1] ;
$data['name'] = "AAAA" ;
$data['lat'] = '13.741202' ;
$data['lon'] = '100.598168' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
//$ds[] = $data ;


print_r(json_encode($data)) ;

?>

