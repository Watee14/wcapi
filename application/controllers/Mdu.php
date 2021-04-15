<?php error_reporting(E_ERROR) ;
date_default_timezone_set("Asia/Bangkok");
header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdu extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('CryptoJS');
		$this->load->model('Config_model');
		$this->load->model('Writelogs_model');
	}

	public function index()
	{
		 // $this->db->select('id,case_id,detail,created_date,modified_date,user_create,user_modify');
			// $this->db->from('case_note');
			// $result = $this->db->get()->result_array();
			// print_r(json_encode( $result) ) ;
		//$SVDB02 = $this->load->database('SVDB02', TRUE);
		//$SVDB02->select('*');
		//$result = $SVDB02->get('PB_AGENCYTYPE')->result_array();
		//$result = $SVDB02->get('PB_AGENCYTYPE')->result_array();
		//print_r(json_encode($result));
	}



	public function caseList($case_id = '' )
	{
		$time_start = microtime(true);
		$data_x =  file_get_contents('php://input') ;   //file_put_contents("/var/www/html/case_EX", $data , FILE_APPEND | LOCK_EX);
 		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = json_decode( $data , true ) ;
 		//print_r($data ) ; die();
 		//user_code

 		/*$this->db->select('police_vehicle_code');
		$this->db->from( 'police_vehicle' );
		$this->db->where('device_name =',  $data['device_name']   );
		$vehicle = $this->db->get()->row();
		if( isset($vehicle->police_vehicle_code) ){*/
		if( isset($data['police_vehicle_code']) ){
			//echo _CONFIG.'case_status_code' ;
			$case_status = $this->Config_model->readConfig('case_status') ;
			$command = $this->Config_model->readConfig('command') ;
			$police_station = $this->Config_model->readConfig('police_station') ;
			$casetype = $this->Config_model->readConfig('casetype') ;
			$case_inform = $this->Config_model->readConfig('case_inform') ;
			//print_r($casetype) ; die();

			$this->db->select('case.case_id,casetype_code,priority,phone_number,case.case_status_code,result_code,case_detail,command_code,police_station_code,case_location_address,case_location_detail,case_route,case_lat,case_lon,citizen_code,created_date,started_date,commanded_date,received_date,arrived_date,closed_date,user_create,user_receive,user_close,police_vehicle_code,receive_date,arrive_date,close_date,case_transaction.case_status_code as case_status_code_t,user_code,viewed,case_transaction.id as case_transaction_id,ways');
			$this->db->from('case_transaction');
			$this->db->join('case', 'case.case_id = case_transaction.case_id');

			$this->db->where('case_transaction.police_vehicle_code =', $data['police_vehicle_code'] );
			if($case_id!=''){
				$this->db->where('case_transaction.case_id =', $case_id );
			}
/*			$this->db->where('case_transaction.case_status_code <>',   '007' );
			$this->db->where('case_transaction.case_status_code <>',   '007' );*/
			$id_not_in  = array('007' , '008' , '009' , '013' , '014' , '015' );
			//$this->db->where_not_in('case_transaction.case_status_code', $id_not_in );
			$this->db->where_not_in('case_transaction.case_status_code', $id_not_in );
			$this->db->order_by("started_date", "desc");
			$result = $this->db->get()->result_array();
			$rec = 0;
			$ds = [];
			//print_r($result) ; die();
			foreach ($result as $key => $value) {
				//if($key=='case_status_code'){
					if( $value['case_status_code_t'] == '004' ){ $value['case_status_code_t'] ='005'; }
					$value['case_status_code'] = $value['case_status_code_t'] ;
					$value['case_status_name'] = $case_status[ $value['case_status_code'] ]['case_status_name']  ;
				//}
				//if($key=='command_code'){
					$value['command_name'] = $command[ $value['command_code'] ]['command_name']  ;
				//}
				//if($key=='police_station_code'){
					$value['police_station_name'] = $police_station[ $value['police_station_code'] ]['police_station_name']  ;
				//}

				//if($key=='casetype_code'){
					//echo $key.'->'.$value['case_id'] ;
					$value['casetype_name'] = $casetype[ $value['casetype_code'] ]['casetype_name']  ;
				//}
				//if($key=='ways'){
					//print_r($case_inform) ;
					$value['ways_name'] = $case_inform[ $value['ways'] ]['inform_name']  ;
				//}
				$this->db->select('citizen_code,citizen_name,citizen_middle,citizen_surname,citizen_address,citizen_phone_number,citizen_current_lat,citizen_current_lon,contact_name,contact_surname,contact_phone_number');
				$this->db->from( 'citizen_profile' );
				$this->db->where('citizen_phone_number =',  $value['phone_number']   );
				$citizen_ = $this->db->get()->row();
				//print_r($citizen_) ;
				$value['citizen_profile'] = [] ;
				if(count($citizen_)>0){
					foreach ($citizen_ as $kk => $vv) {
						if($vv==null){
							$dsa[$kk] = "";
						}else{
							$dsa[$kk] = $vv ;
						}

					}
					$value['citizen_profile'] = $dsa ;
				}
				//print_r($value) ;
				$ds[] = $value ;
				$rec++ ;
			}

			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
			$ret['count'] = count($ds) ;
			$ret['data'] = $ds ;

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'Found MDU ID';

		}
		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['user_code']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'caseLists', 'select' , $duration  ,  $ret['status'] , $data_x , $res_x  , 'MDU');

		print_r(  $res_x ) ;

	}

	public function is_timestamp($timestamp)
	{
		return ((is_int($timestamp) OR is_float($timestamp)) ? $timestamp : (string) (int) $timestamp === $timestamp) AND ((int) $timestamp <= PHP_INT_MAX) AND ((int) $timestamp >= ~PHP_INT_MAX);
	}

	public function login($username ='' , $password = '')
	{
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;    //file_put_contents("/var/www/html/loginEX", $data , FILE_APPEND | LOCK_EX);
 		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = json_decode( $data , true ) ;
 		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
 		//print_r($data ) ; die();
		if($data['username']=='' || $data['password']=='' || $data['device_name']==''){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';

			$res_x = json_encode($ret) ;
			$time_end = microtime(true);
			$duration = $time_end - $time_start;
			$this->Writelogs_model->wLogs(  $data['username']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'login', 'select' , $duration  ,  $ret['status'] , $data_x , $res_x , 'MDU');

			print_r(  $res_x ) ;  die();
		}
		//print_r($data ) ; die();

		$pass = $this->CryptoJS->aes_encrypt( $data['password']  );
		$table = 'uc_users';
		$this->db->select('id,user_code,user_name,password,rank_code,department_code,command_code,police_station_code,first_name,last_name,role_code,approve');
		$this->db->from($table);
		$this->db->where('user_name =',  $data['username']);
		$this->db->where('password =',   $pass );
		$user = $this->db->get()->row();

		$ret = [];

		//if($entered_pass != $data["password"])
		if( !isset($user->user_code) )
		{
			$ret['status'] = -1 ;
			$ret['message'] = 'No data';
			//$ret['pass'] = $pass;
			//$ret['pass2'] = $data['password'];


		}else{

			if ($user->user_status==0 && $user->approve==1 ) {

				$this->db->select('police_vehicle_code,police_vehicle_number');
				$this->db->from( 'police_vehicle' );
				$this->db->where('device_name  ',  $data['device_name']   );
				$vehicle = $this->db->get()->row();
				 
				if( isset($vehicle->police_vehicle_code) ){
					$police_vehicle_code_c = $vehicle->police_vehicle_code ;
					$police_vehicle_number_c = $vehicle->police_vehicle_number ;
				}else{
					$police_vehicle_code_c = '' ;
					$police_vehicle_number_c = '';
				}
 
				$ret['status'] = 0 ;
				$ret['message'] = '' ;
				$ret['device_name'] = $data['device_name'] ;
				$ret['device_token'] = $data['device_token'] ;
				$ret['police_vehicle_code'] = $police_vehicle_code_c ;
				$ret['police_vehicle_number'] = $police_vehicle_number_c ;
				$ret['user'] = $user ;

				//$this->db->select('role_matches_code,role_code,permission_code,p_add,p_view,p_edit,p_delete,p_command,permission_name');
				$this->db->select('role_matches_code,role_code,permission.permission_code,p_add,p_view,p_edit,p_delete,p_command,permission_name');
				$this->db->from('roles_permission_matches');
				$this->db->join('permission', 'permission.permission_code = roles_permission_matches.permission_code');
				$this->db->where('role_code =', $user->role_code );
				$per = $this->db->get()->result_array();
				//print_r($query) ;
				//#### Permission
				$ret['permission'] = $per ;

				//#### Update freeze
				$ds = array(
			        //'device_token' => $data['device_token'],
			        'is_login' => 1 ,
			        'is_freeze' => 0 ,
				);
				$this->db->where( 'device_name', $data['device_name']  );
				$query = $this->db->update('police_vehicle', $ds);
				//------------------------
				//########## Clear device name
				$ds = array(
			        'device_token' => '',
			        'device_name' => '' ,
				);
				$this->db->where('device_name', $data['device_name'] );
				$query = $this->db->update('uc_users', $ds);
				//########## Update device name
				$ds = array(
			        'device_token' => $data['device_token'],
			        'device_name' => $data['device_name'],
				);
				$this->db->where('user_name', $data['username'] );
				$query = $this->db->update('uc_users', $ds);
				if($query==1){
					$ret['update_token']['status'] = 0 ;
					$ret['update_token']['message'] = 'Success';

				}else{
					$ret['update_token']['status'] = -1 ;
					$ret['update_token']['message'] = 'False';
				}


			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Account not available';
			}


		}
		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['username']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'login', 'select' , $duration  ,  $ret['status'] , $data_x , $res_x , 'MDU');

		print_r(  $res_x ) ;
	}

	public function logout()
	{
		$time_start = microtime(true);
		//echo date('Y/m/d H:i:s') ;
		//$data = $_REQUEST ;
		//$data = json_decode(file_get_contents('php://input'), true);
		$data_x =  file_get_contents('php://input') ;    file_put_contents("/var/www/html/logoutEX", $data , FILE_APPEND | LOCK_EX);
 		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
 		$data = json_decode( $data , true ) ;
 		 //$data = json_decode( '{ "username": "mdu1", "password": "mdu1" ,"device_name": "edd0a3c0209ac85f"}' , true ) ;
 		//print_r($data ) ; die();
		if( $data['username']=='' || $data['device_name']==''){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';

			$res_x = json_encode($ret) ;
			$time_end = microtime(true);
			$duration = $time_end - $time_start;
			$this->Writelogs_model->wLogs(  $data['username']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'logout', 'select' , $duration  ,  $ret['status'] , $data_x , $res_x , 'MDU');

			print_r(  $res_x ) ;  die();
		}

		$ds = array(
			//'device_token' => $data['device_token'],
	        'device_name' => '' ,
	        'device_token' => '' ,
		);
		$this->db->where( 'user_name', $data['username']  );
		$query = $this->db->update('uc_users', $ds);


		$ds = array(
			//'device_token' => $data['device_token'],
	        'is_login' => 0 ,
	        'is_freeze' => 0 ,
		);
		$this->db->where( 'device_name', $data['device_name']  );
		$query = $this->db->update('police_vehicle', $ds);

		if($query==1){
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'False';
		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['username']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'logout', 'select' , $duration  ,  $ret['status'] , $data_x , $res_x , 'MDU');

		print_r(  $res_x ) ;


	}


	public function updateLocation()
	{
		$time_start = microtime(true);
		//$data = $_REQUEST ;
		$data_x = file_get_contents('php://input') ;  //file_put_contents("/var/www/html/ccEX", $data , FILE_APPEND | LOCK_EX);
		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
		$data = json_decode( $data , true ) ;
		
		$dx['url'] = _SYS_SETTING ;
		$dx['method'] = 'GET';
		$setitng = json_decode( $this->curlService($dx) , true );

		//print_r($setitng) ;
		// print_r($data) ; 
		// die();
		if($data['device_name']=='' || $data['lat']=='' || $data['lon']==''  ){
			$ret['status'] = -1 ;
			$ret['message'] = 'Data invalid';

			$res_x = json_encode($ret) ;
			$time_end = microtime(true);
			$duration = $time_end - $time_start;
			$this->Writelogs_model->wLogs(  $data['device_name']  , $_SERVER['REMOTE_ADDR'] , 'low', 'updateLocation', 'update' , $duration  ,  $ret['status'] , $data_x , $res_x , 'MDU');

			print_r(  $res_x ) ; die();
		}

		$ds = array(
	        'police_vehicle_lat' => $data['lat'],
			'police_vehicle_lon' => $data['lon'],
			'police_vehicle_bearing' => $data['bearing'],
	        'break_duration' =>  $data['break_duration'],
		);

		//print_r($data) ;
		 
		if($setitng['mdu']['freeze']==1){
			
			if( $data['break_duration'] > ($setitng['mdu']['freeze_time']*60*1000) ){
				$time_noti = microtime(true);
				//Noti();
				//$Vname = $data['device_name'] ;
				$data_freeze = $data ;
				$police_vehicle = $this->Config_model->readConfigStd('police_vehicle') ;
				foreach ($police_vehicle as $k_1 => $v_1) {
					if($v_1['device_name']==$data['device_name']){
						$data_freeze['police_vehicle_name'] = $v_1['police_vehicle_number'] ; 
						$data_freeze['police_station_code'] = $v_1['police_station_code'] ; 
						$data_freeze['police_vehicle_code'] = $v_1['police_vehicle_code'].'_freeze' ;
					}
				}
				$dx['url'] = _MDU_NOTI ;
				$dx['method'] = 'POST' ;
				$data_freeze['message'] = $data['device_name'] ;
				$data_freeze['message'] = str_replace('{freeze}', $setitng['mdu']['freeze_time'] , _MDU_NOTI_FREEZE_MSG) ;
				$dx['data'] = $data_freeze ;

				$result_x = $this->curlService($dx) ;
				$result = json_decode($result_x , true) ;
				//print_r($result) ;
				/*if($result['status']=='true'){
					$st = 1 ;
				}else{
					$st = 0 ;
				}*/
 
				$time_end = microtime(true);
				$duration = $time_end - $time_noti;
				$this->Writelogs_model->wLogs(  $data['device_name']  , $_SERVER['REMOTE_ADDR'] , 'low', 'notification', 'Mdu_freeze' , $duration  ,  $result['status'] , json_encode($dx) , $result_x , 'MDU');

				$ds['is_freeze'] = '1' ;
			}else{
				$ds['is_freeze'] = '0' ;
			}
		}else{
			$ds['is_freeze'] = '0' ;
		}
		 
		//die();
		//print_r($ds) ;
		$this->db->set('updated_time_location', 'GETDATE()', FALSE);
		$this->db->where('device_name', $data['device_name'] );
		$query = $this->db->update('police_vehicle', $ds);
		if($query==1){
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'False';
		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['device_name']  , $_SERVER['REMOTE_ADDR'] , 'low', 'updateLocation', 'update' , $duration  ,  $ret['status'] , $data_x , $res_x , 'MDU');

		/// Tracking file
		/*if (!file_exists(_LOGS.'/tracking')) {
		    mkdir( _LOGS.'/tracking' , 0777, true);
		}
		if (!file_exists(_LOGS.'/tracking/'.$data['device_name'] ) ) {
		    mkdir( _LOGS.'/tracking/'.$data['device_name'] , 0777, true);
		}
		file_put_contents( _LOGS."tracking/".$data['device_name'].'/'.date('Y_m_d').'.log'    , date("Y/m/d H:i:s").'|'.json_encode($ds).PHP_EOL  , FILE_APPEND  );
		*/

		/// Tracking DB 
		//print_r($data);
		$time_start_t = microtime(true);
		$SVDB02 = $this->load->database('SVDB02', TRUE);
		$SVDB02->select('track_data');
		$SVDB02->where('track_date =', date('Y/m/d')."" );
		$SVDB02->where('track_hour =', date('H')."" );
		$SVDB02->where('track_minute =', date('i')."" );
		$SVDB02->where('owner =', $data['device_name']);
		$result = $SVDB02->get('mdu_tracking')->result_array();
		//print_r($result) ;
		if(isset($result[0])){
			$dx = json_decode( $result[0]['track_data'] , true ) ;
			 //print_r($result[0]) ;
			//print_r($dx) ;
		}
		
		$dx[time().""] = json_encode($ds) ;
		//print_r( ($dx) ) ;
		//$this->db->where('device_name', $data['device_name'] );
		$tr['track_date'] = date('Y/m/d');
		$tr['track_hour'] = date('H');
		$tr['track_minute'] = date('i');
		$tr['track_datetime'] = time()."";
		$tr['track_data'] = json_encode($dx);

		$tr['owner'] = $data['device_name'] ;
		
		if(isset($result[0])){
			$SVDB02->where('track_date =', date('Y/m/d')."" );
			$SVDB02->where('track_hour =', date('H')."" );
			$SVDB02->where('track_minute =', date('i')."" );
			$SVDB02->where('owner', $data['device_name']);
			$query = $SVDB02->update('mdu_tracking', $tr);
		}else{
			$query = $SVDB02->insert('mdu_tracking', $tr);
		}
		$time_end = microtime(true);
		$duration = $time_end - $time_start_t;
		$this->Writelogs_model->wLogs(  $data['device_name']  , $_SERVER['REMOTE_ADDR'] , 'low', 'updateLocation', 'tracking' , $duration  ,  $ret['status'] , json_encode($dx) , $query , 'MDU');

		print_r(  $res_x ) ;
	}

	public function readCase(){
		$time_start = microtime(true);

		$data_x = file_get_contents('php://input') ;
		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
		$data = json_decode( $data , true ) ;
		 //print_r($data); die();
		if(isset($data['case_id'])){
			$ds = array(
	        'viewed' => $data['viewed'] ,
			);

			$this->db->where('case_id', $data['case_id'] );
			$this->db->where('police_vehicle_code', $data['police_vehicle_code'] );
			$query = $this->db->update('case_transaction', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'False1';
			}
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'False2';
		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['user_code']  , $_SERVER['REMOTE_ADDR'] , 'low', 'readCase', 'update' , $duration  ,  $ret['status'] , $data_x , $res_x , 'MDU');

		print_r(  $res_x ) ;

	}

	public function updateCase()
	{
		$time_start = microtime(true);
		/*[case_status_code] => 004
        [case_status_name] => "สายตรวจตอบรับเหตุ"

        [case_status_code] => 005
        [case_status_name] => "กำลังเดินทาง"

		[case_status_code] => 006
        [case_status_name] => "ถึงที่เกิดเหตุ"

        [case_status_code] => 007
        [case_status_name] => "ปิดเหตุ"*/

        /*001	พร้อมปฏิบัติการ
		002	ตอบรับ
		003	กำลังเดินทาง
		004	ถึงที่เกิดเหตุ
		005	กำลังปฏิบัติการ
		006	ปิดเหตุ*/

		$data_x = file_get_contents('php://input') ;   //file_put_contents( '/var/www/html/be' , $data ) ;
 		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );    //file_put_contents( '/var/www/html/af' , $data ) ;
 		$data = json_decode( $data , true ) ;
 		$police_vehicle = $this->Config_model->readConfigStd('police_vehicle') ;
		foreach ($police_vehicle as $k_1 => $v_1) {
			if($v_1['device_name']==$data['device_name']){
				$data['police_vehicle_name'] = $v_1['police_vehicle_number'] ; 
				//$data_freeze['police_station_code'] = $v_1['police_station_code'] ; 
			}
		}
		//print_r($data) ; die();
 		$val = explode(',', 'user_name,user_code,case_id,device_name,case_status_code,case_transaction_id,police_vehicle_code' );
 		//$data['case_transaction_id'] = '' ;
 		foreach ($val as $key => $value) {
 			if( $data[ $value ] =='' ){
 				$ret['status'] = -1 ;
				$ret['message'] = 'Data invalid : '.$value;

				$res_x = json_encode($return_ds) ;
				$time_end = microtime(true);
				$duration = $time_end - $time_start;
				$this->Writelogs_model->wLogs(  $data['user_name']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'updateCase', 'update' , $duration  , '--' , $data_x , $res_x , 'MDU');

				print_r(  $res_x ) ; die();
 			}
 		}

 #################  CURRENT CASE #######
 		$this->db->select('*');
		$this->db->from('case');
		$this->db->where('case_id =', $data['case_id']);
		$query = $this->db->get()->result_array();
		$caseJob =  $query[0] ;

####################################################################
#######################  RECEIVE CASE  #############################
####################################################################

		if( $data['case_status_code']=='004' ){
			$status_case = 0 ;
			$slas = $this->Config_model->readConfig('casetype') ;
			$sla = $slas[ $caseJob['casetype_code'] ]['casetype_receive_sla']  ;
			//print_r($sla) ; die();
#################  CASE #####
			//array('007' , '008' , '009' , '013' , '014' , '015' );
			if($caseJob['case_status_code']=='003' || $caseJob['case_status_code']=='010'){
				if($caseJob['received_date']!=''){
					$ds = array(
				        //'received_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'case_status_code' => '005',
				        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'user_modify' => $data['user_name'] ,
				        //'user_receive' => $data['user_name'] ,
					);
				}else{
					$ds = array(
				        'received_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'case_status_code' => '005',
				        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'user_modify' => $data['user_name'] ,
				        'user_receive' => $data['user_name'] ,
					);
				}
				

				$this->db->where('case_id', $data['case_id'] );
				$query = $this->db->update('case', $ds);
				if($query==1){
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';

				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
					$status_case = -1 ;
				}
				$return_ds['case'] = $ret ;
			}else{
				$ds = array(
			        //'received_date' => date('Y-m-d H:i:s' ,  time() ) ,
			        //'case_status_code' => '005',
			        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
			        'user_modify' => $data['user_name'] ,
			        //'user_receive' => $data['user_name'] ,
				);
				$this->db->where('case_id', $data['case_id'] );
				$query = $this->db->update('case', $ds);
			}
			
#################  CASE TRANSACTION #####
			$duration = time() - strtotime( $caseJob['created_date'] ) ;
			if($duration<=$sla){
				$user_sla = 0 ;
			}else{
				$user_sla = 1 ;
			}
			$ds = array(
			    'receive_date' =>  date('Y-m-d H:i:s' ,  time() ) ,
			    'duration' => $duration ,
			    'user_sla' => $user_sla ,
			    'case_status_code' => '005',
			    'user_closed_job' => $data['user_name'] ,
			);

			$this->db->where('id', $data['case_transaction_id'] );
			$query = $this->db->update('case_transaction', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['case_transaction'] = $ret ;


#################  VEHICLE #######

			$ds = array(
		        'police_vehicle_status_code' => '005' ,
			);
			$this->db->where('device_name', $data['device_name'] );
			$query = $this->db->update('police_vehicle', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['police_vehicle'] = $ret ;
			

#################  Log Note #######
			$ds_note = array(
		         'case_id' => $data['case_id'] ,
			     'created_date' =>date('m/d/Y H:i:s' ,  time() )  ,
			     'modified_date' => date('m/d/Y H:i:s' ,  time() )  ,
			     'detail' =>    $data['police_vehicle_name'].' ตอบรับ' ,
			     'user_create' => $data['user_name'],
			     'user_modify' => $data['user_name'],
			);
			//print_r($ds_note) ;
			$query = $this->db->insert('case_note', $ds_note);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['note'] = $ret ;
			$return_ds['task'] = 'Received' ;
			$return_ds['status'] = $status_case ;
		}

####################################################################
#######################  ARRIVE CASE  #############################
####################################################################

		if( $data['case_status_code']=='006' ){
			$status_case = 0;
			$slas = $this->Config_model->readConfig('casetype') ;
			$sla = $slas[ $caseJob['casetype_code'] ]['casetype_arrive_sla']  ;
			//print_r($sla) ; die();
#################  CASE #####
			$duration = time() - strtotime( $caseJob['created_date'] ) ;
			if($duration<=$sla){
				$user_sla = 0 ;
			}else{
				$user_sla = 1 ;
			}
			if($caseJob['case_status_code']=='004' || $caseJob['case_status_code']=='005' || $caseJob['case_status_code']=='011' ){
				if($caseJob['arrived_date']!=''){
					$ds = array(
				        //'arrived_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'case_status_code' => $data['case_status_code'] ,
				        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'user_modify' => $data['user_name'] ,
				        //'duration' => $duration ,
					);
				}else{
					$ds = array(
				        'arrived_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'case_status_code' => $data['case_status_code'] ,
				        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'user_modify' => $data['user_name'] ,
				        'duration' => $duration ,
				        'user_arrive' => $data['user_name'] ,
					);
				}
				

				$this->db->where('case_id', $data['case_id'] );
				$query = $this->db->update('case', $ds);
				if($query==1){
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';

				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
					$status_case = -1 ;
				}
				$return_ds['case'] = $ret ;
			}else{
				$ds = array(
			        //'received_date' => date('Y-m-d H:i:s' ,  time() ) ,
			        //'case_status_code' => '005',
			        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
			        'user_modify' => $data['user_name'] ,
			        //'user_receive' => $data['user_name'] ,
				);
				$this->db->where('case_id', $data['case_id'] );
				$query = $this->db->update('case', $ds);
			}

#################  CASE TRANSACTION #####

			$ds = array(
			    'arrive_date' =>  date('Y-m-d H:i:s' ,  time() ) ,
			    'case_status_code' => $data['case_status_code'] ,
			    'user_closed_job' => $data['user_name'] ,
			);

			$this->db->where('id', $data['case_transaction_id'] );
			$query = $this->db->update('case_transaction', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['case_transaction'] = $ret ;

#################  VEHICLE #######

			$ds = array(
		        'police_vehicle_status_code' => '005' ,
			);
			$this->db->where('device_name', $data['device_name'] );
			$query = $this->db->update('police_vehicle', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['police_vehicle'] = $ret ; 

#################  Log Note #######
			$ds_note = array(
		         'case_id' => $data['case_id'] ,
			     'created_date' =>date('m/d/Y H:i:s' ,  time() )  ,
			     'modified_date' => date('m/d/Y H:i:s' ,  time() )  ,
			     'detail' =>    $data['police_vehicle_name'].' ถึงที่เกิดเหตุ' ,
			     'user_create' => $data['user_name'],
			     'user_modify' => $data['user_name'],
			);
			//print_r($ds_note) ;
			$query = $this->db->insert('case_note', $ds_note);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['note'] = $ret ;
			$return_ds['task'] = 'Arrived' ;
			$return_ds['status'] = $status_case ;
		}

####################################################################
#######################  CLOSE CASE  #############################
####################################################################

		if( $data['case_status_code']=='007' ){
			//print_r($data) ;
#################  CASE #####
			$status_case = 0;
			if($data['result_code']!=''){
				if( !isset($data['result_detail']) ){ $data['result_detail']="" ; }

				if($caseJob['case_status_code']=='006' || $caseJob['case_status_code']=='012'  ){
					if( $caseJob['result_code']!=''  && $caseJob['result_code']!=null ){
						$ds = array(
					        //'closed_date' => date('Y-d-m H:i:s' ,  time() ) ,
					        //'case_status_code' => $data['case_status_code'] ,
					        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
					        'user_modify' => $data['user_name'] ,
					        //'user_close' => $data['user_name'] ,
					        //'result_code' => $data['result_code'] ,
					        'result_detail' => $caseJob['result_detail'].PHP_EOL.$data['result_detail'] ,
						);
					}else{
						$ds = array(
					        //'closed_date' => date('Y-d-m H:i:s' ,  time() ) ,
					        //'case_status_code' => $data['case_status_code'] ,
					        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
					        'user_modify' => $data['user_name'] ,
					        //'user_close' => $data['user_name'] ,
					        'result_code' => $data['result_code'] ,
					        'result_detail' => $data['result_detail'] ,
						);
					}
					

					$this->db->where('case_id', $data['case_id'] );
					$query = $this->db->update('case', $ds); 
					if($query==1){
						$ret['status'] = 0 ;
						$ret['message'] = 'Success';

					}else{
						$ret['status'] = -1 ;
						$ret['message'] = 'Fail';
						$status_case = -1 ;
					}
					$return_ds['case'] = $ret ;
				}else{
					$ds = array(
				        //'received_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        //'case_status_code' => '005',
				        'modified_date' => date('Y-m-d H:i:s' ,  time() ) ,
				        'user_modify' => $data['user_name'] ,
				        //'user_receive' => $data['user_name'] ,
					);
					$this->db->where('case_id', $data['case_id'] );
					$query = $this->db->update('case', $ds);
				}

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail : result_code' ;
			}
			$return_ds['case'] = $ret ;

#################  CASE TRANSACTION #####

			$ds = array(
			    'close_date' =>  date('Y-m-d H:i:s' ,  time() ) ,
			    'case_status_code' => $data['case_status_code'] ,
			    'user_closed_job' => $data['user_name'] ,
			    'result_code_t' => $data['result_code'] ,
				'result_detail_t' => $data['result_detail'] ,
			);

			$this->db->where('id', $data['case_transaction_id'] );
			$query = $this->db->update('case_transaction', $ds);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['case_transaction'] = $ret ;


#################  VEHICLE #######

			$this->db->select('count(case_transaction.id) as total');
			$this->db->from('case_transaction');

			$this->db->where('police_vehicle_code =', $data['police_vehicle_code'] );

			$id_not_in  = array('007' , '008' , '013' , '014' , '015' );
			$this->db->where_not_in('case_status_code', $id_not_in );

			$result = $this->db->get()->result_array();
			$total = $result[0]['total'] ;
			//print_r($total); die();
			if($total==0){
				$ds = array(
			        'police_vehicle_status_code' => '001' ,
				);
				$this->db->where('device_name', $data['device_name'] );
				$query = $this->db->update('police_vehicle', $ds);
				if($query==1){
					$ret['status'] = 0 ;
					$ret['message'] = 'Success';

				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'Fail';
					$status_case = -1 ;
				}

			}else{
				$ret['status'] = 0 ;
				$ret['message'] = 'Cannot change stage MDU (Have another case) ';
			}

			$return_ds['police_vehicle'] = $ret ; 

#################  Log Note #######
			$ds_note = array(
		         'case_id' => $data['case_id'] ,
			     'created_date' =>date('m/d/Y H:i:s' ,  time() )  ,
			     'modified_date' => date('m/d/Y H:i:s' ,  time() )  ,
			     'detail' =>    $data['police_vehicle_name'].' เสร็จสิ้นการปฏิบัติการ' ,
			     'user_create' => $data['user_name'],
			     'user_modify' => $data['user_name'],
			);
			//print_r($ds_note) ;
			$query = $this->db->insert('case_note', $ds_note);
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$status_case = -1 ;
			}
			$return_ds['note'] = $ret ;
			$return_ds['task'] = 'Closed' ;
			$return_ds['status'] = $status_case ;

		}

		$res_x = json_encode($return_ds) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  $data['user_name']  , $_SERVER['REMOTE_ADDR'] , 'medium', 'updateCase', 'update' , $duration  , $status_case , $data_x , $res_x , 'MDU');

		print_r(  $res_x ) ;

	}


	public function addNote(){
		$time_start = microtime(true);

		$data_x = file_get_contents('php://input') ;
		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
		$data = json_decode( $data_x , true ) ;
		//print_r($data); die();
		$val = explode(',', 'user_name,user_code,case_id,detail' );
 		//$data['case_transaction_id'] = '' ;
 		foreach ($val as $key => $value) {
 			if( $data[ $value ] =='' ){
 				$ret['status'] = -1 ;
				$ret['message'] = 'Data invalid : '.$value;

				$res_x = json_encode($ret) ;
				$time_end = microtime(true);
				$duration = $time_end - $time_start ;
				$this->Writelogs_model->wLogs(  $data['user_name']  , $_SERVER['REMOTE_ADDR'] , 'low', 'addNote', 'insert' , $duration  , $ret['status'] , $data_x , $res_x , 'MDU');

				print_r(  $res_x ) ;

				die();
 			}
 		}

		$ds_note = array(
	         'case_id' => $data['case_id'] ,
		     'created_date' =>date('Y-d-m H:i:s' ,  time() )  ,
		     'modified_date' => date('Y-d-m H:i:s' ,  time() )  ,
		     'detail' => $data['detail'] ,
		     'user_create' => $data['user_name'],
		     'user_modify' => $data['user_name'],
		);
		$query = $this->db->insert('case_note', $ds_note);
		if($query==1){
			$ret['status'] = 0 ;
			$ret['message'] = 'Success';

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'Fail';
		}
		//$return_ds['case_note'] = $ret  ;


		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start ;
		$this->Writelogs_model->wLogs(  $data['user_name']  , $_SERVER['REMOTE_ADDR'] , 'low', 'addNote', 'insert' , $duration  , $ret['status'] , $data_x , $res_x , 'MDU');

		print_r(  $res_x ) ;


	}

	public function listNote( $case_id = ''){
		$time_start = microtime(true);

		if( isset($case_id) ){
			$this->db->select('id,case_id,detail,created_date,modified_date,user_create,user_modify');
			$this->db->from('case_note');
			$this->db->where('case_id =', $case_id );
			$result = $this->db->get()->result_array();
			$rec = 0;

			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
			$ret['count'] = count($result) ;
			$ret['data'] = $result ;

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'No data';

		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start ;
		$this->Writelogs_model->wLogs(  $case_id  , $_SERVER['REMOTE_ADDR'] , 'low', 'listNote', 'select' , $duration  , $ret['status'] , $case_id   , $res_x , 'MDU');

		print_r(  $res_x ) ;

	}

	public function listResult( $casetype_code = ''){
		$time_start = microtime(true);

		if( $casetype_code!='' ){
			 $case_closejob = $this->Config_model->readConfig('case_closejob') ;
			 $case_result = $this->Config_model->readConfig('case_result') ;


			 $xx['id'] = 'x' ;
			 $xx['result_code'] = 'x' ;
			 $xx['case_result_name'] = 'กรุณาเลือกผลการปฏิบัติการ' ;
			 $ds_[] =  $xx ;


			 foreach ($case_closejob as $key => $value) {
			  		if($value['casetype_code'] == $casetype_code){
			  			$res[] = $value['result_code'] ;
			  		}
			  }

			  foreach ($res as $key => $value) {
			  		$ds_[] =  $case_result[ $value ] ;
			  }


			$ret['status'] = 0 ;
			$ret['message'] = 'Success';
			$ret['data'] = $ds_ ;
			//print_r(json_encode($ds_) ) ;
		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'No data';

		}

		$res_x = json_encode($ret) ;
		$time_end = microtime(true);
		$duration = $time_end - $time_start ;
		$this->Writelogs_model->wLogs(  $casetype_code , $_SERVER['REMOTE_ADDR'] , 'low', 'listResult', 'insert' , $duration  , $ret['status'] , '' , $res_x , 'MDU');

		print_r(  $res_x ) ;

	}

	function heartbeat(){
		$time_start = microtime(true);
		$data_x = file_get_contents('php://input') ;
		$data = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt , $data_x );
		$data = json_decode( $data , true ) ;
		//print_r($data) ; die();
		//$res_x = json_encode($ret) ;
		

		if($data['device_name']!=''){
			
			//## Update hearthbeat 
			$this->db->set('is_healthchk_status', 1 );
			$this->db->set('is_healthchk_date', 'GETDATE()', FALSE);
			$this->db->where('device_name', $data['device_name'] );
			 
			$query = $this->db->update('police_vehicle' );
			if($query==1){
				$ret['status'] = 0 ;
				$ret['message'] = 'Success';
				$ret['device_name'] = $data['device_name'] ;

			}else{
				$ret['status'] = -1 ;
				$ret['message'] = 'Fail';
				$ret['device_name'] = $data['device_name'] ;
			}

		}else{
			$ret['status'] = -1 ;
			$ret['message'] = 'No data';

		}

		$time_end = microtime(true);
		$duration = $time_end - $time_start ;
		$this->Writelogs_model->wLogs(  $casetype_code , $_SERVER['REMOTE_ADDR'] , 'low', 'heartbeat', 'check' , $duration  , $ret['status'] , $data_x , json_encode($ret) , 'MDU');
		print_r(  json_encode( $ret ) ) ;
	}

	function chkLostConn(){
		$time_start = microtime(true);
		//### Check Last connection
		$this->db->select('police_vehicle_code,police_vehicle_number,device_name,is_healthchk_date,is_healthchk_status');
		$this->db->from( 'police_vehicle' );
		$this->db->where('device_name  <>', ''   );
		$result = $this->db->get()->result_array();
		//$ret['data'] = $result ; 
		//print_r($result) ;
		$police_vehicle_code = [];
		foreach ($result as $key => $value) {
			//$value['c'] = strtotime($value['is_healthchk_date']) ; ;
			$value['diff'] = time() - strtotime($value['is_healthchk_date']) ;

			if(_MDU_LOST_CONN<=$value['diff'] ){
				$this->db->set('is_healthchk_status', 2 );
				//$this->db->set('is_healthchk_date', 'GETDATE()', FALSE);
				$this->db->where('device_name', $value['device_name'] );
				$query = $this->db->update('police_vehicle');
				$value['is_healthchk_status'] = 2;
				$police_vehicle_code[] = $value['police_vehicle_code'] ; 
			}

			if(_MDU_DISCONN<=$value['diff'] ){
				$this->db->set('is_healthchk_status', 3 );
				//$this->db->set('is_healthchk_date', 'GETDATE()', FALSE);
				$this->db->where('device_name', $value['device_name'] );
				$query = $this->db->update('police_vehicle');
				$value['is_healthchk_status'] = 3;
				$police_vehicle_code[] = $value['police_vehicle_code'] ; 
			}
			
			if(_MDU_DISCONN>$value['diff'] ){
				$this->db->set('is_healthchk_status', 0 );
				//$this->db->set('is_healthchk_date', 'GETDATE()', FALSE);
				$this->db->where('device_name', $value['device_name'] );
				$query = $this->db->update('police_vehicle');
				$value['is_healthchk_status'] = 0;
				$police_vehicle_code[] = $value['police_vehicle_code'] ; 
			}

			$ret['data'][] = $value ;
			//print_r($value) ;

			# code...
		}
	 

		$time_end = microtime(true);
		$duration = $time_end - $time_start ;
		$this->Writelogs_model->wLogs(  $casetype_code , $_SERVER['REMOTE_ADDR'] , 'low', 'chkLostConn', 'check' , $duration  , $ret['status'] , '' , implode(',',$police_vehicle_code) , 'MDU');
		print_r(  json_encode( $ret ) ) ;
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