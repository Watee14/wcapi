<?php  error_reporting(E_ALL) ;
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		$this->load->model('common_model');
	}
 
	public function selectDB($table = NULL)
	{
		try
		{
			if ($table !== NULL && $this->common_model->get_table_exists($table))
			{
				$resources['query'] = $this->db->get($table);
				if ($resources['query']->num_rows() > 0)
				{
					return json_encode($resources['query']->result_array());
				}
				else
				{
					throw new Exception();
				}
			}
			else
			{
				throw new Exception();
			}
		}
		catch (Exception $e)
		{
			return 'This table does not exist, please fill correct table name.';
		}
	}

	public function readConfig( $table )
	{  
		$config = file_get_contents( _CONFIG.$table  ) ;
		$config = json_decode($config , true) ;
		foreach ($config as $key => $value) {
			if($table=='case_result'){
				$ds[  $value['result_code']] = $value ;
			}elseif($table=='case_inform'){
				$ds[ $value["inform_id"] ] = $value ;
			}elseif($table=='case_closejob'){
				$ds[ $value["closejob_code"] ] = $value ;
			}elseif($table=='map_type'){
				$ds[ $value["poi_type_code"] ] = $value ;
			} else{
				$ds[ $value[ $table."_code"] ] = $value ;
			}
			
		}
 		return  $ds ;
	}
	public function readConfigStd( $table )
	{  
		$config = file_get_contents( _CONFIG.$table  ) ;
		$config = json_decode($config , true) ;
		 
 		return  $config ;
	}
	public function readConfigUser( $agent )
	{  
		$config = file_get_contents( _CONFIG_USER.$agent  ) ;
		$config = json_decode($config , true) ;
		 
 		return  $config ;
	}

	public function userPermission( $agent , $id , $type )
	{   //]echo $agent."--".$id."--".$type ;
		//echo '<pre>';
		$config = file_get_contents( _CONFIG_USER.$agent  ) ;
		$config = json_decode($config , true) ;
		//print_r($config) ; die();
		$per['department'] = [] ;
		$per['command'] = [] ;
		$per['police_station'] = [] ;
		
		switch ($type) {
			case 'department':
				//print_r($config['command_profile']) ;
				foreach ($config['command_profile'] as $key => $value) {
					//if($value['code']== $id ){
						$inD[] = $value['code'] ;
					//}
				}
				$per['department'] = $inD ; 
				break;

			case 'command':
				
				foreach ($config['command_profile'] as $key => $value) {
					if($value['code']==$id){
						//print_r($value) ;
						foreach ($value['data'] as $kk => $vv) {
							if($value['code']== $id ){
								$inD[] = $vv['code'] ;
							}
						}
					}
					
				}
				$per['command'] = $inD ; 
				break;

			case 'police_station':
				
				foreach ($config['command_profile'] as $key => $value) {
					foreach ($value['data'] as $kk => $vv) {
						if($vv['code']== $id ){
							foreach ($vv['data'] as $k11 => $v11 ) {
								if($vv['code']== $id ){
									$inD[] = $v11['code'] ;
								}
							}
						}
					}
				} 
				$per['police_station'] = $inD ; 
				break;

			default:
				foreach ($config['command_profile'] as $key => $value) {
					//if($value['code']!=''){
						$inD[] = $value['code'] ;
					//}
				}
				$per['department'] = $inD ;
				$inD = [];
				foreach ($config['command_profile'] as $key => $value) { 
					foreach ($value['data'] as $kk => $vv) {
						if($vv['code']!=''){
							$inD[] = $vv['code'] ;
						}
					} 
				}
				$per['command'] = $inD ; 
				$inD = [];
				foreach ($config['command_profile'] as $key => $value) {
					foreach ($value['data'] as $kk => $vv) {
						foreach ($vv['data'] as $k11 => $v11 ) {
							if($v11['code']!=''){
								$inD[] = $v11['code'] ;
							}
						}
					}
				} 
				$per['police_station'] = $inD ;

				break;
		}
		//print_r($per) ;

 		return  $per ;
	}

}