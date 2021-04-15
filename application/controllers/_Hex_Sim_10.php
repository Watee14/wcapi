<?php error_reporting(E_ALL) ;
date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');

class Hex_Sim_10 extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();
		$this->db = $this->load->database("mysql", TRUE);
		$this->db2 = $this->load->database("default", TRUE);
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
	}

	public function MakeEvent()
	{
		 
		$tokenStart = file_get_contents($uploads_dir)  ;
		$tokenStart = json_decode($tokenStart,true) ; 
		$token = $tokenStart['accessToken'] ;
		if( $token !="" ){
		}else{
			$token = $this->hexAuthen();
			$token = $token['token'] ;
		}

		$fileAgency  = _CONFIG.'/HexToken/patternAgency.txt' ;
		$patternAgency = file_get_contents($fileAgency)  ;
		$patternAgency = json_decode($patternAgency,true) ;


	 	$file = fopen("/var/www/html/logs/HEXAGON/makeEvent_10.txt","r");
	 	$rec = 1;
		while(! feof($file))
		{
			//echo fgets($file). "<br />";
			if(  $rec>=$_REQUEST['start'] &&  $rec <= $_REQUEST['end']){

			
				$str = trim( fgets($file) ); 
				$caseType = explode("	", $str ) ;
				$LL = explode(",", $caseType[1] ) ;
				//print_r( $caseType  ) ;
				//echo $caseType[0].' : '. $LL[0],"--".$LL[1].'<br>' ;
				$pattern['latitude'] = $LL[0]  ;
				$pattern['longitude'] = $LL[1]  ;
				$pattern['locationString'] = "Make location automatic"  ;
				$pattern['EventType'] = $caseType[0] ;
				$pattern['Description'] = "Make Event : ".$rec ;

				$pattern['Schedule']['pickUpTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
				$pattern['Schedule']['dropOffTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
				$pattern['Schedule']['requestedTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;

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
				 	  

				}else{
					$ret['status'] = -1 ;
					$ret['message'] = $call ; 
				}
				echo'<hr>';
				echo $rec.'--'.date('Y/m/d H:i:s') .' : '. json_encode($ret).'<br>' ;
				echo'<hr>';
				sleep(2);
			}else{
				echo fgets($file). "<br />";
			}
			$rec++;
		}

		fclose($file);

	}

	///////////////////////////////////////////////////////
	//////////////////// Hexagon ///////////////////////////
	///////////////////////////////////////////////////////
	 

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

	 

	function getTokenFromApp(){
		$url = HEX_AUTHEN_TOKEN ; 
		$token = $this->curlHexAuthen($url, HEX_AUTHEN_DATA , 'POST') ;
		//$token = json_decode($token,true) ; 
		print_r($token) ;
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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
		//curl_setopt($ch,CURLOPT_HEADER, TRUE);
		if (curl_errno($ch)) {
		    $error_msg = curl_error($ch);
		}
		$response = curl_exec($ch);

		curl_close($ch);

		if (isset($error_msg)) {
		    // TODO - Handle cURL error accordingly
		    //print_r( $error_msg );
		}
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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
		//curl_setopt($ch,CURLOPT_HEADER, TRUE);
		if (curl_errno($ch)) {
		    $error_msg = curl_error($ch);
		}
		$response = curl_exec($ch);
		curl_close($ch);

		if (isset($error_msg)) {
		    // TODO - Handle cURL error accordingly
		    //print_r( $error_msg );
		}
		//print_r($response);

		return $response;
	}
}