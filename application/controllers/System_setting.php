<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_setting extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('writelogs_model');
	}

	public function index()
	{
		try
		{
			$resources = array();
			$resources['casetypes'] = $this->config_model->readConfigStd('casetype');
			if ( ! empty($resources['casetypes']))
			{
				usort($resources['casetypes'], function($a, $b) {
					return $a['casetype_code'] - $b['casetype_code'];
				});
				$this->twiggy->set('casetypes', $resources['casetypes'], TRUE);
			}
			$resources['police_stations'] = $this->config_model->readConfigStd('police_station');
			if ( ! empty($resources['police_stations']))
			{
				usort($resources['police_stations'], function($a, $b) {
					return $a['police_station_code'] - $b['police_station_code'];
				});
				$this->twiggy->set('police_stations', $resources['police_stations'], TRUE);
			}
			$this->twiggy->set('system_setting', $this->config_model->readConfigStd('system_setting'), TRUE);
			$this->twiggy->set('system_setting_json', json_encode($this->config_model->readConfigStd('system_setting')), TRUE);
			$this->twiggy->template('account/myconfig')->display();
		}
		catch (Exception $e)
		{
			show_404();
		}
	}

	public function get_configs_file()
	{
		try
		{
			echo json_encode($this->config_model->readSystemSetting());
		}
		catch (Exception $e)
		{
			echo json_encode();
		}
	}

	public function set_configs_file()
	{
		$this->CI->audit_log->start = microtime(TRUE);
		try
		{
			if ($this->input->post())
			{
				$resources = array();
				$resources['system_setting'] = $this->config_model->readConfigStd('system_setting');
				foreach ($resources['system_setting'] as $keys => $values)
				{
					foreach ($values as $key => $value)
					{
						foreach ($value as $ks => $vs)
						{
							$result = explode(',', $_POST[$keys][$key]);
							$resources['system_setting'][$keys][$key]['value'] = sizeof($result) > 1 ? $result : $result[0];
						}
					}
				}
				if (file_put_contents(_CONFIG.'system_setting', json_encode($resources['system_setting'])) !== FALSE)
				{
					echo json_encode(array('status' => TRUE, 'message' => 'บันทึกการตั้งค่าระบบเรียบร้อย', 'data' => NULL));
					$this->CI->audit_log->end = microtime(TRUE);
					$this->CI->audit_log->duration = $this->CI->audit_log->end - $this->CI->audit_log->start;
					$this->writelogs_model->wLogs($this->audit_log->caller, $this->audit_log->ip_address, 'Info', 'System_setting', 'update', $this->CI->audit_log->duration, 1, json_encode($resources['system_setting']), json_encode(array('status' => TRUE, 'message' => 'บันทึกการตั้งค่าระบบเรียบร้อย')), 'system_setting');
				}
				else
				{
					throw new Exception();
				}
			}
		}
		catch (Exception $e)
		{
			echo json_encode(array('status' => FALSE, 'message' => 'ไม่สามารถบันทึกการตั้งค่าระบบได้ กรุณาทำรายการใหม่', 'data' => NULL));
			$this->CI->audit_log->end = microtime(TRUE);
			$this->CI->audit_log->duration = $this->CI->audit_log->end - $this->CI->audit_log->start;
			$this->writelogs_model->wLogs($this->audit_log->caller, $this->audit_log->ip_address, 'high', 'System_setting', 'update', $this->CI->audit_log->duration, 0, json_encode($resources['system_setting']), json_encode(array('status' => FALSE, 'message' => 'ไม่สามารถบันทึกการตั้งค่าระบบได้ กรุณาทำรายการใหม่')), 'system_setting');
		}
	}

}