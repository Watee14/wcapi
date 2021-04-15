<?php error_reporting(E_ERROR) ;
header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class MobileService extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
	}

	
	public function subScribe()
	{
		$time_start = microtime(true);

		$data_x =  file_get_contents('php://input') ;   
 		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x ); 

 		//////////////////////////////////////////////////////////////////////////////////
		$data = nl2br(str_replace(array("\n", "\r"), ' ', $data));
		//////////////////////////////////////////////////////////////////////////////////
 		 
 		$data = json_decode( $data , true ) ;
 	 	//print_r($data) ; die();
 		foreach ($data as $key => $value) {
 			if( $value =='' ){
 				$ret['status'] = -1 ;
				$ret['message'] = 'Data invalid : '.$key;

				$res_x = json_encode($ret) ;
				$time_end = microtime(true);
				$duration = $time_end - $time_start;
				$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribe', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');
				
				print_r(  $res_x ) ;  die();
 			}

 			//////////////////////////////////////////////////////////////////////////////////
 			else if ($key == 'citizen_password')
			{
				preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/', $value, $matches);
				if (empty($matches) OR ! isset($matches))
				{
					$ret['status'] = -1 ;
					$ret['message'] = 'กรุณากรอกรหัสผ่าน 8 ตัวขึ้นไปและต้องประกอบด้วย ตัวเล็กตัวใหญ่และตัวเลข';

					$res_x = json_encode($ret) ;
					$time_end = microtime(true);
					$duration = $time_end - $time_start;
					$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribe', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');
					
					print_r(  $res_x ) ;  die();
				}
			}
			//////////////////////////////////////////////////////////////////////////////////
 		}

 		//////////////////////////////////////////////////////////////////////////////////
 		$citizen_fulllname = explode(' ', $data['citizen_name'], 3);
		$data['citizen_name'] = $citizen_fulllname[0];
		$data['citizen_middle'] = count($citizen_fulllname) > 2 ? $citizen_fulllname[sizeof($citizen_fulllname) - 2] : NULL;
		$data['citizen_surname'] = count($citizen_fulllname) > 1 ? $citizen_fulllname[sizeof($citizen_fulllname) - 1] : NULL;
		$citizen_fulllname = NULL;
		unset($citizen_fulllname);
		//////////////////////////////////////////////////////////////////////////////////
 		 
		$this->db->select('citizen_code , citizen_phone_number');
		$this->db->from('citizen_profile');
		$this->db->where('citizen_phone_number =',  $data['citizen_phone_number']);
		$user = $this->db->get()->row();
		//$user = $user[0] ;
		//print_r($user->citizen_code) ;
		//print_r($user->citizen_code) ;
		//die();

		//$pass = $this->CryptoJS->aes_decrypt( $data['citizen_password']  ); 

/*  
	[citizen_name] => Jane_test
    [citizen_phone_number] => 0809955502
    [citizen_address] => --Address--
    [device_name] => xx
    [device_token] => ccc
    [citizen_password] => rrr
    [citizen_id] => 17098000xxxxx
*/

		if($user->citizen_code!=''){  
			//Updater
			$profile = array( 
		         'citizen_name' => $data['citizen_name'] ,
		         'citizen_middle' => $data['citizen_middle'] ,
				 'citizen_surname' => $data['citizen_surname'] ,
			     'citizen_phone_number' =>  $data['citizen_phone_number']  ,
			     'citizen_address' => $data['citizen_address']   ,
			     'device_name' => $data['device_name'] ,
			     'device_token' => $data['device_token'], 
			     'citizen_id' => $data['citizen_id'], 
			     'citizen_password' => $this->CryptoJS->aes_encrypt( $data['citizen_password']  ) ,
			     //'user_create' => 'MobileService', 
			     'user_modify' => 'MobileService', 
			     //'created_date' => date('Y-d-m H:i:s') ,
			     'modified_date' => date('Y-m-d H:i:s') ,
			     'citizen_current_lat' => $data['citizen_current_lat'], 
			     'citizen_current_lon' => $data['citizen_current_lon'], 
			     'citizen_email' => $data['citizen_email']
			     
			);
			$this->db->where('citizen_phone_number', $data['citizen_phone_number'] );
			$query = $this->db->update('citizen_profile', $profile); 
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
			 
			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
			}

		}else{  
			//Insert
			$profile = array( 
		         'citizen_name' => $data['citizen_name'] ,
		         'citizen_middle' => $data['citizen_middle'] ,
				 'citizen_surname' => $data['citizen_surname'] ,
			     'citizen_phone_number' =>  $data['citizen_phone_number']  ,
			     'citizen_address' => $data['citizen_address']   ,
			     'device_name' => $data['device_name'] ,
			     'device_token' => $data['device_token'], 
			     'citizen_id' => $data['citizen_id'], 
			     'citizen_password' => $this->CryptoJS->aes_encrypt( $data['citizen_password']  ) ,
			     'user_create' => 'MobileService', 
			     'user_modify' => 'MobileService', 
			     'created_date' => date('Y-m-d H:i:s') ,
			     'modified_date' => date('Y-m-d H:i:s') ,
			     'citizen_current_lat' => $data['citizen_current_lat'], 
			     'citizen_current_lon' => $data['citizen_current_lon'],
			     'citizen_email' => $data['citizen_email'] 
			);
			//print_r( $profile ) ;
			$query = $this->db->insert('citizen_profile', $profile);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
			 
			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
			}
		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribe', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');
				
		print_r(  $res_x ) ;

 
		
	}

	public function login($username ='' , $password = '')
	{
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ; 
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;  
 		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x ); 
 		$data = json_decode( $data , true ) ;
 		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
 		//print_r($data ) ; die();
		if($data['citizen_phone_number']=='' || $data['citizen_password']==''  ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}
		// print_r($data['citizen_password'] ) ; die();
 
		$pass = $this->CryptoJS->aes_encrypt( $data['citizen_password']  ); 
		//print_r($pass) ; die(); 

		//////////////////////////////////////////////////////////////////////////////////
		// $this->db->select('citizen_code,device_name,device_token,citizen_name,citizen_phone_number,citizen_address,citizen_id,citizen_email');
		$this->db->select('citizen_code,device_name,device_token,citizen_name,citizen_middle,citizen_surname,citizen_phone_number,citizen_address,citizen_id,citizen_email');
		//////////////////////////////////////////////////////////////////////////////////

		$this->db->from( 'citizen_profile' );
		$this->db->where('citizen_phone_number =',  $data['citizen_phone_number'] );
		///$this->db->where('citizen_password =',   $pass );
		$user = $this->db->get()->row();
		//print_r($user) ; die();
		$ret = [];
 
		//if($entered_pass != $data["password"])
		if( !isset($user->citizen_code) )
		{ 
			$ret['status'] = -1 ;
			$ret['message'] = 'No data';
 
		}else{
 			$ret['status'] = 0 ;
			$ret['message'] = 'success';
			$ret['data'] = $user ;

			//////////////////////////////////////////////////////////////////////////////////
			if ($user->citizen_middle != '')
			{
				$ret['data']->citizen_name .= ' '.$user->citizen_middle;
			}
			if ($user->citizen_surname != '')
			{
				$ret['data']->citizen_name .= ' '.$user->citizen_surname;
			}
			//////////////////////////////////////////////////////////////////////////////////
		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'login', 'select' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');
				
		print_r(  $res_x ) ; 
	}

	public function checkLocation()
	{
		$time_start = microtime(true);

		$data_x = file_get_contents('php://input') ;
		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x ); 
		$data = json_decode( $data , true ) ;
		// print_r($data) ; die();
		//$data['citizen_current_lat'] = '13.719647892123605' ;
		//$data['citizen_current_lon'] = '100.42705669999123';
		if($data['citizen_current_lat']=='' || $data['citizen_current_lon']=='' ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			//print_r(json_encode($ret)) ; die();
		}else{
			$result = $this->address( $data['citizen_current_lat'] , $data['citizen_current_lon'] ) ;
			$result_ln = json_decode($result , true ) ;
			//print_r($result_ln) ;
			if($result_ln['province']=='กรุงเทพมหานคร' ){
				$ret['status'] = 0 ;
			}else{
				$ret['status'] = -1 ;
			}
			$ret['message'] = $result_ln ;
		}
		$ret['latlon'] = $data ;
		

		/*$ret['status'] = -1 ;
		$ret['message'] = 'Function not complete';*/

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  'XXXXX' , $_SERVER['REMOTE_ADDR'] , 'low', 'checkLocation', 'info' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');
				
		print_r(  $res_x ) ; 

	}	
	public function address($lat = "", $lon = "")
	{
		// echo _MAP_SERVICE;
		//echo '<pre>';
		//print_r($_SESSION['user']->user_name ) ; die();
		$time_start = microtime(true);
		$req = $_REQUEST;
		$url = _MAP_ADDRESS."?lat=".$lat."&lon=".$lon."&lang=th" ;
		$result = $this->curlService($url, '', 'GET');
		$res = json_decode($result , true) ;
		$status = 0 ;
		$ltype = 'low';
		if(isset($res['geocode'])){
			$status = 1;
			$ltype = 'info' ;
		}
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs( 'XXX' , $_SERVER['REMOTE_ADDR'] , $ltype , 'getAddress', 'callws' , $duration  , $status , $url , $result , 'Map_API'  );

		return $result ;
		//print_r($res);
	}


	function curlService($url, $data, $method)
	{
		$ch = curl_init();
		$url = $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		// curl_setopt($ch,CURLOPT_HEADER, TRUE);

		$response = curl_exec($ch);

		// print_r($response);

		return $response;
	}
	 
 
}