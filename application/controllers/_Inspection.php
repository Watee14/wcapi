<?php error_reporting(E_ALL) ;
defined('BASEPATH') OR exit('No direct script access allowed');

class Inspection extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		//$this->load->model('Writelogs_model');
		//$this->load->library('Stomp');
		//include_once(_ASSETS_PATH.'Stomp.php');
		//require_once APPPATH.'third_party/Stomp.php';
		//$this->load->library('Stomp');
	}

	public function index(){
		//echo '<pre>';
		 //$area = file_get_contents(_ASSETS_PATH.'map/inspection/'.$req['police_station_code'].".txt") ; 
		//echo _ASSETS_PATH."map/data.txt" ;
		 $area = file_get_contents( "/var/www/html/191ws/assets/map/data.txt") ; 
		 $area = json_decode($area , true) ;
		 //print_r($area) ;
		 //die();
		 foreach ($area['data'] as $key => $value) {
		 	$as = file_get_contents( '/var/www/html/191ws/assets/map//inspection/'.$value['area_code'].".txt") ; 
		 	$ds = json_decode($as , true) ;
		 	foreach ($ds['data'] as $k1 => $v1) {
		 		$ds_x[] = $v1 ; 
		 	}
		 	 
		 }
		 //print_r($ds_x) ;
		  print_r( json_encode($ds_x) ) ;
	}

	 
}

