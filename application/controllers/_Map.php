<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Map extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->library('twiggy');
		$this->load->model('common_model');
		$this->load->model('config_model');
		$this->load->helper('custom');
		$this->load->helper('general');
		$this->load->helper('security');
		$this->load->helper('string');
	}

	public function index()
	{
		$this->opencase();
	}

	public function opencase()
	{
		$this->twiggy->set('element_case_id', 	getInget('c_id') 	? getInget('c_id') 	: NULL, TRUE);
		$this->twiggy->set('element_address', 	getInget('addr') 	? getInget('addr') 	: NULL, TRUE);
		$this->twiggy->set('element_latitude', 	getInget('lats') 	? getInget('lats') 	: NULL, TRUE);
		$this->twiggy->set('element_longitude', getInget('lons') 	? getInget('lons') 	: NULL, TRUE);
		$this->twiggy->set('_RADIUS', 			defined('_RADIUS') 	? _RADIUS 			: NULL, TRUE);
		$this->twiggy->set('session_user', 		isset($_SESSION) 	? $_SESSION 		: NULL, TRUE);
		if (isset($_REQUEST['token']))
		{
			$path = json_decode(base64_decode(urldecode($_REQUEST['token'])), TRUE);
			$BASE_LOCATION_REQUEST = file_get_contents('../logs/191nn/reporter/'.$path['mobileNo']);
			if ($BASE_LOCATION_REQUEST === FALSE)
			{
				$BASE_LOCATION_REQUEST = '';
			}
			$this->twiggy->set('BASE_LOCATION_REQUEST', $BASE_LOCATION_REQUEST, TRUE);
		}
		$this->twiggy->template('map/case/opencase')->display();
	}

	public function thiscase()
	{
		try
		{
			if (getInget('case_id'))
			{
				$thiscase = rowArray($this->common_model->get_where_custom_field('case', 'case_id', getInget('case_id'), 'case_lat AS lat, case_lon AS lon, case_direction AS dir'));
				echo json_encode(count($thiscase) > 0 ? $thiscase : array());
			}
		}
		catch (Exception $e)
		{
			echo json_encode();
		}
	}

	public function blenc(){
		try{
			if(isset($_REQUEST['mobileNo']))
				echo urlencode(base64_encode(json_encode(['mobileNo'=>$_REQUEST['mobileNo']])));
			else
				throw new Exception();
		}
		catch(Exception $e){
			echo '';
		}
	}

	public function blreq(){
		try{
			if(isset($_REQUEST['token'])){
				$path=json_decode(base64_decode(urldecode($_REQUEST['token'])),TRUE);
				$BASE_LOCATION_REQUEST=file_get_contents('../logs/191nn/reporter/'.$path['mobileNo']);
				if($BASE_LOCATION_REQUEST!==FALSE){
					$BLR=json_decode($BASE_LOCATION_REQUEST);
					echo json_encode(['status'=>1,'message'=>'ได้รับข้อมูลพิกัดแล้ว','lat'=>$BLR->lat,'lon'=>$BLR->lon]);
				}
				else
					throw new Exception();
			}
			else
				throw new Exception();
		}
		catch(Exception $e){
			echo json_encode(['status'=>0,'message'=>'','lat'=>'','lon'=>'']);
		}
	}

}