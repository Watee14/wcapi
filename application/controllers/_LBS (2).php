<?php error_reporting(E_ERROR) ;
//header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class LBS extends CI_Controller
{


	public function index()
	{
	

		$this->load->view('LBS_v1');

	}




}