<?php
function wt_add_field($arr=array()){
	return get_instance()->dbforge->add_field($arr);
}
function wt_add_key($keys='id'){
	return get_instance()->dbforge->add_key($keys,TRUE);
}
function wt_assets_url($file_name='',$path='css'){
	return base_url().'assets/'.$path.'/'.$file_name;
}
function wt_base64_coding($options=0,$str=''){
	if($options==1)
		return base64_decode($str);
	elseif($options==2)
		return base64_encode($str);
	else
		return $str;
}
function wt_base64_encrypt($str,$password='wera'){
	$rand_str='';
	$str=base64_encode(($password?substr(md5($password),0,16):'').$str);
	$bundle_str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$split_str=str_split('+/='.$bundle_str);
	$reverse_str=strrev('-_='.$bundle_str);
	if($password)
		$reverse_str=wt_mixing_password($reverse_str,$password);
	else{
		$rand_str=rand(10,65);
		$reverse_str=mb_substr($reverse_str,$rand_str).mb_substr($reverse_str,0,$rand_str);
	}
	$result='';
	$reverse_str=str_split($reverse_str);
	$str=str_split($str);
	for($i=0;$i<count($str);$i++){
		for($j=0;$j<count($split_str);$j++){
			if($str[$i]==$split_str[$j])
				$result.=$reverse_str[$j];
		}
	}
	return $result.$rand_str;
}
function wt_base64_decrypt($str,$password='wera'){
	$bundle_str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$split_str=str_split('+/='.$bundle_str);
	$reverse_str=strrev('-_='.$bundle_str);
	if($password)
		$reverse_str=wt_mixing_password($reverse_str,$password);
	else{
		$sub_str=mb_substr($str,-2);
		$str=mb_substr($str,0,-2);
		$reverse_str=mb_substr($reverse_str,$sub_str).mb_substr($reverse_str,0,$sub_str);
	}
	$result='';
	$reverse_str=str_split($reverse_str);
	$str=str_split($str);
	for($i=0;$i<count($str);$i++){
		for($j=0;$j<count($reverse_str);$j++){
			if($str[$i]==$reverse_str[$j])
				$result.=$split_str[$j];
		}
	}
	$result=base64_decode($result);
	return ($password&&substr($result,0,16)==substr(md5($password),0,16)?substr($result,16):$result);
}
function casttmp_status($id){
	$js=file_get_contents("../casetmp/".$id.'.json');
	$js=json_decode($js,TRUE);
	if($js='')
		$js=array();
	$js['modify_date']=time();
	file_put_contents("../casetmp/".$id.'.json',json_encode($js));
}
function wt_checkdate($str){
	if($str==''||$str===NULL)
		return FALSE;
	else{
		$arr=explode('-',$str);
		if(count($arr)===3){
			if(checkdate($arr[1],$arr[2],$arr[0]))
				return TRUE;
			else
				return FALSE;
		}else
			return FALSE;
	}
}
function wt_create_table($table){
	return get_instance()->dbforge->create_table($table,FALSE,wt_db_engine());
}
function wt_create_thumbnail($_path,$_name,$_mime,$_width,$_height,$_destination){
	if($_mime['mime']=='image/gif')
		$src_img=imagecreatefromgif($_path);
	elseif($_mime['mime']=='image/jpg'||$_mime['mime']=='image/jpeg'||$_mime['mime']=='image/pjpeg')
		$src_img=imagecreatefromjpeg($_path);
	elseif($_mime['mime']=='image/png')
		$src_img=imagecreatefrompng($_path);
	else
		$src_img=imagecreatefromjpeg($_path);
	$old_x=imageSX($src_img);
	$old_y=imageSY($src_img);
	$thumbs=wt_scale_thumbnail($old_x,$old_y,$_width,$_height);
	$dst_img=ImageCreateTrueColor($thumbs['width'],$thumbs['height']);
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumbs['width'],$thumbs['height'],$old_x,$old_y);
	$new_thumb_loc=$_destination.$_name;
	if($_mime['mime']=='image/gif')
		$result = imagegif($dst_img, $new_thumb_loc);
	elseif($_mime['mime']=='image/jpg'||$_mime['mime']=='image/jpeg'||$_mime['mime']=='image/pjpeg')
		$result=imagejpeg($dst_img,$new_thumb_loc,80);
	elseif($_mime['mime']=='image/png')
		$result=imagepng($dst_img,$new_thumb_loc,8);
	else
		$result=imagejpeg($dst_img,$new_thumb_loc,80);
	imagedestroy($dst_img);
	imagedestroy($src_img);
	return ($result!==FALSE?TRUE:FALSE);
}
function wt_curl_exec($url,$data=array()){
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	if(!empty($data))
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	$result=curl_exec($ch);
	curl_close($ch);
	return $result;
}
function wt_datetime_change($str,$options=NULL,$types=NULL){
	if($str=='')
		return '';
	else{
		$datetime=explode(' ',$str);
		if ($options==1){
			$date=explode('-',$datetime[0]);
			$datetime=explode('.',$datetime[1]);
			return $date[2].'/'.$date[1].'/'.$date[0].' '.$datetime[0];
		}
		elseif ($options==2){
			$date=explode('-',$datetime[0]);
			$datetime=explode('.',$datetime[1]);
			return $date[2].'/'.$date[1].'/'.($date[0]+543).' '.$datetime[0];
		}
		elseif ($options==3){
			$date=explode('-',$datetime[0]);
			$datetime=explode('.',$datetime[1]);
			$datetime=explode(':',$datetime[0]);
			return $date[0].$date[1].$date[2].($types===NULL?$datetime[0].$datetime[1].$datetime[2]:'');
		}
		else{
			$date=explode('/',$datetime[0]);
			return ($date[2]-543).'-'.$date[1].'-'.$date[0].' '.$datetime[1];
		}
	}
}
function wt_day_of_week_generator(){
	$timestamp=strtotime('next Sunday');
	$days=array();
	for($i=0;$i<7;$i++){
		$days[]=strftime('%A',$timestamp);
		$timestamp=strtotime('+1 day',$timestamp);
	}
	return $days;
}
function wt_db_engine($db_engine='InnoDB'){
	return array('ENGINE'=>$db_engine);
}
function wt_db_force(){
	return get_instance()->load->dbforge();
}
function wt_db_loader($db__name=''){
	return ($db__name!=''?get_instance()->load->database($db__name,TRUE):get_instance()->db);
}
function wt_decimal_to_time($dec){
	if(gettype($dec)=='integer'||gettype($dec)=='double'){
		$dt1=new DateTime("@0");
		$dt2=new DateTime("@$dec");
		return $dt1->diff($dt2)->format('%a วัน %h ชั่วโมง %i นาที %s วินาที');
	}
	else
		return '';
}
function aes_decrypt($str=''){
	$aes=new AES($str,AES_KEYS,AES_SIZE);
	return $aes->decrypt();
}
function aes_encrypt($str=''){
	$aes=new AES($str,AES_KEYS,AES_SIZE);
	return $aes->encrypt();
}
function wt_files_mapping_unset($arr=array(),$unset_arr=array()){
	foreach($arr as$keys=>$values){
		foreach((isset($unset_arr)&&!empty($unset_arr)?$unset_arr:wt_index_file_list()) as$k=>$val){
			if(($key=array_search($val,$values))!==FALSE){
				if(is_array($arr[$keys]))
					unset($arr[$keys][$key]);
				else{
					if(in_array($values,wt_index_file_list()))
						unset($arr[$keys]);
				}
			}
		}
	}
	return $arr;
}
function my_generate_random_string($length=10){
	$characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength=strlen($characters);
	$randomString='';
	for($i=0;$i<$length;$i++){
		$randomString.=$characters[rand(0,$charactersLength-1)];
	}
	return $randomString;
}
function wt_get_session($sess_name=''){
	return get_instance()->session->userdata($sess_name);
}
function wt_get_flash_session($sess_name=''){
	return get_instance()->session->flashdata($sess_name);
}
function wt_has_input(){
	if(!isset(get_instance()->input)||empty(get_instance()->input)){
		redirect(base_url(),'refresh');
		exit();
	}
}
function wt_has_session($sess_name=''){
	return get_instance()->session->has_userdata($sess_name);
}
function wt_htmlspecialchar_remove($str){
	return ($str!=''?trim(preg_replace('/ +/',' ',preg_replace('/[^A-Za-z0-9ก-๙, ]/',' ',urldecode(html_entity_decode(strip_tags($str)))))):'');
}
function wt_index_file_list(){
	return array('index.html','index.php');
}
function wt_image_encode($path){
	return 'data:image/'.wt_path_info($path,2).';base64,'.wt_base64_coding(2,wt_curl_exec($path));
}
function wt_ini_get_all($options=0){
	if($options==0)
		dieArray(ini_get_all());
	else
		wt_print_r(ini_get_all());
}
function wt_is_admin(){
	if(wt_is_logged_in()===FALSE){
		redirect(WT_IS_LOCAL===FALSE?WT_PORTAL_URL:'../'.WT_PORTAL_URL,'refresh');
		exit();
	}
}
function wt_is_installed(){
	if(get_instance()->setup_config_model->is_installed()===TRUE){
		redirect(base_url(),'refresh');
		exit();
	}
}
function wt_is_not_install(){
	if(get_instance()->setup_config_model->is_installed()===FALSE){
		redirect(base_url(),'refresh');
		exit();
	}
}
function wt_is_logged_in(){
	return (getSession('subdomain')==WT_SUBDOMAIN&&getSession('subdomain_url')==WT_SUBDOMAIN_URL&&getSession('logged_in')==1?TRUE:FALSE);
}
function wt_list_merge($arr=array(),$key=0){
	$result=array();
	foreach($arr as$value){
		if(is_object($value))
			array_push($result,$value->$key);
		elseif(is_array($value))
			array_push($result,$value[$key]);
	}
	return $result;
}
function wt_mixing_password($reverse_str,$password){
	$str='';
	$replace_str=$reverse_str;
	$reverse_str=str_split($reverse_str);
	$password=str_split(sha1($password));
	for($i=0;$i<count($password);$i++){
		for($j=0;$j<count($reverse_str);$j++){
			if($password[$i]==$reverse_str[$j]){
				$replace_str=str_replace($reverse_str[$j],'',$replace_str);
				if(!preg_match('/'.$reverse_str[$j].'/',$str))
					$str.=$reverse_str[$j];
			}
		}
	}
	return $replace_str.$str;
}
function wt_path_info($path,$key=0){
	return pathinfo($path,unserialize(WT_PATH_INFO)[$key]);
}
function wt_print_r($arr=''){
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}
function wt_scale_thumbnail($old_x,$old_y,$_width,$_height){
	if($old_x>$old_y){
		$thumb_w=$_width;
		$thumb_h=(($_width/$old_x)*$old_y);
	}elseif($old_x<$old_y){
		$thumb_w=(($_height/$old_y)*$old_x);
		$thumb_h=$_height;
	}elseif($old_x==$old_y){
		$thumb_w=$_width;
		$thumb_h=$_height;
	}
	return array('width'=>$thumb_w,'height'=>$thumb_h);
}
function wt_sec_to_time($seconds){
	$inputSeconds=$seconds;
	$secondsInAMinute=60;
	$secondsInAnHour=60*$secondsInAMinute;
	$secondsInADay=24*$secondsInAnHour;
	$days=floor($inputSeconds/$secondsInADay);
	$hourSeconds=$inputSeconds%$secondsInADay;
	$hours=floor($hourSeconds/$secondsInAnHour);
	$minuteSeconds=$hourSeconds%$secondsInAnHour;
	$minutes=floor($minuteSeconds/$secondsInAMinute);
	$remainingSeconds=$minuteSeconds%$secondsInAMinute;
	$seconds=ceil($remainingSeconds);
	$obj=array('d'=>(int)$days,'h'=>(int)$hours,'m'=>(int)$minutes,'s'=>(int)$seconds);
	$timeFormat=sprintf('%d วัน '.'%02d:%02d:%02d',$days,$hours,$minutes,$seconds);
	if($days==0)
		$timeFormat=sprintf('%02d:%02d:%02d',$hours,$minutes,$seconds);
	return $timeFormat;
}
function wt_set_session($sess_name='',$val=''){
	return get_instance()->session->set_userdata($sess_name,$val);
}
function wt_set_flash_session($sess_name='',$val=''){
	return get_instance()->session->set_flashdata($sess_name,$val);
}
function wt_set_pagination($num_rows=0,$limit=0,$offset=0){
	if($num_rows<=$offset)
		$total_rows=1;
	elseif(($num_rows%$offset)==0)
		$total_rows=($num_rows/$offset);
	else
		$total_rows=(($num_rows/$offset)+1);
	return array('per_page'=>(int)(($offset*$limit)-$offset),'total_rows'=>(int)$total_rows,'first_page'=>1,'prev_page'=>(int)$limit-1,'page'=>(int) $limit,'next_page'=>(int)$limit+1,'last_page'=>(int)$total_rows);
}
function wt_unserialize($str){
	return (@unserialize($str)!==FALSE?unserialize($str):$str);
}
function wt_trans($options=1,$db__name=''){
	$db_this=wt_db_loader($db__name);
	if($options==0)
		return $db_this->trans_begin();
	elseif($options==1){
		if($db_this->trans_status()===FALSE)
			return $db_this->trans_rollback();
		else
			return $db_this->trans_commit();
	}elseif($options==2)
		return $db_this->trans_rollback();
}
function wt_unset_session($sess_name=''){
	return get_instance()->session->unset_userdata($sess_name);
}
function wt_user_fullname($firstname='',$lastname=''){
	$fullname='';
	if ($firstname!=''){
		$fullname.=' ('.$firstname;
		if ($lastname!='')
			$fullname.=' '.$lastname;
		$fullname.=')';
	}
	return $fullname;
}
function working_days($days=array()){
	foreach($days as$key=>$value)
		$days[$key]=intval(date('w',strtotime($value)));
	$days_array=array(0=>'Sunday',1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday');
	$possibilities=array();
	foreach($days as$key=>$value){
		$possibilities[$value][]=intval($value);
		for($i=$value+1;$i!=$value;$i++){
			if($i==8&&$value==7)
				$i=1;
			if(!in_array($i,$days))
				break;
			$possibilities[$value][]=$i;
			if($i==7)
				$i=0;
		}
	}
	foreach($possibilities as$key=>$value){
		foreach($possibilities as$key_inner=>$value_inner){
			if($key==$key_inner)
				continue;
			if(in_array($key,$value_inner))
				unset($possibilities[$key]);
		}
	}
	$return=array();
	foreach($possibilities as$key=>$value){
		$count=count($value);
		if($count==1)
			$return[]=$days_array[$value[0]];
		elseif($count==2)
			$return[]=str_replace(array_keys($days_array),$days_array,implode(',',$value));
		elseif($count==7)
			$return[]='Everyday';
		else{
			$value=array_values($value);
			$first=array_shift($value);
			$last=array_pop($value);
			$return[]=str_replace(array_keys($days_array),$days_array,$first.' - '.$last);
		}
	}
	return implode(',',$return);
}