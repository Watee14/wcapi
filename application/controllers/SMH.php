<?php error_reporting(E_ERROR) ;
header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class SMH extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
	}
	public function buildData()
	{
		echo gmdate("H:i:s", 60); die();
		header('Content-Type:text/html');
		echo '<pre>';
		echo '<h1>REQUEST</h1>';
		 //echo '<pre>';
	 	$x['citizen_code']  = '130';
	 	$x['citizen_phone_number'] = '0809955502';
	 	$x['start_lat']  = '13.778304';
	 	$x['start_lon'] = '100.476853';
	 	$x['start_addr'] = '';
	 	$x['end_lat']  = '13.7568542';
	 	$x['end_lon'] = '100.563673';
	 	$x['end_addr'] = '';
	 	$x['smh_type'] = '0';
 		$x['description'] = 'เสื้อดำ กางเกงยีนส์';
 		print_r( ($x)) ;  
	 	$data = $this->CryptoJS->cryptoJsAesEncrypt( key_decrypt , json_encode($x) ); 
	 	print_r(json_encode($data)) ; 
 		echo '<hr>';
 		//###########################
 		//###########################
 		//###########################
 		echo '<h1>ENCODE</h1>';
 		$x = [];
 		$x['citizen_code']  = '130';
	 	$x['citizen_phone_number'] = '0809955502';
	 	$x['smh_job_code']  = '0809955502_1582176732';
	 	 
 		print_r( ($x)) ;  
	 	$data = $this->CryptoJS->cryptoJsAesEncrypt( key_decrypt , json_encode($x) ); 
	 	print_r(json_encode($data)) ; 
 		echo '<hr>';


	}
	public function testAPI()
	{
		$method = 'POST' ; 
		$url = 'http://192.168.1.202/191ws/SMH/requestJob';
		$data = "{\"ct\":\"UYDPzzacjxHRlAdyzxvnYAKskuVWpaYdeoGljeMFef1DgXU6wncjxElYz2OyO01tOskKmWUVr4dOw\\\/H74j+hso4itbReByGyzG6Bx+pLgFqtfIH1CCTootXbtnonCyJZJzjB0lVXOfZqFzx21Ccz5lEJTG0vOj3NTKXVI\\\/fVLWLEwWVxwisxRBXLVCbDao0syloy5f7l37HRXluuCf0vydErqbE+uGvV5uEX1s62kX80FIXXCIvvUYCM97Zqs3CLK\\\/Igc37GNm6yqPYt0WBF+\\\/EK1bUEjDSZmNR1Vo\\\/TORbQutG3m3xYyuqjoLCADGt5egy+\\\/QL9UdhTsnyNkFLAKPK5ZmOLTGZNqf7Yj7wXCS6uc3mCLDJm5KR06V3T4SS8k13YjRuHhjklyz\\\/0qE34JsRgUjbj7vpDnjptdb5snnNb1ro++RxzoZ6pII+dFUg+efEB4sHRhrOnW4OL2AQqQrptkX8VKBq50APBv2Ve0yAfSnd\\\/n7icVKVDbJX0bhQqMjBlLNhfdfvCh6ReXIguEw==\",\"iv\":\"93ffe47530653a3e36238b2ad8dc84ad\",\"s\":\"f74c914e67d3ee75\"}" ; 
		$url = "http://192.168.1.202/191ws/SMH/cancelJob" ; 
		$data =   "{\"ct\":\"8W7cfje0\\\/FbTbAJxvILP+bF\\\/frMq4kT8sohV3UuRA8ALhgfhJNYcGddyEnl4h5eZ4cRLhMT84LydDvQadg22IQbTqDjlm4ixdCcZSBxq+JJmQijg4HXMb2Z4E8hlETiFI7lTAgNR0WaUHvN+F1XkDA==\",\"iv\":\"d96a3dcb42fcda12f5927c8b5c3dfe30\",\"s\":\"b538349e4c508c75\"}" ;
		$res = $this->curlTest($url, $data, $method) ;
		print_r($res) ;
	}
	public function dataJob($smh_job_code="")
	{
		$ret['status'] = -1 ;
		$ret['message'] = 'No data' ;
		$ret['data'] = [] ;
		if($smh_job_code!=''){
			$this->db->select('*');
			$this->db->from('smh_job'); 
			if($smh_job_code!='allData'){
				$this->db->where('smh_job_code =',  $smh_job_code );
			} 
			
			$data = $this->db->get()->result();

			if(count($data)>0){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success' ;
				$ret['data'] = $data ;
			}
		}
		print_r( json_encode($ret) ) ;
	}
	public function requestJob()
	{
		 
	 	$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   
		 
	 	$req = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  $data_x  );
	 	$req = nl2br(str_replace(array("\n", "\r"), ' ', $req));
	 	$req = json_decode( $req , true ) ; 
	 	//print_r($req ) ; die();
	 	
	 	$level = '';
	 	if(isset($req['citizen_phone_number']) && $req['citizen_phone_number']!='' ){

	 		$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status,start_police_station_code,start_inspection_area_code,end_police_station_code,end_inspection_area_code');
			$this->db->from('smh_job');
			$this->db->where('citizen_phone_number =',  $req['citizen_phone_number']);
			$this->db->where_not_in('status', ['2', '3', '5'] );
			$job = $this->db->get()->row();
			//print_r($job) ; die();
			if($job->id==''){
				//echo '00'; die();
			 	$this->db->select('citizen_code , citizen_phone_number');
				$this->db->from('citizen_profile');
				$this->db->where('citizen_phone_number =',  $req['citizen_phone_number']);
				$user = $this->db->get()->row();
				if($user->citizen_code!=''){
					//echo '01'; die();
					//$req['smh_job_code'] = $req['citizen_phone_number'].'_'.time() ;
					$req['smh_job_code'] = "S".substr(  (date("Y")+543) , 2 ).date('mdHms').substr(  date("ms")  , -3 )  ;
					$req['req_dt'] = date('Y-m-d H:i:s') ; 
					//$req['police_station_code'] = '' ;
					//$req['est_time'] = '' ;
					//$req['est_distance'] = '' ;
					$req['status'] = '0' ;
					$req['modify_dt'] = date('Y-m-d H:i:s') ;


					if( trim($req['start_addr']) =="" ){
						$result = $this->address( $req['start_lat'] , $req['start_lon'] ) ;
						//$result_ln = json_decode($result , true ) ;
						//print_r($result_ln) ;
						$req['start_addr'] = $result['full_addr']  ;
					}
					if( trim($req['end_addr']) =="" ){
						$result = $this->address( $req['end_lat'] , $req['end_lon'] ) ;
						//$result_ln = json_decode($result , true ) ;
						//print_r($result_ln) ;
						$req['end_addr'] = $result['full_addr']  ;
					}
					//print_r($req) ; 

					//Estimate// 
					$routeRestrict= $req['routeRestrict'] ;
					if($routeRestrict!="1"){
						$routeRestrict= 0 ;
					}
					$url = _MAP_ROUTE_LINE."?flat=".$req['start_lat']."&flon=".$req['start_lon']."&tlat=".$req['end_lat']."&tlon=".$req['end_lon']."&mode=".$req['RouteMode']."&type=".$req['RouteType']."&restrict=".$routeRestrict."&locale=th&key="._MAP_KEY;
					//die();
					//echo $url.'<br>' ; die();
					$resultLn = $this->curlService($url, '', 'GET');
					$ds_res = json_decode($resultLn,true) ;
					if($ds_res['properties']){
						//$req['est_distance'] = $ds_res['properties']['distance'] ; // Meter
						//$req['est_time'] = $ds_res['properties']['interval'] ; // sec
						//echo _SMH_LINE.'/'.$req['smh_job_code'] , $resultLn ; 
						file_put_contents(_SMH_DATA.'/lines/'.$req['smh_job_code'] , $resultLn) ;
					}
					//print_r( $req ) ; die();
					 
					 //## Point A policeStatiopn
					//C:\inetpub\wwwroot\191ws\pyChkArea\innerPoint.py 
					//echo _PY_CHECK_POINT.'--'._PY_AREA ;
					//echo _PY_CHECK_POINT.' '._PY_AREA.' '.$req['start_lat'].'  '. $req['start_lon'] ;
					$str = _PY_CHECK_POINT.' '._PY_AREA.' '.$req['start_lat'].'  '. $req['start_lon'];
					$str = '"C:\Program Files\Python38\python.exe" '.$str ; // For Lab
					//$police = escapeshellcmd($str );
					$output = shell_exec($police);  
					$output = json_decode($output ,true )   ;  
					$req['start_police_station_code'] = $output[0][1] ;
					$req['start_inspection_area_code'] = $output[1][1] ;
					 
					//## Point B policeStatiopn
					//C:\inetpub\wwwroot\191ws\pyChkArea\innerPoint.py
					$str = _PY_CHECK_POINT.' '._PY_AREA.' '.$req['end_lat'].'  '. $req['end_lon'] ;
					$str = '"C:\Program Files\Python38\python.exe" '.$str ; // For Lab
					//$police = escapeshellcmd( $str );
					$output = shell_exec($str);  
					$output = json_decode($output ,true )   ;
					$req['end_police_station_code'] = $output[0][1] ; 
					$req['end_inspection_area_code'] = $output[1][1] ;

					
					
					//print_r( $req ) ; die();
					//unset($req['mode']);
					//unset($req['type']);
					//echo $output;
					$query = $this->db->insert('smh_job', $req );
					//print_r( $this->db->last_query() ) ;
					if($query==1){
						$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status,start_police_station_code,start_inspection_area_code,end_police_station_code,end_inspection_area_code');
						$this->db->from('smh_job');
						$this->db->where('smh_job_code =',  $req['smh_job_code']); 
						$job = $this->db->get()->row();
						$ret['status'] = 0 ;
						$ret['message'] = 'Success';
						$ret['data'] = $job ;
					 	$level = 'Info';
					}else{
						$ret['status'] = -1 ;
						$ret['message'] = 'Create Fail';
						$level = 'High';
					}	
				}else{
					$ret['status'] = '-1' ;
		 			$ret['message'] = 'Data not found' ;
		 			$level = 'High';
				}
			}else{
				$ret['status'] = '-1' ;
			 	$ret['message'] = 'Duplicate request' ;
			 	$ret['data'] = $job ;
			 	$level = 'Info';
			}
	 	}else{
	 		$ret['status'] = '-1' ;
	 		$ret['message'] = 'Data error!' ;
	 		$level = 'High';
	 	}
		

	 	$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $req['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , $level , 'smh', 'requestJob' , $duration  , $ret['status'] , $data_x , json_encode($ret )  , 'MobileApp') ;
				
	 	print_r(json_encode($ret)) ;
	}

	public function startJob()
	{
		 
	 	$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   
		 
	 	$req = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  $data_x  );
	 	$req = nl2br(str_replace(array("\n", "\r"), ' ', $req));
	 	$req = json_decode( $req , true ) ; 
	 	//print_r($req ) ; die();
	 	
	 	$level = '';
	 	if(isset($req['smh_job_code']) && $req['smh_job_code']!=''){
	 		$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
			$this->db->from('smh_job');
			$this->db->where('smh_job_code =',  $req['smh_job_code']);
			//$this->db->where('status =',  0 );
			$this->db->where_in('status', ['0', '4', '8'] );
			$job = $this->db->get()->row();
			//print_r($job) ;
			if($job->id!=''){

				$ds = [] ;
				$ds['start_dt'] = date('Y-m-d H:i:s') ; 
				$ds['status'] = '1' ;
				$ds['modify_dt'] = date('Y-m-d H:i:s') ;
				//print_r($ds ) ; die();
				$this->db->where('smh_job_code', $req['smh_job_code'] );
				$query = $this->db->update('smh_job', $ds); 

				if($query==1){
					$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
					$this->db->from('smh_job');
					$this->db->where('smh_job_code =',  $req['smh_job_code']);
					$job = $this->db->get()->row();
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';
					$ret['data'] = $job ;
					$level = 'Info' ;
				 
				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
					$level = 'High';
				}
			}else{
				$ret['status'] = '-1' ;
			 	$ret['message'] = 'Data not found' ;
			 	$level = 'Info';
			 	$level = 'Info' ;
			}
	 	}else{
	 		$ret['status'] = '-1' ;
	 		$ret['message'] = 'Data error!' ;
	 		$level = 'High';
	 	}

	 	$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $req['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , $level , 'smh', 'startJob' , $duration  , $ret['status'] , $data_x , json_encode($ret )  , 'MobileApp') ;
		
	 	print_r(json_encode($ret)) ;
	}

	public function endJob()
	{
		 
	 	$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   
		 
	 	$req = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  $data_x  );
	 	$req = nl2br(str_replace(array("\n", "\r"), ' ', $req));
	 	$req = json_decode( $req , true ) ; 
	 	//print_r($req ) ; die();
	 	
	 	$level = '';
	 	if(isset($req['smh_job_code']) && $req['smh_job_code']!=''){
	 		$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
			$this->db->from('smh_job');
			$this->db->where('smh_job_code =',  $req['smh_job_code']);
			$this->db->where_in('status',  ['1','4','6','7','8'] );
			$job = $this->db->get()->row();
			//print_r($job) ; die();
			if($job->id!=''){

				$ds = [] ;
				$ds['end_dt'] = date('Y-m-d H:i:s') ; 
				$ds['status'] = '2' ;
				$ds['modify_dt'] = date('Y-m-d H:i:s') ;
				//print_r($ds ) ; die();
				$this->db->where('smh_job_code', $req['smh_job_code'] );
				$query = $this->db->update('smh_job', $ds); 

				if($query==1){
					$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
					$this->db->from('smh_job');
					$this->db->where('smh_job_code =',  $req['smh_job_code']);
					$job = $this->db->get()->row();
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';
					$ret['data'] = $job ;
					$level = 'Info' ;
				 
				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
					$level = 'High';
				}
			}else{
				$ret['status'] = '-1' ;
			 	$ret['message'] = 'Data not found' ;
			 	$level = 'Info';
			 	$level = 'Info' ;
			}
	 	}else{
	 		$ret['status'] = '-1' ;
	 		$ret['message'] = 'Data error!' ;
	 		$level = 'High';
	 	}

	 	$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $req['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , $level , 'smh', 'endJob' , $duration  , $ret['status'] , $data_x , json_encode($ret )  , 'MobileApp') ;
		
	 	print_r(json_encode($ret)) ;
	}

	public function cancelJob()
	{
		 
	 	$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   
		 
	 	$req = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  $data_x  );
	 	$req = nl2br(str_replace(array("\n", "\r"), ' ', $req));
	 	$req = json_decode( $req , true ) ; 
	 	//print_r($req ) ; die();
	 	
	 	$level = '';
	 	if(isset($req['smh_job_code']) && $req['smh_job_code']!=''){
	 		$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
			$this->db->from('smh_job');
			$this->db->where('smh_job_code =',  $req['smh_job_code']);
			$this->db->where_in('status',  ['0','1','4','7','8'] );
			$job = $this->db->get()->row();
			//print_r($job) ; die();
			if($job->id!=''){

				$ds = [] ;
				$ds['cancel_dt'] = date('Y-m-d H:i:s') ; 
				$ds['status'] = '3' ;
				$ds['modify_dt'] = date('Y-m-d H:i:s') ;
				//print_r($ds ) ; die();
				$this->db->where('smh_job_code', $req['smh_job_code'] );
				$query = $this->db->update('smh_job', $ds); 

				if($query==1){
					$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
					$this->db->from('smh_job');
					$this->db->where('smh_job_code =',  $req['smh_job_code']);
					$job = $this->db->get()->row();
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';
					$ret['data'] = $job ;
					$level = 'Info' ;
				 
				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
					$level = 'High';
				}
			}else{
				$ret['status'] = '-1' ;
			 	$ret['message'] = 'Data not found' ;
			 	$level = 'Info';
			 	$level = 'Info' ;
			}
	 	}else{
	 		$ret['status'] = '-1' ;
	 		$ret['message'] = 'Data error!' ;
	 		$level = 'High';
	 	}

	 	$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $req['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , $level , 'smh', 'canceltJob' , $duration  , $ret['status'] , $data_x , json_encode($ret )  , 'MobileApp') ;
		
	 	print_r(json_encode($ret)) ;
	}

	public function emergency()
	{
		 
	 	$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   
		 
	 	$req = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  $data_x  );
	 	$req = nl2br(str_replace(array("\n", "\r"), ' ', $req));
	 	$req = json_decode( $req , true ) ; 
	 	//print_r($req ) ; die();
	 	
	 	$level = '';
	 	if(isset($req['smh_job_code']) && $req['smh_job_code']!='' && $req['emergency']!=""){
	 		$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
			$this->db->from('smh_job');
			$this->db->where('smh_job_code =',  $req['smh_job_code']);
			$this->db->where('citizen_phone_number =',  $req['citizen_phone_number']);
			//$this->db->where_in('status',  ['0','1','4','7','8'] );
			$job = $this->db->get()->row();
			//print_r($job) ; die();
			if($job->id!=''){

				$ds = [] ;
				
				$ds['emergency'] = $req['emergency'] ;
				$ds['modify_dt'] = date('Y-m-d H:i:s') ;
				//print_r($ds ) ; die();
				$this->db->where('smh_job_code', $req['smh_job_code'] );
				$query = $this->db->update('smh_job', $ds); 

				if($query==1){
					$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
					$this->db->from('smh_job');
					$this->db->where('smh_job_code =',  $req['smh_job_code']);
					$job = $this->db->get()->row();
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';
					$ret['data'] = $job ;
					$level = 'Info' ;
				 
				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
					$level = 'High';
				}
			}else{
				$ret['status'] = '-1' ;
			 	$ret['message'] = 'Data not found' ;
			 	$level = 'Info';
			 	$level = 'Info' ;
			}
	 	}else{
	 		$ret['status'] = '-1' ;
	 		$ret['message'] = 'Data error!' ;
	 		$level = 'High';
	 	}

	 	$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $req['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , $level , 'smh', 'emergencyJob' , $duration  , $ret['status'] , $data_x , json_encode($ret )  , 'MobileApp') ;
		
	 	print_r(json_encode($ret)) ;
	}

	public function currentJob()
	{
		 
	 	$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   
		 
	 	$req = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  $data_x  );
	 	$req = nl2br(str_replace(array("\n", "\r"), ' ', $req));
	 	$req = json_decode( $req , true ) ; 
	 	//print_r($req ) ; die();
	 	 
	 	$level = '';
	 	if(isset($req['citizen_phone_number']) && $req['citizen_phone_number']!='' ){

	 		$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,req_dt,start_lat,start_lon,start_addr,start_police_station_code,start_inspection_area_code,end_lat,end_lon,end_addr,end_police_station_code,end_inspection_area_code,est_time,est_distance,start_dt,accept_dt,end_dt,close_dt,cancel_dt,duration,user_accept,user_close,smh_type,status,description,detail,modify_dt,owner,RouteMode,RouteType,emergency,routeRestrict');
			$this->db->from('smh_job');
			$this->db->where('citizen_phone_number =',  $req['citizen_phone_number']);
			$this->db->where_not_in('status', ['2', '3', '5'] );
			$job = $this->db->get()->row();
			//print_r($job) ; die();
			if($job->id!=''){
				if($job->start_dt!="" && $job->start_dt!=null){
			 		$job->timeCounter = $job->est_time - ( time()- strtotime( str_replace("-", "/", $job->start_dt ) ) ) ;
			 		if($job->timeCounter<0){
			 			$job->timeCounter=0 ;
			 		}
			 	}
				$ret['status'] = 0 ;
		 		$ret['message'] = '' ;
		 		$level = 'Info';
		 		$ret['data'] = $job ;
			}else{
				$ret['status'] = 1 ;
			 	$ret['message'] = 'Data not found' ;
			 	$ret['data'] = $job ;
			 	$level = 'Info';
			}
	 	}else{
	 		$ret['status'] = '-1' ;
	 		$ret['message'] = 'Data error!' ;
	 		$level = 'High';
	 	}

		
 		//echo "MMM".$job->est_time .'--'. strtotime($job->start_dt )   .'--'. strtotime("2020-05-05 12:12:12" )   ;
	 	$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $req['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , $level , 'smh', 'currentJob' , $duration  , $ret['status'] , $data_x , json_encode($ret )  , 'MobileApp') ;
				
	 	print_r(json_encode($ret)) ;
	}

	public function currentLocation()
	{
		 
	 	$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   
		 
	 	$req = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  $data_x  );
	 	$req = nl2br(str_replace(array("\n", "\r"), ' ', $req));
	 	$req = json_decode( $req , true ) ; 
	 	//print_r($req ) ; die();
	 	
	 	$level = '';
	 	if( isset($req['lat']) && $req['lat']!='' && isset($req['lon']) && $req['lon']!='' && isset($req['citizen_phone_number']) && $req['citizen_phone_number']!='' ){

	 		$this->db->select('id,smh_job_code,citizen_code,citizen_phone_number,status');
			$this->db->from('smh_job');
			$this->db->where('citizen_phone_number =',  $req['citizen_phone_number']);
			$this->db->where_not_in('status', ['1', '4', '6', '7', '8'] );
			$job = $this->db->get()->row();
			//print_r($job) ; die();
			if($job->id!=''){
				$req['timestampServer'] = time(); 
				file_put_contents(_SMH_DATA.'/locations/'.$req['smh_job_code'] , json_encode($req)) ; 
				$ret['status'] = 0 ;
		 		$ret['message'] = '' ;
		 		$level = 'Info';
		 		//$ret['data'] = $job ;
			}else{
				$ret['status'] = 1 ;
			 	$ret['message'] = 'Data not found' ;
			 	$ret['data'] = $job ;
			 	$level = 'Info';
			}
	 	}else{
	 		$ret['status'] = '-1' ;
	 		$ret['message'] = 'Data error!' ;
	 		$level = 'High';
	 	}

		
 		//echo "MMM".$job->est_time .'--'. strtotime($job->start_dt )   .'--'. strtotime("2020-05-05 12:12:12" )   ;
	 	$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $req['citizen_phone_number']  , $_SERVER['REMOTE_ADDR'] , $level , 'smh', 'currentJob' , $duration  , $ret['status'] , $data_x , json_encode($ret )  , 'MobileApp') ;
				
	 	print_r(json_encode($ret)) ;
	}

	public function address($lat = "", $lon = "")
	{
		// echo _MAP_SERVICE;
		//echo '<pre>';
		//print_r($_SESSION['user']->user_name ) ; die();
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
		$this->Writelogs_model->wLogs( 'XXX' , $_SERVER['REMOTE_ADDR'] , $ltype , 'getAddress', 'callws' , $duration  , $status , $url , $result , 'Map_API'  );
		$result = json_decode($result , true) ;
		$result['full_addr'] = $result['end_addr'] = $result['aoi'].' '.$result['road'].' '.$result['subdistrict'].' '.$result['district'].' '.$result['province'].' '.$result['country'].' '.$result['postcode'] ; 
		return $result ;
		//print_r($res);
	}
	function routeDetail(){
		$req = $_REQUEST ;
		$url = _MAP_ROUTE_LINE."?flat=".$req['start_lat']."&flon=".$req['start_lon']."&tlat=".$req['end_lat']."&tlon=".$req['end_lon']."&mode=".$req['mode']."&type=".$req['type']."&restrict=".$req['restrict']."&locale=th&key="._MAP_KEY;
		//die();
		//echo $url.'<br>' ; die();
		$req['url'] = $url ;
		$resultLn = $this->curlService($url, '', 'GET');
		$ds_res = json_decode($resultLn,true) ;
		if($ds_res['properties']){
			$req['est_distance'] = $ds_res['properties']['distance'] ; // Meter
			$req['est_time'] = $ds_res['properties']['interval'] ; // sec
			$lineString = [] ;
			foreach ($ds_res['features'] as $k1 => $v1) {
				foreach ($v1['geometry']['coordinates'] as $k2 => $v2) {
					$ll = [];
					$ll['lon'] = $v2[0]  ;
					$ll['lat'] = $v2[1]  ;
					$lineString[] = $ll ;
				}
				//$lineString[] = $v1 ;
			}
			//file_put_contents(_SMH_LINE.'/'.$req['smh_job_code'] , $resultLn) ;
			$req['url'] = $url ;
			$req['lineString'] = $lineString ;
			print_r(json_encode($req));
		}
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
	function curlTest($url, $data, $method)
	{
		$ch = curl_init();
		$url = $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 
		// curl_setopt($ch,CURLOPT_HEADER, TRUE);

		$response = curl_exec($ch);

		// print_r($response);

		return $response;
	} 
 
}