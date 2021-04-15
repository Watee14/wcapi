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
$ds['routing']['to'] = $data ;

$data="";
$data['type'] = "mdu" ;
$data['plate'] = "ทะเบียน 3333" ; 
$data['status'] = "0" ;
$data['icon'] = "police_car.png" ; 
$data['user'] = "cop3" ;   
$data['lat'] = '13.648929529952412' ;
$data['lon'] = '100.50190106034279' ;
$data['priority'] = 1 ;
$ds['routing']['from'] = $data ;
 


$data="";
$data['type'] = "mdu" ;
$data['plate'] = "ทะเบียน 1111" ; 
$data['status'] = "0" ;
$data['icon'] = "police_car.png" ; 
$data['user'] = "cop1" ;  
$data['lat'] = '13.854992055514487' ;
$data['lon'] = '100.46773958217823' ;
$data['priority'] = 0 ;
$ds['mdu'][] = $data ;

$data="";
$data['type'] = "mdu" ;
$data['plate'] = "ทะเบียน 2222" ; 
$data['status'] = "0" ;
$data['icon'] = "police_car.png" ; 
$data['user'] = "cop2" ;  
$data['lat'] = '13.786982034919845' ;
$data['lon'] = '100.62086153042041' ;
$data['priority'] = 1 ;
$ds['mdu'][] = $data ;




 
print_r(json_encode($ds) ) ;
 
 

 