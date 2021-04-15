<?php session_start() ;
defined('BASEPATH') OR exit('No direct script access allowed');

class Patrol extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Config_model');
	}

	/*public function index()
	{

	}
*/
	public function plan($fn = NULL)
	{
		if ($fn == 'create')
		{
			$this->twiggy->template('patrol/plan/create')->display();
		}
		else if ($fn == 'read')
		{
			$this->twiggy->template('patrol/plan/read')->display();
		}
		else if ($fn == 'update')
		{
			$this->twiggy->template('patrol/plan/update')->display();
		}
		else if ($fn == 'delete')
		{
			$this->twiggy->template('patrol/plan/delete')->display();
		}
		else
		{
			$this->twiggy->template('patrol/plan/list')->display();
		}
	}
	public function tracking($fn = NULL)
	{
		$this->twiggy->template('patrol/tracking/tracking')->display();
	}
	public function vehicle($fn = NULL, $me = NULL)
	{
		if ($fn == 'create')
		{
			$this->twiggy->set('_CONFIG_READ', _CONFIG_READ, TRUE);
			$this->twiggy->template('patrol/vehicle/create')->display();
		}
		else if ($fn == 'read')
		{
			$this->twiggy->template('patrol/vehicle/read')->display();
		}
		else if ($fn == 'update')
		{
			$this->db->select('id,police_vehicle_code,police_vehicle_number,police_vehicle_type,department_code,command_code,police_station_code,police_vehicle_status_code,device_name,police_vehicle_lat,police_vehicle_lon,schedule_code,duty_code');
        	$this->db->from('police_vehicle');
        	$this->db->where('police_vehicle.id =', $me );
        	$result = $this->db->get()->result_array();
        	$result = $result[0] ;
        	 //echo '<pre>'; print_r($result) ; die();
        	$this->twiggy->set('data' , $result , TRUE);
			$this->twiggy->template('patrol/vehicle/update')->display();
		}
		else if ($fn == 'delete')
		{
			$this->twiggy->template('patrol/vehicle/delete')->display();
		}
		else
		{
			$this->twiggy->set('me', $me, TRUE);
			$this->twiggy->set('typeFilter' , $me , TRUE);
			$this->twiggy->template('patrol/vehicle/list')->display();
		}
	}


	function vehicleAction($action='', $F=''){
		if($action == 'list'){
			$userCode = $_SESSION['user']->user_code ;
			$police_station = $this->Config_model->readConfig('police_station') ;
			$command = $this->Config_model->readConfig('command') ;
			$police_vehicle_status = $this->Config_model->readConfig('police_vehicle_status') ;
			$user = $this->Config_model->userPermission( $userCode , '' , 'All' ) ;
			//echo '<pre>'; print_r($user) ; die();

			/*$this->db->select('id,police_vehicle_code,police_vehicle_number,police_vehicle_type,department_code,command_code,police_station_code,police_vehicle_status_code,device_name,police_vehicle_lat,police_vehicle_lon');
        	$this->db->from('police_vehicle');
        	if($F==1){
        		$id_not_in  = array('001' , '000'   );
				$this->db->where_not_in('police_vehicle_status_code', $id_not_in );
        	}else if($F==2){
        		$this->db->where('police_vehicle_status_code =', '001' );
        	}
        	if(count($user['department'])>0){
        		$this->db->where_in('department_code', $user['department'] );
        	}
        	if(count($user['command'])>0){
        		$this->db->where_in('command_code', $user['command'] );
        	}
        	if(count($user['police_station'])>0){
        		$this->db->where_in('police_station_code', $user['police_station'] );
        	}
        	$result = $this->db->get()->result_array();*/
        	//print_r($result) ; die();
        	if($F==1){
				$statud_d = " and police_vehicle_status_code not in ('001' , '000') " ;
        	}else if($F==2){
        		$statud_d = " and police_vehicle_status_code ='001' " ;
        	}

        	$sql = "select
        	tt1.id,tt1.police_vehicle_code,tt1.police_vehicle_number,tt1.police_vehicle_type,tt1.department_code,tt1.command_code,tt1.police_station_code,tt1.police_vehicle_status_code,tt1.device_name,tt1.police_vehicle_lat,tt1.police_vehicle_lon,

        	tt2.case_id,tt2.receive_date,tt2.case_status_code,DATEDIFF(SECOND,tt2.receive_date,getdate()) as duration
				from(
				select *
				,isnull((select top 1 [id] from case_transaction where [case_status_code]<>'007'
				 and police_vehicle_code=t1.police_vehicle_code order by [receive_date]),'') as tran_id
				from police_vehicle t1
				)tt1 left outer join case_transaction tt2 on tt1.tran_id=tt2.id
				where   [department_code] in (".implode(',', $user['department']).")
				and [command_code] in (".implode(',', $user['command']).")
				and [police_station_code] in (".implode(',', $user['police_station']).")
				".$statud_d."
				" ;




			$result = $this->db->query( $sql );

        	$dsx = [] ;
        	$rec = 0;
	        //foreach ($result as $key => $value) {
	        foreach ($result->result_array() as $value){
	            //print_r($value ) ;
	            $ds = [] ;
	            $ds['record'] = $value['id'] ;
	            $ds['police_vehicle_number'] = $value['police_vehicle_number'] ;
	            $ds['police_station_name'] = $police_station[$value['police_station_code']]['police_station_name'] ;
	            $ds['command_name'] = $command[$value['command_code']]['command_name'] ;
	            $ds['police_vehicle_status_name'] =  $police_vehicle_status[$value['police_vehicle_status_code']]['police_vehicle_status_name'] ;

	            if($value['receive_date']!=null && $value['receive_date']!=''){
	            	$ds['dateTime'] = date('d/m/' , strtotime($value['receive_date'])  ).(date('Y', strtotime($value['receive_date'])  ) +543 )." ".date('H:i:s') ;
	            }else{
	            	$ds['dateTime'] = '' ;
	            }
	            if($value['duration']!=null && $value['duration']!=''){
	            	$ds['duration'] = gmdate( "H:i:s", $value['duration'] ) ;
	            }else{
	            	$ds['duration'] = '' ;
	            }
	            $ds['case_id'] = $value['case_id']  ;
	            $ds['case_status_code'] = $value['case_status_code']  ;
	            $ds['police_vehicle_lat'] = $value['police_vehicle_lat']  ;
	            $ds['police_vehicle_lon'] = $value['police_vehicle_lon']  ;
	            /*if($F==){
	            	$ds['dateTime'] = '' ;
	            	$ds['duration'] = '' ;
	            	$ds['case_id']  = '' ;
	            }*/



	            //$ds['xx'] = '--' ;
	            $z = [] ;
	            foreach ($ds as $kk => $yy) {
	            	$z[] = $yy ;
	            }
	            $dsx[] = $z ;
	            $rec++;
	        }
			$ds_data['draw'] =
			$ds_data['recordsTotal'] = count($dsx);
			$ds_data['recordsFiltered']  = count($dsx);
		 	$ds_data['data'] = $dsx ;
		 	print_r( json_encode( $ds_data ) ) ;
		}

		if($action == 'insert'){
			//echo "<pre>" ;
			//print_r($_SESSION['user']->user_name) ; die();
			$req = $_REQUEST ;
			$this->db->select(' count(police_vehicle_code) as total');
        	$this->db->from('police_vehicle');
        	$this->db->where('police_vehicle.police_vehicle_code =', $req['police_vehicle_code'] );
        	$result = $this->db->get()->result_array();
        	$result = $result[0] ;
        	if($result['total']==0){
        		//echo "Can" ;
        		$req['priority'] = 1 ;
        		$req['created_date'] = date('Y-m-d H:i:s' ,  time() )  ;
        		$req['modified_date'] =  date('Y-m-d H:i:s' ,  time() ) ;
        		$req['owner'] =  $_SESSION['user']->user_name  ;
        		//print_r($req) ;
				$query = $this->db->insert('police_vehicle', $req);
				//print_r(  $this->db->error() ) ;
				if($query==1){
					$ret['status'] = 0 ;
					$ret['message'] = 'บันทึกรถสายตรวจ '.$req['police_vehicle_number']." ทะเบียน ".$req['police_vehicle_code']." สำเร็จ";

				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'บันทึกรถสายตรวจ '.$req['police_vehicle_number']." ทะเบียน ".$req['police_vehicle_code']." ไม่สำเร็จ";
				}
        	}else{
        		$ret['status'] = -1 ;
				$ret['message'] = "บันทึกรถสายตรวจ ทะเบียน ".$req['police_vehicle_code']." มีใช้ในระบบแล้ว";
        	}
        	print_r(json_encode( $ret ) ) ;


		}

		if($action == 'edit'){
			//echo "<pre>" ;
			 //print_r( $_REQUEST ) ; die();
			$req = $_REQUEST ;
			$this->db->select(' id');
        	$this->db->from('police_vehicle');
        	$this->db->where('police_vehicle.police_vehicle_code =', $req['police_vehicle_code'] );
        	$result = $this->db->get()->result_array();
        	$result = $result[0] ;
         	 //print_r(($result)); die();
        	if(count($result)==0 || $result['id'] == $req['id'] ){
        		//echo "Can" ;
        		//$req['priority'] = 1 ;
        		//$req['created_date'] = date('Y-m-d H:i:s' ,  time() )  ;
        		$req['modified_date'] =  date('Y-m-d H:i:s' ,  time() ) ;
        		$req['owner'] =  $_SESSION['user']->user_name  ;
        		$id = $req['id'] ;
        		unset($req['id']);
        		$this->db->where('id', $id );
				$query = $this->db->update('police_vehicle', $req);
				//echo $this->db->set( $req)->where('id', $req['id'] )->get_compiled_update('police_vehicle');
				 //print_r(  $this->db->error() ) ;
				if($query==1){
					$ret['status'] = 0 ;
					$ret['message'] = 'บันทึกรถสายตรวจ '.$req['police_vehicle_number']." ทะเบียน ".$req['police_vehicle_code']." สำเร็จ";

				}else{
					$ret['status'] = -1 ;
					$ret['message'] = 'บันทึกรถสายตรวจ '.$req['police_vehicle_number']." ทะเบียน ".$req['police_vehicle_code']." ไม่สำเร็จ";
				}
        	}else{

        		$ret['status'] = -1 ;
				$ret['message'] = "บันทึกรถสายตรวจ ทะเบียน ".$req['police_vehicle_code']." มีใช้ในระบบแล้ว";
        	}
        	print_r(json_encode( $ret ) ) ;


		}

	}


	function department(){
		//echo "<pre>" ;
		$userCode = $_SESSION['user']->user_code ;
		// print_r($_SESSION  ) ; die();
		$user = $this->Config_model->userPermission( $userCode , '' , 'department' ) ;
		//print_r( $inD ) ; die();
		$department = $this->Config_model->readConfig('department') ;
		foreach ($department as $kk => $vv) {

			if (in_array( $vv['department_code'] , $user['department']))  {
			  	//echo "Match found";
			  	$ds_x[] = $vv ;
			}

		}
		print_r(json_encode( $ds_x ) ) ;
	}


	function command(){
		$req = $_REQUEST ;

		$userCode = $_SESSION['user']->user_code ;
		// print_r($_SESSION  ) ; die();
		$user = $this->Config_model->userPermission( $userCode , $req['department_code']  , 'command' ) ;
		/*print_r($user) ; die();*/
		$command = $this->Config_model->readConfig('command') ;
		//print_r($command) ; die();
		foreach ($command as $key => $value) {
			if($req['department_code']!=''){
				//$dsx[] = $value ;
				if (in_array( $value['command_code'] , $user['command'] ))  {
				  	$ds_x[] = $value ;
				}
			}
			if($req['department_code']==''){
				$ds_x[] = $value ;
			}
		}
		print_r(json_encode( $ds_x ) ) ;
	}

	function police_station(){
		$req = $_REQUEST ;
		//echo '<pre>';
		$userCode = $_SESSION['user']->user_code ;
		// print_r($_SESSION  ) ; die();
		$user = $this->Config_model->userPermission( $userCode , $req['command_code']  , 'police_station' ) ;
		//print_r($user) ; die();

		$police_station = $this->Config_model->readConfig('police_station') ;
		//print_r($police_station); die();
		foreach ($police_station as $key => $value) {
			if($value['command_code']==$req['command_code']){
				$dsx[] = $value ;
				if (in_array( $value['police_station_code'] , $user['police_station'] ))  {
				  	//echo "Match found";
				  	$ds_x[] = $value ;
				}
			}
			if($req['command_code']==''){
				$ds_x[] = $value ;
			}
		}
		print_r(json_encode( $ds_x ) ) ;
	}

	function schedule(){
		$schedule = $this->Config_model->readConfig('schedule') ;
		print_r(json_encode( $schedule ) ) ;
	}

	function duty(){
		$schedule = $this->Config_model->readConfig('duty') ;
		print_r(json_encode( $schedule ) ) ;
	}

	function monPatrol(){
		//echo '<pre>';
		//$plc = $this->Config_model->readConfigStd('police_vehicle') ;
		//print_r($plc) ;
		if (isset($_SESSION['user']->user_code))
		{
			$userCode = $_SESSION['user']->user_code ;
			$police_station = $this->Config_model->readConfig('police_station') ;
			$command = $this->Config_model->readConfig('command') ;
			$department = $this->Config_model->readConfig('department') ;
			$police_vehicle_status = $this->Config_model->readConfig('police_vehicle_status') ;
			$user = $this->Config_model->userPermission( $userCode , '' , 'All' ) ;
			//echo '<pre>'; print_r($user) ; die();

        	if($F==1){
				$statud_d = " and police_vehicle_status_code not in ('001' , '000') " ;
        	}else if($F==2){
        		$statud_d = " and police_vehicle_status_code ='001' " ;
        	}

        	$sql = "select
        	tt1.id,tt1.police_vehicle_code,tt1.police_vehicle_number,tt1.police_vehicle_type,tt1.department_code,tt1.command_code,tt1.police_station_code,tt1.police_vehicle_status_code,tt1.device_name,tt1.police_vehicle_lat,tt1.police_vehicle_lon,tt1.department_code,tt1.police_vehicle_code,

        	tt2.case_id,tt2.receive_date,tt2.case_status_code,DATEDIFF(SECOND,tt2.receive_date,getdate()) as duration
				from(
				select *
				,isnull((select top 1 [id] from case_transaction where [case_status_code]<>'007'
				 and police_vehicle_code=t1.police_vehicle_code order by [receive_date]),'') as tran_id
				from police_vehicle t1
				)tt1 left outer join case_transaction tt2 on tt1.tran_id=tt2.id
				where   [department_code] in (".implode(',', $user['department']).")
				and [command_code] in (".implode(',', $user['command']).")
				and [police_station_code] in (".implode(',', $user['police_station']).")
				".$statud_d."
				" ;



			$result = $this->db->query( $sql );

        	$dsx = [] ;
        	$rec = 0;
	        //foreach ($result as $key => $value) {
	        foreach ($result->result_array() as $value){
	            //print_r($value ) ;
	            $ds = [] ;
	            //$ds['record'] = $value['id'] ;
	            //$ds['police_vehicle_number'] = $value['police_vehicle_number'] ;
	            $value['police_station_name'] = $police_station[$value['police_station_code']]['police_station_name'] ;
	            $value['command_name'] = $command[$value['command_code']]['command_name'] ;
	            $value['police_vehicle_status_name'] =  $police_vehicle_status[$value['police_vehicle_status_code']]['police_vehicle_status_name'] ;
	            $value['department_name'] =  $department[$value['department_code']]['department_name'] ;

	            if($value['receive_date']!=null && $value['receive_date']!=''){
	            	$value['dateTime'] = date('d/m/' , strtotime($value['receive_date'])  ).(date('Y', strtotime($value['receive_date'])  ) +543 )." ".date('H:i:s') ;
	            }else{
	            	$value['dateTime'] = '' ;
	            }
	            if($value['duration']!=null && $value['duration']!=''){
	            	$value['duration'] = gmdate( "H:i:s", $value['duration'] ) ;
	            }else{
	            	$value['duration'] = '' ;
	            }
	            //$ds['case_id'] = $value['case_id']  ;
	            //$ds['case_status_code'] = $value['case_status_code']  ;
	            //$ds['police_vehicle_lat'] = $value['police_vehicle_lat']  ;
	            //$ds['police_vehicle_lon'] = $value['police_vehicle_lon']  ;
	            /*if($F==){
	            	$ds['dateTime'] = '' ;
	            	$ds['duration'] = '' ;
	            	$ds['case_id']  = '' ;
	            }*/

	            //$ds['xx'] = '--' ;

	            $dsx[] = $value ;
	            $rec++;
	        }
			//$ds_data['draw'] =
			//$ds_data['recordsTotal'] = count($dsx);
			//$ds_data['recordsFiltered']  = count($dsx);
		 	$ds_data['data'] = $dsx ;
		 	print_r( json_encode( $ds_data ) ) ;
		}
		else
		{
			print_r(json_encode(array()));
		}
	}


	function police_vehicle_list(){
		//echo '<pre>';
		$userCode = $_SESSION['user']->user_code ;
		// print_r($_SESSION  ) ; die();
		$user = $this->Config_model->userPermission( $userCode , '' , 'All' ) ;
		$department_name = $this->Config_model->readConfig('department') ;
		$police_station_name = $this->Config_model->readConfig('police_station') ;
		$command_name = $this->Config_model->readConfig('command') ;
		$police_vehicle = $this->Config_model->readConfig('police_vehicle') ;
		$carType[0] = 'จักรยานยนต์' ;
		$carType[1] = 'รถยนต์' ;
		foreach ($police_vehicle as $key => $value) {
			if(   in_array( $value['police_station_code'] , $user['police_station'] )   ){
				$value['department_name'] = $department_name[ $value['department_code'] ]['department_name'] ;
				$value['command_name'] = $command_name[ $value['command_code'] ]['command_name'] ;
				$value['police_station_name'] = $police_station_name[ $value['police_station_code'] ]['police_station_name'] ;
				$value['police_vehicle_type_name'] = $carType[ $value['police_vehicle_type'] ]  ;

				$ds_x[] = $value ;
			}
			# code...
		}
		print_r(json_encode($ds_x) ) ;
		//print_r($user) ;

	}

	function usersList(){
		//echo '<pre>';
		$req = $_REQUEST ;
		$uc_users = $this->Config_model->readConfigStd('uc_users') ;
		$uc_users_rank = $this->Config_model->readConfigStd('uc_users_rank') ;
		foreach ($uc_users_rank as $key => $value) {
			$uc_users_rank_name[ $value['rank_code'] ] = $value ;
		}
	 	//print_r($uc_users_rank ) ; die();
		$ds_x = [] ;
		foreach ($uc_users as $key => $value) {
			if(    $value['police_station_code'] ==  $req['police_station_code']  ){
				$value['uc_users_rank_name'] = $uc_users_rank_name[ $value['rank_code'] ]['rank_name'] ;
				$ds_x[] = $value ;
			}
			# code...
		}
		print_r(json_encode($ds_x) ) ;
		//print_r($user) ;

	}

	function inspectionList(){
		// echo '<pre>';
		$req = $_REQUEST ;
		$inspection_area = $this->Config_model->readConfigStd('inspection_area') ;

	 	 //print_r($inspection_area ) ; die();
		$ds_x = [] ;
		foreach ($inspection_area as $key => $value) {
			if(    $value['police_station_code'] ==  $req['police_station_code']  ){
				//$value['uc_users_rank_name'] = $uc_users_rank_name[ $value['rank_code'] ]['rank_name'] ;
				$ds_x[] = $value ;
			}
			# code...
		}
		print_r(json_encode($ds_x) ) ;
		//print_r($user) ;

	}

}