<?php error_reporting(E_ALL) ;
date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');

class Hex extends CI_Controller
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

	public function index()
	{
		 
		$this->load->view('hex_api_v1');

	}

	///////////////////////////////////////////////////////
	//////////////////// Hexagon ///////////////////////////
	///////////////////////////////////////////////////////
	function callBack_2(){
		$data_x =  file_get_contents('php://input') ;
		$s = json_decode($data_x , true ) ; 
		$d = date('Ymd');
		$path = _LOGS.'HEXAGON/'.$d  ;
		if (!file_exists( $path )) {
		    mkdir( $path  , 0777, true);
		}
		 
		file_put_contents( $path."/DATA_full" ,  date('Y/m/d H:i:s')." : ".$s['eventType']." -- ". $data_x.PHP_EOL    , FILE_APPEND ) ;  
	}
	//eventCallBack_20200930
	function eventCallBack_20201118(){
		$data_x =  file_get_contents('php://input') ;
		$s = json_decode($data_x , true ) ; 
		$d = date('Ymd');
		$path = _LOGS.'HEXAGON/'.$d  ;
		$pathAll = _LOGS.'HEXAGON/allEvent'  ;
		if (!file_exists( $path )) {
		    mkdir( $path  , 0777, true);
		}
		$path_create = _LOGS.'HEXAGON/'.$d."_create"  ;
		if (!file_exists( $path_create )) {
		    mkdir( $path_create  , 0777, true);
		}
		if( $s['eventType']=='EventCreated' || $s['eventType']=='FieldEvent' || $s['eventType']=='EmergencyEvent' ){  // || $s['eventType']=='EmergencyEvent'
			echo CAD_APP ;
			file_put_contents($path_create."/".$s['data']['agencyEventId'] ,  ($data_x )) ;  
			$res = $this->curlHexAuthen( CAD_APP ,  $data_x , "POST") ;
			print_r($res) ;
		}
		if($s['data']['agencyEventId']!=""){
			$data = $this->getEventByID( $s['data']['agencyEventId'] ) ;
			if( $data['status']==0 ){
				file_put_contents($path."/".$s['data']['agencyEventId'] ,  json_encode($data['data'] ) ) ;  
				file_put_contents($pathAll."/".$s['data']['agencyEventId'] ,  json_encode($data['data'] ) ) ;  
			}
		}
		file_put_contents( $path."/HEADER",  date('Y/m/d H:i:s')." : ".json_encode($_SERVER ) , FILE_APPEND ) ;  
		file_put_contents( $path."/DATA" ,  date('Y/m/d H:i:s')." : ". $data_x.PHP_EOL    , FILE_APPEND ) ;  
	}

	function getEventByID( $agencyEventId="" ){
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

		//http://hex-oncall.191poc.local:8000/api/v1/Unit?AgencyEventId=1&AgencyId=2&DispatchGroup=3&UnitType=4&Beat=5
		$url = HEX_SERVICE.'/Event/'.$agencyEventId ; 
		$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
		$ds = json_decode( $ds, true ) ;

		if($ds['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
			$ds = json_decode( $ds, true ) ;
		}

		if( count( $ds)>0 && !$ds['errorMessage'] ){

			$ret['status'] = 0 ;
			$ret['message'] = "Write success" ;
			$ret['data'] = $ds ;
			//$ret['data'] = $dataY ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] =  $ds   ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Service', 'getEventByID' , $duration  , $ret['status'] ,  $agencyEventId  , json_encode($ret)  , 'Hex-Service');
		return $ret ;
	}

	function Spill( $ani="" ){
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

		$data_x =  file_get_contents('php://input') ;
		$dataY = json_decode( $data_x , true ) ;
		//$xx= '"Xml_Tagged_P2","errorMessage": null, "fields": [{"Column":"CallId", "Value":"010A03048C21803F"},{"Column":"Position", "Value":"81001"},{"Column":"CallerName", "Value":""}, {"Column":"PhoneNumber", "Value":"0814044478"},   {"Column":"Latitude", "Value":"13.662285"}, {"Column":"Longitude", "Value":"100.682063"}], "commandPacketType": 9, "spillToUser": true}';
		//print_r($dataY ) ;
		if( $dataY['packetMapTableName'] ){

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "Wrong format!!" ;
			print_r( json_encode( $ret ) );
			die();
		}
		$url = HEX_SERVICE.'/AniAli/Spill' ; 
		$spill = $this->curlHexService($url, $data_x , 'PUT' , $token ) ;
		$spill = json_decode( $spill, true ) ;

		if(($spill['errorMessage']=='not authorized') || ($spill['errorMessage']=='The user does not have access to command ANIALISENDPCKT')){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$spill = $this->curlHexService($url, $data_x , 'PUT' , $token ) ;
			$spill = json_decode( $spill, true ) ;
		}

		if( $spill ==""  ){
			$ret['status'] = 0 ;
			$ret['message'] = "success" ;
			//$ret['data'] = $dataY ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] = json_encode( $spill ) ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Spill', 'Spill' , $duration  , $ret['status'] , $data_x  , json_encode($ret)  , 'MobileApp');
		//$ret['status'] = 0 ;
		//$ret['message'] = "success" ;
		print_r( json_encode( $ret ) );
	}

	function getEvent( $app = "false"){
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



		$area = file_get_contents( _CONFIG.'areaTH.json' ) ;
		$area = json_decode($area,true) ;
		$areaTH = [] ;
		foreach ($area as $key => $value) {
			$areaTH[ $value[1] ] = $value[0] ;
		}


		$url = HEX_SERVICE.'/Event' ; 
		$event = $this->curlHexService($url, '' , 'GET' , $token ) ;
		$event = json_decode( $event, true ) ;

		if($event['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$event = $this->curlHexService($url, '' , 'GET' , $token ) ;
			$event = json_decode( $event, true ) ;
		}

		//echo count($event);
		//echo '<pre>';
		file_put_contents( _LOGS.'HEXAGON/eventActive.txt' , json_encode($event)) ;
		$total = 0 ;
		$ds = [] ; 
		foreach ($event as $key => $value) {
			$dispatchGroup = str_replace("จ.", "", $value['dispatchGroup'] ) ;
			$ds[ $areaTH[$dispatchGroup] ][] = $value ;
			$total++ ;
		}

		$ds['total'] = $total ;
		
 
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$ds['duration'] = number_format($duration, 3 , '.', '');   
 
 		$this->db->truncate('eventActive');
 		$dsX = [] ;
		foreach ($ds as $key => $value) {
			foreach ($value as $k => $v) {
				$dataSet = array(
			         'agencyEventId' => $v['agencyEventId'] ,
			         'dispatchGroup' => $v['dispatchGroup'] ,
					 'typeCode' => $v['typeCode'] ,
				     'latitude' =>  $v['latitude']  ,
				     'longitude' =>  $v['longitude']  ,
				     'statusCode' =>  $v['statusCode']  ,
				     'fulldata' => json_encode( $v )  ,
				     'startedTime' => ""
				);

				//Get Created Time Event 
				$d = date('Ymd');
				$path = _LOGS.'HEXAGON/allEvent'  ;
				$EVDetail = file_get_contents( $path."/".$v['agencyEventId']  ) ;   //2020-11-12T12:14:36.0000000+07:00
				if( $EVDetail !="" ){
					$EVDetail = json_decode( $EVDetail , true ) ;
					
				}else{
					$EVDetail = $this->getEventByID( $v['agencyEventId']  ) ;
					if( $EVDetail['status']==0 ){ 
						file_put_contents( $path."/".$v['agencyEventId'] ,  json_encode($EVDetail['data'] ) ) ;  
					}
					$EVDetail = $EVDetail['data'] ;
				}
				$timeC = explode( ".",  $EVDetail['startedTime'] ) ; 
				$timeC =  str_replace("T", " " , $timeC[0]) ;
				$timeC = strtotime( $timeC ) ;
				$dataSet['startedTime'] =  $timeC  ;
				
				$dsX[] = $dataSet ;
				//print_r( $dataSet ) ;
				//$this->db->insert('eventActive', $dataSet); die();
			} 
		} 
 		$this->db->insert_batch('eventActive', $dsX); 
 		if($app=="false"){
 			print_r( json_encode( $ds ) );
 		}else{
 			$ret['status'] = 0;
 			$ret['message'] = "success" ;
 			print_r( json_encode( $ret ) );
 		}

		$url = HEX_SERVICE.'/Event/StatusCodes' ; 
		$status = $this->curlHexService($url, '' , 'GET' , $token ) ;
		$status = json_decode( $status, true ) ;
		file_put_contents( _LOGS.'HEXAGON/eventStatus.txt' , json_encode($status)) ;
		
	}
	 	
	function eventData( $distance = "5" , $agencyEventId = ""  ){
		//SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;
		echo '<pre>';
		$this->db->select('agencyEventId,latitude,longitude');
		$this->db->from( 'eventActive' );
		if( $agencyEventId!="" ){
			$this->db->where('agencyEventId =',  $agencyEventId );
		}
		$event = $this->db->get()->result_array();
		//print_r($event) ;
		foreach ($event as $key => $value) {
			//3959 - miles
			//6371 - kilos 
			$query = sprintf("SELECT *, latitude, longitude, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM eventActive HAVING distance < '%s' ORDER BY distance ",
			   $value['latitude'] ,
			   $value['longitude']  ,
			   $value['latitude']  ,
			   $distance  );
			$res = $this->db->query($query)->result_array();	
 
			echo "<hr>".$value['agencyEventId']."<br>" ; 
			foreach ($res as $k1 => $v1) {
				print_r($v1) ;
			}
			echo '<hr>';
		}
	}
	function eventData2( $distance = "5" , $latitude = "" , $longitude="" , $typeCode="" ){
		//SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;
		echo '<pre>';
		 
			$query = sprintf("SELECT *, latitude, longitude, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM eventActive HAVING distance < '%s' AND typeCode='%s'  ORDER BY distance ",
			   $latitude ,
			   $longitude  ,
			   $latitude  ,
			   $distance,
			   $typeCode  );
			$res = $this->db->query($query)->result_array();	
 
			echo "<hr>".$value['agencyEventId']."<br>" ; 
			foreach ($res as $k1 => $v1) {
				print_r($v1) ;
			}
			echo '<hr>';
		 
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

	function getUnit( $format="" ){
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

		$params =[] ;
		if($_REQUEST['AgencyEventId']!=""){
			$params[] = "AgencyEventId=".$_REQUEST['AgencyEventId'] ;
		}
		if($_REQUEST['AgencyId']!=""){
			$params[] = "AgencyId=".$_REQUEST['AgencyId'] ;
		}
		if($_REQUEST['DispatchGroup']!=""){
			$params[] = "DispatchGroup=".$_REQUEST['DispatchGroup'] ;
		}
		if($_REQUEST['UnitType']!=""){
			$params[] = "UnitType=".$_REQUEST['UnitType'] ;
		}
		if($_REQUEST['Beat']!=""){
			$params[] = "Beat=".$_REQUEST['Beat'] ;
		}
		//http://hex-oncall.191poc.local:8000/api/v1/Unit?AgencyEventId=1&AgencyId=2&DispatchGroup=3&UnitType=4&Beat=5
		$url = HEX_SERVICE.'/Unit?'.implode("&", $params ) ; 
		$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
		$ds = json_decode( $ds, true ) ;

		if($ds['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
			$ds = json_decode( $ds, true ) ;
		}
 
		if( count( $ds)>0 && !$ds['errorMessage']){ 

			$this->db2->truncate('hex_unit');
			$this->db2->insert_batch('hex_unit', $ds ); 

			$file = _CONFIG."/hex_unit" ;
			file_put_contents( $file , json_encode($ds )) ;
			$ret['status'] = 0 ;
			$ret['message'] = "Write success" ;
			$ret['pathFile'] =  $file  ;
			$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
			$ret['url'] = $protocol.$_SERVER['SERVER_NAME']."/config/hex_unit"  ;
			$ret['server'] = $_SERVER['SERVER_ADDR'] ;
			//$ret['data'] = $dataY ;
			if($format=='json'){
				$ret['data'] = $ds ;
			}
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] = json_encode( $ds ) ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Service', 'Unit' , $duration  , $ret['status'] , $data_x  , json_encode($ret)  , 'Hex-Service');
		print_r(  json_encode( $ret ) );
	}

	function DispatchGroups( $format="" ){
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
 
		//http://hex-oncall.191poc.local:8000/api/v1/Unit?AgencyEventId=1&AgencyId=2&DispatchGroup=3&UnitType=4&Beat=5
		$url = HEX_SERVICE.'/AgencyDispatchGroups' ; 
		$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
		$ds = json_decode( $ds, true ) ;

		if($ds['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
			$ds = json_decode( $ds, true ) ;
		}

		if( count( $ds)>0 && !$ds['errorMessage']){

			$agency = [] ;
			$dispatchGroups = [];
			foreach ($ds as $key => $value ) {
				$agency[] = $value['agency'] ;
				foreach ($value['dispatchgroup'] as $key1 => $value1 ) {
					$value1['agencyId'] = $value['agency']['agencyId'] ; 
					$dispatchGroups[] = $value1 ; 
				}
			}
			
			$this->db2->truncate('hex_agency');
			$this->db2->insert_batch('hex_agency', $agency ); 
			
			$this->db2->truncate('hex_dispatchGroups');
			$this->db2->insert_batch('hex_dispatchGroups', $dispatchGroups ); 
 

			$file = _CONFIG."/hex_dispatchGroups" ;
			file_put_contents( $file , json_encode($ds )) ;
			$ret['status'] = 0 ;
			$ret['message'] = "Write success" ;
			$ret['pathFile'] =  $file  ;
			$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
			$ret['url'] = $protocol.$_SERVER['SERVER_NAME']."/config/hex_dispatchGroups"  ;
			$ret['server'] = $_SERVER['SERVER_ADDR'] ;
			if($format=='json'){
				$ret['data'] = $ds ;
			}
			//$ret['data'] = $dataY ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] = json_encode( $ds ) ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Service', 'DispatchGroups' , $duration  , $ret['status'] , $data_x  , json_encode($ret)  , 'Hex-Service');
		print_r(  json_encode( $ret ) );
	}

	function Stations( $format="" ){
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

		//http://hex-oncall.191poc.local:8000/api/v1/Unit?AgencyEventId=1&AgencyId=2&DispatchGroup=3&UnitType=4&Beat=5
		$url = HEX_SERVICE.'/Stations' ; 
		$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
		$ds = json_decode( $ds, true ) ;

		if($ds['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
			$ds = json_decode( $ds, true ) ;
		}

		if( count( $ds)>0 && !$ds['errorMessage'] ){

			$this->db2->truncate('hex_stations');
			$this->db2->insert_batch('hex_stations', $ds ); 

			$file = 	_CONFIG."/hex_stations" ;
			file_put_contents( $file , json_encode($ds )) ;
			$ret['status'] = 0 ;
			$ret['message'] = "Write success" ;
			$ret['pathFile'] =  $file  ;
			$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
			$ret['url'] = $protocol.$_SERVER['SERVER_NAME']."/config/hex_stations"  ;
			$ret['server'] = $_SERVER['SERVER_ADDR'] ;
			if($format=='json'){
				$ret['data'] = $ds ;
			}
			//$ret['data'] = $dataY ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] = json_encode( $ds ) ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Service', 'Stations' , $duration  , $ret['status'] , $data_x  , json_encode($ret)  , 'Hex-Service');
		print_r(  json_encode( $ret ) );
	}

	function unitStatusCodes( $format="" ){
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

		//http://hex-oncall.191poc.local:8000/api/v1/Unit?AgencyEventId=1&AgencyId=2&DispatchGroup=3&UnitType=4&Beat=5
		$url = HEX_SERVICE.'/Unit/StatusCodes' ; 
		$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
		$ds = json_decode( $ds, true ) ;

		if($ds['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
			$ds = json_decode( $ds, true ) ;
		}

		if( count( $ds)>0 && !$ds['errorMessage']){

			$this->db2->truncate('hex_unitStatusCodes');
			$this->db2->insert_batch('hex_unitStatusCodes', $ds ); 

			$file = 	_CONFIG."/hex_unitStatusCodes" ;
			file_put_contents( $file , json_encode($ds )) ;
			$ret['status'] = 0 ;
			$ret['message'] = "Write success" ;
			$ret['pathFile'] =  $file  ;
			$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
			$ret['url'] = $protocol.$_SERVER['SERVER_NAME']."/config/hex_unitStatusCodes"  ;
			$ret['server'] = $_SERVER['SERVER_ADDR'] ;
			if($format=='json'){
				$ret['data'] = $ds ;
			}
			//$ret['data'] = $dataY ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] = json_encode( $ds ) ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Service', 'unitStatusCodes' , $duration  , $ret['status'] , $data_x  , json_encode($ret)  , 'Hex-Service');
		print_r(  json_encode( $ret ) );
	}

	function eventStatusCodes( $format="" ){
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

		//http://hex-oncall.191poc.local:8000/api/v1/Unit?AgencyEventId=1&AgencyId=2&DispatchGroup=3&UnitType=4&Beat=5
		$url = HEX_SERVICE.'/Event/StatusCodes' ; 
		$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
		$ds = json_decode( $ds, true ) ;
 
		if($ds['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
			$ds = json_decode( $ds, true ) ;
		}

		if( count( $ds)>0 && !$ds['errorMessage']){
 
			$this->db2->truncate('hex_eventStatusCodes');
			$this->db2->insert_batch('hex_eventStatusCodes', $ds ); 

			$file = 	_CONFIG."/hex_eventStatusCodes" ;
			file_put_contents( $file , json_encode($ds )) ;
			$ret['status'] = 0 ;
			$ret['message'] = "Write success" ;
			$ret['pathFile'] =  $file  ;
			$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
			$ret['url'] = $protocol.$_SERVER['SERVER_NAME']."/config/hex_eventStatusCodes"  ;
			$ret['server'] = $_SERVER['SERVER_ADDR'] ;
			if($format=='json'){
				$ret['data'] = $ds ;
			}
			//$ret['data'] = $dataY ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] = json_encode( $ds ) ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Service', 'eventStatusCodes' , $duration  , $ret['status'] , $data_x  , json_encode($ret)  , 'Hex-Service');
		print_r(  json_encode( $ret ) );
	}

	function TypeSubtypes( $format="" ){
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

		//http://hex-oncall.191poc.local:8000/api/v1/Unit?AgencyEventId=1&AgencyId=2&DispatchGroup=3&UnitType=4&Beat=5
		$url = HEX_SERVICE.'/Event/TypeSubtypes' ; 
		$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
		$ds = json_decode( $ds, true ) ;
 
		if($ds['errorMessage']=='not authorized'){

			$token = $this->hexAuthen();
			$token = $token['token'] ;
 			$ds = $this->curlHexService($url, $data_x , 'GET' , $token ) ;
			$ds = json_decode( $ds, true ) ;
		}

		if( count( $ds)>0 && !$ds['errorMessage']){
 
			//$this->db2->truncate('hex_eventStatusCodes');
			//$this->db2->insert_batch('hex_eventStatusCodes', $ds ); 

			$file = 	_CONFIG."/hex_TypeSubtypes" ;
			file_put_contents( $file , json_encode($ds )) ;
			$ret['status'] = 0 ;
			$ret['message'] = "Write success" ;
			$ret['pathFile'] =  $file  ;
			$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
			$ret['url'] = $protocol.$_SERVER['SERVER_NAME']."/config/hex_TypeSubtypes"  ;
			$ret['server'] = $_SERVER['SERVER_ADDR'] ;
			if($format=='json'){
				$ret['data'] = $ds ;
			}
			//$ret['data'] = $dataY ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = "fail" ;
			$ret['errorMsg'] = json_encode( $ds ) ;
		}
		$time_end = microtime(true) ;
		$duration = $time_end - $time_start ;
		$duration = number_format($duration, 3 , '.', '') ;   
		$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Service', 'eventStatusCodes' , $duration  , $ret['status'] , $data_x  , json_encode($ret)  , 'Hex-Service');
		print_r(  json_encode( $ret ) );
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