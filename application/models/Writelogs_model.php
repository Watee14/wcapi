<?php error_reporting(E_ERROR) ;
defined('BASEPATH') OR exit('No direct script access allowed');

class Writelogs_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('common_model');
		//$this->load->database();
		//$this->load->model('Config_model');
	}
	public function wLogs( $Caller='', $IPaddress='', $Severity='', $Module='', $Task='', $Duration='', $Result='', $JsonRequest='', $jsonResult='' , $Folder='' )
	{  
		//{Y/m/d H:i:s}|{Caller}|{IPaddress}|Severity={low,medium,high,criticle}|{function,Menu,Module}|Task={Add,Edit,Delete,Read,..}|{Duration(ms)}|Result={1=success,0=Fail}|{JsonRequest}|{jsonResult}
		if($Module!=''){
			if (!file_exists(_LOGS.$Folder)) {
			    mkdir( _LOGS.$Folder , 0777, true);
			}
		}

		$ds['Caller'] = $Caller ;
		$ds['IPaddress'] = $IPaddress ;
		$ds['Severity'] = $Severity ;
		$ds['Module'] = $Module ;
		$ds['Task'] = $Task ;
		$ds['Duration'] = number_format($Duration, 3 , '.', ''); 
		$ds['Result'] = $Result ;
		$ds['JsonRequest'] = $JsonRequest ;
		$ds['jsonResult'] = $jsonResult ;
		//print_r($ds) ;
		$data = date('Y/m/d H:i:s')."|".implode('|', $ds ).PHP_EOL;
		file_put_contents( _LOGS.$Folder.'/'.date('Y_m_d').'_'.$Module.'.log' , $data  , FILE_APPEND ) ;
	}
	public function xxx( $x)
	{
		print_r($x) ;
		return "ccccc";
	}
	 
}


?>



