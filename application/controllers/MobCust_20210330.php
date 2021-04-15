<?php error_reporting(E_ERROR) ;
date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');
 
class MobCust extends CI_Controller
{

	public function __construct()
	{ 

		parent::__construct();
		////$this->load->database("citizenDB");
		//$this->db = $this->load->database("default", TRUE);
		//$this->db ;
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
		$this->load->helper('base64');
	}

	public function index($gen = "")
	{
		//$this->load->view('welcome_message');
		 //$this->load->helper('base64_helper');
	 	//echo "Decrypted String: " ;
	 	
		//$this->load->view('mobile_api_v2');
		//echo _AUTHEN_USER_CUST.'--'._AUTHEN_CYPHER.'--'._AUTHEN_KEY.'--'._AUTHEN_ENCRYPT ;
		if($gen==true){

			$encryption = openssl_encrypt( _AUTHEN_USER_CUST , _AUTHEN_CYPHER ,  _AUTHEN_KEY , $options, _AUTHEN_ENCRYPT );
			echo $authen = base64_encode( $encryption ) ;
			echo '<br>';
		  	echo $decryption=openssl_decrypt ( base64_decode($authen) , _AUTHEN_CYPHER ,  _AUTHEN_KEY, $options, _AUTHEN_ENCRYPT );

		  	echo '<hr>';
		  	
		  	echo $xx = _base64_encrypt( '{"username":"watee14","password":"watee1234","tenant":"000020","expire":"1614075615"}' ) ;
		  	echo   _base64_decrypt( $xx ) ; 
		}
		

	} 

	function connDB( $database , $username ){
		$time_start = microtime(true);

		$database = 'welcome_'. $database.'_smm';
		$DB2 = $this->load->database('mysqli://'.DB_USER.':'.DB_PASS.'@'.DB_SERVER.'/'.$database , TRUE);
		//print_r( $DB2 ) ;
		if( $DB2->conn_id->thread_id) {
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
			$duration = microtime(true)  - $time_start; 
		   $this->Writelogs_model->wLogs(  $username  , $_SERVER['REMOTE_ADDR'] , 'high', 'connDB', 'connDB' , $duration  , $ret['status'] , $database , json_encode( $ret )  , 'MobCust');
		} else{
			$ret['status'] = -2 ;
			$ret['message'] = $DB2->error();  ;
			$duration = microtime(true)  - $time_start; 
			$this->Writelogs_model->wLogs(  $username  , $_SERVER['REMOTE_ADDR'] , 'high', 'connDB', 'connDB' , $duration  , $ret['status'] , $database , json_encode( $ret )  , 'MobCust');
			print_r(json_encode($ret)) ; die();
		}
		return $DB2 ;
	}

	function authen($header){
		$status = false ;
		if($header['Authorization']!="" || $header['authorization']!=""){
			$header['Authorization'] = $header['Authorization'] ;
			if($header['authorization']!=""){
				$header['Authorization'] = $header['authorization'] ;
			}
			$decryption=openssl_decrypt ( base64_decode($header['Authorization']) , _AUTHEN_CYPHER ,  _AUTHEN_KEY, $options, _AUTHEN_ENCRYPT );
			if(_AUTHEN_USER_CUST==$decryption){
				$status = true ;
			}else{
				$ret['status'] = -2 ;
				$ret['message'] = 'Authentication failed please try again';
				$this->Writelogs_model->wLogs(  "Authen"  , $_SERVER['REMOTE_ADDR'] , 'high', 'authenHeader', 'authen' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobCust');
				print_r(json_encode($ret)) ; die();
			}

		}else{
			$ret['status'] = -3 ;
			$ret['message'] = 'Authentication failed please try again';
			$this->Writelogs_model->wLogs(  "Authen"  , $_SERVER['REMOTE_ADDR'] , 'high', 'authenHeader', 'authen' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobCust');
			print_r(json_encode($ret)) ; die();
		}
		//$this->Writelogs_model->wLogs(  "Authen"  , $_SERVER['REMOTE_ADDR'] , 'high', 'authenHeader', 'authen' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobCust');
		return $status ;
	}

	function chkToken( $header ){ 
		$deToken =  _base64_decrypt( $header['access_token']) ;
		$deToken = json_decode($deToken,true) ;
		//print_r( $deToken ) ;
		if($deToken['username']=='' || $deToken['password']=='' || $deToken['tenant']==''  || $deToken['expire']==''   ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Format invalid!!'; 
			$this->Writelogs_model->wLogs(  $deToken['username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'chkToken', 'chkToken' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobCust');
			print_r(json_encode($ret)) ; die();
		}else{
			if( time() >$deToken['expire'] ){
				$ret['status'] = -1 ;
				$ret['message'] = 'Token Expire!';
				$this->Writelogs_model->wLogs( $deToken['username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'chkToken', 'chkToken' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobCust');
				print_r(json_encode($ret)) ; die();
			}
		}
		return $deToken ;
	}

	public function genPassword($password){

        return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

    }
    public function getUserProfile($data){
    	$time_start = microtime(true);

    	$DB2 = $this->connDB( $data['tenant'] , $data['username']  ) ;
    	$pass = $this->genPassword($data['password']);
	 
		$DB2->select('user_name,display_name,password,email,activation_token,last_activation_request,lost_password_request,active,title,sign_up_stamp,last_sign_in_stamp,password2,role,first_name,last_name,birthday,phone,address,country_id,facebook,twitter,google_plus,linked_in,dribbble,skype,photo,device_token,device_name');
		$DB2->from( 'uc_users' );
		$DB2->where('user_name =',  $data['username'] );
		$DB2->where('role =',  3 );
		$user = $DB2->get()->row() ; 

		if (password_verify( $user->password2 , $pass)) { 
			$ret['status'] = 0 ; 
			$ret['data'] = $user  ; 
		} else {
		    //echo 'Invalid password.';
		    $ret['message'] = 'Invalid User or Password.';
			$ret['status'] = -1 ; 

 
			$duration = microtime(true)  - $time_start; 

			$this->Writelogs_model->wLogs(  $data['username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'getUserProfile', 'Profile' ,  $duration   , $ret['status'] , json_encode($data) , json_encode($ret)  , 'MobCust');
			print_r(json_encode($ret)) ; die();
		}	

        return $ret ;

    }

	public function getToken($username ='' , $password = '')
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		//$authen = true ;
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
		$data = json_decode( $data , true ) ;
		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
		//print_r($data ) ; die();
		if($data['username']=='' || $data['password']=='' || $data['tenant']==''   ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}
 
 		//$DB2 = $this->connDB( $data['tenant'] , $data['username']  ) ;


 		$user = $this->getUserProfile( $data ) ;
 		$user = $user['data']; 
 		//print_r($user) ; die();


		// $pass = $this->genPassword($data['password']);
	 
		// $DB2->select('user_name,activation_token,password2');
		// $DB2->from( 'uc_users' );
		// $DB2->where('user_name =',  $data['username'] );
		// $DB2->where('role =',  3 );
		// $user = $DB2->get()->row() ; 

		// if (password_verify( $user->password2 , $pass)) { 
		if (isset($user)) { 	
			$deToken =  _base64_decrypt( $user->activation_token ) ;
			//print_r($deToken ) ; die();
			$deToken = json_decode($deToken,true) ;

			if( time() <  $deToken['expire'] ){
				$ret['status'] = 0 ; 
				$ret['token'] = $user->activation_token ;
				$ret['expire'] = $deToken['expire'] ;
				$ret['expire_'] = $deToken['expire_'] ;

			}else{

				$data['expire_'] = date ("Y/m/d H:i:s", strtotime("+".TOKEN_EXPIRE." day", time() )) ; 
				$data['expire'] = strtotime( $data['expire_'] ) ; 
				//print_r( $data) ;
				$token=  _base64_encrypt( json_encode( $data ) )  ;
				$expire = $data['expire'] ; 
				$expire_ = $data['expire_'] ;
				$stage = 'Generate' ;

				$ds = array(
					'activation_token' => $token  ,
					'device_name' => $data['device_name']  ,
					'device_token' => $data['device_token']  ,
				);
				//print_r( $ds ) ;
				$DB2->where('user_name', $data['username'] );
				$query = $DB2->update('uc_users', $ds );
				if($query==1){
					$ret['status'] = 0 ;

					$ret['token'] = $token  ;
					$ret['expire'] = $expire ;
					$ret['expire_'] = $expire_ ;
					$ret['stage'] = 'generate'  ;
				}else{

					$ret['status'] = -1 ;
					$ret['message'] = 'Generate Fail';

				}

			}

		} else {
		    //echo 'Invalid password.';
		    $ret['message'] = 'No data';
			$ret['status'] = -1 ; 
		}
		
		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start; 

		$this->Writelogs_model->wLogs(  $data['user_name']  , $_SERVER['REMOTE_ADDR'] , 'high', 'getToken', 'generate' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobCust');
		print_r(  ( $res_x ) ) ;

	}

	

	public function getProfile()
	{
		$time_start = microtime(true);

		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		//$authen = true ;
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}

		//Check Token format & Expire date
		$deToken = $this->chkToken($header);
		 
		$user = $this->getUserProfile( $deToken ) ;
 		$user = $user['data']; 
 		if (isset($user)) { 
 			$ret['status'] = 0 ; 
			$ret['data'] =  $user ;
 		}else{
			$ret['status'] = -1 ; 
			$ret['message'] = 'No data';
 		}
 		 
 		$res_x = json_encode($ret) ; 
		$duration = microtime(true)- $time_start; 
 		$this->Writelogs_model->wLogs(  $user->user_name  , $_SERVER['REMOTE_ADDR'] , 'high', 'getToken', 'generate' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobCust');
 		print_r( $res_x ) ;
	}

	public function UpdateProfile()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		//$authen = true ;
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}

		$data_x =  file_get_contents('php://input') ; 
		$data = $data_x ;
		$data = json_decode( $data , true ) ;

		//Check Token format & Expire date
		$deToken = $this->chkToken($header);
		 
		$user = $this->getUserProfile( $deToken ) ;
 		$user = $user['data']; 
 		if (isset($user)) { 


 			/*$DB2 = $this->connDB( $data['tenant'] , $data['username']  ) ;
 			$ds = array(
				'activation_token' => $token  ,
				'device_name' => $data['device_name']  ,
				'device_token' => $data['device_token']  ,
			);
			//print_r( $ds ) ;
			$DB2->where('user_name', $data['username'] );
			$query = $DB2->update('uc_users', $ds );
			if($query==1){
				$ret['status'] = 0 ;

				$ret['token'] = $token  ;
				$ret['expire'] = $expire ;
				$ret['expire_'] = $expire_ ;
				$ret['stage'] = 'generate'  ;
			}else{

				$ret['status'] = -1 ;
				$ret['message'] = 'Generate Fail';

			}*/




 			$ret['status'] = 0 ; 
			$ret['data'] =  $user ;
 		}else{
			$ret['status'] = -1 ; 
			$ret['message'] = 'No data';
 		}

 		print_r( json_encode( $ret ) ) ;
	}

	public function getEvent()
	{
		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		$authen = $this->authen($header);
		//$authen = true ;
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}

		$data_x =  file_get_contents('php://input') ; 
		$data = $data_x ;
		$data = json_decode( $data , true ) ;

		//Check Token format & Expire date
		$deToken = $this->chkToken($header);
		 
		$user = $this->getUserProfile( $deToken ) ;
 		$user = $user['data']; 
 		if (isset($user)) { 


 			$DB2 = $this->connDB( $data['tenant'] , $user->user_name  ) ;
 			 

 			$DB2->select('case.case_id,casetype_code,priority,phone_number,case.case_status_code,result_code,case_detail,command_code,police_station_code,case_location_address,case_location_detail,case_route,case_lat,case_lon,citizen_code,created_date,started_date,commanded_date,received_date,arrived_date,closed_date,user_create,user_receive,user_close,police_vehicle_code,receive_date,arrive_date,close_date,case_transaction.case_status_code as case_status_code_t,user_code,viewed,case_transaction.id as case_transaction_id,ways');
			$DB2->from('case_transaction');
			$DB2->join('case', 'case.case_id = case_transaction.case_id');

			$DB2->where('case_transaction.police_vehicle_code =', $user->user_name );

			if($case_id!=''){
				$DB2->where('case_transaction.case_id =', $case_id );
			}
			 
			 echo $DB2->last_query();




 			$ret['status'] = 0 ; 
			$ret['data'] =  $user ;
 		}else{
			$ret['status'] = -1 ; 
			$ret['message'] = 'No data';
 		}

 		print_r( json_encode( $ret ) ) ;
	}

	public function eventCommand()
	{
		// For Ack , Enroute , Arrive , Clear
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



	 
}