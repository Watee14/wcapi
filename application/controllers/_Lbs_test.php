<?php
defined('BASEPATH')OR exit('No direct script access allowed');
class Lbs_test extends CI_Controller{
	var $def_ani;
	var $def_header;
	var $def_loc;
	var $def_url;
	public function __construct(){
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->helper('general');
		$this->load->library('session');
		$this->load->library('twiggy');
		$this->load->model('config_model');
		$this->load->model('CryptoJS');
		$this->def_ani='0858655102';
		$this->def_header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
		//$this->def_loc=['https'=>['lat'=>'55.85732','lon'=>'-4.26325'],'sms'=>['lat'=>'55.85732','lon'=>'-4.26325']];
		//$this->def_loc=['lat'=>'55.85732','lon'=>'-4.26325'];
		$this->def_loc=['lat'=>'13.756653','lon'=>'100.501857'];
		$this->def_path=['lbs2cad'=>_LOGS.'/191nn/lbs2cad/'];
		$this->def_url=[
			'addr'=>base_url('V2/address/'),
			'incident'=>base_url('lbs/incident'),
			'login'=>base_url('V2/login'),
			'sms'=>base_url('lbs/pull/sms'),
			'subscribe'=>base_url('V2/subScribe'),
			'subscriber'=>base_url('lbs/subscriber'),
		];
		if($_SERVER['SERVER_NAME']=='203.170.193.91'){
			//$this->def_url['aml']='http://43.255.240.22:8500/aml';
			//$this->def_url['cad']='http://43.255.240.22:8500/cad';
			$this->def_url['aml']='http://147.50.7.36:8005/aml';
			$this->def_url['cad']='http://147.50.7.36:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.8.57'){
			$this->def_url['aml']='http://192.168.8.57:8005/aml';
			$this->def_url['cad']='http://192.168.8.57:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.108.57'){
			$this->def_url['aml']='http://192.168.108.57:8005/aml';
			$this->def_url['cad']='http://192.168.108.57:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='147.50.7.36'){
			$this->def_url['aml']='http://192.168.8.57:8005/aml';
			$this->def_url['cad']='http://192.168.8.57:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.101.16'){
			$this->def_url['aml']='http://192.168.101.16:8005/aml';
			$this->def_url['cad']='http://192.168.101.16:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='aml.191poc.local'){
			//$this->def_url['aml']='http://aml.191poc.local:8005/aml';
			//$this->def_url['cad']='http://aml.191poc.local:8005/cad';
			$this->def_url['aml']='http://127.0.0.1:8005/aml';
			$this->def_url['cad']='http://127.0.0.1:8005/cad';
		}
		else if($_SERVER['SERVER_NAME']=='192.168.101.46'){
			$this->def_url['aml']='http://192.168.101.46:8005/aml';
			$this->def_url['cad']='http://192.168.101.46:8005/cad';
		}
	}
	public function index(){
		try{
			$def_index=[
				['href'=>'http://203.170.193.91/191ws/lbs','text'=>'Link #1 - 203.170.193.91'],
				['href'=>'http://192.168.8.57/191ws/lbs','text'=>'Link #2 - 192.168.8.57 (VPN)'],
				['href'=>'http://192.168.108.57/191ws/lbs','text'=>'Link #3 - 192.168.108.57 (VPN)'],
				['href'=>'http://147.50.7.36/191ws/lbs','text'=>'Link #4 - 147.50.7.36'],
				['href'=>'http://192.168.101.16/191ws/lbs','text'=>'Link #5-DC1 - 192.168.101.16'],
				['href'=>'http://192.168.101.46/191ws/lbs','text'=>'Link #5-DC2 - 192.168.101.46'],
			];
			$this->twiggy->set('def_index',$def_index,TRUE);
			$this->twiggy->template('lbs/index')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function addr_extract($str=''){
		try{
			if($str!=''){
				$addr=explode(',',$str);
				$addr_cnt=count($addr);
				$pve_pce=['',''];
				if($addr_cnt>1)
					$pve_pce=explode(' ',trim($addr[$addr_cnt-2]));
				$pve_pce_cnt=count($pve_pce);
				$pve='';
				foreach($pve_pce as $key=>$value)
					$pve.=($key<$pve_pce_cnt-1?$value.' ':'');
				$pce='';
				if($pve_pce_cnt>1)
					$pce=$pve_pce[$pve_pce_cnt-1];
				$result=[];
				$result[]=$addr_cnt>0?trim($addr[0]):'';
				$result[]=$addr_cnt>4?trim($addr[$addr_cnt-4]):'';
				$result[]=$addr_cnt>3?trim($addr[$addr_cnt-3]):'';
				$result[]=trim($pve);
				$result[]=trim($pce);
				$result[]=$addr_cnt>1?trim($addr[$addr_cnt-1]):'';
				return $result;
			}
			else
				throw new Exception();
		}
		catch(Exception $e){
			return '';
		}
	}
	public function aml(){
		try{
			$def_ani=$this->def_ani;
			$def_url=$this->def_url;
			$def_ipt=[
				//'https'=>[
					//'v=3&device_number=%2B447477593102&location_latitude=55.85732&location_longitude=-4.26325&location_time=1476189444435&location_accuracy=10.4&location_source=GPS&location_certainty=83&location_altitude=0.0&location_floor=5&device_model=ABC+ABC+Detente+530&device_imei=354773072099116&device_imsi=234159176307582&device_os=AOS&cell_carrier=&cell_home_mcc=234&cell_home_mnc=15&cell_network_mcc=234&cell_network_mnc=15&cell_id=0213454321'
				//],
				'https'=>[
					'v=3&device_number=+66858655102&location_latitude=55.85732&location_longitude=-4.26325&location_time='.round(microtime(TRUE)*1000).'&location_accuracy=10.4&location_source=GPS&location_certainty=83&location_altitude=0.0&location_floor=5&device_model=ABC+ABC+Detente+530&device_imei=354773072099116&device_imsi=520030858655102&device_os=AOS&cell_carrier=&cell_home_mcc=234&cell_home_mnc=15&cell_network_mcc=234&cell_network_mnc=15&cell_id=0213454321'
				],
				//'sms'=>[
					//'AML=1;lt=+54.76397;lg=0.18305;rd=50;top=20130717141935;lc=90;pm=W;si=123456789012345;ei=1234567890123456;mcc=234;mnc=30;ml=128',
					//'AML=1;lt=+55.74297;lg=-4.26880;rd=10;top=20130717175329;lc=95;pm=G;si=234302543446355;ei=356708041746734;mcc=234;mnc=30;ml=127'
				//]
				'sms'=>[
					'A"ML=1;lt=+54.76397;lg=0.18305;rd=50;top='.date('YmdHis').';lc=90;pm=W;si=520030858655102;ei=1234567890123456;mcc=234;mnc=30;ml=128',
					'A"ML=1;lt=+55.74297;lg=-4.26880;rd=10;top='.date('YmdHis').';lc=95;pm=G;si=520030858655102;ei=356708041746734;mcc=234;mnc=30;ml=127'
				]
			];
			$def_loc=$this->def_loc;
			$def_msg=json_encode([
				//'https'=>'v=3&device_number=%2B447477593102&location_latitude=[lat]&location_longitude=[lon]&location_time=1476189444435&location_accuracy=10.4&location_source=GPS&location_certainty=83&location_altitude=0.0&location_floor=5&device_model=ABC+ABC+Detente+530&device_imei=354773072099116&device_imsi=234159176307582&device_os=AOS&cell_carrier=&cell_home_mcc=234&cell_home_mnc=15&cell_network_mcc=234&cell_network_mnc=15&cell_id=0213454321',
				'https'=>'v=3&device_number=[dn]&location_latitude=[lat]&location_longitude=[lon]&location_time='.round(microtime(TRUE)*1000).'&location_accuracy=10.4&location_source=GPS&location_certainty=83&location_altitude=0.0&location_floor=5&device_model=ABC+ABC+Detente+530&device_imei=354773072099116&device_imsi=[si]&device_os=AOS&cell_carrier=&cell_home_mcc=234&cell_home_mnc=15&cell_network_mcc=234&cell_network_mnc=15&cell_id=0213454321',
				//'sms'=>'AML=1;lt=[lat];lg=[lon];rd=50;top=20130717141935;lc=90;pm=W;si=123456789012345;ei=1234567890123456;mcc=234;mnc=30;ml=128'
				'sms'=>'A"ML=1;lt=[lat];lg=[lon];rd=50;top='.date('YmdHis').';lc=90;pm=W;si=[si];ei=1234567890123456;mcc=234;mnc=30;ml=128'
			]);
			$this->twiggy->set('def_ani',$def_ani,TRUE);
			$this->twiggy->set('def_url',$def_url,TRUE);
			$this->twiggy->set('def_ipt',$def_ipt,TRUE);
			$this->twiggy->set('def_loc',$def_loc,TRUE);
			$this->twiggy->set('def_msg',$def_msg,TRUE);
			$this->twiggy->template('lbs/aml')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function cad(){
		try{
			$def_ani=$this->def_ani;
			//$def_loc=$this->def_loc['sms'];
			$def_loc=$this->def_loc;
			$def_url=$this->def_url;
			$this->twiggy->set('def_ani',$def_ani,TRUE);
			$this->twiggy->set('def_loc',$def_loc,TRUE);
			$this->twiggy->set('def_url',$def_url,TRUE);
			$this->twiggy->template('lbs/cad')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function config(){
		try{
			$this->twiggy->template('lbs/config')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function config_event($methods=NULL){
		try{
			$DB1=$this->load->database('mysql',TRUE);
			if(isset($_POST)&&!empty($_POST)){
				if($methods=='post'){
					$query=$DB1->insert('eventTypeGrp',$_POST);
					if($query)
						echo json_encode(['status'=>1,'message'=>'Adding successfully.']);
					else{
						$error=$DB1->error();
						throw new Exception('Adding fail, '.$error['message']);
					}
				}
				else if($methods=='put'){
					$id=$_POST['id'];
					unset($_POST['id']);
					$query=$DB1->where('id',$id)->update('eventTypeGrp',$_POST);
					if($query)
						echo json_encode(['status'=>1,'message'=>'Updating successfully.']);
					else{
						$error=$DB1->error();
						throw new Exception('Updating fail, '.$error['message']);
					}
				}
				else if($methods=='delete'){
					$id=$_POST['id'];
					unset($_POST['id']);
					$query=$DB1->where('id',$id)->delete('eventTypeGrp');
					if($query)
						echo json_encode(['status'=>1,'message'=>'Deleting successfully.']);
					else{
						$error=$DB1->error();
						throw new Exception('Deleting fail, '.$error['message']);
					}
				}
				else
					throw new Exception();
			}
			else if(isset($_GET)){
				$result=[];
				$query=$DB1->get('eventTypeGrp');
				if($query)
					$result['eventTypeGrp']=$query->result_array();
				else
					throw new Exception();
				$tmp_TypeSubtypes=[];
				$query=file_get_contents('/var/www/html/config/hex_TypeSubtypes');
				$tmp_result=json_decode($query);
				foreach($tmp_result as $keys=>$values){
					foreach($values as $key=>$value){
						foreach($value as $ks=>$vs)
							$tmp_TypeSubtypes[$vs->typeCode]=$vs->description;
					}
				}
				ksort($tmp_TypeSubtypes);
				$hex_TypeSubtypes=[];
				foreach($tmp_TypeSubtypes as $key=>$value){
					$hex_TypeSubtypes[]=['typeCode'=>$key,'description'=>$value];
				}
				$result['hex_TypeSubtypes']=$hex_TypeSubtypes;
				$get_config=file_get_contents('/home/cv/adaptorHex/config.properties');
				$arr_config=explode(PHP_EOL,$get_config);
				$var_config=['HEX_DISTANCE','HEX_MAX_EVENT_CROSS','HEX_MAX_EVENT_CROSS_COUNT','HEX_MAX_EVENT_TYPE','HEX_BACKWARD_TIME'];
				$val_config=[];
				foreach($arr_config as$keys=>$values){
					foreach($var_config as$key=>$value){
						if(preg_match_all('/\b'.$value.'\b/',$values,$matches,PREG_OFFSET_CAPTURE)&&strpos($values,'#')===FALSE){
							$tmp=explode(' = ',$values);
							$val_config[]=['id'=>$value,'value'=>intval($tmp[1])];
						}
					}
				}
				$result['config']=$val_config;
				echo json_encode($result);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){
			echo json_encode(['status'=>-1,'message'=>$e->getMessage()]);
		}
	}
	public function config_event_global($methods=NULL){
		try{
			if(isset($_POST)&&!empty($_POST)){
				if($methods=='put'){
					$config_copy=copy('/home/cv/adaptorHex/config.properties','/home/cv/adaptorHex/config.properties_'.date('Ymd').'_'.time());
					if($config_copy){
						$get_config=file_get_contents('/home/cv/adaptorHex/config.properties');
						$arr_config=explode(PHP_EOL,$get_config);
						$post_val=$arr_config;
						$post_var=$_POST;
						foreach($arr_config as$keys=>$values){
							foreach($post_var as$key=>$value){
								if(preg_match_all('/\b'.$key.'\b/',$values,$matches,PREG_OFFSET_CAPTURE)&&strpos($values,'#')===FALSE)
									$post_val[$keys]=$key.' = '.$value;
							}
						}
						$str_val=implode(PHP_EOL,$post_val);
						$query=file_put_contents('/home/cv/adaptorHex/config.properties',$str_val);
						if($query){
							exec("sudo kill -9 $( ps ax | grep adaptorHex | fgrep -v grep | awk '{ print $1 }' )",$output,$retval);
							$outputs=0;
							while($outputs<5){
								exec("ps aux | grep adaptorHex",$output,$retval);
								$outputs=$output;
							}
							echo json_encode(['status'=>1,'message'=>'Updating successfully.']);
						}
						else
							throw new Exception('Updating fail.');
					}
					else
						throw new Exception('Updating fail.');
				}
				else
					throw new Exception('Updating fail.');
			}
			else
				throw new Exception('Updating fail.');
		}
		catch(Exception $e){
			echo json_encode(['status'=>-1,'message'=>$e->getMessage()]);
		}
	}
	public function crud($functions=NULL){
		try{
			$this->twiggy->template('lbs/crud')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function incident($functions=NULL){
		try{
			if($functions=='display'&&isset($_REQUEST)&&!empty($_REQUEST)){
				$req=$_REQUEST;
				$recordsTotal=0;
				$data=[];
				$start=0;
				$CITIZEN=$this->load->database('citizenDB',TRUE);
				$total=$CITIZEN->get('case_mapping');
				if($total){
					$recordsTotal=count($total->result_array());
				}
				$recordsFiltered=$recordsTotal;
				$select=['mapping_id','incident_id','citizen_phone_number','modify_date','incident_status','data'];
				$query=$CITIZEN;
				$query=$query->select(implode(',',$select));
				if(isset($req['search']['value'])&&$req['search']['value']!=''){
					$query=$query->like('"mapping_id"',$req['search']['value']);
					$query=$query->or_like('"citizen_phone_number"',$req['search']['value']);
				}
				$query=$query->order_by('"modify_date"','desc');
				$filtered=clone $query;
				$filtered=$filtered->get('case_mapping');
				if($filtered){
					$filtered=$filtered->result_array();
					if(count($filtered)>0)
						$recordsFiltered=count($filtered);
				}
				if(isset($req['start'])&&isset($req['length'])){
					$query=$query->limit($req['length'],$req['start']);
					$start=$req['start']+1;
				}
				$query=$query->get('case_mapping');
				if($query){
					$data=[];
					$result=$query->result_array();
					foreach($result as$keys=>$values){
						$nested_data=[];
						$nested_data[]=$start;
						foreach($values as$key=>$value)
							$nested_data[]=$value;
						$data[]=$nested_data;
						$start+=1;
					}
					echo json_encode(['draw'=>$req['draw'],'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>$data]);
				}
				else
					throw new Exception();
			}
			else if(isset($_GET['mapping_id'])){
				$id=$_GET['mapping_id'];
				$CITIZEN=$this->load->database('citizenDB',TRUE);
				$query=$CITIZEN;
				$query=$query->like('"mapping_id"',$id)->get('case_mapping');
				if($query){
					$data=[];
					$result=$query->result_array();
					foreach($result[0] as$key=>$value){
						if($key=='data')
							$data[$key]=json_decode($value);
						else
							$data[$key]=$value;
					}
					echo json_encode($data);
				}
			}
			else{
				$def_url=$this->def_url;
				$this->twiggy->set('def_url',$def_url,TRUE);
				$this->twiggy->template('lbs/incident')->display();
			}
		}
		catch(Exception $e){
			echo json_encode(['draw'=>0,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[]]);
		}
	}
	/*public function org_profile(){
		$data=['results'=>[],'pagination'=>['more'=>FALSE]];
		try{
			if(isset($_POST['q'])){
				$DB1=$this->load->database('citizenDB',TRUE);
				$query=$DB1->select('org_id,org_name')->like(['org_name'=>$_POST['q']])->get('org_profile');
				if($query){
					$query=$query->result_array();
					$data['pagination']=['more'=>TRUE];
					foreach($query as $key=>$value)
						$data['results'][]=['id'=>$value['org_id'],'text'=>$value['org_name']];
				}
			}
			echo json_encode($data);
		}
		catch(Exception $e){
			echo json_encode($data);
		}
	}*/
	public function profile($functions=NULL){
		try{
			$this->twiggy->set('addr_url',$this->def_url['addr'],TRUE);
			if($_POST){
				$req=$_POST;
				if(isset($_SESSION['org_profile'])){
					$req['org_addr']=['bldg'=>$req['bldg'],'room_no'=>$req['room_no'],'floor'=>$req['floor'],'vle'=>$req['vle'],'h_no'=>$req['h_no'],'vle_no'=>$req['vle_no'],'lane'=>$req['lane'],'road'=>$req['road'],'sdt'=>$req['sdt'],'dt'=>$req['dt'],'pve'=>$req['pve'],'cty'=>$req['cty'],'pce'=>$req['pce'],'lat'=>$req['lat'],'lon'=>$req['lon']];
					if($_FILES){
						if(isset($_FILES["org_logo"])){
							if(is_uploaded_file($_FILES["org_logo"]["tmp_name"])){
								$dir_name=_UPLOADS.'\files\\';
								$tmp_name=$_FILES["org_logo"]["tmp_name"];
								$ext_name=explode(".",$_FILES["org_logo"]["name"]);
								$img_name=time().".".$ext_name[sizeof($ext_name)-1];
								move_uploaded_file($tmp_name,$dir_name.$img_name);
								$protocol=$_SERVER["HTTPS"]=="on"?"https://":"http://";
								$ret["link"]=$protocol.$_SERVER["SERVER_NAME"]."/"._UPLOADS_NAME."/files/".$img_name;
								$req["org_logo"]=$ret["link"];
							}
						}
						if(isset($_FILES["bldg_plan"])){
							if(is_uploaded_file($_FILES["bldg_plan"]["tmp_name"])){
								$dir_name=_UPLOADS.'\files\\';
								$tmp_name=$_FILES["bldg_plan"]["tmp_name"];
								$ext_name=explode(".",$_FILES["bldg_plan"]["name"]);
								$img_name=time().".".$ext_name[sizeof($ext_name)-1];
								move_uploaded_file($tmp_name,$dir_name.$img_name);
								$protocol=$_SERVER["HTTPS"]=="on"?"https://":"http://";
								$ret["link"]=$protocol.$_SERVER["SERVER_NAME"]."/"._UPLOADS_NAME."/files/".$img_name;
								$req["bldg_plan"]=$ret["link"];
							}
						}
					}
					$DB1=$this->load->database('citizenDB',TRUE);
					$org=$DB1->select('org_id,phone_no,pwd')->where('phone_no',$req['phone_no'])->get('org_profile')->row();
					if($org->org_id!=""){
						$profile=[
							'email'=>trim($req['email']),
							'org_type'=>trim($req['org_type']),
							'org_name'=>trim($req['org_name']),
							'org_branch'=>trim($req['org_branch']),
							'bsn_type'=>trim($req['bsn_type']),
							'estbm_year'=>trim($req['estbm_year']),
							'epy_no'=>trim($req['epy_no']),
							'org_size'=>trim($req['org_size']),
							'bsn_desc'=>trim($req['bsn_desc']),
							'org_addr'=>json_encode($req['org_addr']),
							'fax_no'=>trim($req['fax_no']),
							'org_web'=>trim($req['org_web']),
							'org_ctps'=>trim($req['org_ctps']),
							'org_facility'=>trim($req['org_facility']),
							'other_info'=>trim($req['other_info']),
							'modified_date'=>date('Y-m-d H:i:s'),
						];
						if(isset($req['org_logo']))
							$profile['org_logo']=trim($req['org_logo']);
						if(isset($req['bldg_plan']))
							$profile['bldg_plan']=trim($req['bldg_plan']);
						if($req['pwd_old']!=""){
							$pass=$this->CryptoJS->aes_encrypt($req['pwd_old']);
							if($org->pwd!=$pass){
								$this->twiggy->set('status',-1,TRUE);
								$this->twiggy->set('message','แก้ไขรหัสผ่านไม่สำเร็จ<br>Update password fail!',TRUE);
								$this->twiggy->template('lbs/profile_org')->display();
								die();
							}
							else
								$profile['pwd']=trim($this->CryptoJS->aes_encrypt($req['pwd']));
						}
						$query=$DB1->update('org_profile',$profile,array('phone_no'=>$req['phone_no']));
						if($query==1){
							$this->session->set_flashdata('status',1);
							$this->session->set_flashdata('message','แก้ไขข้อมูลส่วนตัวเรียบร้อยแล้ว<br>Update profile successfully');
							$_SESSION['org_profile']=$req;
							redirect(base_url('lbs/profile'));
						}
						else{
							$this->twiggy->set('status',-1,TRUE);
							$this->twiggy->set('message','แก้ไขข้อมูลส่วนตัวไม่สำเร็จ<br>Update profile fail!',TRUE);
							$this->twiggy->template('lbs/profile_org')->display();
						}
					}
				}
				else if(isset($_SESSION['citizen_profile'])){
					if(!isset($req['citizen_password_old'])OR$req['citizen_password_old']=='')
						unset($req['citizen_password_old']);
					if((!isset($req['citizen_password'])OR$req['citizen_password']=='')&&isset($_SESSION['citizen_profile']->citizen_password))
						$req['citizen_password']=$_SESSION['citizen_profile']->citizen_password;
					$req["citizen_photo"]='';
					$url=$this->def_url['subscribe'];
					if($_FILES){
						if(isset($_FILES["citizen_photo"])){
							if(is_uploaded_file($_FILES["citizen_photo"]["tmp_name"])){
								$dir_name=_UPLOADS.'\files\\';
								$tmp_name=$_FILES["citizen_photo"]["tmp_name"];
								$ext_name=explode(".",$_FILES["citizen_photo"]["name"]);
								$img_name=time().".".$ext_name[sizeof($ext_name)-1];
								move_uploaded_file($tmp_name,$dir_name.$img_name);
								$protocol=$_SERVER["HTTPS"]=="on"?"https://":"http://";
								$ret["link"]=$protocol.$_SERVER["SERVER_NAME"]."/"._UPLOADS_NAME."/files/".$img_name;
								$req["citizen_photo"]=$ret["link"];
							}
						}
					}
					if(!isset($req['citizen_name'])OR$req['citizen_name']=='')
						$req['citizen_name']=$req['citizen_phone_number'];
					if(isset($req['citizen_middle'])&&$req['citizen_middle']!=''){
						$req['citizen_name'].=' '.$req['citizen_middle'];
						unset($req['citizen_middle']);
					}
					if(isset($req['citizen_surname'])&&$req['citizen_surname']!=''){
						$req['citizen_name'].=' '.$req['citizen_surname'];
						unset($req['citizen_surname']);
					}
					$req['citizen_telephone']=$req['citizen_phone_number'];
					//$req['citizen_home_addr']='';
					if(isset($req['citizen_use_pmnt_addr'])&&$req['citizen_use_pmnt_addr']=='on'){
						//if(isset($req['citizen_pmnt_address'])&&$req['citizen_pmnt_address']!='')$req['citizen_home_addr'].=$req['citizen_pmnt_address'];
						//if(isset($req['citizen_pmnt_subdistrict'])&&$req['citizen_pmnt_subdistrict']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_subdistrict'];
						//if(isset($req['citizen_pmnt_district'])&&$req['citizen_pmnt_district']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_district'];
						//if(isset($req['citizen_pmnt_province'])&&$req['citizen_pmnt_province']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_province'];
						//if(isset($req['citizen_pmnt_postcode'])&&$req['citizen_pmnt_postcode']!='')$req['citizen_home_addr'].=' '.$req['citizen_pmnt_postcode'];
						//if(isset($req['citizen_pmnt_country'])&&$req['citizen_pmnt_country']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_country'];
						if(isset($req['citizen_pmnt_address'])&&$req['citizen_pmnt_address']!='')$req['citizen_home_address']=$req['citizen_pmnt_address'];
						if(isset($req['citizen_pmnt_subdistrict'])&&$req['citizen_pmnt_subdistrict']!='')$req['citizen_home_subdistrict']=$req['citizen_pmnt_subdistrict'];
						if(isset($req['citizen_pmnt_district'])&&$req['citizen_pmnt_district']!='')$req['citizen_home_district']=$req['citizen_pmnt_district'];
						if(isset($req['citizen_pmnt_province'])&&$req['citizen_pmnt_province']!='')$req['citizen_home_province']=$req['citizen_pmnt_province'];
						if(isset($req['citizen_pmnt_postcode'])&&$req['citizen_pmnt_postcode']!='')$req['citizen_home_postcode']=$req['citizen_pmnt_postcode'];
						if(isset($req['citizen_pmnt_country'])&&$req['citizen_pmnt_country']!='')$req['citizen_home_country']=$req['citizen_pmnt_country'];
					}
					//else{
						//if(isset($req['citizen_home_address'])&&$req['citizen_home_address']!='')$req['citizen_home_addr'].=$req['citizen_home_address'];
						//if(isset($req['citizen_home_subdistrict'])&&$req['citizen_home_subdistrict']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_subdistrict'];
						//if(isset($req['citizen_home_district'])&&$req['citizen_home_district']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_district'];
						//if(isset($req['citizen_home_province'])&&$req['citizen_home_province']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_province'];
						//if(isset($req['citizen_home_postcode'])&&$req['citizen_home_postcode']!='')$req['citizen_home_addr'].=' '.$req['citizen_home_postcode'];
						//if(isset($req['citizen_home_country'])&&$req['citizen_home_country']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_country'];
					//}
					//$req['citizen_address']=$req['citizen_home_addr'];
					$req['address']=[];
					$req['address'][0]=[
						'citizen_home_no'=>$req['citizen_home_no'],
						//'citizen_home_addr'=>$req['citizen_home_addr'],
						'street'=>$req['citizen_home_address'],
						'country'=>$req['citizen_home_country'],
						'province'=>$req['citizen_home_province'],
						'district'=>$req['citizen_home_district'],
						'subdistrict'=>$req['citizen_home_subdistrict'],
						'postcode'=>$req['citizen_home_postcode'],
						'citizen_home_lat'=>$req['citizen_home_lat'],
						'citizen_home_lon'=>$req['citizen_home_lon'],
					];
					$citizen_work_addr=$this->addr_extract($req['citizen_work_addr']);
					$req['address'][1]=[
						//'citizen_work_no'=>$req['citizen_work_no'],
						//'citizen_work_addr'=>$req['citizen_work_addr'],
						//'citizen_work_lat'=>$req['citizen_work_lat'],
						//'citizen_work_lon'=>$req['citizen_work_lon'],
						'citizen_home_no'=>$req['citizen_work_no'],
						'street'=>$citizen_work_addr[0],
						'country'=>$citizen_work_addr[5],
						'province'=>$citizen_work_addr[3],
						'district'=>$citizen_work_addr[2],
						'subdistrict'=>$citizen_work_addr[1],
						'postcode'=>$citizen_work_addr[4],
						'citizen_home_lat'=>$req['citizen_work_lat'],
						'citizen_home_lon'=>$req['citizen_work_lon'],
					];
					$req['info']=[
						'illness_history'=>isset($req['illness_history'])?$req['illness_history']:'',
						'other'=>isset($req['other'])?$req['other']:'',
						'drug_allergy'=>isset($req['drug_allergy'])?$req['drug_allergy']:'',
						'hospital'=>isset($req['hospital'])?$req['hospital']:'',
						'group_blood'=>isset($req['group_blood'])?$req['group_blood']:'',
						'brithday'=>isset($req['brithday'])?$req['brithday']:'',
						'gender'=>isset($req['gender'])?$req['gender']:'',
						'other_body'=>isset($req['other_body'])?$req['other_body']:'',
						'congenital_disease'=>isset($req['congenital_disease'])?$req['congenital_disease']:'',
						'citizen_type'=>isset($req['citizen_type'])?$req['citizen_type']:'',
						'citizen_title'=>isset($req['citizen_title'])?$req['citizen_title']:'',
						'citizen_height'=>isset($req['citizen_height'])?$req['citizen_height']:'',
						'citizen_weight'=>isset($req['citizen_weight'])?$req['citizen_weight']:'',
						'citizen_religion'=>isset($req['citizen_religion'])?$req['citizen_religion']:'',
						'citizen_scar'=>isset($req['citizen_scar'])?$req['citizen_scar']:'',
						'citizen_handicapped'=>isset($req['citizen_handicapped'])?$req['citizen_handicapped']:'',
						'citizen_nationality'=>isset($req['citizen_nationality'])?$req['citizen_nationality']:'',
						'citizen_citizenship'=>isset($req['citizen_citizenship'])?$req['citizen_citizenship']:'',
						'citizen_passport'=>isset($req['citizen_passport'])?$req['citizen_passport']:'',
						'citizen_photo'=>isset($req['citizen_photo'])?$req['citizen_photo']:'',
						'contact_name'=>isset($req['contact_name'])?$req['contact_name']:'',
						'contact_surname'=>isset($req['contact_surname'])?$req['contact_surname']:'',
						'contact_phone_number'=>isset($req['contact_phone_number'])?$req['contact_phone_number']:'',
						'contact_occupation'=>isset($req['contact_occupation'])?$req['contact_occupation']:'',
						'contact_relationship'=>isset($req['contact_relationship'])?$req['contact_relationship']:'',
						'contact_address'=>isset($req['contact_address'])?$req['contact_address']:'',
						'citizen_ft_title'=>isset($req['citizen_ft_title'])?$req['citizen_ft_title']:'',
						'citizen_ft_name'=>isset($req['citizen_ft_name'])?$req['citizen_ft_name']:'',
						'citizen_ft_surname'=>isset($req['citizen_ft_surname'])?$req['citizen_ft_surname']:'',
						'citizen_ft_birthday'=>isset($req['citizen_ft_birthday'])?$req['citizen_ft_birthday']:'',
						'citizen_ft_occupation'=>isset($req['citizen_ft_occupation'])?$req['citizen_ft_occupation']:'',
						'citizen_ft_status'=>isset($req['citizen_ft_status'])?$req['citizen_ft_status']:'',
						'citizen_blood_ft'=>isset($req['citizen_blood_ft'])?$req['citizen_blood_ft']:'',
						'congenital_disease_ft'=>isset($req['congenital_disease_ft'])?$req['congenital_disease_ft']:'',
						'drug_allergy_ft'=>isset($req['drug_allergy_ft'])?$req['drug_allergy_ft']:'',
						'regular_hospital_ft'=>isset($req['regular_hospital_ft'])?$req['regular_hospital_ft']:'',
						'illness_history_ft'=>isset($req['illness_history_ft'])?$req['illness_history_ft']:'',
						'citizen_mt_title'=>isset($req['citizen_mt_title'])?$req['citizen_mt_title']:'',
						'citizen_mt_name'=>isset($req['citizen_mt_name'])?$req['citizen_mt_name']:'',
						'citizen_mt_surname'=>isset($req['citizen_mt_surname'])?$req['citizen_mt_surname']:'',
						'citizen_mt_birthday'=>isset($req['citizen_mt_birthday'])?$req['citizen_mt_birthday']:'',
						'citizen_mt_occupation'=>isset($req['citizen_mt_occupation'])?$req['citizen_mt_occupation']:'',
						'citizen_mt_status'=>isset($req['citizen_mt_status'])?$req['citizen_mt_status']:'',
						'citizen_blood_mt'=>isset($req['citizen_blood_mt'])?$req['citizen_blood_mt']:'',
						'congenital_disease_mt'=>isset($req['congenital_disease_mt'])?$req['congenital_disease_mt']:'',
						'drug_allergy_mt'=>isset($req['drug_allergy_mt'])?$req['drug_allergy_mt']:'',
						'regular_hospital_mt'=>isset($req['regular_hospital_mt'])?$req['regular_hospital_mt']:'',
						'illness_history_mt'=>isset($req['illness_history_mt'])?$req['illness_history_mt']:'',
						'citizen_marital'=>isset($req['citizen_marital'])?$req['citizen_marital']:'',
						'citizen_sp_title'=>isset($req['citizen_sp_title'])?$req['citizen_sp_title']:'',
						'citizen_sp_name'=>isset($req['citizen_sp_name'])?$req['citizen_sp_name']:'',
						'citizen_sp_surname'=>isset($req['citizen_sp_surname'])?$req['citizen_sp_surname']:'',
						'citizen_sp_birthday'=>isset($req['citizen_sp_birthday'])?$req['citizen_sp_birthday']:'',
						'citizen_sp_occupation'=>isset($req['citizen_sp_occupation'])?$req['citizen_sp_occupation']:'',
						'citizen_sp_status'=>isset($req['citizen_sp_status'])?$req['citizen_sp_status']:'',
						'citizen_blood_sp'=>isset($req['citizen_blood_sp'])?$req['citizen_blood_sp']:'',
						'congenital_disease_sp'=>isset($req['congenital_disease_sp'])?$req['congenital_disease_sp']:'',
						'drug_allergy_sp'=>isset($req['drug_allergy_sp'])?$req['drug_allergy_sp']:'',
						'regular_hospital_sp'=>isset($req['regular_hospital_sp'])?$req['regular_hospital_sp']:'',
						'illness_history_sp'=>isset($req['illness_history_sp'])?$req['illness_history_sp']:'',
						'org_id'=>isset($req['org_id'])?$req['org_id']:'',
						'citizen_work_org'=>isset($req['citizen_work_org'])?$req['citizen_work_org']:'',
						'citizen_work_pos'=>isset($req['citizen_work_pos'])?$req['citizen_work_pos']:'',
						'citizen_pmnt_no'=>isset($req['citizen_pmnt_no'])?$req['citizen_pmnt_no']:'',
						'citizen_pmnt_address'=>isset($req['citizen_pmnt_address'])?$req['citizen_pmnt_address']:'',
						'citizen_pmnt_subdistrict'=>isset($req['citizen_pmnt_subdistrict'])?$req['citizen_pmnt_subdistrict']:'',
						'citizen_pmnt_district'=>isset($req['citizen_pmnt_district'])?$req['citizen_pmnt_district']:'',
						'citizen_pmnt_province'=>isset($req['citizen_pmnt_province'])?$req['citizen_pmnt_province']:'',
						'citizen_pmnt_postcode'=>isset($req['citizen_pmnt_postcode'])?$req['citizen_pmnt_postcode']:'',
						'citizen_pmnt_country'=>isset($req['citizen_pmnt_country'])?$req['citizen_pmnt_country']:'',
						'citizen_pmnt_lat'=>isset($req['citizen_pmnt_lat'])?$req['citizen_pmnt_lat']:'',
						'citizen_pmnt_lon'=>isset($req['citizen_pmnt_lon'])?$req['citizen_pmnt_lon']:'',
					];
					$fields=array_filter($req);
					$header=$this->def_header;
					//echo '<pre>';
					//die(print_r($fields,TRUE));
					//echo '</pre>';
					$postvars=json_encode($fields);
					$ch=curl_init();
					curl_setopt($ch,CURLOPT_URL,$url);
					curl_setopt($ch,CURLOPT_POST,count($fields));
					curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
					curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
					curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
					curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
					$result=curl_exec($ch);
					curl_close($ch);
					$arr=json_decode($result);
					if($arr->status==0){
						$this->session->set_flashdata('status',1);
						$this->session->set_flashdata('message','แก้ไขข้อมูลส่วนตัวเรียบร้อยแล้ว<br>Update profile successfully');
						$_SESSION['citizen_profile']=$arr->data;
						redirect(base_url('lbs/profile'));
					}
					else{
						$this->twiggy->set('status',-1,TRUE);
						if($arr->status==-2)
							$this->twiggy->set('message','แก้ไขรหัสผ่านไม่สำเร็จ<br>Update password fail!',TRUE);
						else
							$this->twiggy->set('message','แก้ไขข้อมูลส่วนตัวไม่สำเร็จ<br>Update profile fail!',TRUE);
						//$this->twiggy->template('lbs/profile_citizen')->display();
					}
				}
				else
					throw new Exception();
			}
			if(isset($_SESSION['citizen_profile'])OR isset($_SESSION['org_profile'])){
				if($this->session->flashdata('status'))
					$this->twiggy->set('status',$this->session->flashdata('status'),TRUE);
				if($this->session->flashdata('message'))
					$this->twiggy->set('message',$this->session->flashdata('message'),TRUE);
				$this->twiggy->set('active','profile',TRUE);
				$this->twiggy->set('functions','update',TRUE);
				$this->twiggy->set('title','ข้อมูลส่วนตัว / Profile',TRUE);
				if(isset($_SESSION['citizen_profile'])){
					$result=[];
					$DB1=$this->load->database('citizenDB',TRUE);
					$query=$DB1->select('org_id,org_name')->get('org_profile')->result_array();
					foreach($query as $key=>$value)
						$result[]=['id'=>$value['org_id'],'name'=>$value['org_name']];
					$this->twiggy->set('org_profiles',$result,TRUE);
					//$addresses=$this->addr_extract($_SESSION['citizen_profile']->address[0]->citizen_home_addr);
					//$_SESSION['citizen_profile']->address[0]->citizen_home_country=$addresses[5];
					//$_SESSION['citizen_profile']->address[0]->citizen_home_postcode=$addresses[4];
					//$_SESSION['citizen_profile']->address[0]->citizen_home_province=$addresses[3];
					//$_SESSION['citizen_profile']->address[0]->citizen_home_district=$addresses[2];
					//$_SESSION['citizen_profile']->address[0]->citizen_home_subdistrict=$addresses[1];
					//$_SESSION['citizen_profile']->address[0]->citizen_home_address=$addresses[0];
					$_SESSION['citizen_profile']->address[0]->citizen_home_country=$_SESSION['citizen_profile']->address[0]->country;
					$_SESSION['citizen_profile']->address[0]->citizen_home_postcode=$_SESSION['citizen_profile']->address[0]->postcode;
					$_SESSION['citizen_profile']->address[0]->citizen_home_province=$_SESSION['citizen_profile']->address[0]->province;
					$_SESSION['citizen_profile']->address[0]->citizen_home_district=$_SESSION['citizen_profile']->address[0]->district;
					$_SESSION['citizen_profile']->address[0]->citizen_home_subdistrict=$_SESSION['citizen_profile']->address[0]->subdistrict;
					$_SESSION['citizen_profile']->address[0]->citizen_home_address=$_SESSION['citizen_profile']->address[0]->street;
					if((!isset($_SESSION['citizen_profile']->info->citizen_pmnt_no)OR$_SESSION['citizen_profile']->info->citizen_pmnt_no=='')&&
						(!isset($_SESSION['citizen_profile']->info->citizen_pmnt_address)OR$_SESSION['citizen_profile']->info->citizen_pmnt_address=='')&&
						(!isset($_SESSION['citizen_profile']->info->citizen_pmnt_subdistrict)OR$_SESSION['citizen_profile']->info->citizen_pmnt_subdistrict=='')&&
						(!isset($_SESSION['citizen_profile']->info->citizen_pmnt_district)OR$_SESSION['citizen_profile']->info->citizen_pmnt_district=='')&&
						(!isset($_SESSION['citizen_profile']->info->citizen_pmnt_province)OR$_SESSION['citizen_profile']->info->citizen_pmnt_province=='')&&
						(!isset($_SESSION['citizen_profile']->info->citizen_pmnt_postcode)OR$_SESSION['citizen_profile']->info->citizen_pmnt_postcode=='')&&
						(!isset($_SESSION['citizen_profile']->info->citizen_pmnt_country)OR$_SESSION['citizen_profile']->info->citizen_pmnt_country=='')){
						$_SESSION['citizen_profile']->info->citizen_pmnt_no=$_SESSION['citizen_profile']->address[0]->citizen_home_no;
						$_SESSION['citizen_profile']->info->citizen_pmnt_address=$_SESSION['citizen_profile']->address[0]->citizen_home_address;
						$_SESSION['citizen_profile']->info->citizen_pmnt_subdistrict=$_SESSION['citizen_profile']->address[0]->citizen_home_subdistrict;
						$_SESSION['citizen_profile']->info->citizen_pmnt_district=$_SESSION['citizen_profile']->address[0]->citizen_home_district;
						$_SESSION['citizen_profile']->info->citizen_pmnt_province=$_SESSION['citizen_profile']->address[0]->citizen_home_province;
						$_SESSION['citizen_profile']->info->citizen_pmnt_postcode=$_SESSION['citizen_profile']->address[0]->citizen_home_postcode;
						$_SESSION['citizen_profile']->info->citizen_pmnt_country=$_SESSION['citizen_profile']->address[0]->citizen_home_country;
					}
					$_SESSION['citizen_profile']->address[1]->citizen_work_no=$_SESSION['citizen_profile']->address[1]->citizen_home_no;
					$_SESSION['citizen_profile']->address[1]->citizen_work_addr='';
					if(isset($_SESSION['citizen_profile']->address[1]->street)&&$_SESSION['citizen_profile']->address[1]->street!='')$_SESSION['citizen_profile']->address[1]->citizen_work_addr.=$_SESSION['citizen_profile']->address[1]->street;
					if(isset($_SESSION['citizen_profile']->address[1]->subdistrict)&&$_SESSION['citizen_profile']->address[1]->subdistrict!='')$_SESSION['citizen_profile']->address[1]->citizen_work_addr.=', '.$_SESSION['citizen_profile']->address[1]->subdistrict;
					if(isset($_SESSION['citizen_profile']->address[1]->district)&&$_SESSION['citizen_profile']->address[1]->district!='')$_SESSION['citizen_profile']->address[1]->citizen_work_addr.=', '.$_SESSION['citizen_profile']->address[1]->district;
					if(isset($_SESSION['citizen_profile']->address[1]->province)&&$_SESSION['citizen_profile']->address[1]->province!='')$_SESSION['citizen_profile']->address[1]->citizen_work_addr.=', '.$_SESSION['citizen_profile']->address[1]->province;
					if(isset($_SESSION['citizen_profile']->address[1]->postcode)&&$_SESSION['citizen_profile']->address[1]->postcode!='')$_SESSION['citizen_profile']->address[1]->citizen_work_addr.=' '.$_SESSION['citizen_profile']->address[1]->postcode;
					if(isset($_SESSION['citizen_profile']->address[1]->country)&&$_SESSION['citizen_profile']->address[1]->country!='')$_SESSION['citizen_profile']->address[1]->citizen_work_addr.=', '.$_SESSION['citizen_profile']->address[1]->country;
					//echo '<pre>';
					//die(print_r($_SESSION['citizen_profile']->address,TRUE));
					//echo '</pre>';
					$this->twiggy->set('citizen',$_SESSION['citizen_profile'],TRUE);
					$this->twiggy->template('lbs/profile_citizen')->display();
				}
				else if(isset($_SESSION['org_profile'])){
					//$arr=[];
					$result=[];
					$DB1=$this->load->database('citizenDB',TRUE);
					$query=$DB1->select('citizen_code,citizen_name,citizen_surname')->get('citizen_profile')->result_array();
					/*$query=$DB1->select('citizen_code,citizen_name,citizen_surname,info')->get('citizen_profile')->result_array();
					foreach($query as $key=>$value){
						if($value['info']!=""){
							$info=json_decode($value['info']);
							if(is_object($info)&&isset($info->org_id)){
								if($_SESSION['org_profile']->org_id==$info->org_id)
									$arr[]=$value;
							}
						}
					}
					foreach($arr as $key=>$value)
						$result[]=[
							'id'=>$value['citizen_code'],
							'name'=>$value['citizen_name'].($value['citizen_surname']!=""?" ".$value['citizen_surname']:"")
						];*/
					foreach($query as $key=>$value)
						$result[]=[
							'id'=>$value['citizen_code'],
							'name'=>$value['citizen_name'].($value['citizen_surname']!=""?" ".$value['citizen_surname']:"")
						];
					$this->twiggy->set('citizen_profiles',$result,TRUE);
					$this->twiggy->set('org_ctps',explode(',',$_SESSION['org_profile']->org_ctps),TRUE);
					$this->twiggy->set('org',$_SESSION['org_profile'],TRUE);
					$this->twiggy->template('lbs/profile_org')->display();
				}
				else
					throw new Exception();
			}
			else
				throw new Exception();
		}
		catch(Exception $e){
			redirect(base_url('lbs/signin'));
		}
	}
	public function profiles($functions=NULL){
		try{
			if(isset($_GET['ani'])){
				$DB1=$this->load->database('citizenDB',TRUE);
				$user=$DB1->where(['citizen_phone_number'=>$_GET['ani']])->get('citizen_profile')->row();
				if(!isset($user->citizen_code)){
					$this->session->set_flashdata('status',0);
					$this->session->set_flashdata('message',$_GET['ani'].' not found!');
					throw new Exception();
				}
				else{
					$user->address=json_decode($user->address);
					//$addresses=$this->addr_extract($user->address[0]->citizen_home_addr);
					//$user->address[0]->citizen_home_country=$addresses[5];
					//$user->address[0]->citizen_home_postcode=$addresses[4];
					//$user->address[0]->citizen_home_province=$addresses[3];
					//$user->address[0]->citizen_home_district=$addresses[2];
					//$user->address[0]->citizen_home_subdistrict=$addresses[1];
					//$user->address[0]->citizen_home_address=$addresses[0];
					$user->address[0]->citizen_home_address=$user->address[0]->street;
					$user->address[0]->citizen_home_country=$user->address[0]->country;
					$user->address[0]->citizen_home_province=$user->address[0]->province;
					$user->address[0]->citizen_home_district=$user->address[0]->district;
					$user->address[0]->citizen_home_subdistrict=$user->address[0]->subdistrict;
					$user->address[0]->citizen_home_postcode=$user->address[0]->postcode;
					$user->info=json_decode($user->info);
					if((!isset($user->info->citizen_pmnt_no)OR$user->info->citizen_pmnt_no=='')&&
						(!isset($user->info->citizen_pmnt_address)OR$user->info->citizen_pmnt_address=='')&&
						(!isset($user->info->citizen_pmnt_subdistrict)OR$user->info->citizen_pmnt_subdistrict=='')&&
						(!isset($user->info->citizen_pmnt_district)OR$user->info->citizen_pmnt_district=='')&&
						(!isset($user->info->citizen_pmnt_province)OR$user->info->citizen_pmnt_province=='')&&
						(!isset($user->info->citizen_pmnt_postcode)OR$user->info->citizen_pmnt_postcode=='')&&
						(!isset($user->info->citizen_pmnt_country)OR$user->info->citizen_pmnt_country=='')){
						$user->info->citizen_pmnt_no=$user->address[0]->citizen_home_no;
						$user->info->citizen_pmnt_address=$user->address[0]->citizen_home_address;
						$user->info->citizen_pmnt_subdistrict=$user->address[0]->citizen_home_subdistrict;
						$user->info->citizen_pmnt_district=$user->address[0]->citizen_home_district;
						$user->info->citizen_pmnt_province=$user->address[0]->citizen_home_province;
						$user->info->citizen_pmnt_postcode=$user->address[0]->citizen_home_postcode;
						$user->info->citizen_pmnt_country=$user->address[0]->citizen_home_country;
					}
					$user->address[1]->citizen_work_no=$user->address[1]->citizen_home_no;
					$user->address[1]->citizen_work_addr='';
					if(isset($user->address[1]->street)&&$user->address[1]->street!='')$user->address[1]->citizen_work_addr.=$user->address[1]->street;
					if(isset($user->address[1]->subdistrict)&&$user->address[1]->subdistrict!='')$user->address[1]->citizen_work_addr.=', '.$user->address[1]->subdistrict;
					if(isset($user->address[1]->district)&&$user->address[1]->district!='')$user->address[1]->citizen_work_addr.=', '.$user->address[1]->district;
					if(isset($user->address[1]->province)&&$user->address[1]->province!='')$user->address[1]->citizen_work_addr.=', '.$user->address[1]->province;
					if(isset($user->address[1]->postcode)&&$user->address[1]->postcode!='')$user->address[1]->citizen_work_addr.=' '.$user->address[1]->postcode;
					if(isset($user->address[1]->country)&&$user->address[1]->country!='')$user->address[1]->citizen_work_addr.=', '.$user->address[1]->country;
					//echo '<pre>';
					//die(print_r($user->address,TRUE));
					//echo '</pre>';
					$this->twiggy->set('addr_url',$this->def_url['addr'],TRUE);
					$this->twiggy->set('ani',$_GET['ani'],TRUE);
					$this->twiggy->set('citizen',$user,TRUE);
					$this->twiggy->set('functions','update',TRUE);
					$this->twiggy->set('sub_functions','read',TRUE);
					$this->twiggy->set('title','ข้อมูลส่วนตัว / Profile',TRUE);
					$result=[];
					$query=$DB1->select('org_id,org_name')->get('org_profile')->result_array();
					foreach($query as $key=>$value)
						$result[]=['id'=>$value['org_id'],'name'=>$value['org_name']];
					$this->twiggy->set('org_profiles',$result,TRUE);
					if(isset($_GET['ext']))$this->twiggy->set('ext',$_GET['ext'],TRUE);
					if(isset($_GET['connid']))$this->twiggy->set('connid',$_GET['connid'],TRUE);
					if(isset($_GET['callerName']))$this->twiggy->set('callerName',$_GET['callerName'],TRUE);
					if(isset($_GET['latitude']))$this->twiggy->set('latitude',$_GET['latitude'],TRUE);
					if(isset($_GET['longitude']))$this->twiggy->set('longitude',$_GET['longitude'],TRUE);
					if(isset($_GET['altitude']))$this->twiggy->set('altitude',$_GET['altitude'],TRUE);
					$this->twiggy->template('lbs/profile_citizen')->display();
				}
			}
			else
				throw new Exception();
		}
		catch(Exception $e){
			redirect(base_url('lbs/signin'));
			//redirect(base_url('lbs/subscriber'));
		}
	}
	public function pull($options=NULL){
		try{
			if($_REQUEST){
				$req=$_REQUEST;
				if($options==='sms'){
					if($req['ani']===''OR$req['ani']===NULL)
						throw new Exception('ANI not found.');
					else{
						$lbs=file_get_contents($this->def_path['lbs2cad'].$req['ani']);
						if($lbs!==FALSE){
							$lbs_dec=json_decode($lbs);
							echo json_encode(['status'=>1,'message'=>'Reads a file successfully.','data'=>$lbs_dec]);
						}
						else
							throw new Exception('Reads a file not success.');
					}
				}
			}
			else
				throw new Exception('Request not found.');
		}
		catch(Exception $e){
			echo json_encode(['status'=>-1,'message'=>'Location not found.','lat'=>'','lon'=>'']);
		}
	}
	public function push($options=NULL){
		try{
			if($_REQUEST){
				$req=$_REQUEST;
				if($options==='sms'){
					if($req['ani']===''OR$req['ani']===NULL)
						throw new Exception('ANI not found.');
					else{
						$req['timestamp']=time();
						if(file_put_contents($this->def_path['lbs2cad'].$req['ani'],json_encode($req)))
							echo json_encode(['status'=>1,'message'=>'Writes data to a file successfully.']);
						else
							throw new Exception('Writes data to a file not success.');
					}
				}
			}
			else
				throw new Exception('Request not found.');
		}
		catch(Exception $e){
			echo json_encode(['status'=>-1,'message'=>$e->getMessage()]);
		}
	}
	public function sender(){
		try{
			if($_POST){
				$req=$_POST;
				$url=$req['url'];
				unset($req['url']);
				$fields=$req;
				$postvars=json_encode($fields);
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch,CURLOPT_POST,count($fields));
				curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
				curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
				curl_setopt($ch,CURLOPT_TIMEOUT,10);
				$result=curl_exec($ch);
				curl_close($ch);
				echo $result;
			}
			else
				throw new Exception();
		}
		catch(Exception $e){
			echo json_encode(['status'=>-1]);
		}
	}
	public function signin($functions=NULL){
		try{
			if($_POST){
				$req=$_POST;
				$url=$this->def_url['login'];
				$fields=['citizen_phone_number'=>$req['citizen_phone_number'],'citizen_password'=>$req['citizen_password']];
				$header=$this->def_header;
				$postvars=json_encode($fields);
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch,CURLOPT_POST,count($fields));
				curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
				curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
				curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
				$result=curl_exec($ch);
				curl_close($ch);
				$arr=json_decode($result);
				if($arr->status==0){
					$citizen_name=explode(' ',$arr->data->citizen_name);
					$arr->data->citizen_name=$citizen_name[0];
					$_SESSION['citizen_profile']=$arr->data;
					$_SESSION['citizen_profile']->citizen_password=$req['citizen_password'];
					redirect(base_url('lbs/profile'));
				}
				else{
					$pass=$this->CryptoJS->aes_encrypt($req['citizen_password']);
					$DB1=$this->load->database('citizenDB',TRUE);
					$org=$DB1->where('phone_no',$req['citizen_phone_number'])->where('pwd',$pass)->get('org_profile')->row();
					if(!isset($org->org_id)){
						$this->twiggy->set('status',-1,TRUE);
						$this->twiggy->set('message','หมายเลขโทรศัพท์ หรือ รหัสผ่าน ไม่ถูกต้อง<br>Wrong phone number or password!',TRUE);
						$this->twiggy->set('citizen_phone_number',$req['citizen_phone_number'],TRUE);
						$this->twiggy->template('lbs/signin')->display();
					}
					else{
						$org->org_addr=json_decode($org->org_addr);
						$_SESSION['org_profile']=$org;
						redirect(base_url('lbs/profile'));
					}
				}
			}
			else{
				if($this->session->flashdata('status'))
					$this->twiggy->set('status',$this->session->flashdata('status'),TRUE);
				if($this->session->flashdata('message'))
					$this->twiggy->set('message',$this->session->flashdata('message'),TRUE);
				if($this->session->flashdata('citizen_phone_number'))
					$this->twiggy->set('citizen_phone_number',$this->session->flashdata('citizen_phone_number'),TRUE);
				$this->twiggy->template('lbs/signin')->display();
			}
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function signout($functions=NULL){
		try{
			session_unset();
			session_destroy();
			redirect('lbs/signin');
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function signup($categories='person',$functions=NULL){
		try{
			$this->twiggy->set('addr_url',$this->def_url['addr'],TRUE);
			$this->twiggy->set('functions','create',TRUE);
			$this->twiggy->set('title','สมัครใช้งาน / Register',TRUE);
			if($_POST){
				$req=$_POST;
				if($categories=='org'){
					$req['org_addr']=['bldg'=>$req['bldg'],'room_no'=>$req['room_no'],'floor'=>$req['floor'],'vle'=>$req['vle'],'h_no'=>$req['h_no'],'vle_no'=>$req['vle_no'],'lane'=>$req['lane'],'road'=>$req['road'],'sdt'=>$req['sdt'],'dt'=>$req['dt'],'pve'=>$req['pve'],'cty'=>$req['cty'],'pce'=>$req['pce'],'lat'=>$req['lat'],'lon'=>$req['lon']];
					$req["org_logo"]="";
					$req["bldg_plan"]="";
					if($_FILES){
						if(isset($_FILES["org_logo"])){
							if(is_uploaded_file($_FILES["org_logo"]["tmp_name"])){
								$dir_name=_UPLOADS.'\files\\';
								$tmp_name=$_FILES["org_logo"]["tmp_name"];
								$ext_name=explode(".",$_FILES["org_logo"]["name"]);
								$img_name=time().".".$ext_name[sizeof($ext_name)-1];
								move_uploaded_file($tmp_name,$dir_name.$img_name);
								$protocol=$_SERVER["HTTPS"]=="on"?"https://":"http://";
								$ret["link"]=$protocol.$_SERVER["SERVER_NAME"]."/"._UPLOADS_NAME."/files/".$img_name;
								$req["org_logo"]=$ret["link"];
							}
						}
						if(isset($_FILES["bldg_plan"])){
							if(is_uploaded_file($_FILES["bldg_plan"]["tmp_name"])){
								$dir_name=_UPLOADS.'\files\\';
								$tmp_name=$_FILES["bldg_plan"]["tmp_name"];
								$ext_name=explode(".",$_FILES["bldg_plan"]["name"]);
								$img_name=time().".".$ext_name[sizeof($ext_name)-1];
								move_uploaded_file($tmp_name,$dir_name.$img_name);
								$protocol=$_SERVER["HTTPS"]=="on"?"https://":"http://";
								$ret["link"]=$protocol.$_SERVER["SERVER_NAME"]."/"._UPLOADS_NAME."/files/".$img_name;
								$req["bldg_plan"]=$ret["link"];
							}
						}
					}
					$DB1=$this->load->database('citizenDB',TRUE);
					$org=$DB1->select('org_id,phone_no,pwd')->where('phone_no',$req['phone_no'])->get('org_profile')->row();
					if($org->org_id!=""){
						$profile=[
							'email'=>trim($req['email']),
							'org_type'=>trim($req['org_type']),
							'org_name'=>trim($req['org_name']),
							'org_branch'=>trim($req['org_branch']),
							'bsn_type'=>trim($req['bsn_type']),
							'estbm_year'=>trim($req['estbm_year']),
							'epy_no'=>trim($req['epy_no']),
							'org_size'=>trim($req['org_size']),
							'bsn_desc'=>trim($req['bsn_desc']),
							'org_addr'=>json_encode($req['org_addr']),
							'fax_no'=>trim($req['fax_no']),
							'org_web'=>trim($req['org_web']),
							'org_ctps'=>trim($req['org_ctps']),
							'org_facility'=>trim($req['org_facility']),
							'other_info'=>trim($req['other_info']),
							'modified_date'=>date('Y-m-d H:i:s'),
						];
						if(isset($req['org_logo']))
							$profile['org_logo']=trim($req['org_logo']);
						if(isset($req['bldg_plan']))
							$profile['bldg_plan']=trim($req['bldg_plan']);
						if($req['pwd_old']!=""){
							$pass=$this->CryptoJS->aes_encrypt($req['pwd_old']);
							if($org->pwd!=$pass){
								$this->twiggy->set('status',-1,TRUE);
								$this->twiggy->set('message','แก้ไขรหัสผ่านไม่สำเร็จ<br>Update password fail!',TRUE);
								$this->twiggy->template('lbs/profile_org')->display();
								die();
							}
							else
								$profile['pwd']=trim($this->CryptoJS->aes_encrypt($req['pwd']));
						}
						$query=$DB1->update('org_profile',$profile,array('phone_no'=>$req['phone_no']));
						if($query==1){
							$this->session->set_flashdata('status',1);
							$this->session->set_flashdata('message','แก้ไขข้อมูลส่วนตัวเรียบร้อยแล้ว<br>Update profile successfully');
							$_SESSION['org_profile']=$req;
							redirect(base_url('lbs/profile'));
						}
						else{
							$this->twiggy->set('status',-1,TRUE);
							$this->twiggy->set('message','แก้ไขข้อมูลส่วนตัวไม่สำเร็จ<br>Update profile fail!',TRUE);
							$this->twiggy->template('lbs/profile_org')->display();
						}
					}
					else{
						$profile=[
							'org_id'=>uniqid(),
							'phone_no'=>trim($req['phone_no']),
							'email'=>trim($req['email']),
							'pwd'=>trim($this->CryptoJS->aes_encrypt($req['pwd'])),
							'org_type'=>trim($req['org_type']),
							'org_name'=>trim($req['org_name']),
							'org_logo'=>trim($req['org_logo']),
							'org_branch'=>trim($req['org_branch']),
							'bsn_type'=>trim($req['bsn_type']),
							'estbm_year'=>trim($req['estbm_year']),
							'epy_no'=>trim($req['epy_no']),
							'org_size'=>trim($req['org_size']),
							'bsn_desc'=>trim($req['bsn_desc']),
							'org_addr'=>json_encode($req['org_addr']),
							'fax_no'=>trim($req['fax_no']),
							'org_web'=>trim($req['org_web']),
							'org_ctps'=>trim($req['org_ctps']),
							'org_facility'=>trim($req['org_facility']),
							'bldg_plan'=>trim($req['bldg_plan']),
							'other_info'=>trim($req['other_info']),
							'registered_date'=>date('Y-m-d H:i:s'),
							'modified_date'=>date('Y-m-d H:i:s'),
						];
						$query=$DB1->insert('org_profile',$profile);
						if($query){
							$this->session->set_flashdata('status',1);
							$this->session->set_flashdata('message','สมัครใช้งานเรียบร้อยแล้ว<br>Register successfully');
							$this->session->set_flashdata('citizen_phone_number',$req['phone_no']);
							redirect(base_url('lbs/signin'));
						}
						else{
							$this->twiggy->set('status',-1,TRUE);
							$this->twiggy->set('message','สมัครใช้งานไม่สำเร็จ<br>Register fail!',TRUE);
							$this->twiggy->set('org',$req,TRUE);
							//$this->twiggy->template('lbs/profile_org')->display();
						}
					}
				}
				else{
					$req["citizen_photo"]='';
					$url=$this->def_url['subscribe'];
					if($_FILES){
						if(isset($_FILES["citizen_photo"])){
							if(is_uploaded_file($_FILES["citizen_photo"]["tmp_name"])){
								$dir_name=_UPLOADS.'\files\\';
								$tmp_name=$_FILES["citizen_photo"]["tmp_name"];
								$ext_name=explode(".",$_FILES["citizen_photo"]["name"]);
								$img_name=time().".".$ext_name[sizeof($ext_name)-1];
								move_uploaded_file($tmp_name,$dir_name.$img_name);
								$protocol=$_SERVER["HTTPS"]=="on"?"https://":"http://";
								$ret["link"]=$protocol.$_SERVER["SERVER_NAME"]."/"._UPLOADS_NAME."/files/".$img_name;
								$req["citizen_photo"]=$ret["link"];
							}
						}
					}
					if(!isset($req['citizen_name'])OR$req['citizen_name']=='')
						$req['citizen_name']=$req['citizen_phone_number'];
					if(isset($req['citizen_middle'])&&$req['citizen_middle']!='')
						$req['citizen_name'].=' '.$req['citizen_middle'];
					if(isset($req['citizen_surname'])&&$req['citizen_surname']!='')
						$req['citizen_name'].=' '.$req['citizen_surname'];
					$req['citizen_telephone']=$req['citizen_phone_number'];
					$req['citizen_home_addr']='';
					if(isset($req['citizen_use_pmnt_addr'])&&$req['citizen_use_pmnt_addr']=='on'){
						if(isset($req['citizen_pmnt_address'])&&$req['citizen_pmnt_address']!='')$req['citizen_home_addr'].=$req['citizen_pmnt_address'];
						if(isset($req['citizen_pmnt_subdistrict'])&&$req['citizen_pmnt_subdistrict']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_subdistrict'];
						if(isset($req['citizen_pmnt_district'])&&$req['citizen_pmnt_district']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_district'];
						if(isset($req['citizen_pmnt_province'])&&$req['citizen_pmnt_province']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_province'];
						if(isset($req['citizen_pmnt_postcode'])&&$req['citizen_pmnt_postcode']!='')$req['citizen_home_addr'].=' '.$req['citizen_pmnt_postcode'];
						if(isset($req['citizen_pmnt_country'])&&$req['citizen_pmnt_country']!='')$req['citizen_home_addr'].=', '.$req['citizen_pmnt_country'];
					}
					else{
						if(isset($req['citizen_home_address'])&&$req['citizen_home_address']!='')$req['citizen_home_addr'].=$req['citizen_home_address'];
						if(isset($req['citizen_home_subdistrict'])&&$req['citizen_home_subdistrict']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_subdistrict'];
						if(isset($req['citizen_home_district'])&&$req['citizen_home_district']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_district'];
						if(isset($req['citizen_home_province'])&&$req['citizen_home_province']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_province'];
						if(isset($req['citizen_home_postcode'])&&$req['citizen_home_postcode']!='')$req['citizen_home_addr'].=' '.$req['citizen_home_postcode'];
						if(isset($req['citizen_home_country'])&&$req['citizen_home_country']!='')$req['citizen_home_addr'].=', '.$req['citizen_home_country'];
					}
					$req['citizen_address']=$req['citizen_home_addr'];
					$req['address']=[];
					$req['address'][0]=[
						'citizen_home_no'=>$req['citizen_home_no'],
						'citizen_home_addr'=>$req['citizen_home_addr'],
						'citizen_home_lat'=>$req['citizen_home_lat'],
						'citizen_home_lon'=>$req['citizen_home_lon'],
					];
					$req['address'][1]=[
						'citizen_work_no'=>$req['citizen_work_no'],
						'citizen_work_addr'=>$req['citizen_work_addr'],
						'citizen_work_lat'=>$req['citizen_work_lat'],
						'citizen_work_lon'=>$req['citizen_work_lon'],
					];
					$req['info']=[
						'illness_history'=>isset($req['illness_history'])?$req['illness_history']:'',
						'other'=>isset($req['other'])?$req['other']:'',
						'drug_allergy'=>isset($req['drug_allergy'])?$req['drug_allergy']:'',
						'hospital'=>isset($req['hospital'])?$req['hospital']:'',
						'group_blood'=>isset($req['group_blood'])?$req['group_blood']:'',
						'brithday'=>isset($req['brithday'])?$req['brithday']:'',
						'gender'=>isset($req['gender'])?$req['gender']:'',
						'other_body'=>isset($req['other_body'])?$req['other_body']:'',
						'congenital_disease'=>isset($req['congenital_disease'])?$req['congenital_disease']:'',
						'citizen_type'=>isset($req['citizen_type'])?$req['citizen_type']:'',
						'citizen_title'=>isset($req['citizen_title'])?$req['citizen_title']:'',
						'citizen_height'=>isset($req['citizen_height'])?$req['citizen_height']:'',
						'citizen_weight'=>isset($req['citizen_weight'])?$req['citizen_weight']:'',
						'citizen_religion'=>isset($req['citizen_religion'])?$req['citizen_religion']:'',
						'citizen_scar'=>isset($req['citizen_scar'])?$req['citizen_scar']:'',
						'citizen_handicapped'=>isset($req['citizen_handicapped'])?$req['citizen_handicapped']:'',
						'citizen_nationality'=>isset($req['citizen_nationality'])?$req['citizen_nationality']:'',
						'citizen_citizenship'=>isset($req['citizen_citizenship'])?$req['citizen_citizenship']:'',
						'citizen_passport'=>isset($req['citizen_passport'])?$req['citizen_passport']:'',
						'citizen_photo'=>isset($req['citizen_photo'])?$req['citizen_photo']:'',
						'contact_name'=>isset($req['contact_name'])?$req['contact_name']:'',
						'contact_surname'=>isset($req['contact_surname'])?$req['contact_surname']:'',
						'contact_phone_number'=>isset($req['contact_phone_number'])?$req['contact_phone_number']:'',
						'contact_occupation'=>isset($req['contact_occupation'])?$req['contact_occupation']:'',
						'contact_relationship'=>isset($req['contact_relationship'])?$req['contact_relationship']:'',
						'contact_address'=>isset($req['contact_address'])?$req['contact_address']:'',
						'citizen_ft_title'=>isset($req['citizen_ft_title'])?$req['citizen_ft_title']:'',
						'citizen_ft_name'=>isset($req['citizen_ft_name'])?$req['citizen_ft_name']:'',
						'citizen_ft_surname'=>isset($req['citizen_ft_surname'])?$req['citizen_ft_surname']:'',
						'citizen_ft_birthday'=>isset($req['citizen_ft_birthday'])?$req['citizen_ft_birthday']:'',
						'citizen_ft_occupation'=>isset($req['citizen_ft_occupation'])?$req['citizen_ft_occupation']:'',
						'citizen_ft_status'=>isset($req['citizen_ft_status'])?$req['citizen_ft_status']:'',
						'citizen_blood_ft'=>isset($req['citizen_blood_ft'])?$req['citizen_blood_ft']:'',
						'congenital_disease_ft'=>isset($req['congenital_disease_ft'])?$req['congenital_disease_ft']:'',
						'drug_allergy_ft'=>isset($req['drug_allergy_ft'])?$req['drug_allergy_ft']:'',
						'regular_hospital_ft'=>isset($req['regular_hospital_ft'])?$req['regular_hospital_ft']:'',
						'illness_history_ft'=>isset($req['illness_history_ft'])?$req['illness_history_ft']:'',
						'citizen_mt_title'=>isset($req['citizen_mt_title'])?$req['citizen_mt_title']:'',
						'citizen_mt_name'=>isset($req['citizen_mt_name'])?$req['citizen_mt_name']:'',
						'citizen_mt_surname'=>isset($req['citizen_mt_surname'])?$req['citizen_mt_surname']:'',
						'citizen_mt_birthday'=>isset($req['citizen_mt_birthday'])?$req['citizen_mt_birthday']:'',
						'citizen_mt_occupation'=>isset($req['citizen_mt_occupation'])?$req['citizen_mt_occupation']:'',
						'citizen_mt_status'=>isset($req['citizen_mt_status'])?$req['citizen_mt_status']:'',
						'citizen_blood_mt'=>isset($req['citizen_blood_mt'])?$req['citizen_blood_mt']:'',
						'congenital_disease_mt'=>isset($req['congenital_disease_mt'])?$req['congenital_disease_mt']:'',
						'drug_allergy_mt'=>isset($req['drug_allergy_mt'])?$req['drug_allergy_mt']:'',
						'regular_hospital_mt'=>isset($req['regular_hospital_mt'])?$req['regular_hospital_mt']:'',
						'illness_history_mt'=>isset($req['illness_history_mt'])?$req['illness_history_mt']:'',
						'citizen_marital'=>isset($req['citizen_marital'])?$req['citizen_marital']:'',
						'citizen_sp_title'=>isset($req['citizen_sp_title'])?$req['citizen_sp_title']:'',
						'citizen_sp_name'=>isset($req['citizen_sp_name'])?$req['citizen_sp_name']:'',
						'citizen_sp_surname'=>isset($req['citizen_sp_surname'])?$req['citizen_sp_surname']:'',
						'citizen_sp_birthday'=>isset($req['citizen_sp_birthday'])?$req['citizen_sp_birthday']:'',
						'citizen_sp_occupation'=>isset($req['citizen_sp_occupation'])?$req['citizen_sp_occupation']:'',
						'citizen_sp_status'=>isset($req['citizen_sp_status'])?$req['citizen_sp_status']:'',
						'citizen_blood_sp'=>isset($req['citizen_blood_sp'])?$req['citizen_blood_sp']:'',
						'congenital_disease_sp'=>isset($req['congenital_disease_sp'])?$req['congenital_disease_sp']:'',
						'drug_allergy_sp'=>isset($req['drug_allergy_sp'])?$req['drug_allergy_sp']:'',
						'regular_hospital_sp'=>isset($req['regular_hospital_sp'])?$req['regular_hospital_sp']:'',
						'illness_history_sp'=>isset($req['illness_history_sp'])?$req['illness_history_sp']:'',
						'org_id'=>isset($req['org_id'])?$req['org_id']:'',
						'citizen_work_org'=>isset($req['citizen_work_org'])?$req['citizen_work_org']:'',
						'citizen_work_pos'=>isset($req['citizen_work_pos'])?$req['citizen_work_pos']:'',
						'citizen_pmnt_no'=>isset($req['citizen_pmnt_no'])?$req['citizen_pmnt_no']:'',
						'citizen_pmnt_address'=>isset($req['citizen_pmnt_address'])?$req['citizen_pmnt_address']:'',
						'citizen_pmnt_subdistrict'=>isset($req['citizen_pmnt_subdistrict'])?$req['citizen_pmnt_subdistrict']:'',
						'citizen_pmnt_district'=>isset($req['citizen_pmnt_district'])?$req['citizen_pmnt_district']:'',
						'citizen_pmnt_province'=>isset($req['citizen_pmnt_province'])?$req['citizen_pmnt_province']:'',
						'citizen_pmnt_postcode'=>isset($req['citizen_pmnt_postcode'])?$req['citizen_pmnt_postcode']:'',
						'citizen_pmnt_country'=>isset($req['citizen_pmnt_country'])?$req['citizen_pmnt_country']:'',
						'citizen_pmnt_lat'=>isset($req['citizen_pmnt_lat'])?$req['citizen_pmnt_lat']:'',
						'citizen_pmnt_lon'=>isset($req['citizen_pmnt_lon'])?$req['citizen_pmnt_lon']:'',
					];
					$fields=array_filter($req);
					$header=$this->def_header;
					$postvars=json_encode($fields);
					$ch=curl_init();
					curl_setopt($ch,CURLOPT_URL,$url);
					curl_setopt($ch,CURLOPT_POST,count($fields));
					curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
					curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
					curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
					curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
					$result=curl_exec($ch);
					curl_close($ch);
					$arr=json_decode($result);
					if($arr->status==0){
						if($functions=='ajax'){
							echo json_encode(['status'=>1,'message'=>'เพิ่มผู้ติดต่อเรียบร้อยแล้ว<br>Add new contacts successfully']);
							die();
						}
						else{
							$this->session->set_flashdata('status',1);
							$this->session->set_flashdata('message','สมัครใช้งานเรียบร้อยแล้ว<br>Register successfully');
							$this->session->set_flashdata('citizen_phone_number',$req['citizen_phone_number']);
							redirect(base_url('lbs/signin'));
						}
					}
					else{
						if($functions=='ajax'){
							echo json_encode(['status'=>0,'message'=>'เพิ่มผู้ติดต่อไม่สำเร็จ<br>Add new contacts fail!']);
							die();
						}
						else{
							$this->twiggy->set('status',-1,TRUE);
							$this->twiggy->set('message','สมัครใช้งานไม่สำเร็จ<br>Register fail!',TRUE);
							$this->twiggy->set('citizen',$req,TRUE);
							$this->twiggy->template('lbs/profile_citizen')->display();
						}
					}
				}
			}
			if($categories=='org'){
				$result=[];
				$DB1=$this->load->database('citizenDB',TRUE);
				$query=$DB1->select('citizen_code,citizen_name,citizen_surname')->get('citizen_profile')->result_array();
				foreach($query as $key=>$value)
					$result[]=[
						'id'=>$value['citizen_code'],
						'name'=>$value['citizen_name'].($value['citizen_surname']!=""?" ".$value['citizen_surname']:"")
					];
				$this->twiggy->set('citizen_profiles',$result,TRUE);
				$this->twiggy->template('lbs/profile_org')->display();
			}
			else{
				$result=[];
				$DB1=$this->load->database('citizenDB',TRUE);
				$query=$DB1->select('org_id,org_name')->get('org_profile')->result_array();
				foreach($query as $key=>$value)
					$result[]=['id'=>$value['org_id'],'name'=>$value['org_name']];
				$this->twiggy->set('org_profiles',$result,TRUE);
				$this->twiggy->template('lbs/profile_citizen')->display();
			}
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function subscriber($functions=NULL){
		try{
			if($functions=='display'&&isset($_REQUEST)&&!empty($_REQUEST)){
				$req=$_REQUEST;
				$recordsTotal=0;
				$data=[];
				$start=0;
				$CITIZEN=$this->load->database('citizenDB',TRUE);
				$total=$CITIZEN->get('citizen_profile');
				if($total){
					$recordsTotal=count($total->result_array());
				}
				$recordsFiltered=$recordsTotal;
				$select=['citizen_name','citizen_surname','citizen_phone_number','citizen_email','citizen_id','citizen_code'];
				$query=$CITIZEN;
				$query=$query->select(implode(',',$select));
				if(isset($req['search']['value'])&&$req['search']['value']!=''){
					$query=$query->like('"citizen_name"',$req['search']['value']);
					$query=$query->or_like('"citizen_surname"',$req['search']['value']);
					$query=$query->or_like('"citizen_phone_number"',$req['search']['value']);
					$query=$query->or_like('"citizen_email"',$req['search']['value']);
					$query=$query->or_like('"citizen_id"',$req['search']['value']);
				}
				$filtered=clone $query;
				$filtered=$filtered->get('citizen_profile');
				if($filtered){
					$filtered=$filtered->result_array();
					if(count($filtered)>0)
						$recordsFiltered=count($filtered);
				}
				if(isset($req['start'])&&isset($req['length'])){
					$query=$query->limit($req['length'],$req['start']);
					$start=$req['start']+1;
				}
				$query=$query->get('citizen_profile');
				if($query){
					$data=[];
					$result=$query->result_array();
					foreach($result as$keys=>$values){
						$nested_data=[];
						$nested_data[]=$start;
						foreach($values as$key=>$value)
							$nested_data[]=$value;
						$data[]=$nested_data;
						$start+=1;
					}
					echo json_encode(['draw'=>$req['draw'],'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>$data]);
				}
				else
					throw new Exception();
			}
			else if(isset($_GET['citizen_code'])){
				$id=$_GET['citizen_code'];
				$CITIZEN=$this->load->database('citizenDB',TRUE);
				$query=$CITIZEN;
				$query=$query->where('"citizen_code"',$id)->get('citizen_profile');
				if($query){
					$data=[];
					$result=$query->result_array();
					foreach($result[0] as$key=>$value){
						if($key=='address'OR$key=='info')
							$data[$key]=json_decode($value);
						else
							$data[$key]=$value;
					}
					echo json_encode($data);
				}
			}
			else{
				$def_url=$this->def_url;
				$this->twiggy->set('def_url',$def_url,TRUE);
				$this->twiggy->template('lbs/subscriber')->display();
			}
		}
		catch(Exception $e){
			echo json_encode(['draw'=>0,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[]]);
		}
	}
	public function genesys_create_user(){
		try{
			$url='https://demosrv-gws.genesyslab.com/api/v2/users';
			$uname='user'.time();
			//$uname='wisarud';
			$data=[];
			$data['firstName']='firstName';
			//$data['firstName']='Wisarud';
			$data['lastName']='lastName';
			//$data['lastName']='Techa';
			$data['userName']=$uname;
			$data['password']='1234';
			//$data['password']='123456';
			$data['emailAddress']=$uname.'@email.domain';
			//$data['emailAddress']=$uname.'_t@callvoice.co.th';
			$data['roles']=['ROLE_AGENT'];
			$data['enabled']=TRUE;
			$data['changePasswordOnFirstLogin']=FALSE;
			$data['contactCenterId']='b1146522-39e6-4cc9-99cb-b05f58e0df1a';
			$response=$this->curl_services($url,json_encode($data),'POST');
			$result=(array)json_decode($response,TRUE);
			echo $result['statusCode']==0?'User created, ID: '.$result['id'].'.':'';
		}
		catch(Exception $e){}
	}
	public function genesys_delete_user($id=''){
		try{
			if($id!=''){
				$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id;
				$data=[];
				$response=$this->curl_services($url,json_encode($data),'DELETE');
				$result=(array)json_decode($response,TRUE);
				redirect(base_url('lbs/genesys_get_users'));
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_update_user($id=''){
		try{
			if($id!=''){
				$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id;
				$updated=time();
				$data=[];
				$data['firstName']='firstName '.$updated;
				$data['lastName']='lastName '.$updated;
				$response=$this->curl_services($url,json_encode($data),'PUT');
				$result=(array)json_decode($response,TRUE);
				redirect(base_url('lbs/genesys_get_users'));
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_delete_user_skills($id=''){
		try{
			if($id!=''){
				$data=[];
				$ids=[
					'Collections'=>'d76c9b48-1ded-4cb0-8d46-584427731988',
					'JaySkilltest1'=>'08d327ae-1b76-4290-a12a-ccd903502a64',
				];
				foreach($ids as$key=>$value){
					$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id.'/skills/'.$value;
					$response=$this->curl_services($url,json_encode($data),'DELETE');
					$result=(array)json_decode($response,TRUE);
					$results[]=['name'=>$key,'statusCode'=>$result['statusCode']];
				}
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($results);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_update_user_skills($id=''){
		try{
			if($id!=''){
				$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id.'/skills';
				$data=[];
				$results=[];
				$uris=[
					'CallTaker'=>'http://demosrv.genesyslab.com:8089/api/v2/skills/88bf071d-c43e-4e50-8cca-f7a83385bb2f',
				];
				foreach($uris as$key=>$value){
					$data['uri']=$value;
					$data['level']=0;
					$response=$this->curl_services($url,json_encode($data),'POST');
					$result=(array)json_decode($response,TRUE);
					$results[]=['name'=>$key,'statusCode'=>$result['statusCode']];
				}
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($results);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_delete_user_groups($id=''){
		try{
			if($id!=''){
				$data=[];
				$ids=[
					'JayzGroupBusinessUnit'=>'2-ace7c796-8bfb-4041-947d-9fabdf0de9fe',
				];
				foreach($ids as$key=>$value){
					$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id.'/groups/'.$value;
					$response=$this->curl_services($url,json_encode($data),'DELETE');
					$result=(array)json_decode($response,TRUE);
					$results[]=['name'=>$key,'statusCode'=>$result['statusCode']];
				}
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($results);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_update_user_groups($id=''){
		try{
			if($id!=''){
				$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id.'/groups';
				$data=[];
				$results=[];
				$uris=[
					'BKK'=>'http://demosrv.genesyslab.com:8089/api/v2/groups/2-cde88957-d269-4d30-8f25-6920087b1490',
				];
				foreach($uris as$key=>$value){
					$data['uri']=$value;
					$response=$this->curl_services($url,json_encode($data),'POST');
					$result=(array)json_decode($response,TRUE);
					$results[]=['name'=>$key,'statusCode'=>$result['statusCode']];
				}
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($results);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_delete_user_places($id=''){
		try{
			if($id!=''){
				$data=[];
				$ids=[
					'SF74'=>'0212854c-4be5-46f8-910d-db430681637d',
				];
				foreach($ids as$key=>$value){
					$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id.'/places/'.$value;
					$response=$this->curl_services($url,json_encode($data),'DELETE');
					$result=(array)json_decode($response,TRUE);
					$results[]=['name'=>$key,'statusCode'=>$result['statusCode']];
				}
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($results);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_update_user_places($id=''){
		try{
			if($id!=''){
				$url='https://demosrv-gws.genesyslab.com/api/v2/users/'.$id.'/places';
				$data=[];
				$data['uris']=[
					'http://demosrv.genesyslab.com:8089/api/v2/places/020b9806-a89a-4d48-8ca1-98376da0a075',
					'http://demosrv.genesyslab.com:8089/api/v2/places/0212854c-4be5-46f8-910d-db430681637d'
				];
				$response=$this->curl_services($url,json_encode($data),'POST');
				$result=(array)json_decode($response,TRUE);
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($result);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_update_user_access_group($id=''){
		try{
			if($id!=''){
				// $url='https://demosrv-gws.genesyslab.com/api/v2/platform/configuration/access-groups/102';
				$url='https://demosrv-gws.genesyslab.com/api/v2/platform/configuration/access-groups';
				$data=
				[
					'delta-access-group'=>
					[
						'CfgDeltaGroup'=>
						[
							'CfgGroup'=>
							[
								'DBID'=>'24464',
							],
							'memberIDs'=>
							[
								[
									'cfgID'=>
									[
										'CSID'=>'0',
										'DBID'=>$id,
										'type'=>'3',
									],
								],
							],
							'type'=>'1', // 1: Switch, 3: Person, 5: Agent Group, 21: Access Group
						],
					],
				];
				// dieArray($data);
				$response=$this->curl_services($url,json_encode($data),'PUT');
				$result=(array)json_decode($response,TRUE);
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($result);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_update_contacts($id=''){
		try{
			if($id!=''){
				//$url='https://demosrv-gws.genesyslab.com/api/v2/contacts/'.$id;
				$url='https://demosrv-gws.genesyslab.com/api/v2/contacts';
				$data=[];
				$data['phoneNumber']='1212312121';
				$data['employeeId']='JayzTestNa';
				$data['name']='JayzTestContactNa';
				$response=$this->curl_services($url,json_encode($data),'POST');
				$result=(array)json_decode($response,TRUE);
				//redirect(base_url('lbs/genesys_get_users'));
				echo json_encode($result);
			}
			else
				throw new Exception();
		}
		catch(Exception $e){}
	}
	public function genesys_get_users($offset=0,$limit=100,$sortBy='userName'){
		try{
			$j=1;
			$users=[];
			$url='https://demosrv-gws.genesyslab.com/api/v2/users';
			$data='{}';
			$method='GET';
			$tc=(array)json_decode($this->curl_services($url,$data,$method),TRUE);
			for($i=0;$i<=$tc['totalCount'];$i+=$limit){
				$url='https://demosrv-gws.genesyslab.com/api/v2/users?fields=*&offset='.$i.'&limit='.$limit.'&sortBy='.$sortBy;
				$result=(array)json_decode($this->curl_services($url,$data,$method),TRUE);
				foreach($result['users']as$key=>$value){
					$users[]=$value;
					//echo $j.'.) '.$value['userName'].' ';
					//echo '<a href="'.base_url('lbs/genesys_update_user_skills/'.$value['id']).'">Assign skills</a> / ';
					//echo '<a href="'.base_url('lbs/genesys_delete_user_skills/'.$value['id']).'">Delete skills</a> / ';
					//echo '<a href="'.base_url('lbs/genesys_update_user/'.$value['id']).'">Update user</a> / ';
					//echo '<a href="'.base_url('lbs/genesys_delete_user/'.$value['id']).'">Delete user</a><br>';
					$j+=1;
				}
			}
			echo json_encode($users);
		}
		catch(Exception $e){
			echo json_encode([]);
		}
	}
	public function curl_services($url,$data,$method){
		try{
			$token=base64_encode('jayz:1234');
			$header=[];
			$header[]='Accept: application/json';
			$header[]='Authorization: Basic '.$token;
			$header[]='X-CSRF-HEADER: X-CSRF-TOKEN';
			$header[]='X-CSRF-TOKEN: ';
			$header[]='Content-Type: application/json';
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
			curl_setopt($ch,CURLOPT_USERPWD,base64_decode($token));
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			$response=curl_exec($ch);
			return $response;
		}
		catch(Exception $e){
			return [];
		}
	}
	public function test(){
		try{
			$filename=time().'_C'.date('YmdHis').(sprintf('%03d',round((microtime()-floor(microtime()))*999))).'.jpg';
			echo ($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['HTTP_HOST'].'/uploads/campaign/'.$filename;
			die();
			/*****************************************************************************************************/
			// Create
			// $url=base_url('V2/campaign/create');
			// $input=['title'=>'Campaign creation','message'=>'Test create.'];
			// $url=base_url('V2/polygon/create');
			// $input=['title'=>'Polygon creation','message'=>'Test create.'];
			/*****************************************************************************************************/
			// Read
			// $url=base_url('V2/campaign/read');
			// $input=['campaign_id'=>'C20210118174000696'];
			// $url=base_url('V2/polygon/read');
			// $input=['polygon_id'=>'PP20210121161204067'];
			/*****************************************************************************************************/
			// Update
			// $url=base_url('V2/campaign/update');
			// $input=['campaign_id'=>'C20210118174000696','title'=>'Campaign updation','message'=>'Test update.'];
			// $url=base_url('V2/polygon/update');
			// $input=['polygon_id'=>'PP20210121161204067','title'=>'Polygon updation','message'=>'Test update.'];
			/*****************************************************************************************************/
			// Delete
			// $url=base_url('V2/campaign/delete');
			// $input=['campaign_id'=>'C20210118174000696'];
			// $url=base_url('V2/polygon/delete');
			// $input=['polygon_id'=>'PP20210121161204067'];
			/*****************************************************************************************************/
			// List
			// $url=base_url('V2/campaign');
			// $input=['campaign_id'=>''];
			// $input=['campaign_id'=>'','check_approve'=>'NO'];
			$url=base_url('V2/polygon');
			$input=['polygon_id'=>''];
			/*****************************************************************************************************/
			$fields=array_filter($input);
			$postvars=json_encode($fields);
			$header=['Accept:application/json','Content-Type:application/json','Authorization:bXlabkZsMWZXVFhDK2NsMWgvMm9GZz09'];
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			$result=curl_exec($ch);
			if(curl_errno($ch)){
				$result['status']=-1;
				$result['message']='Fail';
				$result['severity']='high';
			}
			curl_close($ch);
			echo '<pre>';
			print_r(json_decode($result));
			echo '</pre>';
		}
		catch(Exception $e){
			print_r($e);
		}
	}
	public function user($functions=NULL){
		try{
			$this->twiggy->template('lbs/user')->display();
		}
		catch(Exception $e){
			show_404();
		}
	}
	public function V1(){
		try{
			$this->load->view('LBS_v1');
		}
		catch(Exception $e){
			show_404();
		}
	}
}