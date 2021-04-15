<?
$color[1] = '#008000';
$color[2] = '#e6e600';
$color[3] = '#00b300';
$color[4] = '#1affa3';
$color[5] = '#1a75ff';
$color[6] = '#ff99ff';
$color[7] = '#ff33cc';
$color[8] = '#800000';
$color[9] = '#cc6600';

$data['type'] = "mdu" ;
$data['plate'] = "ทะเบียน 1111" ; 
$data['status'] = "0" ;
$data['icon'] = "police_car.png" ; 
$data['user'] = "cop1" ;  
$data['lat'] = '13.854992055514487' ;
$data['lon'] = '100.46773958217823' ;
$ds[] = $data ;

$data['type'] = "mdu" ;
$data['plate'] = "ทะเบียน 2222" ; 
$data['status'] = "0" ;
$data['icon'] = "police_car.png" ; 
$data['user'] = "cop2" ;  
$data['lat'] = '13.786982034919845' ;
$data['lon'] = '100.62086153042041' ;
$ds[] = $data ;

$data['type'] = "mdu" ;
$data['plate'] = "ทะเบียน 3333" ; 
$data['status'] = "0" ;
$data['icon'] = "police_car.png" ; 
$data['user'] = "cop3" ;   
$data['lat'] = '13.760306024692781' ;
$data['lon'] = '100.46773958217823' ;
$ds[] = $data ;

$data['type'] = "mdu" ;
$data['plate'] = "ทะเบียน 4444" ; 
$data['status'] = "0" ;
$data['icon'] = "police_car.png" ; 
$data['user'] = "cop4" ;   
$data['lat'] = '13.776674073023665' ;
$data['lon'] = '100.54515972733498' ;
$ds[] = $data ;
 
 
//$data['data'] = $ds ;
//$ds[] = $data ;


print_r(json_encode($ds)) ;

?>

