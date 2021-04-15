<?php
class my_general{
	public $ci;
	public function __construct(){
		$this->ci=&get_instance();
	}
	public function get_ci(){
		return $this->ci;
	}
}
function getDatetime(){
	return date('Y-m-d H:i:s');
}
function sortArray($arr,$key){
	$temp=array();
	foreach($arr as$data)
		$temp[$data[$key]]=$data;
	return $temp;
}
function sortArrayKey($arr){
	$temp=array();
	foreach($arr as$key=>$data)
		$temp[]=$data;
	return $temp;
}
function sortArrayKeyPrev($arr){
	$temp=array();
	$data_return=array();
	if(count($arr)<=1)
		return $arr;
	$min='';
	foreach($arr as$key=>$data)
		$temp[]=$key;
	for($i=0;$i<count($arr);$i++){
		$min=$temp[$i];
		$key='';
		for($j=$i;$j<count($arr);$j++){
			if($temp[$j]<$min){
				$min=$temp[$j];
				$key=$j;
			}
		}
		if($key!=''){
			$datatmp=$temp[$i];
			$temp[$i]=$temp[$key];
			$temp[$key]=$datatmp;
		}
	}
	for($i=0;$i<count($temp);$i++)
		$data_return[$temp[$i]]=$arr[$temp[$i]];
	return $data_return;
}
function nameTitle($str='',$length=80){
	return (strlen($str)>$length?mb_substr($str,0,$length,'UTF-8').' ...':$str);
}
function downloadName($str='',$length=35){
	return mb_substr(str_replace(array("+","(",")","'","\"","&quot;"),"",$str),0,$length,'UTF-8');
}
function urlName($str='',$length=70){
	return mb_substr(str_replace(array("+","(",")","'","\"","&quot;"," "),"-",$str),0,$length,'UTF-8');
}
function randomFileName(){
	$result='';
	for($a=1;$a<10;$a++){
		$number='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		for($i=1;$i<10;$i++){
			$cut_txt=substr($number,rand(0,strlen($number)-1),1);
			$result.=$cut_txt;
			$number=str_replace($cut_txt,'',$number);
		}
		if($a<10)
			$result='';
	}
	return $result;
}
function randomFileNameEncode(){
	return str_replace('.',rand(),uniqid(md5(strtotime(getDatetime()).rand()),TRUE));
}
function rowArray($rows){
	return (sizeof($rows)>0?$rows[0]:array());
}
function dieArray($arr=''){
	echo '<pre>';
	die(print_r($arr,TRUE));
	echo '</pre>';
}
function randomString(){
	$characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring='';
	for($i=0;$i<10;$i++)
		$randstring=$characters[rand(0,strlen($characters))];
	return $randstring;
}
function setZeroString($string,$num){
	if($string=='')
		return '';
	$str='';
	for($i=0;$i<($num-strlen($string));$i++)
		$str.='0';
	return $str.$string;
}
function dateChange($date='',$method=0){
	if($date=='')
		return '';
	if($method==0){
		$arr=explode('/',$date);
		return ($arr[2]-543).'-'.$arr[1].'-'.$arr[0];
	}
	elseif($method==2){
		$arr=explode('/',$date);
		return $arr[2].'-'.$arr[1].'-'.$arr[0];
	}
	elseif($method==3){
		$arr=explode('-',$date);
		if($arr[0]=='0000'||$arr[1]=='00'||$arr[2]=='00')
			return '';
		return $arr[2].'/'.$arr[1].'/'.($arr[0]);
	}
	else{
		$arr=explode('-',$date);
		if($arr[0]=='0000'||$arr[1]=='00'||$arr[2]=='00')
			return '';
		return $arr[2].'/'.$arr[1].'/'.($arr[0]+543);
	}
}
function formatDateThai($date){
	$arr=explode('-',$date);
	if($date=='')
		return '';
	if($arr[0]=='0000'||$arr[1]=='00'||$arr[2]=='00')
		return '';
	$thai_month_arr=array("0"=>"","1"=>"มกราคม","2"=>"กุมภาพันธ์","3"=>"มีนาคม","4"=>"เมษายน","5"=>"พฤษภาคม","6"=>"มิถุนายน","7"=>"กรกฎาคม","8"=>"สิงหาคม","9"=>"กันยายน","10"=>"ตุลาคม","11"=>"พฤศจิกายน","12"=>"ธันวาคม");
	$arr=explode('-',$date);
	return (int)$arr[2].' '.$thai_month_arr[(int)$arr[1]].' '.($arr[0]+543);
}
function formatDateThaiFromDatatime($datetime){
	$arr=explode(' ',$datetime);
	return formatDateThai($arr[0]).' '.$arr[1];
}
function formatDateUniNoM($date){
	$arr=explode('-',$date);
	if($date=='')
		return '';
	if($arr[0]=='0000'||$arr[1]=='00'||$arr[2]=='00')
		return '';
	return $arr[2].'/'.$arr[1].'/'.$arr[0];
}
function formatDateUniNoMFromDatatime($datetime){
	$arr=explode(' ',$datetime);
	return formatDateUniNoM($arr[0]).' '.$arr[1];
}
function formatDateUni($date){
	$arr=explode('-',$date);
	if($date=='')
		return '';
	if($arr[0]=='0000'||$arr[1]=='00'||$arr[2]=='00')
		return '';
	$ui_month_arr=array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
	$arr=explode('-',$date);
	return (int)$arr[2].' '.$ui_month_arr[(int)$arr[1]].' '.$arr[0];
}
function formatDateUniFromDatatime($datetime){
	$arr=explode(' ',$datetime);
	return formatDateThai($arr[0]).' '.$arr[1];
}
function formatDateUniMonthThai($date){
	$arr=explode('-',$date);
	if($date=='')
		return '';
	if($arr[0]=='0000'||$arr[1]=='00'||$arr[2]=='00')
		return '';
	$ui_month_arr=array("0"=>"","1"=>"มกราคม","2"=>"กุมภาพันธ์","3"=>"มีนาคม","4"=>"เมษายน","5"=>"พฤษภาคม","6"=>"มิถุนายน","7"=>"กรกฎาคม","8"=>"สิงหาคม","9"=>"กันยายน","10"=>"ตุลาคม","11"=>"พฤศจิกายน","12"=>"ธันวาคม");
	$arr=explode('-',$date);
	return (int)$arr[2].' '.$ui_month_arr[(int)$arr[1]].' '.$arr[0];
}
function formatDateUniMonthThaiFromDatatime($datetime){
	$arr=explode(' ',$datetime);
	return formatDateUniMonthThai($arr[0]).' '.$arr[1];
}
function formatDateThai_WithOutDayNumber($date){
	$thai_month_arr=array("0"=>"","1"=>"มกราคม","2"=>"กุมภาพันธ์","3"=>"มีนาคม","4"=>"เมษายน","5"=>"พฤษภาคม","6"=>"มิถุนายน","7"=>"กรกฎาคม","8"=>"สิงหาคม","9"=>"กันยายน","10"=>"ตุลาคม","11"=>"พฤศจิกายน","12"=>"ธันวาคม");
	$arr=explode('-',$date);
	return $thai_month_arr[(int)$arr[1]].' '.($arr[0]+543);
}
function formatDateThai_NextMonth($date){
	$thai_month_arr=array("0"=>"","1"=>"มกราคม","2"=>"กุมภาพันธ์","3"=>"มีนาคม","4"=>"เมษายน","5"=>"พฤษภาคม","6"=>"มิถุนายน","7"=>"กรกฎาคม","8"=>"สิงหาคม","9"=>"กันยายน","10"=>"ตุลาคม","11"=>"พฤศจิกายน","12"=>"ธันวาคม");
	$arr=explode('-',$date);
	if($arr[1]==12)
		$arr[0]+=1;
	return $thai_month_arr[(int)$arr[1]].' '.($arr[0]+543);
}
function formatDateThai_MonthOnly($date){
	$thai_month_arr=array("0"=>"","1"=>"มกราคม","2"=>"กุมภาพันธ์","3"=>"มีนาคม","4"=>"เมษายน","5"=>"พฤษภาคม","6"=>"มิถุนายน","7"=>"กรกฎาคม","8"=>"สิงหาคม","9"=>"กันยายน","10"=>"ตุลาคม","11"=>"พฤศจิกายน","12"=>"ธันวาคม");
	$arr=explode('-',$date);
	if($arr[1]==12)
		$arr[0]+=1;
	return $thai_month_arr[(int)$arr[1]];
}
function setSession($sess_name='',$data=''){
	$obj=new my_general();
	return $obj->get_ci()->session->set_userdata($sess_name,$data);
}
function getSession($sess_name=''){
	$obj=new my_general();
	return $obj->get_ci()->session->userdata($sess_name);
}
function getInpost($post_name=''){
	$obj=new my_general();
	return xss_clean(htmlspecialchars(($obj->get_ci()->input->post(trim($post_name)))));
}
function getInpostArray($post_name=''){
	$obj=new my_general();
	$arr=array();
	$arr=$obj->get_ci()->input->post(trim($post_name));
	$arr_temp=array();
	if(!empty($arr)){
		foreach($arr as$key=>$value)
			$arr_temp[$key]=xss_clean(htmlspecialchars(($value)));
	}
	return $arr_temp;
}
function getInget($get_name=''){
	$obj=new my_general();
	return xss_clean(htmlspecialchars(($obj->get_ci()->input->get(trim($get_name)))));
}
function uriSeg($segment_num){
	$obj=new my_general();
	return $obj->get_ci()->uri->segment(trim($segment_num));
}
function apppath(){
	return str_replace('\\','/',APPPATH);
}
function ens($arr=array()){
	return @serialize($arr);
}
function uns($str_arr){
	return @unserialize($str_arr);
}