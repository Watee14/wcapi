<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Arcgis extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->library('twiggy');
		$this->load->model('common_model');
		$this->load->model('config_model');
 		$this->load->model('Writelogs_model');
	}

	public function Route( $routeType="" ,  $AgencyEventId =""  ,  $latitude ="" , $longitude= "" , $stagingAreaId ="" )
	{
		// echo $latlon.'--'.$smhID ;
		//echo '--------------------';
		if($routeType !=""  && $AgencyEventId !="" && $latitude !="" && $longitude !=""  ){
			error_reporting(E_ERROR) ;

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
			$url = HEX_SERVICE.'/Event/'.$AgencyEventId."?Expand=CommonEvent,StagingArea" ; 
			$ds = $this->curlHexService($url, "" , 'GET' , $token ) ;
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
				 //echo '<pre>';
 				//print_r( $ds ) ;
 				$this->twiggy->set('ARCGIS_MAPTILE',  ARCGIS_MAPTILE  );
 				$this->twiggy->set('ARCGIS_POI_LAYER',   ARCGIS_POI_LAYER  );
 				$this->twiggy->set('ARCGIS_ROUTE',   ARCGIS_ROUTE  );

 				$this->twiggy->set('routeType',  $routeType  );

				$this->twiggy->set('start_lat',   $latitude  );
				$this->twiggy->set('start_lon',   $longitude  );
				if($routeType=="Event"){
					$this->twiggy->set('end_lat',   $ds['commonEvent']['latitude'] );
					$this->twiggy->set('end_lon',   $ds['commonEvent']['longitude']  );
				}elseif ($routeType=="Staging") {
					foreach ($ds['stagingAreaData'] as $k1 => $v1) {
						if($v1['stagingAreaId'] == $stagingAreaId ){
							$this->twiggy->set('end_lat',   $v1['latitude'] );
							$this->twiggy->set('end_lon',   $v1['longitude']  );
						}
					}
					$this->twiggy->set('event_lat',   $ds['commonEvent']['latitude'] );
					$this->twiggy->set('event_lon',   $ds['commonEvent']['longitude']  );
				}
				
				//$this->twiggy->set('staging_lat',   $ds['stagingAreaData'][1]['latitude'] );
				//$this->twiggy->set('staging_lon',   $ds['stagingAreaData'][1]['longitude']  );

				$this->twiggy->set('staging',   json_encode($ds['stagingAreaData']) );
//echo $latitude ." | ".$longitude." ---> ". $ds['commonEvent']['latitude'] ." | ".$ds['commonEvent']['longitude'] ." --staging-> ". $ds['stagingAreaData'][0]['latitude'] ." | ".$ds['stagingAreaData'][0]['longitude'];
				$this->twiggy->template('arcgis/Route')->display() ; 


			}else{
				$ret['status'] = -1 ;
				$ret['message'] = "fail" ;
				$ret['errorMsg'] =  $ds   ;
			}
		 
			$time_end = microtime(true) ;
			$duration = $time_end - $time_start ;
			$duration = number_format($duration, 3 , '.', '') ;   
			$this->Writelogs_model->wLogs(  $ani  , $_SERVER['REMOTE_ADDR'] , 'info', 'Route', 'getEventByID' , $duration  , $ret['status'] ,  $agencyEventId  , json_encode($ret)  , 'Responsder');

			 

			/*$strLL = explode("_", $latlon ) ;
			$this->twiggy->set('_smh_lat', 	  $strLL[0] );
			$this->twiggy->set('_smh_lon',   $strLL[1]  );

			$this->db->select('*');
			$this->db->from('smh_job'); 
			$this->db->where('smh_job_code =',  $smhID );
			 
			$data = $this->db->get()->result_array();
			$data = $data[0] ;*/
			//print_r($data) ;
			
			/*$this->twiggy->set('start_lat',   $data['start_lat'] );
			$this->twiggy->set('start_lon',   $data['start_lon']  );
			$this->twiggy->set('end_lat',   $data['end_lat'] );
			$this->twiggy->set('end_lon',   $data['end_lon']  );
			$this->twiggy->set('stage',   "view"  );*/

			//$line = file_get_contents(_SMH_DATA.'\\lines\\'.$smhID  ) ;
			//$ds_res = json_decode($line,true) ;
			//$lineString = [] ;
			/*if($ds_res['properties']){
				 
				
				foreach ($ds_res['features'] as $k1 => $v1) {
					foreach ($v1['geometry']['coordinates'] as $k2 => $v2) {
						$ll = [];
						$ll['lon'] = $v2[0]  ;
						$ll['lat'] = $v2[1]  ;
						$lineString[] = $ll ;
					}
					//$lineString[] = $v1 ;
				}
			}*/
			//file_put_contents(_SMH_LINE.'/'.$req['smh_job_code'] , $resultLn) ;
			//$req['lineString'] = $lineString ;
			//$this->twiggy->set('lineString',   json_encode( $lineString ) );
			//$this->twiggy->template('arcgis/Route')->display() ;
			//print_r($lineString) ;

		} else{
			$this->twiggy->template('arcgis/Route')->display() ; 
		}
		
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