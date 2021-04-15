<?php error_reporting(E_ERROR) ;
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdu_map extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
	}

	 public function current( $police_vehicle_code=''    ){ 
		 
		//$data['lat'] = $lat  ;
		//$data['lon'] = $lon  ;
		//echo urldecode($police_vehicle_code) ;
		$police_vehicle_code =  urldecode($police_vehicle_code)   ;
		$data['police_vehicle_code'] = $police_vehicle_code  ;
		$data['task'] = 'current'  ;
		$this->load->view('navigator' , $data);



	} 
	
	public function navigator( $case_id='' , $police_vehicle_code='' , $user_code = ''  ){ 
		//echo $case_id.'<br>' ;
		//echo $police_vehicle_code.'<br>' ;
		//echo $user_code.'<br>' ;
		//echo "ddd" ;
		//$this->set('el', TRUE, TRUE);
		//$this->load->library('parser');
		//$this->load->view('layout/index' , '') ;
/*		$this->set('case_id' , $case_id) ;
		$this->set('police_vehicle_code' , $police_vehicle_code) ;*/
		$police_vehicle_code =  urldecode($police_vehicle_code)   ;
		$data['case_id'] = $case_id  ;
		$data['police_vehicle_code'] = $police_vehicle_code  ;
		$data['user_code'] = $user_code  ;
		$this->load->view('navigator' , $data);
	}	

	public function tracking( ){ 
		//echo "<pre>" ;
		$time_start = microtime(true);
		$case_status = $this->Config_model->readConfig('case_status') ; 
		//$command = $this->Config_model->readConfig('command') ; 
		//$police_station = $this->Config_model->readConfig('police_station') ;  
		$casetype = $this->Config_model->readConfig('casetype') ; 
		$police_vehicle_status = $this->Config_model->readConfig('police_vehicle_status') ; 
 		file_put_contents("/var/www/html/ccEX",  json_encode($_REQUEST) , FILE_APPEND );
		$req = $_REQUEST ; 
		$this->db->select('case.case_id,casetype_code,priority,phone_number,case.case_status_code,result_code,case_detail,command_code,police_station_code,case_location_address,case_location_detail,case_route,case_lat,case_lon,citizen_code,created_date,started_date,commanded_date,received_date,arrived_date,closed_date,user_create,user_receive,user_close,police_vehicle_code,receive_date,arrive_date,close_date,case_transaction.case_status_code as case_status_code_t,user_code,viewed,case_transaction.id as case_transaction_id,ways');
		$this->db->from('case_transaction');
		$this->db->join('case', 'case.case_id = case_transaction.case_id');

		$this->db->where('case_transaction.case_id =', $req['case_id'] );
		$this->db->where('case_transaction.police_vehicle_code =', $req['police_vehicle_code'] );
		$result = $this->db->get()->result_array();

		$result = $result[0] ;
		$ds['type'] = "case" ;
		$ds['icon'] = "marker.png" ;
		$ds['name'] = $result['case_id'] ;
		$ds['lat'] = $result['case_lat'] ;
		$ds['lon'] = $result['case_lon'] ;
		$ds['address'] = $result['case_location_address'] ;
		$ds['description'] = $result['case_location_detail'] ;

		$ds['status'] = $result['case_status_code'] ;
		$ds['casetype_name'] = $casetype[$result['casetype_code']]['casetype_name'] ;
		$ds['case_status_name'] = $case_status[$result['case_status_code']]['case_status_name'] ;
 		$ds_route['to'] = $ds ;
 
  		
 		$this->db->select('police_vehicle_code,police_vehicle_number,police_vehicle_lat,police_vehicle_lon,police_vehicle_status_code,user_name');
		$this->db->from('police_vehicle');
		$this->db->join('uc_users', 'police_vehicle.device_name = uc_users.device_name');
		$this->db->where('police_vehicle.device_name <>','' ); 
		$result = $this->db->get()->result_array();
		//echo '<pre>'; print_r( $result );
		foreach ($result as $key => $value) {
				//print_r( $value['police_vehicle_status_code']) ;
				$ds_a = [] ;
				$ds_a['type'] = "mdu" ;
				$ds_a['icon'] = "police_car.png" ;
				$ds_a['name'] = $value['police_vehicle_number'] ;
				$ds_a['lat'] = $value['police_vehicle_lat'] ;
				$ds_a['lon'] = $value['police_vehicle_lon'] ;
				$ds_a['address'] = '' ;
				$ds_a['description'] = "" ;
				$ds_a['user'] = $value['user_name'] ;
				$ds_a['status'] = $value['police_vehicle_status_code'] ;
				$ds_a['police_vehicle_status'] = $police_vehicle_status[$value['police_vehicle_status_code']]['police_vehicle_status_name'] ;
				//$ds_xxxx[] = $ds_a ;
				//print_r($ds_a) ;
			if($value['police_vehicle_code']==$req['police_vehicle_code']){
				//echo "---------------";
				//print_r($ds) ;
				$ds_route['from'] = $ds_a ;
			}else{
				$ds_mdu[] = $ds_a ;
			}

		}
  
		$ds_x['routing'] = $ds_route ;
		$ds_x['mdu'] = $ds_mdu ;


		$res_x = json_encode($ds_x) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['user_code']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'tracking', 'route' , $duration  ,  '--', json_encode($req) , $res_x  , 'MDU');
		
		print_r(  $res_x ) ;

 


	}	

	public function currentLocation( $police_vehicle_code='' ){ 
		$police_vehicle_code =  urldecode($police_vehicle_code)   ;
		$this->db->select('police_vehicle_code,police_vehicle_number,police_vehicle_status_code,device_name,police_vehicle_lat,police_vehicle_lon,police_vehicle_bearing,is_login,police_station_code');
		$this->db->from('police_vehicle');
		$this->db->where('police_vehicle_code =', $police_vehicle_code  );
		$result = $this->db->get()->result_array();
		//echo '<pre>';
		$ds = [];
		if(count( $result)>0){
			$ds =  $result[0] ;
		}
		print_r( json_encode($ds ) ) ;
	}

	public function outOfArea(){ 
		$time_start = microtime(true);
		$data_out = $_REQUEST ;
		$dx['url'] = _MDU_NOTI ;
		$dx['method'] = 'POST' ;
		//$data_out['device_name'] = $data_out['device_name'].'_out' ;
		$data_out['police_station_code'] = $data_out['police_station_code'].'_outArea' ;
		$data_out['police_vehicle_code'] = $data_out['police_vehicle_code'].'_outArea' ;
		$data_out['message'] = _MDU_OUT_OF_AREA  ;
		$dx['data'] = $data_out ;

		$result_x = $this->curlService($dx) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['user_code']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'outArea', 'outArea' , $duration  ,  '--', json_encode($dx) , $result_x  , 'MDU');
		print_r($result_x) ;
	}

	function curlService($data){
		$ch = curl_init();
		$url = $data['url'] ;
		$time = 5 ;
	    curl_setopt($ch, CURLOPT_URL, $url );
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $data['method'] );
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data['data']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($ch,CURLOPT_HEADER, true);
		curl_setopt($ch,CURLOPT_TIMEOUT,$time);
	    
	    $response = curl_exec ($ch);
		 
	    curl_close ($ch);
	   
	    return  $response ;
	}

}