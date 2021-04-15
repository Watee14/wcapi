<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SMHMap extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->library('twiggy');
		$this->load->model('common_model');
		$this->load->model('config_model');
 
	}

	public function mapTracking( $latlon = "" , $smhID=""   )
	{
		//echo $latlon.'--'.$smhID ;
		if($smhID!=""){
			error_reporting(E_ERROR) ;
			$strLL = explode("_", $latlon ) ;
			$this->twiggy->set('_smh_lat', 	  $strLL[0] );
			$this->twiggy->set('_smh_lon',   $strLL[1]  );

			$this->db->select('*');
			$this->db->from('smh_job'); 
			$this->db->where('smh_job_code =',  $smhID );
			 
			$data = $this->db->get()->result_array();
			$data = $data[0] ;
			//print_r($data) ;
			
			$this->twiggy->set('start_lat',   $data['start_lat'] );
			$this->twiggy->set('start_lon',   $data['start_lon']  );
			$this->twiggy->set('end_lat',   $data['end_lat'] );
			$this->twiggy->set('end_lon',   $data['end_lon']  );
			$this->twiggy->set('stage',   "view"  );

			$line = file_get_contents(_SMH_DATA.'\\lines\\'.$smhID  ) ;
			$ds_res = json_decode($line,true) ;
			$lineString = [] ;
			if($ds_res['properties']){
				 
				
				foreach ($ds_res['features'] as $k1 => $v1) {
					foreach ($v1['geometry']['coordinates'] as $k2 => $v2) {
						$ll = [];
						$ll['lon'] = $v2[0]  ;
						$ll['lat'] = $v2[1]  ;
						$lineString[] = $ll ;
					}
					//$lineString[] = $v1 ;
				}
			}
			//file_put_contents(_SMH_LINE.'/'.$req['smh_job_code'] , $resultLn) ;
			//$req['lineString'] = $lineString ;
			$this->twiggy->set('lineString',   json_encode( $lineString )   );
			$this->twiggy->template('map/tracking/mapTrack')->display();
			//print_r($lineString) ;

		}else{
			$strLL = explode("_", $latlon ) ;
			//print_r( $smhID ) ; 
			$this->twiggy->set('_smh_lat', 	  $strLL[0] );
			$this->twiggy->set('_smh_lon',   $strLL[1]  );
			$this->twiggy->template('map/tracking/mapTrack')->display();
		}
		
	}

	 

}