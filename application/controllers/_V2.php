<?php error_reporting(E_ERROR) ;
date_default_timezone_set('Asia/Bangkok');
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
		if($header['Authorization']!="" || $header['authorization']!=""){
			$header['Authorization'] = $header['Authorization'] ;
			if($header['authorization']!=""){
				$header['Authorization'] = $header['authorization'] ;
			}
			$decryption=openssl_decrypt ( base64_decode($header['Authorization']) , _AUTHEN_CYPHER ,  _AUTHEN_KEY, $options, _AUTHEN_ENCRYPT );
			if(_AUTHEN_USER==$decryption){
				$status = true ;
			}else{
				$ret['status'] = -2 ;
				$ret['message'] = 'Authentication failed please try again';
				$this->Writelogs_model->wLogs(  "Authen"  , $_SERVER['REMOTE_ADDR'] , 'high', 'authenHeader', 'authen' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobileApp');
				print_r(json_encode($ret)) ; die();
			}

		}else{
			$ret['status'] = -3 ;
			$ret['message'] = 'Authentication failed please try again';
			$this->Writelogs_model->wLogs(  "Authen"  , $_SERVER['REMOTE_ADDR'] , 'high', 'authenHeader', 'authen' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobileApp');
			print_r(json_encode($ret)) ; die();
		}
		$this->Writelogs_model->wLogs(  "Authen"  , $_SERVER['REMOTE_ADDR'] , 'high', 'authenHeader', 'authen' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobileApp');
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
			/*else if ($key == 'citizen_password')
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
			}*/
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

		$this->db->select('citizen_code , citizen_phone_number , citizen_password');
		$this->db->from('citizen_profile');
		$this->db->where('citizen_phone_number =',  $data['citizen_phone_number']);
		
		
		$user = $this->db->get()->row();
		
		//$user = $user[0] ;
		//print_r($user->citizen_code) ;
		//print_r($user ) ;
		//die();

		

/*
	[citizen_name] => Jane_test
	[citizen_phone_number] => 0809955502
	[citizen_address] => --Address--
	[device_name] => xx
	[device_token] => ccc
	[citizen_password] => rrr
	[citizen_id] => 17098000xxxxx
*/
		$stage = "";
		if($user->citizen_code!=''){

			if($data['citizen_password_old']!=""){
				$pass = $this->CryptoJS->aes_encrypt( $data['citizen_password_old']  );
				if($user->citizen_password==$pass){

				}else{
					$ret['status'] = -2 ;
					$ret['message'] = 'Change password fail!!';
					print_r(json_encode($ret)) ; die();
				}
				//$this->db->where('citizen_password =',  $pass);
			}else{

				$pass = $this->CryptoJS->aes_encrypt( $data['citizen_password']  );
				if($user->citizen_password==$pass){

				}else{
					//$ret['status'] = -2 ;
					//$ret['message'] = 'Password fail!!';
					//print_r(json_encode($ret)) ; die();
				}
			}

			//Updater
			$stage = "Update" ;
			if($data['citizen_middle']==""){ $data['citizen_middle']=$user->citizen_middle; }
			if($data['citizen_surname']==""){ $data['citizen_surname']=$user->citizen_surname; }
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
				 'noti_flag' => $data['noti_flag'] ,
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
			if($data['device_token']!="" && $data['device_token']!="null" && $data['device_token']!=null){ 

			}else{
				unset($profile['device_token']) ;
			}

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
			$stage = "Insert" ;
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
				 'noti_flag' => $data['noti_flag'] ,
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
			if($data['device_token']!="" && $data['device_token']!="null" && $data['device_token']!=null){ 

			}else{
				unset($profile['device_token']) ;
			}
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
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribe', $stage , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');

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
		$this->db->select('citizen_code,device_name,device_token,noti_flag,citizen_name,citizen_middle,citizen_surname,citizen_phone_number,citizen_address,citizen_id,citizen_email,citizen_telephone,citizen_telephone_office,citizen_current_lat,citizen_current_lon, address,info');

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
		$this->db->select('citizen_code, citizen_name,citizen_surname');
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
			$dsHex = [] ; 
			$dsHex['mapping_id'] = $mpID ;
			$dsHex['data'] = $data;
			$dsHex['user'] = $user ;
			$hexEvent = $this->createEvent($dsHex);
			$eventID = '';
			if( $hexEvent['status']==0 ){
				$eventID = $hexEvent['data']['agencyEventId'] ;
			}
			$ds = array(
				 'mapping_id' => $mpID ,
				 'citizen_phone_number' => $data['citizen_phone_number'] ,
				 'incident_id' => $eventID ,
				 'data' => json_encode( $data['data'] ) ,
				 'incident_status' => "0" 
			);
			//print_r( $ds ) ;
			$query = $this->db->insert('case_mapping', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
				$ret['mapping_id'] = $mpID ;
				$ret['incident_id'] = $eventID ;
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
		//$ret['status'] = 0 ;
		//$ret['message'] = 'Success';
		//print_r( json_encode( $ret)) ; die();
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

		if( $data['citizen_phone_number']!="" && $data['current_lat']!="" && $data['current_lon']!=""){

			$dataL = [] ;
			$retArea = [] ; 
			$this->postgres = $this->load->database ( 'postgres', TRUE );
			$areas = $this->postgres->query("SELECT ADM2_EN,ADM2_TH,ADM2_PCODE,ADM1_EN,ADM1_TH,ADM1_PCODE,ADM0_EN,ADM0_TH,ADM0_PCODE , ST_WITHIN( ST_GeomFromText('POINT( ".$data['current_lon']."  ".$data['current_lat']." )' ) ,  geom) as status FROM geometries  ");
			foreach ($areas->result_array() as $kFile => $vFile) {
				if( $vFile['status']=='t' ){
					//print_r(  ( $vFile ) ) ;
					$dataL = $vFile ;
					unset( $vFile['status'] ) ;
					//print_r(  ( $vFile ) ) ; 
					$this->db->where('citizen_phone_number', $data['citizen_phone_number'] );
					$query = $this->db->update('citizen_profile', $vFile); 
					//print_r(  $this->db->last_query() ) ; 
					if($query==1){
						$retArea['status'] = 0 ;
						$retArea['message'] = 'Success'; 
					}else{
						$retArea['status'] = -1 ;
						$retArea['message'] = 'Fail';
					}
				}
			}

			$dataL['ani'] = $data['citizen_phone_number'] ;
			$dataL['timekey'] = time() ;
			$dataL['latitude'] = $data['current_lat'] ;
			$dataL['longitude'] = $data['current_lon'] ;
			 
			$resL = $this->curlServiceLBS( "http://127.0.0.1:8005/mb" , $dataL, "POST");
			$ret['LBS'] = json_decode( $resL , true ) ;
			$ret['boundary'] = $dataL ;
			$ret['boundary']['status'] = $retArea ;


			if($data['citizen_phone_number']==''){
				$ret['status'] = -1 ;
				$ret['message'] = 'Data invalid';
				print_r(json_encode($ret)) ; die();
			}else{
				$uploads_dir = _UPLOADS.'/locations/'.$data['citizen_phone_number'].".txt";
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
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
		}
		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$ret['duration'] =  number_format( $duration , 3, '.', ''); 
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'sendLocation', 'lbs' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');
		print_r( json_encode($ret ) ) ;
	}


	public function uploadFile()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$time_start = microtime(true);
		$file_name = "--";
		$link = "--";
		if (is_uploaded_file($_FILES['fileUpload']['tmp_name'])) {
			$uploads_dir = _UPLOADS.'/files/';
			$tmp_name = $_FILES['fileUpload']['tmp_name'];
			//$pic_name = $_FILES['fileUpload']['name'];
			$ext_name = explode('.', $_FILES['fileUpload']['name']);
			// $pic_name = time().".".$_FILES['fileUpload']['type'] ;
			$pic_name = time().".".$ext_name[sizeof($ext_name) - 1];
			$file_name = $uploads_dir.$pic_name ;
			move_uploaded_file($tmp_name, $file_name );
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
			if($_SERVER['HTTPS']=="on"){  $protocol="https://";}else{  $protocol="http://"; }
			$ret['link'] = $protocol.$_SERVER['SERVER_NAME']."/"._UPLOADS_NAME."/files/".$pic_name;
			$link = $ret['link'] ;

			$up = $this->uploadFileToHex( $_FILES , $file_name , $pic_name ) ;
			if($up['status']==0){
				//$ret['attachmentId']  ;
				$ret['link'] = $up['attachmentId'] ;
			}

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'Fail';

			//echo "File not uploaded successfully.";
		}

		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'low', 'uploadFile', 'upload' , $duration  , $ret['status'] , json_encode($ret) ,  $file_name .': File '. json_encode($_FILES['fileUpload']), 'MobileApp');
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


			$this->db->select('mapping_id, incident_id,data, citizen_phone_number , convert(varchar,modify_date,111)+\' \' +convert(varchar,modify_date,108) modify_date,incident_status');

			//////////////////////////////////////////////////////////////////////////////////

			$this->db->from( 'case_mapping' );
			$this->db->where('citizen_phone_number =',  $data['citizen_phone_number'] );
			$this->db->order_by("modify_date", "desc");
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
			$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'List', 'select' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');
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

	public function updateTicket()
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

		if($data['citizen_phone_number']=='' || $data['incident_status']=="" ){
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


			$this->db->select('mapping_id');
			$this->db->from('case_mapping');
			$this->db->where('mapping_id =',  $data['mapping_id']);
			$case = $this->db->get()->row();
			if( isset($case->mapping_id) && $case->mapping_id!=''){
				//Update
				$ds = array(
					 'incident_status' => $data['incident_status'] ,
					/* 'modify_date' =>  GETDATE()*/ 
				);
				//print_r( $ds ) ;
				$this->db->set('modify_date', 'GETDATE()', FALSE);
				$this->db->where('mapping_id', $data['mapping_id'] );
				$query = $this->db->update('case_mapping', $ds);
				if($query==1){
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';
					$ret['data'] = $data;
				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
				}

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Mapping Id : No data';
			}
		}

 

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'SocialProfile', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobileApp');

		print_r(  $res_x ) ;
	}

	function convertLL($ani=""  , $returnType ='json'){
		//header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			//print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
		// echo $ani ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;
		//$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
		$data = $data_x ;
		//$data = json_decode( $data , true ) ;
		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
		//print_r($_SERVER ) ; die();
		if($data=='' || $ani=="" ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}else{
// 			$str = "SOS!
// http://maps.google.com/maps?f=q&q=13.848697,100.639867 (18:14:08)"; 
// 			$str = "[SOS ฉุกเฉิน]
// มีเหตุฉุกเฉินและฉันต้องการความช่วยเหลือจากคุณ คุณจะได้รับแจ้งเนื่องจากคุณเป็นหนึ่งในรายชื่อติดต่อฉุกเฉินของฉัน
// ตำแหน่งปัจจุบันของฉันคือ: 71, 50 ถนน บรมราชชนนี แขวง อรุณอมรินทร์ เขตบางกอกน้อย กรุงเทพมหานคร 10700 ประเทศไทย (13°46′46.04″N 100°28′29.66″E)";
			//echo $data ;
			$mapType = "";
			$dsx = [] ;
			if ( strstr( $data , 'maps.google.com' ) ) {
				$mediaType = "google" ;
				$ds = explode("?", $data) ; 
				$ds = explode("(", $ds[1] ) ; 
				$ds = explode("&", $ds[0] ) ; 
				foreach ($ds as $key => $value) {
					$k = explode("=", $value) ;
					$dsx[$k[0]] = $k[1] ;
				}
				//print_r($dsx) ;
				$ll = explode(",", $dsx['q']);
				$ret['status'] = 0 ;
				$ret['ani'] = $ani ;
				$ret['type'] = $mediaType ;
				$ret['data']['lat'] = trim($ll[0]) ;
				$ret['data']['lon'] = trim($ll[1]) ;
			}
			if ( strstr( $data , 'maps.apple.com' ) ) {
				$mediaType = "apple" ;
				$ds = explode("?", $data) ;  
				$ds = explode("&", $ds[1] ) ; 
				foreach ($ds as $key => $value) {
					$k = explode("=", $value) ;
					$dsx[$k[0]] = $k[1] ;
				}
				//print_r($dsx) ;
				$ll = explode(",", $dsx['ll']);
				$ret['status'] = 0 ;
				$ret['ani'] = $ani ;
				$ret['type'] = $mediaType ;
				$ret['data']['lat'] = trim($ll[0]) ;
				$ret['data']['lon'] = trim($ll[1]) ;
			}
			if ( strstr( $data , '″N ' ) ) {
				$mediaType = "DMS" ;
				preg_match_all("/\(.*?\)/i", $data, $matches);
				//(13°46′46.04″N 100°28′29.66″E)
				$dataX = str_replace("(", "" , $matches[0][0] ) ;
				$dataX = str_replace(")", "" , $dataX ) ;
				$dataX = explode(" ", $dataX ) ; 

				$latDMS = $this->convertDMStoArray( trim($dataX[0]) );  
				$lonDMS = $this->convertDMStoArray( trim($dataX[1]) ); 
				$lat = $this->DMS2Decimal($latDMS[0] , $latDMS[1] , $latDMS[2] , $latDMS[3] ) ; 
				$lon = $this->DMS2Decimal($lonDMS[0] , $lonDMS[1] , $lonDMS[2] , $lonDMS[3]) ;
				$ret['status'] = 0 ;
				$ret['ani'] = $ani ;
				$ret['type'] = $mediaType ;
				$ret['data']['lat'] = $lat  ;
				$ret['data']['lon'] = $lon  ;
				 
			}  

		}
		$str = [];
		if($ret['data']['lat']!="" && $ret['data']['lon']!="" ){
			$url = _MAP_ADDRESS."?key="._MAP_KEY."&lat=".$ret['data']['lat']."&lon=".$ret['data']['lon']."&lang=th" ;
			$result = $this->curlService($url, '', 'GET');
			$res = json_decode($result , true) ;
			//print_r($res) ;
			
			$str[] = "status=0" ;
			$str[] = "latitude=".$ret['data']['lat']  ;
			$str[] = "longitude=".$ret['data']['lon']  ;
			foreach ($res as $key => $value) {
				$str[] = $key."=".$value ;
			}
			$ret['address'] = $res ;
		}
		

		if($returnType=="String"){
			//print_r($str) ;
			//status=1&latitude=12.725135&longitude=99.957702&geocode=76&province_th=จ.เพชรบุรี&province_en=Phetchaburi
			echo implode("&", $str) ;
		}else{
			header('Content-Type: application/json');
			print_r( json_encode($ret ) ) ;
		}
	}

	public function userAddress( $ani = "")
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		//$authen = $this->authen($header);
		$authen = true ;
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}
		$time_start = microtime(true);
 
		if( $ani ==''){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}
		// print_r($data['citizen_password'] ) ; die();

		 

		$this->db->select('citizen_phone_number,citizen_email,citizen_id,address');

		//////////////////////////////////////////////////////////////////////////////////

		$this->db->from( 'citizen_profile' );
		$this->db->where('citizen_phone_number =', $ani ); 
		$list = $this->db->get()->result_array();

		//print_r($list) ; die();
		$result = [] ;
		if( count($list)>0 ){
			$list = $list[0] ;
			 
			$list['address'] = json_decode($list['address'],true) ;
			$ret['status'] = 0 ;
			$ret['message'] = "" ;
			$ret['data'] = $list ;
		} else{
			$ret['status'] = -1 ;
			$ret['message'] = "No data" ;
		}
			 
		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  'WDE'  , $_SERVER['REMOTE_ADDR'] , 'info', 'UserAddress', 'select' , $duration  , $ret['status'] , $ani , $res_x  , 'MobileApp');

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

	function convertDMStoArray( $str ){
		//$str = "13°46′46.04″N" ;
		$ds = explode("°", $str ) ;  $ret[] = $ds[0] ;
		$ds = explode("′", $ds[1] ) ;  $ret[] = $ds[0] ;
		$ds = explode("″", $ds[1] ) ;  $ret[] = $ds[0] ; $ret[] = $ds[1] ;
		return $ret ; 
	}

	function DMS2Decimal($degrees = 0, $minutes = 0, $seconds = 0, $direction = 'n') {
	   //converts DMS coordinates to decimal
	   //returns false on bad inputs, decimal on success
		
	   //direction must be n, s, e or w, case-insensitive
	   $d = strtolower($direction);
	   $ok = array('n', 's', 'e', 'w');
		
	   //degrees must be integer between 0 and 180
	   if(!is_numeric($degrees) || $degrees < 0 || $degrees > 180) {
		  $decimal = false;
	   }
	   //minutes must be integer or float between 0 and 59
	   elseif(!is_numeric($minutes) || $minutes < 0 || $minutes > 59) {
		  $decimal = false;
	   }
	   //seconds must be integer or float between 0 and 59
	   elseif(!is_numeric($seconds) || $seconds < 0 || $seconds > 59) {
		  $decimal = false;
	   }
	   elseif(!in_array($d, $ok)) {
		  $decimal = false;
	   }
	   else {
		  //inputs clean, calculate
		  $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
		   
		  //reverse for south or west coordinates; north is assumed
		  if($d == 's' || $d == 'w') {
			 $decimal *= -1;
		  }
	   }
		
	   return $decimal;
	}

	function curlServiceLBS($url, $data, $method)
	{
		$ch = curl_init();
		$url = $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		 
		//curl_setopt($ch,CURLOPT_HEADER, TRUE);

		$response = curl_exec($ch);

		//print_r($response);

		return $response;
	}



	///////////////////////////////////////////////////////
	//////////////////// Hexagon ///////////////////////////
	///////////////////////////////////////////////////////
	function createEvent($res){
		$time_start = microtime(true);

		$uploads_dir = _CONFIG.'/HexToken/token.txt' ;
		$tokenStart = file_get_contents($uploads_dir)  ;
		$tokenStart = json_decode($tokenStart,true) ; 
		$token = $tokenStart['accessToken'] ;
		if( $token !="" ){
		}else{
			$token = $this->hexAuthen();
			$token = $token['token'] ;
		}
		$file  = _CONFIG.'/HexToken/patternCreateEvent.txt' ;
		$pattern = file_get_contents($file)  ;
		$pattern = json_decode($pattern,true) ;  $patternx = $pattern ;

		$fileAgency  = _CONFIG.'/HexToken/patternAgency.txt' ;
		$patternAgency = file_get_contents($fileAgency)  ;
		$patternAgency = json_decode($patternAgency,true) ;

		$convAsk = parse_ini_file(  _CONFIG.'/HexToken/convertDetail.txt' , true );

		$data = $res['data'] ;
		$pattern['latitude'] = $data['data']['case_lat']  ;
		$pattern['longitude'] = $data['data']['case_lon']  ;
		$pattern['locationString'] = $data['data']['case_location_address']  ;
		$pattern['EventType'] = $data['data']['casetype_code'] ;
		$pattern['Description'] = $data['data']['case_detail'] ;

		$agency_ = "RTP" ; 
		$alarmLevel_ = 0 ; 
		$information = "";
		switch ( $pattern['EventType'] ) {
			case '131':
				$patternAgency['agency'] = "RTP" ;
				$patternAgency['priority'] = 0 ;
				$pattern['Deployments'][] = $patternAgency ;

				break;

			case '733': 
				$patternAgency['agency'] = "TFB" ;
				$patternAgency['priority'] = 5 ;
				$pattern['Deployments'][] = $patternAgency ;

				$information .= $convAsk['case_when'][ $data['data']['case_when'] ] ;
				$information .= " ".$convAsk['case_insurjent'][ $data['data']['case_insurjent'] ] ;
				$supUnit = explode( ",", $data['data']['support_unit'] ) ; 
				$unitArray = [] ;
				$fire_sprinkler = "" ;
				foreach ($supUnit as $key => $value) {
					$unitArray[] = $convAsk['support_unit'][  $value ] ;
					if($value==1){
						$fire_sprinkler = $convAsk['fire_sprinkler'][ $data['data']['fire_sprinkler'] ] ;
					}
				}
				$information .= " ".$convAsk['support_unit'][ "pre" ].implode( ",", $unitArray ) ; 
				$information .= " ".$fire_sprinkler ; 
				$information .= " ".$convAsk['road_condition'][ $data['data']['road_condition'] ] ;
 
				break;

			case '411': 
				$patternAgency['agency'] = "TES" ;
				$patternAgency['priority'] = 1 ;
				$pattern['Deployments'][] = $patternAgency ;

				break;

			case '731': 
				$patternAgency['agency'] = "RTP" ;
				$patternAgency['priority'] = 0 ;
				$pattern['Deployments'][] = $patternAgency ;
				//$patternAgency['agency'] = "TFB" ;
				//$patternAgency['priority'] = 0 ;
				//$pattern['Deployments'][] = $patternAgency ;

				break;

			case '231':
				$patternAgency['agency'] = "RTP" ;
				$patternAgency['priority'] = 0 ;
				$pattern['Deployments'][] = $patternAgency ;
				//$patternAgency['agency'] = "TES" ;
				//$patternAgency['priority'] = 0 ;
				//$pattern['Deployments'][] = $patternAgency ;

				break;

			case '501':
				$patternAgency['agency'] = "RTP" ;
				$patternAgency['priority'] = 1 ;
				$pattern['Deployments'][] = $patternAgency ;

				break;

			case '511':
				$patternAgency['agency'] = "RTP" ;
				$patternAgency['priority'] = 0 ;
				$pattern['Deployments'][] = $patternAgency ;
				//$patternAgency['agency'] = "TES" ;
				//$patternAgency['priority'] = 0 ;
				//$pattern['Deployments'][] = $patternAgency ;
				//$patternAgency['agency'] = "TFB" ;
				//$patternAgency['priority'] = 0 ;
				//$pattern['Deployments'][] = $patternAgency ;
				break;

			default:
				# code...
				break;
		}
		//$pattern['Deployments'][0]['agency'] = $agency_ ;
		//$pattern['Deployments'][0]['alarmLevel'] = $alarmLevel_ ;

		$pattern['Calls'][0]['callId'] = $res['mapping_id']  ;
		$pattern['Calls'][0]['callTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
		$pattern['Calls'][0]['callerName'] = trim( $res['user']->citizen_name ." ". $res['user']->citizen_surname )  ; 
		$pattern['Calls'][0]['callerPhoneNumber'] = $data['citizen_phone_number']   ; 
 
		$fileLat  = _UPLOADS.'/locations/'.$data['citizen_phone_number'].'.txt' ;
		$latlon = file_get_contents($fileLat)  ;
		$latlon = json_decode($latlon,true) ; 
		$pattern['Calls'][0]['latitude'] = $latlon['current_lat']   ; 
		$pattern['Calls'][0]['longitude'] = $latlon['current_lon']   ; 
		$pattern['Calls'][0]['callSource'] = "ANI/ALI"   ; 


		$address = $this->addressService( $latlon['current_lat'] , $latlon['current_lon']  ) ;
		 
		$full_addr  = $address['aoi'].' '.$address['road'].' '.$address['subdistrict'].' '.$address['district'].' '.$address['province'].' '.$address['country'].' '.$address['postcode'] ;  
		$pattern['Calls'][0]['callerAddress'] = $full_addr  ; 

		$pattern['Schedule']['pickUpTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
		$pattern['Schedule']['dropOffTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
		$pattern['Schedule']['requestedTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
 
		$com = [] ;
		$com[] = $data['data']['case_detail']  ;
		$com[] = $information  ;
		$pattern['SimpleComments'] = $com  ; 

		//print_r(  ( $pattern) ) ;
		//die();
		$url = HEX_SERVICE.'/Event/Create' ; 
		$call = $this->curlHexService($url, json_encode( $pattern) , 'POST' , $token ) ;
		$call = json_decode( $call, true ) ;
		if($call['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
			$call = $this->curlHexService($url, json_encode( $pattern) , 'POST' , $token ) ;
			$call = json_decode( $call, true ) ;
		}

		if( count($call)>0 && $call[0]['agencyEventId']!="" ){
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
			$ret['data'] = $call[0];
			if($data['data']['case_img']!="" && $data['data']['case_img']!="null" && $data['data']['case_img']!=null){ 
				foreach ( $data['data']['case_img']  as $key_1 => $value_1) {
					$att = $this->attachments( $call[0]['agencyEventId'] , $value_1 );
				}
			}else{
				foreach ( $data['data']['case_clip']  as $key_1 => $value_1) {
					$att = $this->attachments( $call[0]['agencyEventId'] , $value_1 );
				}
			}
			
			$ret['attachments'] = $att ;

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = $call ; 
		}

		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs( 'CreteEvent'  , $_SERVER['REMOTE_ADDR'] , 'high', 'hexCreateEvent', 'create' , $duration  , $ret['status'] , json_encode($pattern) , json_encode($ret)  , 'MobileApp');
		return $ret ;
	}


	public function addressService($lat = "", $lon = "")
	{
		 
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
		$this->Writelogs_model->wLogs( 'Mobile' , $_SERVER['REMOTE_ADDR'] , $ltype , 'getAddressService', 'callws' , $duration  , $status , $url , $result , 'MobileApp'  );
		//$result = json_decode($result , true) ;

		return $res ;
		//print_r($res);
	}

	public function uploadFileToHex( $file , $imgPath , $pic_name )
	{
		$time_start = microtime(true);
		$uploads_dir = _CONFIG.'/HexToken/token.txt' ;
		$tokenStart = file_get_contents($uploads_dir)  ;
		$tokenStart = json_decode($tokenStart,true) ; 
		$token = $tokenStart['accessToken'] ;
		if( $token !="" ){
		}else{
			$token = $this->hexAuthen(); 
			$token = $token['token'] ;
		}
		$fileUp = [] ;
		$fileUp['name'] = $pic_name ;
		$fileUp['originalFilename'] =  $pic_name ;
		$fileX = file_get_contents(  $imgPath ) ;
		$fileUp['fileData'] = base64_encode( $fileX ) ;

		$f = finfo_open();
		$mime_type = finfo_buffer($f,  $fileX  , FILEINFO_MIME_TYPE); 
		$fileUp['contentType'] =  $mime_type ;
		//$fileUp['contentType'] =  'image/jpeg';
 
		$url = HEX_SERVICE.'/FileAttachment/Upload' ; 
		$result = $this->curlHexService($url, json_encode($fileUp) , 'PUT' , $token) ;
		$result = json_decode( $result , true ) ;
		if($result['errorMessage']=='not authorized'){
			$token = $this->hexAuthen() ;
			$token = $token['token'] ;
			$result = $this->curlHexService($url, json_encode($fileUp) , 'PUT' , $token) ;
			$result = json_decode( $result , true ) ;
		}

		if($result['attachmentId']!=""){ 
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
			$ret['attachmentId'] = $result['attachmentId'] ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'Fail';
			$ret['message'] = $result ;
		}
		$time_end = microtime(true);
		$duration = $time_end - $time_start;

		$fileUp['fileData'] =   $imgPath   ;
		$this->Writelogs_model->wLogs( 'Mobile' , $_SERVER['REMOTE_ADDR'] , 'high' , 'uploadFileToHex', 'callws' , $duration  , $ret['status'] , json_encode($fileUp) , json_encode($ret) , 'MobileApp'  );

		return $ret ;
	} 
	 
	/*public function upload()
	{
		//print_r($_REQUEST);
		print_r($_FILES) ;
		$f = [] ;
		$f['name'] = time() ;
		$f['contentType'] =  $_FILES['fileToUpload']['type'] ;
		$f['originalFilename'] =  $_FILES['fileToUpload']['name'] ;
		$f['fileData'] = base64_encode( file_get_contents( $_FILES['fileToUpload']['tmp_name'] ) ) ;
		echo '<br>';
		print_r($f) ;
		//$xx = file_get_contents( 'https://i.pinimg.com/originals/7f/53/9f/7f539f9023f71c01679ee4b0160b3309.jpg') ;
		//print_r( base64_encode( $xx) )  ;
		echo $url = HEX_SERVICE.'/FileAttachment/Upload' ;  
		$call = $this->curlHexService($url, json_encode($f) , 'PUT' , 'eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJvaWRjVG9rZW4iOiJleUowZVhBaU9pSktWMVFpTENKaGJHY2lPaUpTVXpJMU5pSXNJbmcxZENJNkltRXpjazFWWjAxR2RqbDBVR05zVEdFMmVVWXpla0ZyWm5GMVJTSXNJbXRwWkNJNkltRXpjazFWWjAxR2RqbDBVR05zVEdFMmVVWXpla0ZyWm5GMVJTSjkuZXlKcGMzTWlPaUpvZEhSd09pOHZhR1Y0TFc5dVkyRnNiQzR4T1RGd2IyTXViRzlqWVd3dmIyNWpZV3hzTDJsa1pXNTBhWFI1SWl3aVlYVmtJam9pYUhSMGNEb3ZMMmhsZUMxdmJtTmhiR3d1TVRreGNHOWpMbXh2WTJGc0wyOXVZMkZzYkM5cFpHVnVkR2wwZVM5eVpYTnZkWEpqWlhNaUxDSmxlSEFpT2pFMk1EQTBNakk0TURJc0ltNWlaaUk2TVRZd01EUXhPVEl3TWl3aVkyeHBaVzUwWDJsa0lqb2lRMkZzYkhadmFXTmxJaXdpWTJ4cFpXNTBYMFZ0Y0d4dmVXVmxTV1FpT2lJeUlpd2lZMnhwWlc1MFgyRndhVU5zYVdWdWRDSTZJblJ5ZFdVaUxDSnpZMjl3WlNJNkltRndhU0o5LkZ5RWotT3RxSWZoWFIzdHo1bjlabVg3bXJscG91UzFxQVBiNXNzUk9WNmgxNW1rOUJqVFlRdEhZVVdqUmdSWUl2aWtGb3BCSkFucm9KS0RldTN4ak1uU0xEWGZxMXpMVXBjQ0VaLTBxcjNiUXU1dEc0WDNPNnkyMjZmdDdxSDRIUFFlR21nREllZVpXVFJ0ajVlVmg5NW9pcXpFM3Y0OVpaRHlxZ3lqRDBrZS1lTFhTOThxZU1vSGZqNlhXd2RvZG1VOVN2VV9wLVY5Q3gxNTE1aktLZ0l1NjY3c3c4Nmo1UW1nc0ZGS1FoS3lHSzV5eW15MVp6eVFOVTR3NWRsOW45R3loaGpGYU1fQ3I1cllRcC1iQmV1TUFXQ1JsQzBtWkpXVDVXcVlvMEIwemJncHpCYlpXbVFPSlBEX1JhRDBrU1Bnb2Z4NmdSbENoY0kwQ3ItOHNlZyIsInNlc3Npb25JZCI6IjI5ZWQ4OTQ2LTRhMjEtNDNiNC04YzU5LWZhZjc3MmVjNjAxZSIsIkVtcGxveWVlSWQiOiIyIiwiTGFuZ3VhZ2UiOiJlbi11cyIsIm5iZiI6MTYwMDQxOTIwMiwiZXhwIjoxNjAwNDIyODAyLCJpc3MiOiJSZXN0ZnVsQ2FkIiwiYXVkIjoiYWxsIn0.6oEi4yC-I067_A1ItHq4eQYEE2jgqSXyHi9v2046BVk' ) ;
		print_r($call) ;
	}*/

	public function attachments( $agencyEventId , $AttachmentIds )
	{
		$time_start = microtime(true);
		$uploads_dir = _CONFIG.'/HexToken/token.txt' ;
		$tokenStart = file_get_contents($uploads_dir)  ;
		$tokenStart = json_decode($tokenStart,true) ; 
		$token = $tokenStart['accessToken'] ;
		if( $token !="" ){
		}else{
			$token = $this->hexAuthen();
			$token = $token['token'] ;
		}
		 
		 
		$url = HEX_SERVICE.'/Event/'.$agencyEventId.'/Attachments?AttachmentIds='.$AttachmentIds ; 
		$result = $this->curlHexService($url, '' , 'PUT' , $token) ;
		$result = json_decode( $result , true ) ;
		if($result['errorMessage']=='not authorized'){
			$token = $this->hexAuthen();
			$token = $token['token'] ;
			$result = $this->curlHexService($url, '' , 'PUT' , $token) ;
			$result = json_decode( $result , true ) ;
		}

		if( $result=="" ){
			$ret['status'] = 0 ;
			$ret['message'] = 'Success'; 
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'Fail';
		}
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs( 'Mobile' , $_SERVER['REMOTE_ADDR'] , 'high' , 'attachFiletoHex', 'callws' , $duration  , $ret['status'] , $url , json_encode($ret) , 'MobileApp'  );

		return $ret ;
	}

	function hexAuthen(){
		$time_start = microtime(true);
		$url = HEX_AUTHEN_TOKEN ; 
		$token = $this->curlHexAuthen($url, HEX_AUTHEN_DATA , 'POST') ;
		$token = json_decode($token,true) ; 
		
		$ret = [] ; 


		if( $token['access_token']!="" ){
			$uploads_dir = _CONFIG.'/HexToken/authenToken.txt' ;
			if( file_put_contents( $uploads_dir , json_encode($token)) ){
				$ret['status'] = 0 ; 
				$ret['authenToken'] = $token['access_token'] ; 


				$url = HEX_TOKEN_START.'?Token='.$token['access_token'] ; 
				$tokenStart = $this->curlHexAuthen($url, HEX_AUTHEN_DATA , 'POST') ;
				$tokenStart = json_decode($tokenStart,true) ;
				//print_r($tokenStart) ;
				if( $tokenStart['accessToken']!="" ){
					$uploads_dir = _CONFIG.'/HexToken/token.txt' ;
					if( file_put_contents( $uploads_dir , json_encode($tokenStart)) ){
						$ret['status'] = 0 ; 
						$ret['token'] = $tokenStart['accessToken'] ; 

					}else{
						$ret['status'] = -1 ; 
						$ret['token'] = false ; 
					}
				}


			}else{
				$ret['status'] = -1 ; 
				$ret['authenToken'] = false ; 
			}
		}

		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs( 'Authen'  , $_SERVER['REMOTE_ADDR'] , 'high', 'hexAuthenToken', 'authen' , $duration  , $ret['status'] , '' , json_encode($ret)  , 'MobileApp');

		return $ret ;
	}

	function curlHexAuthen($url, $data, $method)
	{
		$ch = curl_init();
		$url = $url;
		//echo $data ;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS,  ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		 
		//curl_setopt($ch,CURLOPT_HEADER, TRUE);

		$response = curl_exec($ch);

		//print_r($response);

		return $response;
	}

	function curlHexService($url, $data, $method , $token )
	{
		$ch = curl_init();
		$url = $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS,  ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$header[] =  'Content-Type: application/json'  ;
		$header[] =  'Accept: application/json'  ;
		$header[] =  'Authorization: Bearer '. $token  ;
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header );
		 
		//curl_setopt($ch,CURLOPT_HEADER, TRUE);

		$response = curl_exec($ch);

		//print_r($response);

		return $response;
	}

	public function campaign(){
		//ini_set('display_errors', '1');
		//ini_set('display_startup_errors', '1');
		//error_reporting(E_ALL);
		//Initial header
		header("Content-Type:application/json");
		$_header=apache_request_headers();
		$_authen=$this->authen($_header);
		//Initial variable
		$_start=microtime(TRUE);
		$_caller=$_SERVER['REMOTE_ADDR'];
		$_ip_address=$_SERVER['REMOTE_ADDR'];
		$_severity='info';
		$_module='campaign';
		$_task=$_SERVER['REQUEST_METHOD'];
		$_status=-1;
		$_request=json_encode([]);
		$_result=json_encode([]);
		$_directory='MobileApp';
		$_message='';
		$_query='';
		$_result_array=[];
		//Receive input request
		$_input=file_get_contents('php://input');
		$_input_rpe=nl2br(str_replace(array("\n","\r")," ",$_input));
		$_data=json_decode($_input_rpe,TRUE);
		$_pretty_input=json_encode($_data);
		//Authentication
		if($_authen!==TRUE){
			$_status=-2;
			$_message='Authentication failed please try again';
			$_severity='high';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
			$_return=json_encode($_result);
			$_end=microtime(TRUE);
			$_duration=$_end-$_start;
			$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
			print_r($_return);
			die();
		}
		//Data valid or invalid
		/*
		foreach($_data as $_key=>$_value){
			if($_value==''){
				$_status=-1;
				$_message='Data invalid: '.$_key;
				$_severity='high';
				$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
				$_return=json_encode($_result);
				$_end=microtime(TRUE);
				$_duration=$_end-$_start;
				$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
				print_r($_return);
				die();
			}
		}
		*/
		//Main function
		$C3I=$this->load->database('C3I',TRUE);
		if($_task=='POST'){
			$_columns=[];
			$_values=[];
			$_dt=$_data;
			$_dt['ID']='C'.date('YmdHis').(sprintf('%03d',round((microtime()-floor(microtime()))*999)));
			$_dt['CreateDate']=date('Y-m-d H:i:s');
			$_dt['ModifyDate']=$_dt['CreateDate'];
			if(isset($_data['Photo'])&&$_data['Photo']!=''){
				$_base64_string=$_data['Photo'];
				$_output_name='/uploads/'.$_module.'/'.$_dt['ID'].'.jpg';
				$_output_file='/var/www/html'.$_output_name;
				$_uploaded=$this->base64_to_img_uploader($_base64_string,$_output_file);
				if($_uploaded===1)
					$_dt['Photo']=($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['HTTP_HOST'].$_output_name;
				else{
					$_status=-1;
					$_message='Photo upload failed please try again';
					$_severity='medium';
					$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
					$_return=json_encode($_result);
					$_end=microtime(TRUE);
					$_duration=$_end-$_start;
					$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
					print_r($_return);
					die();
				}
			}
			$_pretty_input=json_encode($_dt);
			foreach($_dt as $key=>$value){
				$_columns[]='['.$C3I->escape_str($key).']';
				$_values[]=$C3I->escape_str($value);
			}
			$_dt_tbl=$C3I->escape_str($_module);
			$_dt_columns=implode(',',$_columns);
			$_dt_values=$_values;
			$_dt_query="INSERT INTO [{$_dt_tbl}] ({$_dt_columns}) VALUES ?";
			$C3I->trans_begin();
			$C3I->query($_dt_query,[$_dt_values]);
			if ($C3I->trans_status()===FALSE){
				$C3I->trans_rollback();
				$_status=-1;
				$_severity='high';
			}
			else{
				$C3I->trans_commit();
				$_status=0;
			}
		}
		else if($_task=='GET'){
			$_where=[];
			foreach($_data as $key=>$value)
				$_where[$key]=$value;
			if(count($_where)>0)
				$C3I->where($_where);
			$_query_result=$C3I->get($_module);
			if(!$_query_result){
				$_result_array=[];
				$_status=-1;
				$_severity='high';
			}
			else{
				$_result_array=$_query_result->result_array();
				if(count($_result_array)>0){
					$_status=0;
				}
				else{
					$_status=-1;
					$_message='No matching records found';
					$_severity='medium';
				}
			}
		}
		else if($_task=='PUT'){
			$_columns=[];
			$_dt=$_data;
			$_dt['ModifyDate']=date('Y-m-d H:i:s');
			$_where_field='ID';
			$_where_value=$_dt['ID'];
			if(isset($_data['Photo'])&&$_data['Photo']!=''){
				$_base64_string=$_data['Photo'];
				$_output_name='/uploads/'.$_module.'/'.$_dt['ID'].'.jpg';
				$_output_file='/var/www/html'.$_output_name;
				$_uploaded=$this->base64_to_img_uploader($_base64_string,$_output_file);
				if($_uploaded===1)
					$_dt['Photo']=($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['HTTP_HOST'].$_output_name;
				else{
					$_status=-1;
					$_message='Photo upload failed please try again';
					$_severity='medium';
					$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
					$_return=json_encode($_result);
					$_end=microtime(TRUE);
					$_duration=$_end-$_start;
					$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
					print_r($_return);
					die();
				}
			}
			else if(!isset($_data['Photo'])||$_data['Photo']==''){
				$_output_file='/var/www/html/uploads/'.$_module.'/'.$_dt['ID'].'.jpg';
				if(file_exists($_output_file)){
					if(!unlink($_output_file)){
						$_status=-1;
						$_message='Photo delete failed please try again';
						$_severity='medium';
						$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
						$_return=json_encode($_result);
						$_end=microtime(TRUE);
						$_duration=$_end-$_start;
						$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
						print_r($_return);
						die();
					}
				}
			}
			unset($_dt['ID']);
			unset($_dt['CreateDate']);
			unset($_dt['CreateBy']);
			$_pretty_input=json_encode($_dt);
			foreach($_dt as $key=>$value){
				$_columns[]='['.$C3I->escape_str($key).']='.$C3I->escape($value);
			}
			$_dt_tbl=$C3I->escape_str($_module);
			$_dt_columns=implode(',',$_columns);
			$_dt_query="UPDATE [{$_dt_tbl}] SET {$_dt_columns} WHERE [{$_where_field}] = ?";
			$C3I->trans_begin();
			$C3I->query($_dt_query,[$_where_value]);
			if ($C3I->trans_status()===FALSE){
				$C3I->trans_rollback();
				$_status=-1;
				$_severity='high';
			}
			else{
				$C3I->trans_commit();
				$_status=0;
			}
		}
		else if($_task=='DELETE'){
			$_dt=$_data;
			$_output_file='/var/www/html/uploads/'.$_module.'/'.$_dt['ID'].'.jpg';
			if(file_exists($_output_file)){
				if(!unlink($_output_file)){
					$_status=-1;
					$_message='Photo delete failed please try again';
					$_severity='medium';
					$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
					$_return=json_encode($_result);
					$_end=microtime(TRUE);
					$_duration=$_end-$_start;
					$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
					print_r($_return);
					die();
				}
			}
			$_where_field='ID';
			$_where_value=$_dt['ID'];
			$_pretty_input=json_encode($_dt);
			$_dt_tbl=$C3I->escape_str($_module);
			$_dt_query="DELETE FROM [{$_dt_tbl}] WHERE [{$_where_field}] = ?";
			$C3I->trans_begin();
			$C3I->query($_dt_query,[$_where_value]);
			if ($C3I->trans_status()===FALSE){
				$C3I->trans_rollback();
				$_status=-1;
				$_severity='high';
			}
			else{
				$C3I->trans_commit();
				$_status=0;
			}
		}
		if($_status==0){
			$_message='Success';
			$_severity='info';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
			if(count($_result_array)>0){
				$_result['data']=$_result_array;
			}
		}
		else{
			$_error=$C3I->error();
			if($_error['code'])
				$_status=$_error['code'];
			if($_error['message'])
				$_message=$_error['message'];
			$_query=$C3I->last_query();
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity,'query'=>$_query];
		}
		$_return=json_encode($_result);
		$_end=microtime(TRUE);
		$_duration=$_end-$_start;
		$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
		print_r($_return);
		die();
	}

	public function base64_to_img_uploader($base64_string,$output_file){
		$ifp=fopen($output_file,'wb');
		if(fwrite($ifp,base64_decode($base64_string))!==FALSE)
			$uploaded=1;
		else
			$uploaded=0;
		fclose($ifp);
		return $uploaded;
	}

	/*
	public function campaign($methods=''){
		header('Content-Type: application/json');
		$header=apache_request_headers();
		$authen=$this->authen($header);
		if($authen!=TRUE){
			$ret['status']=-2;
			$ret['message']='Authentication failed please try again';
			print_r(json_encode($ret));
			die();
		}
		$time_start=microtime(TRUE);
		$input=file_get_contents('php://input');
		$data=$input;
		$data=nl2br(str_replace(array("\n","\r")," ",$data));
		$data=json_decode($data,TRUE);
		foreach($data as $key=>$value){
			if($value==''){
				$ret['status']=-1;
				$ret['message']='Data invalid: '.$key;
				$res=json_encode($ret);
				$time_end=microtime(TRUE);
				$duration=$time_end-$time_start;
				$this->Writelogs_model->wLogs(
					$_SERVER['REMOTE_ADDR'],
					$_SERVER['REMOTE_ADDR'],
					'high',
					'campaign',
					$methods,
					$duration,
					$ret['status'],
					$input,
					$res,
					'MobileApp'
				);
				print_r($res_x);
				die();
			}
		}
		if($data['campaign_id']!=''){
			$campaign_path=_CONFIG.'campaign/'.$data['campaign_id'].'.json';
			if($methods=='read'OR$methods=='update'){
				$campaign_message=file_get_contents($campaign_path);
				if($campaign_message!==FALSE){
					$data['data']=(array)json_decode($campaign_message);
					if($methods=='read'){
						if(count($data['data'])>0){
							$campaign_status=0;
						}
						else{
							$campaign_status=-1;
						}
					}
					else if($methods=='update'){
						$data['created_date']=$data['data']['created_date'];
						$data['updated_date']=date('Y-m-d H:i:s');
						unset($data['data']);
						$this_campaign=file_put_contents($campaign_path,json_encode($data));
						if($this_campaign!==FALSE){
							$campaign_status=0;
						}
						else{
							$campaign_status=-1;
						}
					}
					else{
						$campaign_status=-1;
					}
				}
				else{
					$campaign_status=-1;
				}
			}
			else if($methods=='delete'){
				if(unlink($campaign_path)){
					$campaign_status=0;
				}
				else{
					$campaign_status=-1;
				}
			}
			if($campaign_status==0){
				$ret['status']=0;
				$ret['message']='Success';
				$ret['data']=$data;
				$ret['severity']='info';
			}
			else{
				$ret['status']=-1;
				$ret['message']='Fail';
				$ret['severity']='high';
			}
		}
		else{
			if($methods=='create'){
				$data['campaign_id']='C'.date('YmdHis').(sprintf("%03d",round((microtime()-floor(microtime()))*999)));
				$data['created_date']=date('Y-m-d H:i:s');
				$campaign_path=_CONFIG.'campaign/'.$data['campaign_id'].'.json';
				$new_campaign=file_put_contents($campaign_path,json_encode($data));
				if($new_campaign!==FALSE){
					chmod($campaign_path,0777);
					$ret['status']=0;
					$ret['message']='Success';
					$ret['data']=$data;
					$ret['severity']='info';
				}
				else{
					$ret['status']=-1;
					$ret['message']='Fail';
					$ret['severity']='high';
				}
			}
			else{
				$campaign_path=_CONFIG.'campaign';
				if($handle=opendir($campaign_path)){
					$check_approve=isset($data['check_approve'])?$data['check_approve']:'';
					$data=[];
					while(FALSE!==($entry=readdir($handle))){
						if($entry!='.'&&$entry!='..'){
							$campaign_message=file_get_contents($campaign_path.'/'.$entry);
							$campaign_arr=(array)json_decode($campaign_message);
							if($check_approve!=''){
								if($check_approve==$campaign_arr['check_approve']){
									$data[]=$campaign_arr;
								}
							}
							else{
								$data[]=$campaign_arr;
							}
							//$data[]=(array)json_decode($campaign_message);
						}
					}
					closedir($handle);
					$ret['status']=0;
					$ret['message']='Success';
					$ret['data']=$data;
					$ret['severity']='info';
				}
				else{
					$ret['status']=-1;
					$ret['message']='Fail';
					$ret['severity']='high';
				}
			}
		}
		$res=json_encode($ret);
		$time_end=microtime(TRUE);
		$duration=$time_end-$time_start;
		$this->Writelogs_model->wLogs(
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['REMOTE_ADDR'],
			$ret['severity'],
			'campaign',
			$methods,
			$duration,
			$ret['status'],
			$input,
			$res,
			'MobileApp'
		);
		print_r($res);
	}
	*/

	public function polygon(){
		//ini_set('display_errors', '1');
		//ini_set('display_startup_errors', '1');
		//error_reporting(E_ALL);
		//Initial header
		header("Content-Type:application/json");
		$_header=apache_request_headers();
		$_authen=$this->authen($_header);
		//Initial variable
		$_start=microtime(TRUE);
		$_caller=$_SERVER['REMOTE_ADDR'];
		$_ip_address=$_SERVER['REMOTE_ADDR'];
		$_severity='info';
		$_module='polygon';
		$_task=$_SERVER['REQUEST_METHOD'];
		$_status=-1;
		$_request=json_encode([]);
		$_result=json_encode([]);
		$_directory='MobileApp';
		$_message='';
		$_query='';
		$_result_array=[];
		//Receive input request
		$_input=file_get_contents('php://input');
		$_input_rpe=nl2br(str_replace(array("\n","\r")," ",$_input));
		$_data=json_decode($_input_rpe,TRUE);
		$_pretty_input=json_encode($_data);
		//Authentication
		if($_authen!==TRUE){
			$_status=-2;
			$_message='Authentication failed please try again';
			$_severity='high';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
			$_return=json_encode($_result);
			$_end=microtime(TRUE);
			$_duration=$_end-$_start;
			$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
			print_r($_return);
			die();
		}
		//Data valid or invalid
		/*
		foreach($_data as $_key=>$_value){
			if($_value==''){
				$_status=-1;
				$_message='Data invalid: '.$_key;
				$_severity='high';
				$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
				$_return=json_encode($_result);
				$_end=microtime(TRUE);
				$_duration=$_end-$_start;
				$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
				print_r($_return);
				die();
			}
		}
		*/
		//Main function
		$C3I=$this->load->database('C3I',TRUE);
		if($_task=='POST'){
			$_columns=[];
			$_values=[];
			$_dt=$_data;
			$_dt['ID']='P'.date('YmdHis').(sprintf('%03d',round((microtime()-floor(microtime()))*999)));
			$_dt['CreateDate']=date('Y-m-d H:i:s');
			$_dt['ModifyDate']=$_dt['CreateDate'];
			$_pretty_input=json_encode($_dt);
			foreach($_dt as $key=>$value){
				$_columns[]='['.$C3I->escape_str($key).']';
				$_values[]=$C3I->escape_str($value);
			}
			$_dt_tbl=$C3I->escape_str($_module);
			$_dt_columns=implode(',',$_columns);
			$_dt_values=$_values;
			$_dt_query="INSERT INTO [{$_dt_tbl}] ({$_dt_columns}) VALUES ?";
			$C3I->trans_begin();
			$C3I->query($_dt_query,[$_dt_values]);
			if ($C3I->trans_status()===FALSE){
				$C3I->trans_rollback();
				$_status=-1;
				$_severity='high';
			}
			else{
				$C3I->trans_commit();
				$_status=0;
			}
		}
		else if($_task=='GET'){
			$_where=[];
			foreach($_data as $key=>$value)
				$_where[$key]=$value;
			if(count($_where)>0)
				$C3I->where($_where);
			$_query_result=$C3I->get($_module);
			if(!$_query_result){
				$_result_array=[];
				$_status=-1;
				$_severity='high';
			}
			else{
				$_result_array=$_query_result->result_array();
				if(count($_result_array)>0){
					$_status=0;
				}
				else{
					$_status=-1;
					$_message='No matching records found';
					$_severity='medium';
				}
			}
		}
		else if($_task=='PUT'){
			$_columns=[];
			$_dt=$_data;
			$_dt['ModifyDate']=date('Y-m-d H:i:s');
			$_where_field='ID';
			$_where_value=$_dt['ID'];
			unset($_dt['ID']);
			unset($_dt['CreateDate']);
			unset($_dt['CreateBy']);
			$_pretty_input=json_encode($_dt);
			foreach($_dt as $key=>$value){
				$_columns[]='['.$C3I->escape_str($key).']='.$C3I->escape($value);
			}
			$_dt_tbl=$C3I->escape_str($_module);
			$_dt_columns=implode(',',$_columns);
			$_dt_query="UPDATE [{$_dt_tbl}] SET {$_dt_columns} WHERE [{$_where_field}] = ?";
			$C3I->trans_begin();
			$C3I->query($_dt_query,[$_where_value]);
			if ($C3I->trans_status()===FALSE){
				$C3I->trans_rollback();
				$_status=-1;
				$_severity='high';
			}
			else{
				$C3I->trans_commit();
				$_status=0;
			}
		}
		else if($_task=='DELETE'){
			$_dt=$_data;
			$_queries=[];
			$C3I->select('ID,PolygonID')->like('PolygonID',$_dt['ID']);
			$_query_result=$C3I->get('campaign');
			if($_query_result){
				$_result_array=$_query_result->result_array();
				foreach($_result_array as $keys=>$values){
					$_PolygonID=explode(",",$values['PolygonID']);
					foreach($_PolygonID as $key=>$value){
						if($value==$_dt['ID'])
							unset($_PolygonID[$key]);
					}
					$_PolygonNew=implode(",",$_PolygonID);
					$_queries[]="UPDATE [campaign] SET [PolygonID] = '{$_PolygonNew}' WHERE [ID] = '".$values['ID']."'";
				}
			}
			$_where_field='ID';
			$_where_value=$_dt['ID'];
			$_dt_tbl=$C3I->escape_str($_module);
			$_pretty_input=json_encode($_dt);
			$_dt_query="DELETE FROM [{$_dt_tbl}] WHERE [{$_where_field}] = ?";
			$C3I->trans_begin();
			foreach($_queries as $key=>$value)
				$C3I->query($value);
			$C3I->query($_dt_query,[$_where_value]);
			if ($C3I->trans_status()===FALSE){
				$C3I->trans_rollback();
				$_status=-1;
				$_severity='high';
			}
			else{
				$C3I->trans_commit();
				$_status=0;
			}
		}
		if($_status==0){
			$_message='Success';
			$_severity='info';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
			if(count($_result_array)>0){
				$_result['data']=$_result_array;
			}
		}
		else{
			$_error=$C3I->error();
			if($_error['code'])
				$_status=$_error['code'];
			if($_error['message'])
				$_message=$_error['message'];
			$_query=$C3I->last_query();
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity,'query'=>$_query];
		}
		$_return=json_encode($_result);
		$_end=microtime(TRUE);
		$_duration=$_end-$_start;
		$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
		print_r($_return);
		die();
	}

	/*
	public function polygon($methods=''){
		header('Content-Type: application/json');
		$header=apache_request_headers();
		$authen=$this->authen($header);
		if($authen!=TRUE){
			$ret['status']=-2;
			$ret['message']='Authentication failed please try again';
			print_r(json_encode($ret));
			die();
		}
		$time_start=microtime(TRUE);
		$input=file_get_contents('php://input');
		$data=$input;
		$data=nl2br(str_replace(array("\n","\r")," ",$data));
		$data=json_decode($data,TRUE);
		foreach($data as $key=>$value){
			if($value==''){
				$ret['status']=-1;
				$ret['message']='Data invalid: '.$key;
				$res=json_encode($ret);
				$time_end=microtime(TRUE);
				$duration=$time_end-$time_start;
				$this->Writelogs_model->wLogs(
					$_SERVER['REMOTE_ADDR'],
					$_SERVER['REMOTE_ADDR'],
					'high',
					'polygon',
					$methods,
					$duration,
					$ret['status'],
					$input,
					$res,
					'MobileApp'
				);
				print_r($res_x);
				die();
			}
		}
		if($data['polygon_id']!=''){
			$polygon_path=_CONFIG.'polygon/'.$data['polygon_id'].'.json';
			if($methods=='read'OR$methods=='update'){
				$polygon_message=file_get_contents($polygon_path);
				if($polygon_message!==FALSE){
					$data['data']=(array)json_decode($polygon_message);
					if($methods=='read'){
						if(count($data['data'])>0){
							$polygon_status=0;
						}
						else{
							$polygon_status=-1;
						}
					}
					else if($methods=='update'){
						$data['created_date']=$data['data']['created_date'];
						$data['updated_date']=date('Y-m-d H:i:s');
						unset($data['data']);
						$this_polygon=file_put_contents($polygon_path,json_encode($data));
						if($this_polygon!==FALSE){
							$polygon_status=0;
						}
						else{
							$polygon_status=-1;
						}
					}
					else{
						$polygon_status=-1;
					}
				}
				else{
					$polygon_status=-1;
				}
			}
			else if($methods=='delete'){
				if(unlink($polygon_path)){
					$polygon_status=0;
				}
				else{
					$polygon_status=-1;
				}
			}
			if($polygon_status==0){
				$ret['status']=0;
				$ret['message']='Success';
				$ret['data']=$data;
				$ret['severity']='info';
			}
			else{
				$ret['status']=-1;
				$ret['message']='Fail';
				$ret['severity']='high';
			}
		}
		else{
			if($methods=='create'){
				$data['polygon_id']='P'.date('YmdHis').(sprintf("%03d",round((microtime()-floor(microtime()))*999)));
				$data['created_date']=date('Y-m-d H:i:s');
				$polygon_path=_CONFIG.'polygon/'.$data['polygon_id'].'.json';
				$new_polygon=file_put_contents($polygon_path,json_encode($data));
				if($new_polygon!==FALSE){
					chmod($polygon_path,0777);
					$ret['status']=0;
					$ret['message']='Success';
					$ret['data']=$data;
					$ret['severity']='info';
				}
				else{
					$ret['status']=-1;
					$ret['message']='Fail';
					$ret['severity']='high';
				}
			}
			else{
				$polygon_path=_CONFIG.'polygon';
				if($handle=opendir($polygon_path)){
					$data=[];
					while(FALSE!==($entry=readdir($handle))){
						if($entry!='.'&&$entry!='..'){
							$polygon_message=file_get_contents($polygon_path.'/'.$entry);
							$data[]=(array)json_decode($polygon_message);
						}
					}
					closedir($handle);
					$ret['status']=0;
					$ret['message']='Success';
					$ret['data']=$data;
					$ret['severity']='info';
				}
				else{
					$ret['status']=-1;
					$ret['message']='Fail';
					$ret['severity']='high';
				}
			}
		}
		$res=json_encode($ret);
		$time_end=microtime(TRUE);
		$duration=$time_end-$time_start;
		$this->Writelogs_model->wLogs(
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['REMOTE_ADDR'],
			$ret['severity'],
			'polygon',
			$methods,
			$duration,
			$ret['status'],
			$input,
			$res,
			'MobileApp'
		);
		print_r($res);
	}
	*/

	public function notification(){
		//ini_set('display_errors', '1');
		//ini_set('display_startup_errors', '1');
		//error_reporting(E_ALL);
		//Initial header
		header("Content-Type:application/json");
		$_header=apache_request_headers();
		$_authen=$this->authen($_header);
		//Initial variable
		$_start=microtime(TRUE);
		$_caller=$_SERVER['REMOTE_ADDR'];
		$_ip_address=$_SERVER['REMOTE_ADDR'];
		$_severity='info';
		$_module='notification';
		$_task=$_SERVER['REQUEST_METHOD'];
		$_status=-1;
		$_request=json_encode([]);
		$_result=json_encode([]);
		$_directory='MobileApp';
		$_message='';
		//Receive input request
		$_input=file_get_contents('php://input');
		$_input_rpe=nl2br(str_replace(array("\n","\r")," ",$_input));
		$_data=json_decode($_input_rpe);
		$_pretty_input=json_encode($_data);
		//Authentication
		if($_authen!==TRUE){
			$_status=-2;
			$_message='Authentication failed please try again';
			$_severity='high';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
			$_return=json_encode($_result);
			$_end=microtime(TRUE);
			$_duration=$_end-$_start;
			$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
			print_r($_return);
			die();
		}
		//Data valid or invalid
		foreach($_data as $_key=>$_value){
			if($_value==''){
				$_status=-1;
				$_message='Data invalid: '.$_key;
				$_severity='high';
				$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
				$_return=json_encode($_result);
				$_end=microtime(TRUE);
				$_duration=$_end-$_start;
				$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
				print_r($_return);
				die();
			}
		}
		//Main function
		$_py_fcm=file_get_contents('/var/www/html/191ws/pyFcm/serviceAccountKey.json');
		$_service_account_key=json_decode($_py_fcm);
		$_project_id=$_service_account_key->project_id;
		$_url='https://fcm.googleapis.com/v1/projects/'.$_project_id.'/messages:send';
		$_access_token=$this->get_access_token();
		if($_access_token==''){
			$_status=-1;
			$_message='Get access token failed please try again';
			$_severity='high';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity];
			$_return=json_encode($_result);
			$_end=microtime(TRUE);
			$_duration=$_end-$_start;
			$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
			print_r($_return);
			die();
		}
		$_headers=['Authorization:Bearer '.$_access_token,'Content-Type:application/json'];
		$_ch=curl_init();
		curl_setopt($_ch,CURLOPT_URL,$_url);
		curl_setopt($_ch,CURLOPT_POST,TRUE);
		curl_setopt($_ch,CURLOPT_HTTPHEADER,$_headers);
		curl_setopt($_ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($_ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($_ch,CURLOPT_POSTFIELDS,$_input);
		$_result_exec=curl_exec($_ch);
		$_result_str=(array)$_result_exec;
		$_result_arr=json_decode($_result_str[0]);
		if(!isset($_result_arr->error)){
			$_status=0;
			$_message='Success';
			$_result_msg=$_result_arr;
			$_severity='info';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity,'result'=>$_result_msg];
		}
		else{
			$_status=-1;
			$_message='Fail';
			$_severity='high';
			if(isset($_result_arr->error)){
				if(isset($_result_arr->error->code)&&$_result_arr->error->code==401){
					$_build_access_token=$this->build_access_token();
					if($_build_access_token===TRUE)
						$this->notification();
				}
				else
					$_result_msg=$_result_arr->error;
			}
			else if(curl_errno($_ch))
				$_result_msg=curl_error($_ch);
			else
				$_result_msg='';
			$_result=['status'=>$_status,'message'=>$_message,'severity'=>$_severity,'result'=>$_result_msg];
		}
		curl_close($_ch);
		$_return=json_encode($_result);
		$_end=microtime(TRUE);
		$_duration=$_end-$_start;
		$this->Writelogs_model->wLogs($_caller,$_ip_address,$_severity,$_module,$_task,$_duration,$_status,$_pretty_input,$_return,$_directory);
		print_r($_return);
		die();
	}

	public function build_access_token(){
		$ret=exec("node /var/www/html/fcm/build_access_token.js",$out,$err);
		if(!$err){
			$access_token_path="/var/www/html/fcm/access_token.txt";
			$access_token_file=file_put_contents($access_token_path,$out[0]);
			if($access_token_file!==FALSE){
				chmod($access_token_path,0777);
				return TRUE;
			}
			else
				return FALSE;
		}
		else
			return FALSE;
	}

	public function get_access_token(){
		$access_token_file=file_get_contents("/var/www/html/fcm/access_token.txt");
		if($access_token_file!='')
			return $access_token_file;
		else{
			$access_token=$this->build_access_token();
			if($access_token===TRUE)
				$this->get_access_token();
			else
				return '';
		}
	}

}