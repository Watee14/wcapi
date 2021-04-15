<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opencase extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->library('twiggy');
		$this->load->model('common_model');
		$this->load->model('config_model');
		$this->load->model('writelogs_model');
		$this->load->helper('custom');
		$this->load->helper('general');
		$this->load->helper('security');
		$this->load->helper('string');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->create();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if ((2) !== NULL && uriSeg(3) !== NULL)
		{
			if (isset($_POST) && ! empty($_POST))
			{
				unset($_POST['DataTables_Table_0_length']);
				$this->store($_POST);
			}
			else
			{
				$resources = array();
				$this->twiggy->set('functions', uriSeg(2), TRUE);
				$this->twiggy->set('modules', uriSeg(3), TRUE);
				if ($this->common_model->get_table_exists('casetype'))
				{
					$resources['casetypes'] = $this->db->get('casetype');
					if ( ! empty($resources['casetypes']) && $resources['casetypes']->num_rows() > 0)
					{
						$this->twiggy->set('casetypes', $resources['casetypes']->result_array(), TRUE);
					}
				}
				if ($this->common_model->get_table_exists('command'))
				{
					$resources['commands'] = $this->db->select('id, command_code, command_name, department_code')->from('command')->order_by('command_name ASC, command_code ASC')->get();
					if ( ! empty($resources['commands']) && $resources['commands']->num_rows() > 0)
					{
						$this->twiggy->set('commands', $resources['commands']->result_array(), TRUE);
					}
				}
				if ($this->common_model->get_table_exists('police_station'))
				{
					$resources['police_stations'] = $this->db->select('id, police_station_code, police_station_name, command_code, department_code')->from('police_station')->order_by('police_station_name ASC, police_station_code ASC')->get();
					if ( ! empty($resources['police_stations']) && $resources['police_stations']->num_rows() > 0)
					{
						$this->twiggy->set('police_stations', $resources['police_stations']->result_array(), TRUE);
					}
				}
				if (getInget('un'))
				{
					$resources['uc_users'] = rowArray($this->common_model->get_where_custom_field('uc_users', 'user_name', getInget('un'), 'first_name, last_name, phone_number'));
					if (count($resources['uc_users']) > 0)
					{
						foreach ($resources['uc_users'] as $key => $value)
						{
							$resources['query_value'][$key] = $value;
						}
					}
				}
				if (getInget('lat'))
				{
					$resources['query_value']['case_lat'] = getInget('lat');
				}
				if (getInget('lon'))
				{
					$resources['query_value']['case_lon'] = getInget('lon');
				}
				$police_stations = $this->config_model->readConfigStd('police_station');
				if (empty($police_stations))
				{
					$police_stations = $this->db->select('police_station_code, command_code')->get('police_station')->result_array();
				}
				foreach ($police_stations as $keys => $values)
				{
					foreach ($values as $key => $value)
					{
						if ($key != 'police_station_code' && $key != 'command_code')
						{
							unset($police_stations[$keys][$key]);
						}
					}
				}
				$this->twiggy->set('police_stations', ! empty($police_stations) ? json_encode($police_stations) : NULL, TRUE);
				$resources['datetimes']['datetime'] = date('d/m/Y H:i:s');
				$resources['datetimes']['date'] = explode('/', explode(' ', $resources['datetimes']['datetime'])[0]);
				$resources['datetimes']['time'] = explode(':', explode(' ', $resources['datetimes']['datetime'])[1]);
				$resources['query_value']['case_id'] = 'D'.substr($resources['datetimes']['date'][2] + 543, 2).$resources['datetimes']['date'][1].$resources['datetimes']['date'][0].$resources['datetimes']['time'][0].$resources['datetimes']['time'][1].$resources['datetimes']['time'][2].(sprintf("%03d", round((microtime(TRUE) - floor(microtime(TRUE))) * 1000)));
				$resources['query_value']['created_date'] = $resources['datetimes']['datetime'];
				$resources['query_value']['started_date'] = $resources['datetimes']['datetime'];
				$resources['page_title'] = array('new' => 'สร้างเหตุใหม่', 'past' => 'สร้างเหตุย้อนหลัง');
				$this->twiggy->set('query_value', $resources['query_value'], TRUE);
				$this->twiggy->set('page_title', $resources['page_title'][uriSeg(3)], TRUE);
				$this->twiggy->template('incident/manage')->display();
				unset($resources);
			}
		}
	}
	/**
	 * @return Response
	 */
	public function create_api()
	{
		$this->store($_POST);
	}

	public function curl_service($data)
	{
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $data['url']);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $data['method']);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data['data']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    return  $response;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  Request 	$request
	 * @return Response
	 */
	public function store($request = NULL)
	{
		$audit_log_start = microtime(TRUE);
		$submit_response = 'บันทึก';
		$requests = array('case' => NULL, 'citizen_profile' => NULL, 'case_responsible' => NULL);
		try
		{
			if ($request !== NULL && getInget('un'))
			{
				unset($request['DataTables_Table_0_length']);
				$resources = array();
				$resources['request'] = $request;
				if (isset($resources['request']['case_id']) && ! empty($resources['request']['case_id']))
				{
					$resources['case_id'] = $resources['request']['case_id'];
				}
				else
				{
					$resources['datetimes'] 			= array();
					$resources['datetimes']['datetime'] = date('Y-m-d H:i:s');
					$resources['datetimes']['date'] 	= explode('-', explode(' ', $resources['datetimes']['datetime'])[0]);
					$resources['datetimes']['time'] 	= explode(':', explode(' ', $resources['datetimes']['datetime'])[1]);
					$resources['case_id'] 				= 'D'.substr($resources['datetimes']['date'][0] + 543, 2).$resources['datetimes']['date'][1].$resources['datetimes']['date'][2].$resources['datetimes']['time'][0].$resources['datetimes']['time'][1].$resources['datetimes']['time'][2].(sprintf("%03d", round((microtime() - floor(microtime())) * 1000)));
					unset($resources['datetimes']);
				}
				$resources['system_setting']['case_status_code'] = '001';
				if (isset($resources['request']['casetype_code']) && ! empty($resources['request']['casetype_code']))
				{
					$system_setting = json_decode($this->curl_service(array('url' => _SYS_SETTING, 'method' => 'GET')), TRUE);
					if (in_array($resources['request']['casetype_code'], $system_setting['case_type']['not_command']))
					{
						$resources['system_setting']['case_status_code'] = '007';
					}
				}
				$resources['case'] = array();
				$resources['case']['case_id'] 				= strval($resources['case_id']);
				$resources['case']['casetype_code'] 		= isset($resources['request']['casetype_code']) && ! empty($resources['request']['casetype_code']) ? strval($resources['request']['casetype_code']) : NULL;
				$resources['case']['priority'] 				= intval($resources['request']['priority']);
				$resources['case']['ways'] 					= 6;
				$resources['case']['phone_number'] 			= strval($resources['request']['phone_number']);
				$resources['case']['duration'] 				= 0;
				$resources['case']['case_status_code'] 		= strval($resources['system_setting']['case_status_code']);
				$resources['case']['case_detail'] 			= strval($resources['request']['case_detail']);
				$resources['case']['command_code'] 			= isset($resources['request']['command_code']) && ! empty($resources['request']['command_code']) ? strval($resources['request']['command_code']) : NULL;
				$resources['case']['police_station_code'] 	= isset($resources['request']['police_station_code']) && ! empty($resources['request']['police_station_code']) ? strval($resources['request']['police_station_code']) : NULL;
				$resources['case']['case_location_address'] = strval(trim($resources['request']['case_location_address']));
				$resources['case']['case_location_detail'] 	= '';
				$resources['case']['case_lat'] 				= strval($resources['request']['case_lat']);
				$resources['case']['case_lon'] 				= strval($resources['request']['case_lon']);
				$resources['case']['home'] 					= NULL;
				$resources['case']['extension_receive'] 	= '';
				$resources['case']['case_sla'] 				= 0;
				$resources['case']['opened_date'] 			= date('Y-m-d H:i:s', strtotime(wt_datetime_change($resources['request']['created_date'])));
				$resources['case']['started_date'] 			= date('Y-m-d H:i:s', strtotime(wt_datetime_change($resources['request']['started_date'])));
				$resources['case']['modified_date'] 		= date('Y-m-d H:i:s');
				$resources['case']['user_create'] 			= strval(getInget('un'));
				$resources['case']['user_modify'] 			= strval(getInget('un'));
				if (isset($resources['request']['phone_number']) && ! empty($resources['request']['phone_number']))
				{
					if (isset($resources['request']['citizen_fullname']) && ! empty($resources['request']['citizen_fullname']))
					{
						$resources['citizen_profile']['fullname'] 			= explode(' ', $resources['request']['citizen_fullname'], 3);
						$resources['citizen_profile']['citizen_name'] 		= count($resources['citizen_profile']['fullname']) > 0 ? $resources['citizen_profile']['fullname'][0] : NULL;
						$resources['citizen_profile']['citizen_middle'] 	= count($resources['citizen_profile']['fullname']) > 2 ? $resources['citizen_profile']['fullname'][sizeof($resources['citizen_profile']['fullname']) - 2] : NULL;
						$resources['citizen_profile']['citizen_surname'] 	= count($resources['citizen_profile']['fullname']) > 1 ? $resources['citizen_profile']['fullname'][sizeof($resources['citizen_profile']['fullname']) - 1] : NULL;
					}
					unset($resources['citizen_profile']['fullname']);
					$resources['citizen_profile']['citizen_phone_number'] 	= strval($resources['request']['phone_number']);
					$resources['citizen_profile']['modified_date'] 			= date('Y-m-d H:i:s');
					$resources['citizen_profile']['user_modify'] 			= strval(getInget('un'));
					$resources['phone_number_query'] = rowArray($this->common_model->get_where_custom_field('citizen_profile', 'citizen_phone_number', strval($resources['request']['phone_number']), 'citizen_code'));
					if (count($resources['phone_number_query']) > 0)
					{
						$this->common_model->update('citizen_profile', $resources['citizen_profile'], array('citizen_code' => $resources['phone_number_query']['citizen_code']));
						$resources['case']['citizen_code'] = intval($resources['phone_number_query']['citizen_code']);
					}
					else
					{
						if ( ! isset($resources['request']['citizen_fullname']) OR empty($resources['request']['citizen_fullname']))
						{
							$resources['citizen_profile']['citizen_name'] = '';
						}
						$resources['citizen_profile']['created_date'] 	= date('Y-m-d H:i:s');
						$resources['citizen_profile']['user_create'] 	= strval(getInget('un'));
						$this->db->insert('citizen_profile', $resources['citizen_profile']);
						$resources['case']['citizen_code'] = intval($this->db->insert_id());
					}
					$requests['citizen_profile'] = $resources['citizen_profile'];
				}
				$resources['case_exist'] = rowArray($this->common_model->get_where_custom_field('case', 'case_id', $resources['case_id'], 'case_id'));
				if (count($resources['case_exist']) > 0)
				{
					$this->common_model->update('case', $resources['case'], array('case_id' => $resources['case_id']));
					$submit_response = 'ปรับปรุง';
				}
				else
				{
					$resources['case']['created_date'] = date('Y-m-d H:i:s');
					$this->common_model->insert('case', $resources['case']);
					$submit_response = 'บันทึก';
				}
				$requests['case'] = $resources['case'];
				$resources['case_responsible'] = array();
				$resources['panfah'] = rowArray($this->db->select('department_code, command_code')->where('police_station_code', '191')->get('police_station')->result_array());
				$resources['case_responsible_query'] = $this->common_model->get_where_custom_field('case_responsible', 'case_id', $resources['case_id'], 'responsible_code');
				$resources['case_responsible'][0] = array();
				if (count($resources['case_responsible_query']) > 0)
				{
					$resources['case_responsible'][0]['responsible_code'] 	= intval($resources['case_responsible_query'][0]['responsible_code']);
				}
				$resources['case_responsible'][0]['responsible_value'] 		= 0;
				$resources['case_responsible'][0]['department_code'] 		= strval($resources['panfah']['department_code']);
				$resources['case_responsible'][0]['command_code'] 			= strval($resources['panfah']['command_code']);
				$resources['case_responsible'][0]['police_station_code'] 	= '191';
				$resources['case_responsible'][0]['inspection_area_code'] 	= '';
				$resources['case_responsible'][0]['case_id']				= strval($resources['case_id']);
				$resources['case_responsible'][0]['created_date'] 			= date('Y-m-d H:i:s');
				$resources['case_responsible'][0]['modified_date'] 			= date('Y-m-d H:i:s');
				$resources['case_responsible'][0]['user_create'] 			= strval(getInget('un'));
				$resources['case_responsible'][0]['user_modify'] 			= strval(getInget('un'));
				$resources['insn'] = array();
				$resources['insn']['inspection_area_code'] = strval($resources['request']['inspection_area_code']);
				$resources['dept'] = rowArray($this->db->select('department_code')->where(array('command_code' => $resources['case']['command_code'], 'police_station_code' => $resources['case']['police_station_code'], 'inspection_area_code' => $resources['insn']['inspection_area_code']))->get('inspection_area')->result_array());
				$resources['case_responsible'][1] = array();
				if (count($resources['case_responsible_query']) > 0)
				{
					$resources['case_responsible'][1]['responsible_code'] 	= intval($resources['case_responsible_query'][1]['responsible_code']);
				}
				$resources['case_responsible'][1]['responsible_value'] 		= 1;
				$resources['case_responsible'][1]['department_code'] 		= strval($resources['dept']['department_code']);
				$resources['case_responsible'][1]['command_code'] 			= strval($resources['case']['command_code']);
				$resources['case_responsible'][1]['police_station_code'] 	= strval($resources['case']['police_station_code']);
				$resources['case_responsible'][1]['inspection_area_code'] 	= strval($resources['insn']['inspection_area_code']);
				$resources['case_responsible'][1]['case_id']				= strval($resources['case_id']);
				$resources['case_responsible'][1]['created_date'] 			= date('Y-m-d H:i:s');
				$resources['case_responsible'][1]['modified_date'] 			= date('Y-m-d H:i:s');
				$resources['case_responsible'][1]['user_create'] 			= strval(getInget('un'));
				$resources['case_responsible'][1]['user_modify'] 			= strval(getInget('un'));
				if (count($resources['case_responsible_query']) > 0)
				{
					$this->db->update_batch('case_responsible', $resources['case_responsible'], 'responsible_code');
				}
				else
				{
					$this->db->insert_batch('case_responsible', $resources['case_responsible']);
				}
				$requests['case_responsible'] = $resources['case_responsible'];
				$audit_log_end = microtime(TRUE);
				$audit_log_duration = $audit_log_end - $audit_log_start;
				echo json_encode(array('status' => TRUE, 'message' => $submit_response.'เหตุเรียบร้อย', 'data' => $requests));
				$this->writelogs_model->wLogs(strval(getInget('un'), $_SERVER['HTTP_X_FORWARRDED_FOR'] ? $_SERVER['HTTP_X_FORWARRDED_FOR'] : $_SERVER['REMOTE_ADDR'], 'Info', 'Incident', 'Create case', $audit_log_duration, 1, json_encode($requests), json_encode(array('status' => TRUE, 'message' => $submit_response.'เหตุเรียบร้อย')), 'incident'));
				unset($resources);
			}
		}
		catch (Exception $e)
		{
			echo json_encode(array('status' => FALSE, 'message' => 'ไม่สามารถ'.$submit_response.'เหตุได้<br>'.$e->getMessage().'<br>กรุณาตรวจสอบความถูกต้องของข้อมูลและ'.$submit_response.'เหตุอีกครั้ง', 'data' => $requests));
			$audit_log_end = microtime(TRUE);
			$audit_log_duration = $audit_log_end - $audit_log_start;
			$this->writelogs_model->wLogs(strval(getInget('un'), $_SERVER['HTTP_X_FORWARRDED_FOR'] ? $_SERVER['HTTP_X_FORWARRDED_FOR'] : $_SERVER['REMOTE_ADDR'], 'High', 'Incident', 'Create case', $audit_log_duration, 0, json_encode($requests), json_encode(array('status' => FALSE, 'message' => 'ไม่สามารถ'.$submit_response.'เหตุได้<br>'.$e->getMessage().'<br>กรุณาตรวจสอบความถูกต้องของข้อมูลและ'.$submit_response.'เหตุอีกครั้ง')), 'incident'));
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int 		$id
	 * @return Response
	 */
	public function show($id = NULL)
	{
		try
		{

		}
		catch (Exception $e)
		{
			show_404();
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int 		$id
	 * @return Response
	 */
	public function edit($id = NULL)
	{

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Request 	$request
	 * @param  int 		$id
	 * @return Response
	 */
	public function update($request = NULL, $id = NULL)
	{

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int 		$id
	 * @return Response
	 */
	public function destroy($id = NULL)
	{

	}

	/**
	 *-----------------------------------------------------------------------
	 * Custom method
	 *-----------------------------------------------------------------------
	 */

	/**
	 * @return	Response
	 */
	public function detail($case_id = NULL)
	{
		try
		{
			if ($this->common_model->get_table_exists('case') && $case_id !== NULL)
			{
				$resources = array();
				// Detail
				$resources['select'] = array('case_detail, action_pro_code');
				$resources['query'] = $this->db->select('case_detail, action_pro_code')->where('case_id', $case_id)->get('case');
				if ($resources['query']->num_rows() > 0)
				{
					$resources['query_value'] = rowArray($resources['query']->result_array());
					$resources['case_detail'] = $resources['query_value']['case_detail'];
					$resources['case_detail'] .= $resources['query_value']['case_detail'] !== NULL && $resources['query_value']['case_detail'] != '' ? '<br><br>' : '';
					$resources['action_pro'] = rowArray($this->common_model->get_where_custom_field('action_pro', 'action_pro_code', $resources['query_value']['action_pro_code'], 'title, body'));
					if (count($resources['query']) > 0 && count($resources['action_pro']) > 0)
					{
						$resources['case_detail'] .= $resources['action_pro']['title'] != NULL && $resources['action_pro']['title'] != '' ? $resources['action_pro']['title'].'<br>' : '';
					}
					$resources['query'] = $this->common_model->get_where_custom_and('action_pro_result', array('action_pro_code' => $resources['query_value']['action_pro_code'], 'case_id' => $case_id));
					if (count($resources['query']) > 0)
					{
						$key_no = 1;
						foreach ($resources['query'] as $key => $value)
						{
							if (strlen($value['action_pro_result_value']) > 0)
							{
								$resources['case_detail'] .= $key_no.'. '.$value['action_pro_result_question'].' - ';
								if ($value['action_pro_result_value'] == ' ')
								{
									foreach (json_decode($resources['action_pro']['body']) as $ks => $vs)
									{
										if ($vs->name == $value['action_pro_result_name'])
										{
											foreach ($vs->values as $k => $v)
											{
												if ($v->v_action == 5)
												{
													$resources['case_detail'] .= $v->value;
												}
											}
										}
									}
								}
								else
								{
									$resources['case_detail'] .= $value['action_pro_result_value'];
								}
								$resources['case_detail'] .= $key_no <= (sizeof($resources['query']) - 1) ? '<br>' : '';
								$key_no += 1;
							}
						}
					}
				}
				$this->twiggy->set('case_detail', str_replace('เจ้าหน้าที่', '<br>เจ้าหน้าที่', $resources['case_detail']), TRUE);
				$this->twiggy->template('incident/data_detail')->display();
				unset($resources);
			}
		}
		catch (Exception $e)
		{
			echo json_encode();
		}
	}

	/**
	 * @return	Response
	 */
	public function extra($case_id = NULL)
	{
		try
		{
			if ($this->common_model->get_table_exists('case') && $case_id !== NULL)
			{
				$resources = array();
				/**
				 * Additional
				 */
				$resources['case_information'] = "";
				$resources['query'] = $this->common_model->custom_query("SELECT type_info FROM case_information WHERE case_id = '{$case_id}' GROUP BY type_info");
				if (count($resources['query']) > 0)
				{
					$resources['reference_id'] = array(
						1 => array(
							'title' => 'ลักษณะบุคคล',
							'table' => 'personal',
							'join' => array('personal_accent', 'personal_age', 'personal_defective', 'personal_group', 'personal_hair', 'personal_hair_color', 'personal_nationality', 'personal_scar', 'personal_sex', 'personal_shape', 'personal_skin', 'personal_tall', 'personal_tattoo'),
							'select' => array('personal_group_name', 'personal_name', 'personal_sex_name', 'personal_nationality_name', 'personal_nationality_other', 'personal_skin_name', 'personal_age_name', 'personal_hair_name', 'personal_hair_color_name', 'personal_shape_name', 'personal_tall_name', 'personal_tattoo_name', 'personal_tattoo_style', 'personal_scar_name', 'personal_scar_style', 'personal_defective_name', 'personal_defective_style', 'personal_accent_name', 'personal_accent_style', 'personal_detail'),
							'label' => array('', 'ชื่อ', 'เพศ', 'เชื้อชาติ', '', 'ผิว', 'อายุ ', 'ผม', '', 'รูปร่าง', 'สูง ', 'รอยสักที่', 'ลักษณะ', 'แผลเป็นที่', 'ลักษณะ', 'พิการที่', 'ลักษณะ', 'สำเนียง', 'ลักษณะ', ''),
						),
						2 => array(
							'title' => 'ทรัพย์สิน',
							'table' => 'asset',
							'join' => array('asset_group'),
							'select' => array('asset_group_name', 'asset_count', 'asset_value', 'asset_detail'),
							'label' => array('', 'จำนวน ', 'มูลค่า ', ''),
							'type' => array('string', 'integer', 'integer', 'string'),
						),
						3 => array(
							'title' => 'ยานพาหนะ',
							'table' => 'vehicle',
							'join' => array('vehicle_brand', 'vehicle_color', 'vehicle_group', 'vehicle_model', 'addr_province', 'vehicle_type'),
							'select' => array('vehicle_group_name', 'vehicle_type_name', 'vehicle_brand_name', 'vehicle_model_name', 'vehicle_color_name', 'vehicle_plate', 'PROVINCE_NAME', 'vehicle_detail'),
							'label' => array('', '', 'ยี่ห้อ ', 'รุ่น ', 'สี', 'หมายเลขทะเบียน ', '', ''),
						),
						4 => array(
							'title' => 'สัตว์เลี้ยง',
							'table' => 'pets',
							'join' => array('pets_breed', 'pets_color', 'pets_group', 'pets_type'),
							'select' => array('pets_group_name', 'pets_type_name', 'pets_breed_name', 'pets_color_name', 'pets_skin', 'pets_detail'),
							'label' => array('', '', 'พันธุ์', 'สี', 'ลาย', ''),
						),
					);
					$resources['case_additional'] = array();
					for ($i = 0; $i < sizeof($resources['query']); $i++)
					{
						$resources['sub_query'] = $this->db->select(implode(', ', $resources['reference_id'][$resources['query'][$i]['type_info']]['select']));
						foreach ($resources['reference_id'][$resources['query'][$i]['type_info']]['join'] as $key => $value)
						{
							if ($value == 'addr_province')
							{
								$resources['sub_query'] = $resources['sub_query']->join($value, $resources['reference_id'][$resources['query'][$i]['type_info']]['table'].'.vehicle_province_code = '.$value.'.PROVINCE_ID', 'left');
							}
							else if ($value == 'pets_breed')
							{
								$resources['sub_query'] = $resources['sub_query']->join($value, $resources['reference_id'][$resources['query'][$i]['type_info']]['table'].'.pets_breed = '.$value.'.pets_breed', 'left');
							}
							else
							{
								$resources['sub_query'] = $resources['sub_query']->join($value, $resources['reference_id'][$resources['query'][$i]['type_info']]['table'].'.'.$value.'_code = '.$value.'.'.$value.'_code', 'left');
							}
						}
						$resources['sub_query'] = $resources['sub_query']->where('case_id', $case_id)->get($resources['reference_id'][$resources['query'][$i]['type_info']]['table']);
						if ($resources['sub_query']->num_rows() > 0)
						{
							$resources['sub_query'] = $resources['sub_query']->result_array();
							$resources['case_additional'][] = array_reverse($resources['sub_query']);
						}
					}
					foreach ($resources['case_additional'] as $keys => $values)
					{
						$i = 0;
						$resources['case_information'] .= "<b>".$resources['reference_id'][$resources['query'][$keys]['type_info']]['title'].'</b><br>';
						foreach ($values as $key => $value)
						{
							$j = 0;
							$resources['case_information_temp'] = '';
							foreach ($value as $ks => $vs)
							{
								if (trim($vs) !== NULL && trim($vs) != '')
								{
									if ($resources['reference_id'][$resources['query'][$keys]['type_info']]['type'][$j] == 'integer')
									{
										if ($vs > 0)
										{
											$resources['case_information_temp'] .= $resources['reference_id'][$resources['query'][$keys]['type_info']]['label'][$j].number_format($vs).' ';
										}
									}
									else
									{
										$resources['case_information_temp'] .= $resources['reference_id'][$resources['query'][$keys]['type_info']]['label'][$j].$vs.' ';
									}
								}
								$j += 1;
							}
							if ($resources['case_information_temp'] != '')
							{
								$resources['case_information'] .= ($i + 1).'. '.$resources['case_information_temp'].'<br>';
							}
							$i += 1;
						}
						$resources['case_information'] .= $keys < (sizeof($resources['case_additional']) - 1) ? '<br>' : '';
					}
				}
				$this->twiggy->set('case_information', $resources['case_information'], TRUE);
				$this->twiggy->template('incident/data_extra')->display();
				unset($resources);
			}
		}
		catch (Exception $e)
		{
			echo json_encode();
		}
	}

	/**
	 * @return	Response
	 */
	public function command($case_id = NULL)
	{
		try
		{
			if ($this->common_model->get_table_exists('case') && $case_id !== NULL)
			{
				$resources = array();
				$resources['uc_users'] = $this->config_model->readConfigStd('uc_users');
				/**
				 * Command reponsible
				 */
				$resources['query'] = $this->db
					->select('department_name, police_station_name, inspection_area_name, command_name')
					->join('department', 		'case_responsible.department_code = department.department_code', 				'left')
					->join('police_station', 	'case_responsible.police_station_code = police_station.police_station_code', 	'left')
					->join('inspection_area', 	'case_responsible.inspection_area_code = inspection_area.inspection_area_code', 'left')
					->join('command', 			'case_responsible.command_code = command.command_code', 						'left')
					->where(array('case_id' => $case_id, 'responsible_value' => 1))
					->get('case_responsible');
				if ($resources['query']->num_rows() > 0)
				{
					$resources['result'] = $resources['query']->result_array();
					$resources['result'][0]['police_station_name'] = '';
					$this->twiggy->set('case_responsible', $resources['result'], TRUE);
				}
				/**
				 * Command vehicle
				 */
				$resources['case_transaction'] = '';
				$resources['query'] = $this->db
					->select('command_name, police_station_name, case_transaction.police_vehicle_code, police_vehicle_number, user_name, first_name, last_name, case_transaction.created_modify, receive_date, arrive_date, close_date, user_closed_job')
					->join('police_vehicle', 	'case_transaction.police_vehicle_code = police_vehicle.police_vehicle_code', 	'left')
					->join('command', 			'police_vehicle.command_code = command.command_code',	 						'left')
					->join('police_station', 	'police_vehicle.police_station_code = police_station.police_station_code', 		'left')
					->join('uc_users', 			'case_transaction.user_code = uc_users.id', 									'left')
					->where('case_transaction.case_id', $case_id)
					->order_by('case_transaction.id', 'ASC')
					->get('case_transaction');
				if ($resources['query']->num_rows() > 0)
				{
					$resources['case_transaction'] = $resources['query']->result_array();
					foreach ($resources['case_transaction'] as $key => $value)
					{
						$resources['case_transaction'][$key]['created_modify'] 	= $value['created_modify'] 	!== NULL ? wt_datetime_change($value['created_modify'], 2) : '';
						$resources['case_transaction'][$key]['receive_date'] 	= $value['receive_date'] 	!== NULL && $value['created_modify'] 	!== NULL 	? wt_datetime_change($value['receive_date'], 	2).' ระยะเวลา '.wt_sec_to_time(strtotime($value['receive_date']) 	- strtotime($value['created_modify'])) 	: '';
						$resources['case_transaction'][$key]['arrive_date']		= $value['arrive_date'] 	!== NULL && $value['receive_date'] 		!== NULL 	? wt_datetime_change($value['arrive_date'], 	2).' ระยะเวลา '.wt_sec_to_time(strtotime($value['arrive_date']) 	- strtotime($value['receive_date'])) 	: '';
						$resources['case_transaction'][$key]['close_date']		= $value['close_date'] 		!== NULL && $value['arrive_date'] 		!== NULL 	? wt_datetime_change($value['close_date'], 		2).' ระยะเวลา '.wt_sec_to_time(strtotime($value['close_date'])  	- strtotime($value['arrive_date'])) 	: '';
						foreach ($resources['uc_users'] as $ks => $vs)
						{
							if ($value['user_closed_job'] == $vs['user_name'] OR strtolower($value['user_closed_job']) == $vs['user_name'])
							{
								$resources['case_transaction'][$key]['user_closed_job'] = $vs['first_name'].($vs['first_name'] != '' && $vs['last_name'] != '' ? ' '.$vs['last_name'] : '');
							}
						}
					}
					$this->twiggy->set('case_transaction', $resources['case_transaction'], TRUE);
				}
				/**
				 * Command note
				 */
				$resources['query'] = $this->db
					->select('detail, modified_date, user_modify')
					->where('case_id', $case_id)
					->order_by('modified_date', 'ASC')
					->get('case_note');
				if ($resources['query']->num_rows() > 0)
				{
					$resources['case_note'] = $resources['query']->result_array();
					foreach ($resources['case_note'] as $key => $value)
					{
						$resources['case_note'][$key]['modified_date'] = $value['modified_date'] !== NULL ? wt_datetime_change($value['modified_date'], 2) : '';
						foreach ($resources['uc_users'] as $ks => $vs)
						{
							if ($value['user_modify'] == $vs['user_name'] OR strtolower($value['user_modify']) == $vs['user_name'])
							{
								$resources['case_note'][$key]['user_modify'] = $vs['first_name'].($vs['first_name'] != '' && $vs['last_name'] != '' ? ' '.$vs['last_name'] : '');
							}
						}
					}
					$this->twiggy->set('case_note', $resources['case_note'], TRUE);
				}
				$this->twiggy->template('incident/data_command')->display();
				unset($resources);
			}
		}
		catch (Exception $e)
		{
			echo json_encode();
		}
	}

}