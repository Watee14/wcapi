<?php error_reporting(E_ERROR) ;
//header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class V2 extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();
		//$this->load->database("citizenDB");
		$this->db = $this->load->database("citizenDB", TRUE);
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
	}

	function authen($header){
		$status = false ;
		if($header['Authorization']!=""){
			$decryption=openssl_decrypt ( base64_decode($header['Authorization']) , _AUTHEN_CYPHER ,  _AUTHEN_KEY, $options, _AUTHEN_ENCRYPT );
			if(_AUTHEN_USER==$decryption){
				$status = true ;
			}else{
				$ret['status'] = -2 ;
				$ret['message'] = 'Authentication failed please try again';
				print_r(json_encode($ret)) ; die();
			}

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		return $status ;
	}

	public function index()
	{
		//$this->load->view('welcome_message');
		$simple_string = _AUTHEN_USER;

		// Display the original string
		//echo "Original String: " . $simple_string . "<br>";
		$ciphering =  _AUTHEN_CYPHER ;
		$iv_length = openssl_cipher_iv_length($ciphering);
		$options = 0;
		$encryption_iv = _AUTHEN_ENCRYPT ;
		$encryption_key = _AUTHEN_KEY ;
		$encryption = openssl_encrypt($simple_string, $ciphering,  _AUTHEN_KEY , $options, _AUTHEN_ENCRYPT );
		//echo "Encrypted String: " . base64_encode($encryption) . "<br>";

		// Use openssl_decrypt() function to decrypt the data
		$decryption=openssl_decrypt ($encryption, _AUTHEN_CYPHER ,  _AUTHEN_KEY, $options, _AUTHEN_ENCRYPT );
		//echo "Decrypted String: " . $decryption;

		$this->load->view('mobile_api_v2');

	}

	public function chat()
	{

		$this->load->view('mobile_api_chat');

	}

	public function subScribe()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);

		$data_x =  file_get_contents('php://input') ;
 		//$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = $data_x ;
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
			     'citizen_telephone' =>  $data['citizen_telephone']  ,
			     'citizen_telephone_office' =>  $data['citizen_telephone_office']  ,
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
			     'citizen_email' => $data['citizen_email'],
			     'address' => json_encode($data['address']) ,
			     'info' => json_encode($data['info'])

			);
			$this->db->where('citizen_phone_number', $data['citizen_phone_number'] );
			$query = $this->db->update('citizen_profile', $profile);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
			 	$ret['data'] = $data;
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
			     'citizen_telephone' =>  $data['citizen_telephone']  ,
			     'citizen_telephone_office' =>  $data['citizen_telephone_office']  ,
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
			     'citizen_email' => $data['citizen_email'],

			     'address' => json_encode($data['address']),
			     'info' => json_encode($data['info'])



			);
			//print_r( $profile ) ;
			$query = $this->db->insert('citizen_profile', $profile);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
			 	$ret['data'] = $data;
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
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;
 		//$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = $data_x ;
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
		//$this->db->select('citizen_code,device_name,device_token,citizen_name,citizen_middle,citizen_surname,citizen_phone_number,citizen_address,citizen_id,citizen_email,citizen_telephone,citizen_telephone_office,citizen_current_lat,citizen_current_lon,citizen_home_no,citizen_home_addr,citizen_home_lat,citizen_home_lon,citizen_work_no,citizen_work_addr,citizen_work_lat,citizen_work_lon');
		$this->db->select('citizen_code,device_name,device_token,citizen_name,citizen_middle,citizen_surname,citizen_phone_number,citizen_address,citizen_id,citizen_email,citizen_telephone,citizen_telephone_office,citizen_current_lat,citizen_current_lon, address,info');

		//////////////////////////////////////////////////////////////////////////////////

		$this->db->from( 'citizen_profile' );
		$this->db->where('citizen_phone_number =',  $data['citizen_phone_number'] );
		$this->db->where('citizen_password =',   $pass );
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
			$user->address =  json_decode($user->address)  ;
			$user->info =  json_decode($user->info)  ;
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

	public function openTicket()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;
 		//$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = $data_x ;
 		$data = json_decode( $data , true ) ;
 		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
 		//print_r($data ) ; die();
		if($data['citizen_phone_number']==''){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}
		// print_r($data['citizen_password'] ) ; die();

		$pass = $this->CryptoJS->aes_encrypt( $data['citizen_password']  );
		//print_r($pass) ; die();

		//////////////////////////////////////////////////////////////////////////////////
		// $this->db->select('citizen_code,device_name,device_token,citizen_name,citizen_phone_number,citizen_address,citizen_id,citizen_email');
		$this->db->select('citizen_code');
		$this->db->from( 'citizen_profile' );
		$this->db->where('citizen_phone_number =',  $data['citizen_phone_number'] );
		$this->db->where('citizen_password =',   $pass );
		$user = $this->db->get()->row();
		//print_r($user) ; die();
		$ret = [];

		//if($entered_pass != $data["password"])
		if( !isset($user->citizen_code) )
		{
			$ret['status'] = -1 ;
			$ret['message'] = 'No Permission/No User';

		}else{

 			//Insert
 			$mpID = "mpID_".time() ;
			$ds = array(
		         'mapping_id' => $mpID ,
		         'citizen_phone_number' => $data['citizen_phone_number'] ,
		         'incident_id' => "" ,
				 'data' => json_encode( $data['data'] )
			);
			//print_r( $ds ) ;
			$query = $this->db->insert('case_mapping', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
				$ret['mapping_id'] = $mpID ;
			 	$ret['data'] = $data;
			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
			}

		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'openTicket', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');

		print_r(  $res_x ) ;
	}

	public function sendLocation()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;
 		//$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = $data_x ;
 		$data = json_decode( $data , true ) ;
 		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
 		//print_r($_SERVER ) ; die();
		if($data['citizen_phone_number']==''){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}else{
			$uploads_dir = _UPLOADS.'\locations\\'.$data['citizen_phone_number'].".txt";
			if( file_put_contents( $uploads_dir , json_encode($data)) ){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
				if($_SERVER['HTTPS']=="on"){  $protocol="https://";}else{  $protocol="http://"; }
				$ret['link'] =  $protocol.$_SERVER['SERVER_NAME']."/"._UPLOADS_NAME."/locations/".$data['citizen_phone_number'].".txt" ;
			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Data invalid';
			}
		}
		print_r( json_encode($ret ) ) ;
	}


	public function uploadFile()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;

		if (is_uploaded_file($_FILES['fileUpload']['tmp_name'])) {
	    	$uploads_dir = _UPLOADS.'\files\\';
	        $tmp_name = $_FILES['fileUpload']['tmp_name'];
	        //$pic_name = $_FILES['fileUpload']['name'];
	        $pic_name = time().".".$_FILES['fileUpload']['type'] ;
	        move_uploaded_file($tmp_name, $uploads_dir.$pic_name);
	        $ret['status'] = 0 ;
			$ret['message'] = 'Success';
			if($_SERVER['HTTPS']=="on"){  $protocol="https://";}else{  $protocol="http://"; }
			$ret['link'] = $protocol.$_SERVER['SERVER_NAME']."/"._UPLOADS_NAME."/files/".$pic_name;
        }else{
        	$ret['status'] = -1 ;
			$ret['message'] = 'Fail';
			//echo "File not uploaded successfully.";
        }
        print_r(  json_encode($ret )  );
	}

	public function address($lat = "", $lon = "")
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		if($lat=="" || $lon==""){
			$ret['status'] = -1 ;
			$ret['message'] = 'Please send Latitude,Lontitude';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
		$req = $_REQUEST;
		$url = _MAP_ADDRESS."?key="._MAP_KEY."&lat=".$lat."&lon=".$lon."&lang=th" ;
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
		$this->Writelogs_model->wLogs( 'Mobile' , $_SERVER['REMOTE_ADDR'] , $ltype , 'getAddress', 'callws' , $duration  , $status , $url , $result , 'MobileApp'  );
		//$result = json_decode($result , true) ;

		print_r($result) ;
		//print_r($res);
	}

	public function incidentList()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;
 		//$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = $data_x ;
 		$data = json_decode( $data , true ) ;
 		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
 		//print_r($data ) ; die();
		if($data['citizen_phone_number']==''){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}
		// print_r($data['citizen_password'] ) ; die();

		$pass = $this->CryptoJS->aes_encrypt( $data['citizen_password']  );
		//print_r($pass) ; die();

		//////////////////////////////////////////////////////////////////////////////////
		// $this->db->select('citizen_code,device_name,device_token,citizen_name,citizen_phone_number,citizen_address,citizen_id,citizen_email');
		$this->db->select('citizen_code');
		$this->db->from( 'citizen_profile' );
		$this->db->where('citizen_phone_number =',  $data['citizen_phone_number'] );
		$this->db->where('citizen_password =',   $pass );
		$user = $this->db->get()->row();
		//print_r($user) ; die();
		$ret = [];

		//if($entered_pass != $data["password"])
		if( !isset($user->citizen_code) )
		{
			$ret['status'] = -1 ;
			$ret['message'] = 'No Permission/No User';

		}else{


			$this->db->select('mapping_id, incident_id,data, citizen_phone_number , convert(varchar,modify_date,111)+\' \' +convert(varchar,modify_date,108) modify_date');

			//////////////////////////////////////////////////////////////////////////////////

			$this->db->from( 'case_mapping' );
			$this->db->where('citizen_phone_number =',  $data['citizen_phone_number'] );

			$list = $this->db->get()->result_array();
			//print_r($list) ; die();
			$result = [] ;
			foreach ($list as $key => $value) {
				//print_r(  $value ) ;
				$ds = [] ;
				$ds = $value ;
				$ds['data'] = json_decode($value['data']  , true) ;
				$result[] =  $ds ;
			}
 			print_r( json_encode( $result ) ) ; die();
		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'List', 'select' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');

		print_r(  $res_x ) ;
	}

	public function socialProfile()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;
 		//$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = $data_x ;
 		$data = json_decode( $data , true ) ;
 		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
 		//print_r($data ) ; die();

		// print_r($data['citizen_password'] ) ; die();
  		if($data['userId']=='' || $data['channel']==''  ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}
		$this->db->select('userId');
		$this->db->from('social_profile');
		$this->db->where('userId =',  $data['userId']);
		$userX = $this->db->get()->row();
		if( isset($userX->userId) && $userX->userId!=''){
			//Update
			$ds = array(
		         'channel' => $data['channel'] ,
		         'userId' => $data['userId'] ,
		         'userName' => $data['userName'] ,
		         'profile' => json_encode($data['profile'])
			);
			//print_r( $ds ) ;
			$this->db->where('userId', $data['userId'] );
			$query = $this->db->update('social_profile', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
			 	$ret['data'] = $data;
			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
			}

		}else{
			//Insert
			$ds = array(
		         'channel' => $data['channel'] ,
		         'userId' => $data['userId'] ,
		         'userName' => $data['userName'] ,
		         'profile' => json_encode($data['profile'])
			);
			//print_r( $ds ) ;
			$query = $this->db->insert('social_profile', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
			 	$ret['data'] = $data;
			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
			}
		}


		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'SocialProfile', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');

		print_r(  $res_x ) ;
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