<?php error_reporting(E_ERROR) ;
date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');
//require(APPPATH.'libraries/REST_Controller.php');
//REST_Controller
class MobCust extends CI_Controller
{

	public function __construct()
	{ 
		/*$get['region'] = '66';
		$get['tenant'] = '000020' ;
		 
		$_REQUEST = $get ;*/
		// print_r($_REQUEST) ;
		if( isset($_REQUEST['doc'])  ){

		}else{
			$data_x =  file_get_contents('php://input') ;
			$data = json_decode(  $data_x , true  ) ;
			//print_r( $data ) ; die();
			if( $data['region']!="" && $data['tenant']!=""){
				$_REQUEST = $data ;
			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Invalid Region,Tenant';
				print_r( json_encode($ret) ) ; die();
			}
		}
		
		//print_r( $data_x ) ;
		parent::__construct();
		////$this->load->database("citizenDB");
		//$this->db = $this->load->database("default", TRUE);
		//$this->db ;
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
		$this->load->helper('base64');
		$this->load->model('contact_model_mongodb');
	}

	public function index($gen = "")
	{
		//$this->load->view('welcome_message');
		 //$this->load->helper('base64_helper');
	 	//echo "Decrypted String: " ;
	 	
		//$this->load->view('mobile_api_v2');
		//echo _AUTHEN_USER_CUST.'--'._AUTHEN_CYPHER.'--'._AUTHEN_KEY.'--'._AUTHEN_ENCRYPT ;
		//echo $this->CryptoJS->aes_encrypt( '134'  ) ;
		//echo $this->genPassword('1234');
		if($gen==true){

			$encryption = openssl_encrypt( _AUTHEN_USER_CUST , _AUTHEN_CYPHER ,  _AUTHEN_KEY , $options, _AUTHEN_ENCRYPT );
			echo $authen = base64_encode( $encryption ) ;
			echo '<br>';
		  	echo $decryption=openssl_decrypt ( base64_decode($authen) , _AUTHEN_CYPHER ,  _AUTHEN_KEY, $options, _AUTHEN_ENCRYPT );

		  	echo '<hr>';
		  	
		  	echo $xx = _base64_encrypt( '{"username":"watee14","password":"watee1234","tenant":"000020","expire":"1614075615"}' ) ;
		  	echo   _base64_decrypt( $xx ) ; 
		}
		$this->load->view('MobCust');
		 
		 
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
			$ret['message'] = $DB2->error(); 
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
		if($deToken['cust_username']=='' || $deToken['cust_password']=='' || $deToken['tenant']==''  || $deToken['expire']==''   ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Format invalid!!'; 
			$this->Writelogs_model->wLogs(  $deToken['cust_username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'chkToken', 'chkToken' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobCust');
			print_r(json_encode($ret)) ; die();
		}else{
			if( time() >$deToken['expire'] ){
				$ret['status'] = -1 ;
				$ret['message'] = 'Token Expire!';
				$this->Writelogs_model->wLogs( $deToken['cust_username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'chkToken', 'chkToken' ,  0   , $ret['status'] , json_encode($header) , json_encode($ret)  , 'MobCust');
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
	 
		$PG_username = $this->contact_model_mongodb->get_contact(  $data['cust_username']  , '' , 'cust_username' );
		//print_r( 	$data ) ;  
	 	if( count($PG_username) > 0 ){
	 		
			if (password_verify( $data['cust_password']  , $PG_username[0]['cust_password'] )) { 
				$ret['status'] = 0 ; 
				$ret['data'] = $PG_username[0]  ; 
			} else {
			    //echo 'Invalid password.';
			    $ret['message'] = 'Invalid User or Password.';
				$ret['status'] = -1 ; 

	 
				$duration = microtime(true)  - $time_start; 

				$this->Writelogs_model->wLogs(  $data['cust_username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'getUserProfile', 'Profile' ,  $duration   , $ret['status'] , json_encode($data) , json_encode($ret)  , 'MobCust');
				print_r(json_encode($ret)) ; die();
			}	
	 	}else{

			$ret['message'] = 'Not found this account.';
			$ret['status'] = -1 ; 
			print_r( json_encode( $ret )) ; die();
		}
        return $ret ;

    }
     
 

	 public function subScribe()
	{


		header('Content-Type: application/json');
		$header = apache_request_headers() ;
		//$this->Writelogs_model->wLogs(  "Test"  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribeRec', 'insert' , "--"  , "--" , json_encode($header) , ""  , 'MobCust');
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
				$this->Writelogs_model->wLogs(  $data['cust_username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribe', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobCust');

				print_r(  $res_x ) ;  die();
 			}
 		}
 		//////////////////////////////////////////////////////////////////////////////////
 		/*$citizen_fulllname = explode(' ', $data['cust_name'], 3);
		$data['cust_name'] = $citizen_fulllname[0];
		//$data['cust_middle'] = count($citizen_fulllname) > 2 ? $citizen_fulllname[sizeof($citizen_fulllname) - 2] : NULL;
		$data['cust_surname'] = count($citizen_fulllname) > 1 ? $citizen_fulllname[sizeof($citizen_fulllname) - 1] : NULL;
		$citizen_fulllname = NULL;
		unset($citizen_fulllname);*/
		//////////////////////////////////////////////////////////////////////////////////
		
		$PG_username = $this->contact_model_mongodb->get_contact(  $data['cust_username']  , '' , 'cust_username' );
		//print_r( 	$PG_username) ;
	 	if( count($PG_username) > 0 ){
	 		$ret['status'] = -1 ;
			$ret['message'] = 'Duplicate Username';
	 	}else{
	 		$PG_CitizenID = $this->contact_model_mongodb->get_contact(  $data['citizen_id']  , '' , 'citizen_id' );
			if( count($PG_CitizenID ) >0 ){
				$ret['status'] = -1 ;
				$ret['message'] = 'Duplicate Citizen ID';
			}else{
				//Insert
				$profile = array(
					'region' => $data['region'] ,
					'tenant' => $data['tenant'] ,
			         'cust_name' => $data['cust_name'] ,
			         'cust_middle' => $data['cust_middle'] ,
					 'cust_surname' => $data['cust_surname'] ,
					 'birthdate' => $data['birthdate'] ,
					 'sex' => "0",
				     'cust_phone_number' =>  $data['cust_phone_number']  ,
				     'cust_telephone' =>  $data['cust_telephone']  ,
				     'cust_telephone_office' =>  $data['cust_telephone_office']  ,
				     'cust_address' => $data['cust_address']   ,
				     'device_name' => $data['device_name'] ,
				     'device_token' => $data['device_token'],
				     'citizen_id' => $data['citizen_id'],
				     'driver_license' => $data['driver_license'],
				     'cust_username' => $data['cust_username'],
				     'cust_password' => $this->genPassword( $data['cust_password'] ) ,
				     //'user_create' => 'MobileService',
				     'user_modify' => 'MobileService',
				     //'created_date' => date('Y-d-m H:i:s') ,
				     'modified_date' => date('Y-m-d H:i:s') ,
				     'cust_current_lat' => $data['cust_current_lat'],
				     'cust_current_lon' => $data['cust_current_lon'],
				     'cust_email' => $data['cust_email'],
				     'level' => $data['level'],
				     'address' => json_encode($data['address']),
				     'info' => json_encode($data['info']),
				     'noti_flag' => $data['noti_flag']
				);
				//print_r( $profile ) ;
				//$query = $this->db->insert('cust_profile', $profile);
				$query = $this->contact_model_mongodb->insert_document( 'c_contact' ,$profile);	
				 
				if( count( $query )>0 ){
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';
				 	$ret['data'] = $data;
				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
				}
			}
	 	}
	 	
		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['cust_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribe', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobCust');

		print_r(  $res_x ) ;

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
		if($data['cust_username']=='' || $data['cust_password']==''   ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';
			print_r(json_encode($ret)) ; die();
		}
 
 		//$DB2 = $this->connDB( $data['tenant'] , $data['username']  ) ;


 		$user = $this->getUserProfile( $data ) ;
 		$user = $user['data']; 
 		//print_r($user) ; die();

 
		if (isset($user)) { 	
			$deToken =  _base64_decrypt( $user['activation_token'] ) ;
			//print_r($deToken ) ; die();
			$deToken = json_decode($deToken,true) ;

			if( time() <  $deToken['expire'] && $user['password_status']==0  ){
				$ret['status'] = 0 ; 
				$ret['token'] = $user['activation_token'] ;
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
					'cust_username' => $data['cust_username']  ,
					'activation_token' => $token  ,
					'device_name' => $data['device_name']  ,
					'device_token' => $data['device_token']  ,
					'password_status' => "0"  ,
				);
				// print_r($ds) ; die();
				$query = $this->contact_model_mongodb->update_document( 'c_contact' ,$ds , 'cust_username');	
				  

				if(count( $query )>0){
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

		$this->Writelogs_model->wLogs(  $data['user_name']  , $_SERVER['REMOTE_ADDR'] , 'high', 'getToken', 'generate' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobResp');
		print_r(  ( $res_x ) ) ;

	}

	public function updateProfile()
	{
		header('Content-Type: application/json');
		$time_start = microtime(true);
		$header = apache_request_headers() ;
		//$this->Writelogs_model->wLogs(  "Test"  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribeRec', 'insert' , "--"  , "--" , json_encode($header) , ""  , 'MobCust');
		$authen = $this->authen($header);
		if($authen==true){

		}else{
			$ret['status'] = -2 ;
			$ret['message'] = 'Authentication failed please try again';
			print_r(json_encode($ret)) ; die();
		}

		$deToken = $this->chkToken($header);
		//print_r($deToken) ;
		//die();
		$user = $this->getUserProfile( $deToken ) ;
 		$user = $user['data']; 
 
 
		$data_x =  file_get_contents('php://input') ; 
 		$data = $data_x ; 
		$data = nl2br(str_replace(array("\n", "\r"), ' ', $data)); 

 		$data = json_decode( $data , true ) ;
 	 	//print_r($data) ; die();
 		foreach ($data as $key => $value) {
 			if( $value =='' ){
 				$ret['status'] = -1 ;
				$ret['message'] = 'Data invalid : '.$key;

				$res_x = json_encode($ret) ;
				$time_end = microtime(true);
				$duration = $time_end - $time_start;
				$this->Writelogs_model->wLogs(  $user['cust_username']  , $_SERVER['REMOTE_ADDR'] , 'high', 'updateProfile', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobCust');

				print_r(  $res_x ) ;  die();
 			}
 		} 
		
		unset( $data['citizen_id'])  ;
	 	unset( $data['cust_username'])  ;	

		$profile = array(
			'region' => $data['region'] ,
			'tenant' => $data['tenant'] ,
	         'cust_name' => $data['cust_name'] ,
	         'cust_middle' => $data['cust_middle'] ,
			 'cust_surname' => $data['cust_surname'] ,
			 'birthdate' => $data['birthdate'] ,
			 'sex' => "0",
		     'cust_phone_number' =>  $data['cust_phone_number']  ,
		     'cust_telephone' =>  $data['cust_telephone']  ,
		     'cust_telephone_office' =>  $data['cust_telephone_office']  ,
		     'cust_address' => $data['cust_address']   ,
		     'device_name' => $data['device_name'] ,
		     'device_token' => $data['device_token'],
		     //'citizen_id' => $data['citizen_id'],
		     'driver_license' => $data['driver_license'],
		     'cust_username' => $user['cust_username'],
		     //'cust_password' => $this->genPassword( $data['cust_password'] ) ,
		     //'user_create' => 'MobileService',
		     'user_modify' => 'MobileService',
		     //'created_date' => date('Y-d-m H:i:s') ,
		     'modified_date' => date('Y-m-d H:i:s') ,
		     'cust_current_lat' => $data['cust_current_lat'],
		     'cust_current_lon' => $data['cust_current_lon'],
		     'cust_email' => $data['cust_email'],
		     'level' => $data['level'],
		     'address' => json_encode($data['address']),
		     'info' => json_encode($data['info']),
		     'noti_flag' => $data['noti_flag']
		);

		if( $data['cust_password_old'] !="" ){
			if( $data['cust_password_new'] !="" ){
				//$pass = $this->genPassword( $data['cust_password_new'] ) ;
				if (password_verify( $data['cust_password_old']  , $user['cust_password'] )) { 
					$data['cust_password_new'] ;
					$profile['cust_password'] = $this->genPassword( $data['cust_password_new'] ) ;
					$profile['password_status'] = "1" ;
				} else {
				    //echo 'Invalid password.';
				    $ret['message'] = 'Compare password failed';
					$ret['status'] = -1 ; 
					print_r( json_encode($ret) ) ; die();
		  		}

			}else{

			}
			
		}
		 //print_r( $profile ) ; die();
		//$query = $this->db->insert('cust_profile', $profile);
		$query = $this->contact_model_mongodb->update_document( 'c_contact' ,$profile , 'cust_username');	
		 
		if( count( $query )>0 ){
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
		 	$ret['data'] = $data;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'Fail';
		}
			
	 	
		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['cust_phone_number']  , $_SERVER['REMOTE_ADDR'] , 'high', 'subScribe', 'insert' , $duration  , $ret['status'] , $data_x , $res_x  , 'MobCust');

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



	 
}