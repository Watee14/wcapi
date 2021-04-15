<?php
defined('BASEPATH')OR exit('No direct script access allowed');
class Alert extends CI_Controller{
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

	public function alert(){
		try{
			if(isset($_POST['ADD_POLYGON'])){
				unset($_POST['ADD_POLYGON']);
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
					$url=base_url('alert/alert');
					echo "<script>alert('เพิ่มข้อมูลสำเร็จ');</script>";
					echo "<script>window.location.href = '".$url."'</script>";
				}else{
					print_r($array);
				}
			}

			if (isset($_POST['add_camping'])) {
				unset($_POST['add_camping']);
				$input = $_POST;

				$input['StartDate'][0] = str_replace("/","-",$input['StartDate'][0]);
				$input['EndDate'][0] = str_replace("/","-",$input['EndDate'][0]);


				$input['StartDate'] = $input['StartDate'][0]." ".$input['StartDate'][1];
				$input['EndDate'] = $input['EndDate'][0]." ".$input['EndDate'][1];

				if(!isset($input['IsActive']) || $input['IsActive'] == ""){
					$input['IsActive'] = 0;
				}else{
					$input['IsActive'] = 1;
				}

				if(substr($input['PolygonID'][0],0,3) == "all"){
					$polygon_id = explode(",",$input['PolygonID'][0]);
					foreach ($polygon_id as $key => $value) {
						if($value == "all" || $value == ""){
							unset($polygon_id[$key]);
						}
					}
					$input['PolygonID'] = $polygon_id;
				}

				$input['PolygonID'] = implode(",",$input['PolygonID']);

				if ($_FILES['Photo']['size'] == 0 && $_FILES['Photo']['error'] == 4){
					$input['Photo'] = "";
				}else{
					$type = explode("/",$_FILES['Photo']['type']);
					if(($type[1] == "jpg") || ($type[1] == "jpeg") || ($type[1] == "png")){
						if($_FILES['Photo']['size'] > 5000000){
							echo "<script>alert('ไฟล์ ".$_FILES['Photo']['name']." มีขนาดไฟล์เกินกว่าที่กำหนด');</script>";
							echo "<script>window.history.back();</script>";
							die();
						}else{
							$file_tmp = $_FILES['Photo']['tmp_name'];
							$data = file_get_contents($file_tmp);
							$input['Photo'] = base64_encode($data);
						}
					}else{
						echo "<script>alert('ไฟล์ ".$_FILES['Photo']['name']." ไม่ใช่ไฟล์รูปภาพที่กำหนด');</script>";
						echo "<script>window.history.back();</script>";
						die();
					}
				}
				
				$url=base_url('v2/campaign/create');
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
					$url=base_url('alert/list_alert');
					echo "<script>alert('เพิ่มข้อมูลสำเร็จ');</script>";
					echo "<script>window.location.href = '".$url."'</script>";
				}else{
					print_r($array);
				}
			}else{
				$url=base_url('V2/polygon');
				$input=['ID'=>'','IsActive'=>'1','IsApprove'=>'1'];
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

				$province = $this->province();

				$this->twiggy->set('data',$data,TRUE);
				$this->twiggy->set('province',$province,TRUE);
				$this->twiggy->template('lbs/alert')->display();
			}
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function list_alert(){
		try{
			$url=base_url('V2/campaign');
			$input=['campaign_id'=>''];
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
			$this->twiggy->template('lbs/list_alert')->display();

		}
		catch(Exception $e){
			show_404();
		}
	}

	public function detail_alert($id){
		try{
			$url=base_url('V2/campaign/read');
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

			if(isset($data['PolygonID']) && $data['PolygonID'] != ""){
				$data['PolygonID'] = explode(",",$data['PolygonID']);
				foreach ($data['PolygonID'] as $key => $value) {
					$url=base_url('V2/polygon/read');
					$input=['ID'=>$value];
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
					$data['polygon'][$key] = $array['data'][0];
				}
			}
			
			if((isset($data['InAreaProvince']) && $data['InAreaProvince'] != "") || (isset($data['InAreaDistrict']) && $data['InAreaDistrict'] != "")){
				$data['boundary'] = $this->province_detail($data['InAreaDistrict'],$data['InAreaProvince']);
			}

			$file_tmp = $data['Photo'];
			$img = file_get_contents($file_tmp);
			$data['Photo'] = base64_encode($img);

			if (isset($data['polygon']) && $data['polygon'] != '')
			{
				$Polygon = [];
				foreach ($data['polygon'] as $key => $value)
				{
					$Polygon[] = $value['Polygon'];
				}
				if (count($Polygon) > 0)
				{
					$data['Polygons'] = "[".implode(",", $Polygon)."]";
				}
				$centroid = 0;
				if ( ! isset($data['Description']) OR $data['Description'] == '')
				{
					$centroid = isset($data['polygon']) && count($data['polygon']) > 1 ? intval(round(count($data['polygon']) / 2)) : 0;
					if (isset($data['polygon'][$centroid]) && $data['polygon'][$centroid] != '')
					{
						if (isset($data['polygon'][$centroid]['Description']) && $data['polygon'][$centroid]['Description'] != '')
						{
							$data['Description'] = $data['polygon'][$centroid]['Description'];
						}
					}
				}
				if ( ! isset($data['Zoom']) OR $data['Zoom'] == '')
				{
					$data['Zoom'] = $centroid;
				}
			}
		
			$this->twiggy->set('data',$data,TRUE);
			$this->twiggy->set('pages','DETAIL',TRUE);
			$this->twiggy->template('lbs/detail_alert')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function edit_alert($id){
		try{
			if (isset($_POST['update_camping'])) {
				unset($_POST['update_camping']);
				$input = $_POST;

				if(!isset($input['IsActive']) || $input['IsActive'] == ""){
					$input['IsActive'] = "0";
				}else{
					$input['IsActive'] = "1";
				}

				$input['StartDate'][0] = str_replace("/","-",$input['StartDate'][0]);
				$input['EndDate'][0] = str_replace("/","-",$input['EndDate'][0]);

				$input['StartDate'] = $input['StartDate'][0]." ".$input['StartDate'][1];
				$input['EndDate'] = $input['EndDate'][0]." ".$input['EndDate'][1];

				if(substr($input['PolygonID'][0],0,3) == "all"){
					$polygon_id = explode(",",$input['PolygonID'][0]);
					foreach ($polygon_id as $key => $value) {
						if($value == "all" || $value == ""){
							unset($polygon_id[$key]);
						}
					}
					$input['PolygonID'] = $polygon_id;
				}

				$input['PolygonID'] = implode(",",$input['PolygonID']);

				if ($_FILES['Photo']['size'] != 0 && $_FILES['Photo']['error'] != 4){
					$type = explode("/",$_FILES['Photo']['type']);
					if(($type[1] == "jpg") || ($type[1] == "jpeg") || ($type[1] == "png")){
						if($_FILES['Photo']['size'] > 5000000){
							echo "<script>alert('ไฟล์ ".$_FILES['Photo']['name']." มีขนาดไฟล์เกินกว่าที่กำหนด');</script>";
							echo "<script>window.history.back();</script>";
							die();
						}else{
							$file_tmp = $_FILES['Photo']['tmp_name'];
							$data = file_get_contents($file_tmp);
							$input['Photo'] = base64_encode($data);
						}
					}else{
						echo "<script>alert('ไฟล์ ".$_FILES['Photo']['name']." ไม่ใช่ไฟล์รูปภาพที่กำหนด');</script>";
						echo "<script>window.history.back();</script>";
						die();
					}
				}else{
					if(isset($input['photo_old']) && $input['photo_old'] != ""){
						$input['Photo'] = $input['photo_old'];
						unset($input['photo_old']);
					}
				}

				$input['ID'] = $id;

				$url=base_url('v2/campaign/update');
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
					$url=base_url('alert/list_alert');
					echo "<script>alert('แก้ไขข้อมูลสำเร็จ');</script>";
					echo "<script>window.location.href = '".$url."'</script>";
				}
			}else{
				$url=base_url('V2/campaign/read');
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
				$data['PolygonID'] = explode(",",$data['PolygonID']);

				$url=base_url('V2/polygon');
				$input=['ID'=>'','IsActive'=>'1','IsApprove'=>'1'];
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
				$polygon = $array['data'];
	
				$province = $this->province();

				$file_tmp = $data['Photo'];
				$img = file_get_contents($file_tmp);
				$data['Photo'] = base64_encode($img);

				$this->twiggy->set('data',$data,TRUE);
				$this->twiggy->set('polygon',$polygon,TRUE);
				$this->twiggy->set('province',$province,TRUE);
				$this->twiggy->template('lbs/edit_alert')->display();
			}
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function del_alert($id){
		try{
			$url=base_url('V2/campaign/delete');
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
				echo "<script>alert('ลบสำเร็จ');</script>";
				echo "<script>window.history.back();</script>";
				die();
			}
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function approve_alert(){
		try{
			$url=base_url('V2/campaign');
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
			$this->twiggy->template('lbs/approve_alert')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function approve($id){
		try{
			$url=base_url('V2/campaign/update');
			$input=['ID'=>$id,'IsApprove'=>'1'];
			/*****************************************************************************************************/
			$fields=array_filter($input);
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

	public function province(){
		try{
			$boundary_url = base_url("assets/arcgis/boundaryTH.txt");
			$boundary_json = file_get_contents($boundary_url);
			$boundary = json_decode($boundary_json,true);

			foreach ($boundary as $key => $value) {
				$province[$value['ADM1_PCODE']]['NAME'] = $value['ADM1_TH'];
			}

			foreach ($province as $key => $value) {
				foreach ($boundary as $key2 => $value2) {
					if($key == $value2['ADM1_PCODE']){
						$province[$key]['DISTRICT'][$value2['ADM2_PCODE']] = $value2['ADM2_TH'];
					}
				}
			}
			return $province;
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function province_detail($id_district = "",$id_province = ""){
		try{
			$boundary_url = base_url("assets/arcgis/boundaryTH.txt");
			$boundary_json = file_get_contents($boundary_url);
			$boundary = json_decode($boundary_json,true);
			
			if(isset($id_district) && $id_district != ""){
				foreach ($boundary as $key => $value) {
					if($id_district == $value['ADM2_PCODE']){
						$pv_dt['PROVINCE_ID'] = $value['ADM1_PCODE'];
						$pv_dt['PROVINCE_NAME'] = $value['ADM1_TH'];

						$pv_dt['DISTRICT_ID'] = $value['ADM2_PCODE'];
						$pv_dt['DISTRICT_NAME'] = $value['ADM2_TH'];
					}
				}
			}

			if((!isset($id_district) || $id_district == "") && (isset($id_province) && $id_province != "")){
				foreach ($boundary as $key => $value) {
					if($id_province == $value['ADM1_PCODE']){
						$pv_dt['PROVINCE_ID'] = $value['ADM1_PCODE'];
						$pv_dt['PROVINCE_NAME'] = $value['ADM1_TH'];

						$pv_dt['DISTRICT_ID'] = "";
						$pv_dt['DISTRICT_NAME'] = "";
					}
				}
			}

			return $pv_dt;
		}
		catch(Exception $e){
			show_404();
		}
	}

	public function test_map(){
		try{
			$this->twiggy->template('lbs/test_map')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
}