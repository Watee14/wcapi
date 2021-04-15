<?php error_reporting(E_ALL) ;
date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');

class Hex_Analysis extends CI_Controller
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

	public function index(  ){


		$tokenStart = file_get_contents($uploads_dir)  ;
		$tokenStart = json_decode($tokenStart,true) ; 
		$token = $tokenStart['accessToken'] ;
		if( $token !="" ){
		}else{
			$token = $this->hexAuthen();
			$token = $token['token'] ;
		}

		echo '<pre>';
		$agencyEventId = $_REQUEST['agencyEventId'] ;
		echo '<hr>';
		$distance = 1 ; 
		$backTime = 600 ;
		$singleCount = 10 ;
		$multiCount = 20 ;
		$multiCount_grp = 3 ;
		if( $agencyEventId!="" ){
			$this->db->select('agencyEventId,latitude,longitude,typeCode');
			$this->db->from( 'eventActive' );
		
			$this->db->where('agencyEventId =',  $agencyEventId );
			
			$event = $this->db->get()->result_array();
			if(count($event)>0){
				echo 'Current Event : '.$agencyEventId.' <br>';
				print_r($event) ;
				echo '<hr>';

				$this->db->select('*');
				$this->db->from( 'eventTypeGrp' );
				$this->db->like('eventType',  $event[0]['typeCode'] );
				$typeCode = $this->db->get()->result_array();
				echo 'Group<br>';
				print_r($typeCode) ;
				echo '<hr>';
				$dt = time() - ($backTime*60) ;
				if(count($typeCode)>0){
					//Check Multi Group ;
					//$dt = time() - ($backTime*60) ;
					$chkCount = "SELECT typeCode,count(*) as total from eventActive where ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) < '%s'  AND agencyEventId<>'%s' AND typeCode in (%s) AND startedTime>='%s' AND agencyEventId like('%s')  group by typeCode ORDER by typeCode" ;
					$selEvent = "SELECT *, latitude, longitude, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM eventActive HAVING distance < '%s' AND agencyEventId<>'%s' AND typeCode in (%s) AND startedTime>='%s' AND agencyEventId like('%s')  ORDER BY startedTime";

					$query = sprintf( $chkCount ,
					    $event[0]['latitude'] ,
					    $event[0]['longitude']  ,
					    $event[0]['latitude']  ,
					   $distance ,
					   $agencyEventId,
					   $typeCode[0]['eventType'],
					    $dt , "P%"  ); 
					$res = $this->db->query($query)->result_array();	
					echo '<br>Check Count Multi<br>';
					echo $query.'<br>';
					print_r($res) ;
					echo '<hr>';
					if( count( $res ) >= $multiCount_grp ){
					 	//Create Event 
					 	$eventX = $event[0] ;
					 	$eventX['typeCode'] = $typeCode[0]['eventGroup_Code'] ;
						$result = $this->MakeEvent($eventX ) ;
						//$result = json_decode( $result , true ) ;
						echo '<br>Create Event<br>';
						print_r($result) ;
						echo '<hr>';

 						sleep(2);
						
						if( count($result)>0 && $result[0]['agencyEventId']!=""  ){


							//Flag 
							$arr = '[{"Name": "AgencyEventCustom1","Value": "Yes"}]';
							$url = HEX_SERVICE.'/Event/'.$result[0]['agencyEventId'].'/CustomData' ; 
							$call = $this->curlHexService($url, $arr , 'POST' , $token ) ;
							$call = json_decode( $call, true ) ;
							if($call['errorMessage']=='not authorized'){

								$token = $this->hexAuthen();
								$token = $token['token'] ;
					 			$call = $this->curlHexService($url, $arr , 'POST' , $token ) ;
								$call = json_decode( $call, true ) ;
							}
							//print_r($call) ;
							if(count($call)>0){
								echo 'Flag Fail<br>';
								print_r($call) ;
								echo '<hr>';
								die();
							}else{
								echo 'Flag Success<hr>';
							}
							sleep(2);
							$query = sprintf( $selEvent ,
							    $event[0]['latitude'] ,
							    $event[0]['longitude']  ,
							    $event[0]['latitude']  ,
							   $distance ,
							   $agencyEventId,
							   $typeCode[0]['eventType'],
						    	$dt , "P%"  ); 
							$res = $this->db->query($query)->result_array();
							echo '<br>Select event Multi<br>';
							foreach ($res as $key => $value) {
						 		print_r( $value ) ;
								$url = HEX_SERVICE.'/Event/'.$result[0]['agencyEventId'].'/CrossReference?toAgencyEventId='.$value['agencyEventId'] ; 
								$call = $this->curlHexService($url, "" , 'POST' , $token ) ;
								$call = json_decode( $call, true ) ;
								if($call['errorMessage']=='not authorized'){

									$token = $this->hexAuthen();
									$token = $token['token'] ;
						 			$call = $this->curlHexService($url, "" , 'POST' , $token ) ;
									$call = json_decode( $call, true ) ;
								}
								echo $url ;
								print_r( $call ) ;
								echo '<br>';
								sleep(1);
							}
							
							 
						}


						
						 
 					
					}else{
						//$dt = time() - ($backTime*60) ;
						echo $query = sprintf($chkCount,
						    $event[0]['latitude'] ,
						    $event[0]['longitude']  ,
						    $event[0]['latitude']  ,
						   $distance ,
						   $agencyEventId,
						   $event[0]['typeCode'] ,
						    $dt , "P%"  ); 
						$res = $this->db->query($query)->result_array();
						if( count( $res ) >= $singleCount ){
							//Create
						}

					}
					 
				}else{
					echo 'No EventType';
				}
				die();

				foreach ($event as $key => $value) {
					//3959 - miles
					//6371 - kilos 
				 
					

					 
					$res = $this->db->query($query)->result_array();	
		 
					echo "<hr>".$value['agencyEventId']."<br>" ; 
					foreach ($res as $k1 => $v1) {
						print_r($v1) ;
					}
					echo '<hr>';
				}
			}
			
		}
	}

	public function MakeEvent($req)
	{
		 
		$tokenStart = file_get_contents($uploads_dir)  ;
		$tokenStart = json_decode($tokenStart,true) ; 
		$token = $tokenStart['accessToken'] ;
		if( $token !="" ){
		}else{
			$token = $this->hexAuthen();
			$token = $token['token'] ;
		}

		$str = '{
		     "EventType":"",
		     "EventSubType":"default",
		     "VerifyLocationExactMatch":true,
		     "Description":"",
		     "LocationString":null,
		     "Latitude":"",
		     "Longitude":"",
		     "AlarmLevel":null,
		     "DefaultAgencyForDeployment":null,
		     "DefaultDispatchGroupForDeployment":null,
		     "Deployments":null,
		     "Schedule":null,
		     "Calls":null,
		     "SimpleComments":[]
		}';

		$pattern = json_decode( $str , true) ; 

		//$req = $_REQUEST ; 
		 
		 
		$pattern['Latitude'] = $req['latitude']  ;
		$pattern['Longitude'] = $req['longitude']  ;
		//$pattern['locationString'] = "Make location automatic"  ;
		$pattern['EventType'] = $req['typeCode'] ;
		$pattern['EventSubType'] = "default" ;
		//$pattern['Description'] = "Make Event : ".$rec ;

		/*$pattern['Calls'][0]['CallId'] = time() ;
		$pattern['Calls'][0]['CallSource'] = $req['callType'] ;
		$pattern['Calls'][0]['CallerPhoneNumber'] = $req['callerNo'] ;
		$pattern['Calls'][0]['CallerName'] = $req['callerName'] ;
		$pattern['Calls'][0]['CallTime'] =str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;*/

		/*if( $req['callType']=="eCall"){
			$comment = [] ;
			$comment[] = "Position Confidence : ".$req['positionConfidence'] ;
			$comment[] = "Vehicle Type : ".$req['vehicleType'] ;
			$comment[] = "VIN : ".$req['vin'] ;
			$comment[] = "Propulsion : ".$req['propulsion'] ;
			$comment[] = "Vehicle Direction : ".$req['vehicleDirection'] ;
			$comment[] = "Number of Passenger : ".$req['noPassenger'] ;
			$comment[] = "Optional Additional Data OID : ".$req['additionalOID'] ;
			$comment[] = "Optional Additional Data : ".$req['additionalData'] ; 

			$comments = implode( "\r\n" , $comment) ;
			$pattern['SimpleComments'] = [] ;
			$pattern['SimpleComments'][] = $comments ;
		}*/
		//if( $req['callType']=="iOT"){

			//$pattern['SimpleComments'][] = $req['additionalData'] ;
		//}
		$pattern['Schedule']['pickUpTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
		$pattern['Schedule']['dropOffTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
		$pattern['Schedule']['requestedTime'] = str_replace('+00:00', '.000Z', gmdate('c', time() ))  ;
		//print_r( json_encode($pattern) ) ; die();
		$url = HEX_SERVICE.'/Event/Create' ; 
		$call = $this->curlHexService($url, json_encode( $pattern) , 'POST' , $token ) ;
		$call = json_decode( $call, true ) ;
		if($call['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$call = $this->curlHexService($url, json_encode( $pattern) , 'POST' , $token ) ;
			$call = json_decode( $call, true ) ;
		}
		//print_r(json_encode($call) ) ; die();
		/*if( count($call)>0 && $call[0]['agencyEventId']!="" ){
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
		 	$ret['data'] = $call[0];
		 	  

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = $call ; 
		}*/
		 
		//print_r(json_encode($ret)) ;
		return $call ;
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