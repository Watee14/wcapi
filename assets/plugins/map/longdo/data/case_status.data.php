<?

$data = '';
$data['caseId'] = "1" ;
$data['status'] = "0" ;
$data['type'] = "case" ;
$data['icon'] = "check_icon.png" ;
$data['name'] = "AAAA" ;
$data['lat'] = '13.741202' ;
$data['lon'] = '100.598168' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data = '';
$data['caseId'] = "2" ;
$data['status'] = "2" ;
$data['type'] = "case" ;
$data['icon'] = "warning_icon.png" ;
$data['name'] = "BBBB" ;
$data['lat'] = '13.744432' ;
$data['lon'] = '100.462018' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;
 
$data = '';
$data['caseId'] = "3" ;
$data['status'] = "3" ;
$data['type'] = "case" ;
$data['icon'] = "error_icon.png" ;
$data['name'] = "CCCC" ;
$data['lat'] = '13.730844' ;
$data['lon'] = '100.533612' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data = '';
$data['caseId'] = "4" ;
$data['status'] = "4" ;
$data['type'] = "case" ;
$data['icon'] = "info_icon.png" ;
$data['name'] = "DDDD" ;
$data['lat'] = '13.734132' ;
$data['lon'] = '100.495793' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;
 
$data = '';
$data['caseId'] = "5" ;
$data['status'] = "4" ;
$data['type'] = "case" ;
$data['icon'] = "info_icon.png" ;
$data['name'] = "EEEE" ;
$data['lat'] = '13.780018' ;
$data['lon'] = '100.467333' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data = '';
$data['caseId'] = "6" ;
$data['status'] = "3" ;
$data['type'] = "case" ;
$data['icon'] = "error_icon.png" ;
$data['name'] = "FFFF" ;
$data['lat'] = '13.73244' ;
$data['lon'] = '100.446338' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;
 
$data = '';
$data['caseId'] = "7" ;
$data['status'] = "2" ;
$data['type'] = "case" ;
$data['icon'] = "warning_icon.png" ;
$data['name'] = "GGGG" ;
$data['lat'] = '13.723419' ;
$data['lon'] = '100.454894' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data = '';
$data['caseId'] = "8" ;
$data['status'] = "2" ;
$data['type'] = "case" ;
$data['icon'] = "warning_icon.png" ;
$data['name'] = "HHHH" ;
$data['lat'] = '13.718539' ;
$data['lon'] = '100.51511' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data = '';
$data['caseId'] = "9" ;
$data['status'] = "4" ;
$data['type'] = "case" ;
$data['icon'] = "info_icon.png" ;
$data['name'] = "III" ;
$data['lat'] = '13.747971' ;
$data['lon'] = '100.412998' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data = '';
$data['caseId'] = "10" ;
$data['status'] = "4" ;
$data['type'] = "case" ;
$data['icon'] = "info_icon.png" ;
$data['name'] = "JJJJ" ;
$data['lat'] = '13.868943' ;
$data['lon'] = '100.765176' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;

$data = '';
$data['caseId'] = "11" ;
$data['status'] = "3" ;
$data['type'] = "case" ;
$data['icon'] = "error_icon.png" ;
$data['name'] = "KKKK" ;
$data['lat'] = '13.661421' ;
$data['lon'] = '100.475853' ;
$dx['name'] = $data['name'] ;
$dx['address'] = "Address 123" ;
$dx['description'] = "Detail xxx" ;
$dx['owner'] = "XX" ;
$data['detail'] = $dx ;
$ds[] = $data ;
 

$cat[] = "เบาะแสสถานบริการ" ;
$cat[] = "ปรึกษาข้อกฏหมาย" ;
$cat[] = "ปล่อยเงินกู้และทวงหนี้นอกระบบ" ;
$cat[] = "สอบถามสภาพการจราจร" ;

$dsx['description'] = $cat;
$dsx['caselist'] = $ds ;
print_r(json_encode($dsx)) ;

?>

