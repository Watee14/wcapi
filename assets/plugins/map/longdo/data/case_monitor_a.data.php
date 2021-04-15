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
$data['type'] = "1" ;
$data['color'] = $color[1] ;
$data['name'] = "AAAA" ;
$data['lat'] = '13.741202' ;
$data['lon'] = '100.598168' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "2" ;
$data['type'] = "2" ;
$data['color'] = $color[2] ;
$data['name'] = "BBBB" ;
$data['lat'] = '13.744432' ;
$data['lon'] = '100.462018' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;
 
$data['status'] = "3" ;
$data['type'] = "3" ;
$data['color'] = $color[3] ;
$data['name'] = "CCCC" ;
$data['lat'] = '13.730844' ;
$data['lon'] = '100.533612' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "1" ;
$data['type'] = "4" ;
$data['color'] = $color[4] ;
$data['name'] = "DDDD" ;
$data['lat'] = '13.734132' ;
$data['lon'] = '100.495793' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "2" ;
$data['type'] = "5" ;
$data['color'] = $color[5] ;
$data['name'] = "EEEE" ;
$data['lat'] = '13.780018' ;
$data['lon'] = '100.467333' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "3" ;
$data['type'] = "6" ;
$data['color'] = $color[6] ;
$data['name'] = "FFFF" ;
$data['lat'] = '13.73244' ;
$data['lon'] = '100.446338' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "2" ;
$data['type'] = "7" ;
$data['color'] = $color[7] ;
$data['name'] = "GGGG" ;
$data['lat'] = '13.723419' ;
$data['lon'] = '100.454894' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "2" ;
$data['type'] = "8" ;
$data['color'] = $color[8] ;
$data['name'] = "HHHH" ;
$data['lat'] = '13.718539' ;
$data['lon'] = '100.51511' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "1" ;
$data['type'] = "4" ;
$data['color'] = $color[4] ;
$data['name'] = "III" ;
$data['lat'] = '13.747971' ;
$data['lon'] = '100.412998' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "1" ;
$data['type'] = "5" ;
$data['color'] = $color[5] ;
$data['name'] = "JJJJ" ;
$data['lat'] = '13.868943' ;
$data['lon'] = '100.765176' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data['status'] = "3" ;
$data['type'] = "3" ;
$data['color'] = $color[3] ;
$data['name'] = "KKKK" ;
$data['lat'] = '13.661421' ;
$data['lon'] = '100.475853' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "XX" ;
$dx['description'] = "XX" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$ds['caselist'] = $ds;



$cat[] = '<font color="#ff4d4d">เบาะแสสถานบริการ</font>' ;
$cat[] = '<font color="#cc6600">เปรึกษาข้อกฏหมาย</font>' ;
$cat[] = '<font color="#1a75ff">เปล่อยเงินกู้และทวงหนี้นอกระบบ</font>' ;
$cat[] = '<font color="#00b300">เสอบถามสภาพการจราจร</font>' ;
$ds['description'] = $cat;

print_r(json_encode($ds)) ;

?>

