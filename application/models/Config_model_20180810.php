<?php
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
	{   //echo _CONFIG.$table  ;
		$config = file_get_contents( _CONFIG.$table  ) ;
		$config = json_decode($config , true) ;
		foreach ($config as $key => $value) {
			if($table=='case_result'){
				$ds[  $value['result_code']] = $value ;
			}elseif($table=='case_inform'){
				$ds[ $value["inform_id"] ] = $value ;
			}elseif($table=='case_closejob'){
				$ds[ $value["closejob_code"] ] = $value ;
			}else{
				$ds[ $value[ $table."_code"] ] = $value ;
			}
			
		}
 		return  $ds ;
	}

}