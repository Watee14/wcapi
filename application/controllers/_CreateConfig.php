<?php error_reporting(E_ERROR);
header('Access-Control-Allow-Origin: *'); 
defined('BASEPATH') OR exit('No direct script access allowed');

class CreateConfig extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('Config_model');
	}

	public function getConfig($table = NULL)
	{
		$config = $this->Config_model->selectDB($table);
		print_r($config);

		// print_r($config);

		$this->writeConfig($table, $config);
	}

	public function getConfigAll()
	{
		$succ = 0;
		$fail = 0;
		$tables = explode(',', _WRITE_TABLE);
		foreach ($tables as $key => $value)
		{
			$table = $value;
			$config = $this->Config_model->selectDB($table);
			$res = $this->writeConfig($table, $config);

			// echo $table." -> Success ".$res."<br>";

			if ($res == TRUE)
			{
				$succ++;
			}
			else
			{
				$fail++;
			}
		}
		$ds['total'] = count($tables);
		$ds['success'] = $succ;
		$ds['fail'] = $fail;
		print_r(json_encode($ds));
	}

	public function writeConfig($table, $config)
	{
		// $filename = _CONFIG.$table;
		// if (file_exists($filename))
		// {
		//   $newFile= fopen($filename, 'w+');
		//   fwrite($newFile, $config);
		//   fclose($newFile);
		// }
		// else
		// {
		//   $newFile= fopen($filename, 'w+');
		//   fwrite($newFile, $config);
		//   fclose($newFile);
		//   chmod($filename, 0777);
		// }

		$res = FALSE;
		if (file_put_contents(_CONFIG.$table, $config) !== FALSE) {

			// reset of code
			$res = TRUE;
		}
		else
		{
			$res = FALSE;
		}

		// print_r($res);

		return $res;
	}

	public function users_command()
	{
		// echo '<pre>';

		$succ = 0;
		$fail = 0;
		$command = $this->Config_model->readConfigStd('command');
		foreach ($command as $key => $value)
		{
			$command_name[$value['command_code']] = $value['command_name'];
		}
		$department = $this->Config_model->readConfigStd('department');
		foreach ($department as $key => $value)
		{
			$department_name[$value['department_code']] = $value['department_name'];
		}
		$police_station = $this->Config_model->readConfigStd('police_station');
		foreach ($police_station as $key => $value) {
			$dsx['department_code'] = $value['department_code'];
			$dsx['police_station_code'] = $value['police_station_code'];
			$dsx['command_code'] = $value['command_code'];
			$police_station_ds[$value['police_station_code']] = $dsx;
			$police_station_name[$value['police_station_code']] = $value['police_station_name'];
		}

		// print_r($department_name);
		// die();

		$uc_users_command = $this->Config_model->readConfigStd('uc_users_command');
		foreach ($uc_users_command as $key => $value)
		{
			if ($value['police_station_code'] != '')
			{
				if( isset($police_station_ds[$value['police_station_code']] )){
					$uc_users_command_ds[$value['user_code']][] = $police_station_ds[$value['police_station_code']];
				}
			}
		}
		$uc_users = $this->Config_model->readConfigStd('uc_users');
		foreach ($uc_users as $key => $value)
		{
			//$command_fix = $uc_users_command_ds[$value['user_code']];

			// print_r(json_encode($command_fix));
			// die();
			if(isset($uc_users_command_ds[$value['user_code']])){
				//print_r($value['user_code']) ; echo '<br>';
				$value['command_profile'] = $uc_users_command_ds[$value['user_code']];
			}else{
				//print_r($value['user_code']) ; echo '<br>'; die();
				$value['command_profile'] = [];
			}
			
			$uc_users_ds[$value['user_code']] = $value;
		}
		//echo '<pre>';
		 //print_r($uc_users_ds);
		//  die();

		foreach ($uc_users_ds as $key => $value)
		{
			// if($key."_" == "001_")
			// {
				// echo $key.'<br>';
				// print_r($value);	
			//echo $value['command_profile'].'<br>' ; 
				if(count($value['command_profile'])>0){
					$res = $this->renderCommand($value, $command_name, $department_name, $police_station_name);
				}else{
					$value['command_profile'] = '' ;
					$res = $value ;
				}
				
				$data_user[] = $res;

				// file_put_contents(_CONFIG_USER.$key, json_encode($res));

				if (file_put_contents(_CONFIG_USER.$key, json_encode($res)) !== FALSE)
				{
					// reset of code
					$succ++;
				}
				else
				{
					$fail++;
				}

			// }
		}
		$ds['total'] = count($uc_users_ds);
		$ds['success'] = $succ;
		$ds['fail'] = $fail;
		print_r(json_encode($ds));

		// print_r(json_encode($data_user));
	}

	function renderCommand($data, $command_name, $department_name, $police_station_name)
	{
		// $val = '[{"department_code":"001","police_station_code":"60001","command_code":"101"},{"department_code":"001","police_station_code":"60002","command_code":"101"},{"department_code":"001","police_station_code":"60003","command_code":"101"},{"department_code":"001","police_station_code":"60004","command_code":"101"},{"department_code":"001","police_station_code":"60005","command_code":"101"},{"department_code":"001","police_station_code":"60006","command_code":"101"},{"department_code":"001","police_station_code":"60007","command_code":"101"},{"department_code":"001","police_station_code":"60008","command_code":"101"},{"department_code":"001","police_station_code":"60009","command_code":"101"},{"department_code":"001","police_station_code":"60012","command_code":"101"}]';
		// print_r($val['command_profile']);
		// die();

		$val = $data['command_profile'];
		if (count($data['command_profile']) > 0)
		{

		}
		else
		{
			return $data;
		}

		// print_r($val);
		// die();
		// $val = json_decode($val, TRUE);

		foreach ($val as $key => $value)
		{

			$dsx[$value['department_code']]['code'] = $value['department_code'];
			$dsx[$value['department_code']]['name'] = $department_name[$value['department_code']];
			$dsx[$value['department_code']]['data'][$value['command_code']]['code'] = $value['command_code'];
			$dsx[$value['department_code']]['data'][$value['command_code']]['name'] = $command_name[$value['command_code']];
			$dsx[$value['department_code']]['data'][$value['command_code']]['data'][$value['police_station_code']]['code'] = $value['police_station_code'];
			$dsx[$value['department_code']]['data'][$value['command_code']]['data'][$value['police_station_code']]['name'] = $police_station_name[$value['police_station_code']];

			// [$value['command_code']][$value['police_station_code']]
			// $po['police_station_code'] = $value['police_station_code'];
			// $po['police_station_name'] = $police_station_name[$value['police_station_code']];
			// $po_ds['police_station'][] = $po;
		}
		//print_r($dsx) ;
		foreach ($dsx as $key => $value)
		{
			$ds_command = [];
			$ds_command['code'] = $value['code'];
			$ds_command['name'] = $value['name'];
			foreach ($value['data'] as $k1 => $v1)
			{
				$ds_police = [];
				$ds_police['code'] = $v1['code'];
				$ds_police['name'] = $v1['name'];
				foreach ($v1['data'] as $k2 => $v2)
				{
					$ds_police['data'][] = $v2;
				}
				$ds_command['data'][] = $ds_police;
			}
			$data_ds[] = $ds_command;

			# code...
		}
		if (count($data_ds) > 0)
		{

		}
		else
		{
			$data_ds = [];
		}
		$data['command_profile'] = $data_ds;

		// print_r(json_encode($data_ds));

		return $data;
	}

	function getUserPermission($user_code=''){ 
		$config = $this->Config_model->userPermission( $user_code , '' , 'All' ) ;
		print_r(json_encode($config)) ;
	}

}
?>