<?

$data['status'] = "0" ;
$data['type'] = "case" ;
$data['icon'] = "marker.png" ; 
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
$data['lat'] = '13.771672332381582' ;
$data['lon'] = '100.4363264143467' ;
$data['priority'] = 1 ;
$ds['routing']['from'] = $data ;
 

 
print_r(json_encode($ds) ) ;
 
 

 