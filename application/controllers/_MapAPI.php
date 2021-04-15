<?php session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class MapAPI extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// if( ! $this->session->userdata('logged_in'))
		// {
		// 	redirect('/login', 'refresh');
		// 	exit();
		// }
        $this->load->model('Writelogs_model');
	}

	public function index()
	{
        echo 'xxxxx'; die();
		// $this->twiggy->template('account/account')->display();
	}

	public function address($lat = "", $lon = "")
	{
		// echo _MAP_SERVICE;

		$req = $_REQUEST; 
		$url = _MAP_ADDRESS."?lat=".$req['lat']."&lon=".$req['lon']."&lang=th&key="._MAP_KEY;
		$result = $this->curlService($url, '', 'GET');
		print_r($result);
	}

	public function search($q = "")
	{
		$req = $_REQUEST;
		$url = _MAP_SEARCH."?keyword=".$req['q'];
		$result = $this->curlService($url, '', 'GET');
		print_r($result);
	}

	public function searchSuggest($q = "")
    {
          //echo "<pre>";
        // echo _ASSETS_URL.'/icon/7-11.png';
        // die();
        $time_start = microtime(true);

        $req = $_REQUEST;
        $req['q'] = $_REQUEST['term'];





        //################### Search LONGDO
        $time_start_B = microtime(true);
        //$ds ='' ;
        //echo '<pre>';
        //$req['q'] .'---';
        //$req['q'] = str_replace(' ', '%20', $req['q'] );

       /* 10 - BKK
        13 - Pathumthani
        73 - Nakornpathom
        11 - Samutprakarn
        12 - Nonthaburi
        75 - Samutsongkram*/

        ///### dataset
        $dsx[0] = 'poi_r' ;
        $dsx[1] = 'poi_p,poi2' ;
        $dataset = $_REQUEST['dataset'];
        $ds_set = explode(',', $dataset) ;
        $datas_ ;
        foreach ($ds_set as $k1 => $v1) {
            $datas_[] = $dsx[$v1] ;
        }



        //$url = _MAP_SUGGEST."?keyword=".urlencode( $req['q'] ).'&area=10,11,12,13,73,75&dataset=poi_r';

        $url = _MAP_SUGGEST."?keyword=".urlencode( $req['q'] ).'&dataset='.implode(',', $datas_ ).'&limit=10';
        $result = $this->curlService($url, '', 'GET');
        $ds_all = json_decode($result, TRUE);
        //echo '<pre>'; print_r( $ds_all) ;
        //echo '<pre>';
        foreach ($ds_all['data'] as $key => $value)
        {

            $v1 = [];
            //$v1['id'] = $value['id'];
            //$value['label'] = '<img src="'._ASSETS_URL.'/icon/'.$value['icon'].'" width="36"><p>'.$value['name'].'</p><p>'.$value['address'].'</p>';
            $v1['value'] = $value['w']   ;
            $v1['type'] = "poi";
            $v1['from'] = 'Longdo_suggest';
            $v1['name'] = $value['w'];
            //print_r( $v1) ;
            //$v1['lat'] = $value['lat'];
            //$v1['lon'] = $value['lon'];
            //$v1['icon'] = $value['icon'];
            //$v1['address'] = $value['address'];
             //print_r( $value['w']) ;
            $ds[] = $v1 ;
            /*if(isset($value['address'])){
                $ds[ $value['id'].'_longdo' ] = $v1;
            }*/

        }
        $status=0;
        $ltype = 'high';
        if(isset($ds_all['meta']['keyword'])){
             $status=1 ;
             $ltype = 'info';
        }

        $time_end_B = microtime(true);
        $duration_B = $time_end_B - $time_start_B;
        $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , $ltype , 'searchSuggest', 'longdo' , $duration_B  , $status  , $url , count($ds_all) , 'Map_API'  );


        //echo '<pre>'; print_r( $ds) ; die();



        //################### Search LIKE
        $soundex_enable = true ;
        $thSplit = 0 ;
        if( $soundex_enable == true){


            //################### Split word
            /*include(  _ASSETS_PATH.'/THSplitLib/segment.php');
            $time_start_spl = microtime(true);

            $segment = new Segment();

            $thSplit = $segment->get_segment_array( $req['q'] );
            $status=0;
            $ltype = 'high';
            $sd = '';
            if(count($thSplit)>0){
                 $status=1 ;
                 $ltype = 'info';
            }
            $time_end_S = microtime(true);
            $duration_S = $time_end_S - $time_start_spl;
            $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , $ltype , 'Soundex', 'splitWord' , $duration_S  , $status  , $req['q'] , implode(' ',$thSplit )  , 'Map_API'  );*/

            //print_r($thSplit); die();
            //echo 'Split: '.$time_start_spl.' -- '.$time_end_S." --> Duration:".$duration_S.'<br>';
            //echo $req['q'] .' --> '.implode(' ',$thSplit ).'<br>';






            $time_start_A = microtime(true);
            //$cs = split(' ',  $sounddex['SoundexNew'] ) ;
            //$ds_all['meta']['soundex'] = $sounddex['SoundexNew'];
            //$ds_all['meta']['romandex'] = $sounddex['Romandex'];
            $this->db->select(' poi_type_code, map_type_icon, poi_type_name ');
            $this->db->from('map_type');
            $query = $this->db->get()->result_array();
            foreach ($query as $key => $value)
            {
                $value['from'] = 'Longdo';
                $icon[$value['poi_type_code']] = $value;
            }

            //if( count($cs)>1 ){
            if( count($thSplit)>1 ){

                 //################### Soundex
                $time_start_B = microtime(true);
                //$json = '{"words" : "'.$req['q'].'"}';
                $json = '{"words" : "'.implode(' ',$thSplit ).'"}';
                $sounddex_x = $this->curlService_2(_SOUNDDEX, $json, "POST");
                $sounddex = json_decode($sounddex_x, TRUE);
                //echo _SOUNDDEX ;
                /* echo 'Soundex : '.$time_start_B.' -- '.$time_end_S." --> Duration:".$duration_S.'<br>';
                echo $json .' --> '.$sounddex_x.'<br>';
                die();*/

                $status=0;
                $ltype = 'high';
                $sd = '';
                if(isset($sounddex['SoundexNew'])){
                     $status=1 ;
                     $ltype = 'info';
                     $sd = $sounddex_x ;
                }
                $time_end_S = microtime(true);
                $duration_S = $time_end_S - $time_start_B;
                $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , $ltype , 'Soundex', 'chkData' , $duration_S  , $status  , $json , $sd  , 'Map_API'  );

                $sqlx = "select distinct --t1.poi_soundex_alias1,t1.poi_soundex_name,t2.value,
                t1.* from dbo.map_poi t1
                left outer join (SELECT value FROM STRING_SPLIT('".$sounddex['SoundexNew']."', ' '))t2
                on t1.poi_soundex_alias1 like '%'+t2.value+'%' or t1.poi_soundex_name like '%'+t2.value+'%'
                where isnull(t2.value,'')<>''  ";

                $sqlx = "select poi_code , poi_type_code ,  poi_name , poi_alias1 , poi_address , poi_status , poi_lat ,poi_lon,poi_soundex_name,poi_soundex_alias1,poi_line
                FROM   map_poi WHERE poi_status=1 and ( poi_name like '%".$req['q']."%' or poi_alias1 like '%".$req['q']."%' or poi_soundex_name like '%".$sounddex['SoundexNew']."%' or poi_soundex_alias1 like '%".$sounddex['SoundexNew']."%' ) ";

                //print_r($query) ;
                $query = $this->db->query($sqlx);
                $rec = 0 ;
                foreach ($query->result() as $dx)
                {
                    $v1 = [] ;
                    $v1['type'] = 'poi' ;
                    $v1['id'] = $dx->poi_code;
                    $v1['name'] = $dx->poi_name;
                    $v1['lat'] = (float)$dx->poi_lat  ;
                    $v1['lon'] = (float)$dx->poi_lon  ;
                    $v1['icon'] = $icon[ $dx->poi_type_code ]['map_type_icon'];
                    $v1['address'] =  '<i class="fa fa-star-o" aria-hidden="true"></i> '.$dx->poi_address ;
                    $v1['from'] = 'CV2' ;
                    $v1['value'] = $dx->poi_name;
                    $ds[] = $v1 ;
                    $rec++;

                }

                $status=0;
                $ltype = 'high';
                if(isset($sounddex['SoundexNew'])){
                     $status=1 ;
                     $ltype = 'info';
                }

                $time_end_A = microtime(true);
                $duration_A = $time_end_A - $time_start_A;
                $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , $ltype , 'searchSuggest', 'poi1' , $duration_A  , $status  , $json , $rec , 'Map_API'  );


            }else{

                $this->db->select( 'poi_code , poi_type_code ,  poi_name , poi_alias1 , poi_address , poi_status , poi_lat ,poi_lon,poi_soundex_name,poi_soundex_alias1 , poi_line');
                $this->db->from('map_poi');
                $this->db->where('poi_status =' , 1  );
                $this->db->like('poi_name', $req['q']  );
                $this->db->or_like('poi_alias1', $req['q']  );

                $query = $this->db->get()->result_array();

                $rec = 0 ;
                foreach ($query as $key => $value) {
                    $v1 = [] ;
                    $v1['type'] = 'poi' ;
                    $v1['id'] = $value['poi_code'] ;
                    $v1['name'] = $value['poi_name']  ;
                    $v1['lat'] = (float)$value['poi_lat'] ;
                    $v1['lon'] = (float)$value['poi_lon'] ;
                    $v1['icon'] = $icon[ $value['poi_type_code'] ]['map_type_icon'];
                    $v1['address'] =  '<i class="fa fa-star-o" aria-hidden="true"></i> '.$value['poi_address'] ;
                    $v1['from'] = 'CV1' ;
                    $v1['value'] = $value['poi_name'] ;
                    if($value['poi_line']!='' && $value['poi_line']!='[]'){
                        $v1['poi_line'] = $value['poi_line'] ;
                    }else{
                        $v1['poi_line'] = '' ;
                    }
                    
                    $ds[] = $v1 ;
                    $rec++;

                }

                $status=0;
                $ltype = 'high';
                if( count($query)> 0 ){
                     $status=1 ;
                     $ltype = 'info';
                }


                $time_end_A = microtime(true);
                $duration_A = $time_end_A - $time_start_A;
                $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , $ltype , 'searchSuggest', 'poi2' , $duration_A  , $status , $json , $rec , 'Map_API'  );

            }

        }




        foreach ($ds as $key => $value)
        {
            if($value['name']!='' & $value['name']!= undefined){
                $ds_x[] = $value;
            }

        }

        /*$status=0;
        $ltype = 'high';
        if(isset($sounddex['SoundexNew'])){
             $status=1 ;
             $ltype = 'info';
        }
        $time_end = microtime(true);
        $duration = $time_end - $time_start;
        $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , 'low', 'searchSuggest', 'summary_process' , $duration  , $status , $json , count($ds_x)  , 'Map_API'  );
*/
        //echo  '<pre>';
        print_r( json_encode($ds_x));
        
    }

    public function searchQ($q = "")
    {
         //echo "<pre>";
        // echo _ASSETS_URL.'/icon/7-11.png';
        // die();
        $time_start = microtime(true);

        $req = $_REQUEST;
        //$req['q'] = $_REQUEST['term'];




        //################### Search LONGDO
        $time_start_B = microtime(true);
        //$ds ='' ;
        //echo '<pre>';
        //echo $req['q'].'---' ;
        //$req['q'] = str_replace(' ', '%20', $req['q'] );
        //echo  '<br>';
        $url = _MAP_SEARCH."?keyword=".urlencode( $req['q'] ).'&limit=1000'  ;
        $result = $this->curlService($url, '', 'GET');
        $ds_all = json_decode($result, TRUE);
        foreach ($ds_all['data'] as $key => $value)
        {
            $v1 = [] ;
            $v1['id'] = $value['id'];
            //$value['label'] = '<img src="'._ASSETS_URL.'/icon/'.$value['icon'].'" width="36"><p>'.$value['name'].'</p><p>'.$value['address'].'</p>';
            $v1['value'] = $value['name']   ;
            $v1['type'] = "poi";
            $v1['from'] = 'Longdo';
            $v1['name'] = $value['name'];
            $v1['lat'] = $value['lat'];
            $v1['lon'] = $value['lon'];
            $v1['icon'] = $value['icon'];
            $v1['address'] = $value['address'];

            if(isset($value['address'])){
                 $ds[ $value['id'].'_longdo' ] = $v1;
            }

        }

        $status=0;
        $ltype = 'high';
        if(isset($ds_all['meta']['keyword'])){
             $status=1 ;
             $ltype = 'info';
        }

        $time_end_B = microtime(true);
        $duration_B = $time_end_B - $time_start_B;
        $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , $ltype , 'searchQ', 'longdo' , $duration_B  , $status , $url , count($ds_all) , 'Map_API'  );


        //print_r($ds) ; die();

        //################### Search CROSS WORD
        $time_start_A = microtime(true);
        $this->db->select(' poi_type_code, map_type_icon, poi_type_name ');
        $this->db->from('map_type');
        $query = $this->db->get()->result_array();
        foreach ($query as $key => $value)
        {
            $value['from'] = 'Longdo';
            $icon[$value['poi_type_code']] = $value;
        }
        
        $this->db->select( 'poi_code , poi_type_code ,  poi_name , poi_alias1 , poi_address , poi_status , poi_lat ,poi_lon,poi_soundex_name,poi_soundex_alias1 , poi_line');
        $this->db->from('map_poi');
        $this->db->where('poi_status =' , 1  );
        $this->db->like('poi_name', $req['q']  );
        $this->db->or_like('poi_alias1', $req['q']  );

        $query = $this->db->get()->result_array();

        $rec = 0 ;
        foreach ($query as $key => $value) {
            $v1 = [] ;
            $v1['type'] = 'poi' ;
            $v1['id'] = $value['poi_code'] ;
            $v1['name'] = $value['poi_name']  ;
            $v1['lat'] = (float)$value['poi_lat'] ;
            $v1['lon'] = (float)$value['poi_lon'] ;
            $v1['icon'] = $icon[ $value['poi_type_code'] ]['map_type_icon'];
            $v1['address'] =  '<i class="fa fa-star-o" aria-hidden="true"></i> '.$value['poi_address'] ;
            $v1['from'] = 'CV1' ;
            $v1['value'] = $value['poi_name'] ;
            if($value['poi_line']!='' && $value['poi_line']!='[]'){
                $v1['poi_line'] = $value['poi_line'] ;
            }else{
                $v1['poi_line'] = '' ;
            }
            
            $ds[] = $v1 ;
            $rec++;

        }

        $status=0;
        $ltype = 'high';
        if( count($query)> 0 ){
             $status=1 ;
             $ltype = 'info';
        }


        $time_end_A = microtime(true);
        $duration_A = $time_end_A - $time_start_A;
        $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , $ltype , 'searchQ', 'poi2' , $duration_A  , $status , $json , $rec , 'Map_API'  );


        //print_r($ds) ; die();

        foreach ($ds as $key => $value)
        {
            $ds_x[] = $value;
        }
       /* $ds_all['data'] = $ds_x;
        $ds_all['meta']['end'] = count($ds) - 1;
        if (count($ds) > 0)
        {
            $ds_all['meta']['hasmore'] = TRUE;
        }*/

        //print_r(json_encode($ds_all));


       /* $time_end = microtime(true);
        $duration = $time_end - $time_start;
        $this->Writelogs_model->wLogs( $_SESSION['user']->user_name , $_SERVER['REMOTE_ADDR'] , 'low', 'searchSuggest', 'summary_process' , $duration  , '' , json_encode($sounddex) , count($ds_x)   , 'Map_API'  );
*/
        print_r( json_encode($ds_x));
        die();
        
    }


	public function nearby($q = "")
	{
		// echo '<pre>';

		$poi_type = explode(',', _POI_ALLOW);
		$req = $_REQUEST;

		// print_r($res);

		$url = _MAP_SERVICE."?span=0.01&zoom=15&lon=".$req['lon']."&lat=".$req['lat']."&limit="._MAP_LIMIT."&locale="._MAP_LOCALE."&key="._MAP_KEY."&span=850";
		$result = $this->curlService($url, '', 'GET');
		$result = json_decode($result, TRUE);
		foreach ($result['data'] as $key => $value)
		{
			// print_r($value);

			if (isset($value['tag']))
			{
				foreach ($value['tag'] as $k => $y)
				{
					if (in_array($y, $poi_type))
					{
						$value['type'] = 'nearby';
						$res[$value['id']] = $value;
					}
				}
			}
		}
		foreach ($res as $key => $value)
		{
			$resx[] = $value;
		}
		$result['data'] = $resx;
		print_r(json_encode($result));
	}

   	public function policeCar($q = "")
	{
		$this->db->select('police_vehicle_code,police_vehicle_number,police_vehicle_type,department_code,command_code,police_station_code,police_vehicle_status_code,device_name,police_vehicle_lat,police_vehicle_lon');
		$this->db->from('police_vehicle');

		// $this->db->where('case_id =', $case_id);

		$result = $this->db->get()->result_array();
		foreach ($result as $key => $value)
		{
			$value['case'] = [];

			// echo $value['police_vehicle_code'];

			if ($value['police_vehicle_status_code'] != '001' && $value['police_vehicle_status_code'] != '006')
			{
				$this->db->select('case_id,case_status_code');
				$this->db->from('case_transaction');
				$this->db->where('police_vehicle_code =', $value['police_vehicle_code']);
				$result2 = $this->db->get()->result_array();
				if (count($result2) > 0)
				{
					$value['case'] = $result2;
				}
			}
			$value['type'] = 'police_car';
			$dsx[] = $value;
		}
		$ret['status'] = 0;
		$ret['data'] = [];
		if (count($dsx) > 0)
		{
			$ret['data'] = $dsx;
		}
		print_r(json_encode($ret));
	}

	public function duplicateCase()
	{
		// echo '<pre>';
		// print_r($_SESSION);
		// die();

		$req = $_REQUEST;

		// caseType

		$caseType_code = $req['casetype_code'];
		$userCode = $_SESSION['user']->user_code;
		$casetype_name = $this->config_model->readConfig('casetype');
		$case_status_name = $this->config_model->readConfig('case_status');

		// $user = $this->Config_model->userPermission($userCode, '', 'All');
		// print_r($casetype_name);
		// die();

		$this->db->select('case_id, casetype_code, created_date, case_location_address, case_status_code, case_lat, case_lon');
		$this->db->where('casetype_code =', $caseType_code);
		$this->db->from('case');
		$id_not_in = array('007', '013', '014', '015');
		$this->db->where_not_in('case_status_code', $id_not_in);

		// $this->db->where('created_date BETWEEN \''.date('Y-m-d H:i:s', (time() - 3600)).'\' and \''.date('Y-m-d H:i:s', time()).'\'');
		// $this->db->where("created_date BETWEEN '".date('Y-m-d H:i:s', (time() - 3600))."' and '".date('Y-m-d H:i:s', time())."'");

		$this->db->where("created_date BETWEEN '".date('Y-m-d H:i:s', (time() - 360000))."' and '".date('Y-m-d H:i:s', time())."'");
		$result = $this->db->get()->result_array();
		$ds = [];
		foreach ($result as $key => $value)
		{
			$value['casetype_name'] = $casetype_name[$value['casetype_code']]['casetype_name'];
			$value['case_status_name'] = $case_status_name[$value['case_status_code']]['case_status_name'];
			$ds[] = $value;
		}
		$ds_x['lat'] = $req['lat'];
		$ds_x['lon'] = $req['lon'];
		$ds_x['casetype_code'] = $caseType_code;
		$ds_x['data'] = $ds;
		print_r(json_encode($ds_x));
	}

	function curlService($url, $data, $method)
	{
		$ch = curl_init();
		$url = $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		// curl_setopt($ch,CURLOPT_HEADER, TRUE);

		$response = curl_exec($ch);

		// print_r($response);

		return $response;
	}

	function curlService_2($url, $data, $method)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json;charset=UTF-8',
			'Content-Length: '.strlen($data))
		);
		$response = curl_exec($ch);

		// print_r($response);

		return $response;
	}

}