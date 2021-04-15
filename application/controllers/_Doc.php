<?php error_reporting(E_ERROR) ;
date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Doc extends CI_Controller
{

	public function __construct()
	{ 

		parent::__construct();
		 
	}


	public function index()
	{
		//$this->load->view('welcome_message');
		 //$this->load->helper('base64_helper');
	 	 //echo "Decrypted String: " ;
	 	
		$this->load->view('MobApi_1');

	} 

}