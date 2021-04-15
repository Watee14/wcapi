<?

$color['red']['bg'] = '#ff8080';
$color['red']['icon'] = '#ff1a1a';
$color['red']['title'] = 'มาก';
$color['yellow']['bg'] = '#ffff66';
$color['yellow']['icon'] = '#ffff00';
$color['yellow']['title'] = 'ปานกลาง';
$color['green']['bg'] = '#adebad';
$color['green']['icon'] = '#33cc33';
$color['green']['title'] = 'น้อย';
$ds_x['color'] = $color ;

$dx_2 = '';
$data['areaid'] = "1" ;
$data['name'] = "AAAA" ;
$data['color'] = $color['red'] ;
$data['total'] = "100" ;
$dx['casetype'] = 'เบาะแสสถานบริการ' ;
$dx['total'] = "70" ;
$dx_2[] =  $dx ;
$dx['casetype'] = 'เสอบถามสภาพการจราจร' ;
$dx['total'] = "30" ;
$dx_2[] =  $dx ;
$data['data'] = $dx_2 ;
$ds[] = $data ;

$dx_2 = '';
$data['areaid'] = "2" ;
$data['name'] = "BBBB" ;
$data['color'] = $color['red'] ;
$data['total'] = "150" ;
$dx['casetype'] = 'เปรึกษาข้อกฏหมาย' ;
$dx['total'] = "60" ;
$dx_2[] =  $dx ;
$dx['casetype'] = 'เปล่อยเงินกู้และทวงหนี้นอกระบบ' ;
$dx['total'] = "20" ;
$dx_2[] =  $dx ;
$dx['casetype'] = 'เบาะแสสถานบริการ' ;
$dx['total'] = "70" ;
$dx_2[] =  $dx ;
$data['data'] = $dx_2 ;
$ds[] = $data ;

$dx_2 = '';
$data['areaid'] = "63" ;
$data['name'] = "CCCC" ;
$data['color'] = $color['green'] ;
$data['total'] = "17" ;
$dx['casetype'] = 'เปรึกษาข้อกฏหมาย' ;
$dx['total'] = "5" ;
$dx_2[] =  $dx ;
$dx['casetype'] = 'เปล่อยเงินกู้และทวงหนี้นอกระบบ' ;
$dx['total'] = "5" ;
$dx_2[] =  $dx ;
$dx['casetype'] = 'เบาะแสสถานบริการ' ;
$dx['total'] = "7" ;
$dx_2[] =  $dx ;
$data['data'] = $dx_2 ;
$ds[] = $data ;

$dx_2 = '';
$data['areaid'] = "61" ;
$data['name'] = "DDDD" ;
$data['color'] = $color['yellow'] ;
$data['total'] = "70" ;
$dx['casetype'] = 'เปรึกษาข้อกฏหมาย' ;
$dx['total'] = "50" ;
$dx_2[] =  $dx ;
$dx['casetype'] = 'เปล่อยเงินกู้และทวงหนี้นอกระบบ' ;
$dx['total'] = "20" ;
$dx_2[] =  $dx ; 
$data['data'] = $dx_2 ;
$ds[] = $data ;

$ds_x['caselist'] = $ds;


$cat[] = 'เบาะแสสถานบริการ' ;
$cat[] = 'เปรึกษาข้อกฏหมาย' ;
$cat[] = 'เปล่อยเงินกู้และทวงหนี้นอกระบบ</font>' ;
$cat[] = 'เสอบถามสภาพการจราจร' ;
$ds_x['description'] = $cat;

print_r(json_encode($ds_x)) ;

?>

