<?php
defined('BASEPATH')OR exit('No direct script access allowed');
class Polygon extends CI_Controller{
	var $def_ani;
	var $def_header;
	var $def_loc;
	var $def_url;
	public function __construct(){
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->helper('general');
		$this->load->library('session');
		$this->load->library('twiggy');
		$this->load->model('config_model');
		$this->load->model('CryptoJS');
		$this->def_ani='0858655102';
		$this->def_header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
		//$this->def_loc=['https'=>['lat'=>'55.85732','lon'=>'-4.26325'],'sms'=>['lat'=>'55.85732','lon'=>'-4.26325']];
		//$this->def_loc=['lat'=>'55.85732','lon'=>'-4.26325'];
		$this->def_loc=['lat'=>'13.756653','lon'=>'100.501857'];
		$this->def_path=['lbs2cad'=>_LOGS.'/191nn/lbs2cad/'];
		$this->def_url=[
			'addr'=>base_url('V2/address/'),
			'incident'=>base_url('lbs/incident'),
			'login'=>base_url('V2/login'),
			'sms'=>base_url('lbs/pull/sms'),
			'subscribe'=>base_url('V2/subScribe'),
			'subscriber'=>base_url('lbs/subscriber'),
		];
		if($_SERVER['SERVER_NAME']=='203.170.193.91'){
			//$this->def_url['aml']='http://43.255.240.22:8500/aml';
			//$this->def_url['cad']='http://43.255.240.22:8500/cad';
			$this->def_url['aml']='http://147.50.7.36:8005/aml';
			$this->def_url['cad']='http://147.50.7.36:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.8.57'){
			$this->def_url['aml']='http://192.168.8.57:8005/aml';
			$this->def_url['cad']='http://192.168.8.57:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.108.57'){
			$this->def_url['aml']='http://192.168.108.57:8005/aml';
			$this->def_url['cad']='http://192.168.108.57:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='147.50.7.36'){
			$this->def_url['aml']='http://192.168.8.57:8005/aml';
			$this->def_url['cad']='http://192.168.8.57:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.101.16'){
			$this->def_url['aml']='http://192.168.101.16:8005/aml';
			$this->def_url['cad']='http://192.168.101.16:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='aml.191poc.local'){
			//$this->def_url['aml']='http://aml.191poc.local:8005/aml';
			//$this->def_url['cad']='http://aml.191poc.local:8005/cad';
			$this->def_url['aml']='http://127.0.0.1:8005/aml';
			$this->def_url['cad']='http://127.0.0.1:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.101.46'){
			$this->def_url['aml']='http://192.168.101.46:8005/aml';
			$this->def_url['cad']='http://192.168.101.46:8005/cad';
		}
	}

	public function add_polygon(){
		try{
			if(isset($_POST['add_plg'])){
				unset($_POST['add_plg']);
				if(isset($_POST['PAGE']) && $_POST['PAGE'] == "edit"){
					$page = $_POST['PAGE'];
					unset($_POST['PAGE']);
				}

				$input = $_POST;

				if(!isset($input['IsActive']) || $input['IsActive'] == ""){
					$input['IsActive'] = 0;
				}else{
					$input['IsActive'] = 1;
				}

				$url=base_url('v2/polygon/create');
				/*****************************************************************************************************/
				$fields=array_filter($input, function($input) {
					return ($input !== null && $input !== false && $input !== ''); 
				});

				$postvars=json_encode($fields);
				$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch,CURLOPT_POST,count($fields));
				curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
				curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
				curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
				curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
				$result=curl_exec($ch);
				if(curl_errno($ch)){
					$result['status']=-1;
					$result['message']='Fail';
					$result['severity']='high';
				}
				curl_close($ch);
				$array = json_decode($result,true);
				if($array['message'] == "Success"){
					$url=base_url('polygon/list_polygon');
					echo "<script>alert('เพิ่มข้อมูลสำเร็จ');</script>";
					if(isset($page)){
						echo "<script>window.history.back();</script>";
					}else{
						echo "<script>window.location.href = '".$url."'</script>";
					}
					die();
				}
			}

			$this->twiggy->template('lbs/add_polygon')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function list_polygon(){
		try{
			$url=base_url('V2/polygon');
			$input=['ID'=>''];
			/*****************************************************************************************************/
			$fields=array_filter($input);
			$postvars=json_encode($fields);
			$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			$result=curl_exec($ch);
			if(curl_errno($ch)){
				$result['status']=-1;
				$result['message']='Fail';
				$result['severity']='high';
			}
			curl_close($ch);
			$array = json_decode($result,true);
			$data = $array['data'];

			$this->twiggy->set('data',$data,TRUE);
			$this->twiggy->template('lbs/list_polygon')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function detail_polygon($id){
		try{
			$url=base_url('V2/polygon/read');
			$input=['ID'=>$id];
			/*****************************************************************************************************/
			$fields=array_filter($input);
			$postvars=json_encode($fields);
			$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			$result=curl_exec($ch);
			if(curl_errno($ch)){
				$result['status']=-1;
				$result['message']='Fail';
				$result['severity']='high';
			}
			curl_close($ch);

			$array = json_decode($result,true);
			$data = $array['data'][0];
			
			$this->twiggy->set('data',$data,TRUE);
			$this->twiggy->set('pages','DETAIL',TRUE);
			$this->twiggy->template('lbs/detail_polygon')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function edit_polygon($id){
		try{
			if(isset($_POST['edit_plg'])){
				unset($_POST['edit_plg']);
				$input = $_POST;

				if(!isset($input['IsActive']) || $input['IsActive'] == ""){
					$input['IsActive'] = 0;
				}else{
					$input['IsActive'] = 1;
				}

				$input['ID'] = $id;
				$url=base_url('v2/polygon/update');
				/*****************************************************************************************************/
				$fields=array_filter($input, function($input) {
					return ($input !== null && $input !== false && $input !== ''); 
				});
				$postvars=json_encode($fields);
				$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch,CURLOPT_POST,count($fields));
				curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
				curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
				curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
				curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
				$result=curl_exec($ch);
				if(curl_errno($ch)){
					$result['status']=-1;
					$result['message']='Fail';
					$result['severity']='high';
				}
				curl_close($ch);
				$array = json_decode($result,true);
				if($array['message'] == "Success"){
					$url=base_url('polygon/list_polygon');
					echo "<script>alert('แก้ไขข้อมูลสำเร็จ');</script>";
					echo "<script>window.location.href = '".$url."'</script>";
					die();
				}else{
					print_r($array);
					die();
				}
			}

			$url=base_url('V2/polygon/read');
			$input=['ID'=>$id];
			/*****************************************************************************************************/
			$fields=array_filter($input);
			$postvars=json_encode($fields);
			$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			$result=curl_exec($ch);
			if(curl_errno($ch)){
				$result['status']=-1;
				$result['message']='Fail';
				$result['severity']='high';
			}
			curl_close($ch);
			$array = json_decode($result,true);
			$data = $array['data'][0];

			$this->twiggy->set('data',$data,TRUE);
			$this->twiggy->template('lbs/edit_polygon')->display();
		}
		catch(Exception $e){
			show_404();
		}
		
	}

	public function del_polygon($id){
		try{
			$url=base_url('v2/polygon/delete');
			$input=['ID'=>$id];
			/*****************************************************************************************************/
			$fields=array_filter($input);
			$postvars=json_encode($fields);
			$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			$result=curl_exec($ch);
			if(curl_errno($ch)){
				$result['status']=-1;
				$result['message']='Fail';
				$result['severity']='high';
			}
			curl_close($ch);
			$array = json_decode($result,true);
			if($array['message'] == "Success"){
				$url=base_url('polygon/list_polygon');
				echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
				echo "<script>window.location.href = '".$url."'</script>";
			}
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function approve_polygon(){
		try{
			$url=base_url('V2/polygon');
			$input=['ID'=>'','IsApprove'=>'0'];
			/*****************************************************************************************************/
			$fields=array_filter($input, function($input) {
				return ($input !== null && $input !== false && $input !== ''); 
			});
			$postvars=json_encode($fields);
			$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			$result=curl_exec($ch);
			if(curl_errno($ch)){
				$result['status']=-1;
				$result['message']='Fail';
				$result['severity']='high';
			}
			curl_close($ch);
			$array = json_decode($result,true);
			$data = $array['data'];

			$this->twiggy->set('data',$data,TRUE);
			$this->twiggy->template('lbs/approve_polygon')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function approve($id){
		try{
			$input=['ID'=>$id,'IsApprove'=>'1'];
			$url=base_url('v2/polygon/update');
			/*****************************************************************************************************/
			$fields=array_filter($input, function($input) {
				return ($input !== null && $input !== false && $input !== ''); 
			});
			$postvars=json_encode($fields);
			$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			$result=curl_exec($ch);
			if(curl_errno($ch)){
				$result['status']=-1;
				$result['message']='Fail';
				$result['severity']='high';
			}
			curl_close($ch);
			$array = json_decode($result,true);

			if($array['message'] == "Success"){
				echo "<script>alert('อนุมัติข้อมูลสำเร็จ');</script>";
				echo "<script>window.history.back();</script>";
			}else{
				print_r($result);
				die();
			}
		}
		catch(Exception $e){
			show_404();
		}
	}
}