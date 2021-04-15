<?php error_reporting(E_ERROR) ; 
defined('BASEPATH') OR exit('No direct script access allowed');

class FacebookInbox extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	//TokenID , IxnID, pantip id , agent id
	public function __construct()
	{
		parent::__construct(); 
		//$this->load->model('Wlogs_model');
		//$this->load->database('mysql'); 
		$this->load->model('Writelogs_model');
	}
 
	public function getInbox( $fbID="" , $agentID="" )
	{
		// All by page --> 878099928905712/conversations?fields=subject,senders,messages{message,from,id,to,created_time}
		 
		// message by ID --> t_10201419405843508?fields=subject,senders,messages{message,from,id,to,created_time}
		$time_start = microtime(true);

		$url = _FB_API.'/'.$fbID.'?fields=subject,senders,messages{message,from,id,to,created_time}&access_token='._FB_TOKEN;
		$res = $this->curl_req($url, 'GET' , array() );
		$resData = json_decode($res , true) ;
		if( isset($resData['error']) ){
			$this->Writelogs_model->wLogs( $agentID , $_SERVER['REMOTE_ADDR'] , 'ERROR', 'FB-GetInbox' ,  "" , $time_start , '-1', $fbID , $res , 'Social'  ) ;
		}else{
			$this->Writelogs_model->wLogs( $agentID , $_SERVER['REMOTE_ADDR'] , 'INFO', 'FB-GetInbox' , "" , $time_start , '0', $fbID  ,  $res , 'Social'  ) ;
		}
		
		print_r($res) ; 

		//echo '<pre>';
		 

	}
	function sendMessage(){
		$time_start = microtime(true);
		$req = $_REQUEST ;
		if(isset($req['chat_id']) && $req['chat_id'] !='' ){

		}else{
			$ret['error'] = true ;
			$ret['error_message'] = "ไม่มีรหัสกระทู้" ;
			$this->Writelogs_model->wLogs( $req['agentID']  , $_SERVER['REMOTE_ADDR'] , 'Error', 'SentInbox' , 'ไม่มีรหัสกระทู้', $time_start , '-1', json_encode( $req) , json_encode($ret), 'Social' ) ;
			print_r( json_encode($ret)) ; die();
		}
		if(isset($req['message']) && $req['message'] !='' ){

		}else{
			$ret['error'] = true ;
			$ret['error_message'] = "ไม่มีข้อความ" ;
			$this->Writelogs_model->wLogs( $req['agentID']  , $_SERVER['REMOTE_ADDR'] , 'Error', 'SentInbox' , 'ไม่มีข้อความ', $time_start , '-1', json_encode( $req) , json_encode($ret), 'Social' ) ;
			print_r( json_encode($ret)) ; die();
		}

		$data_x = [] ;
		$data_x['access_token'] = _PANTIP_TOKEN ;
		$data_x['inbox_owner_uid'] = $req['owner'] ;
		$data_x['msgid'] = $req['chat_id'] ;
		$data_x['title'] = 'สอบถามข้อมูล' ;
		$data_x['desc'] = $req['message'] ;
		$data_x['remote_addr'] = $_SERVER['REMOTE_ADDR'] ;
		$data_x['x_forwarded_for'] = $_SERVER['HTTP_HOST'] ;
 		$senData['json'] = json_encode($data_x) ;
 		//print_r($senData) ; die();
 		//echo '<pre>';
 		//echo pm_reply_message ;
 		//print_r($senData) ;  
		$res = $this->curl_req( pm_reply_message , 'POST' , $senData  );

		$this->Writelogs_model->wLogs( $req['agentID']  , $_SERVER['REMOTE_ADDR'] , 'Success', 'SentInbox' , 'CheckResult', $time_start , '0', json_encode( $req)  , $res, 'Social' ) ;
		print_r( ( $res)) ;
		//print_r('{"status":"success","comment_no":33,"comment_id":90344989,"topic_id":"39439264","member_id":1920,"nickname":"Nrasra","createAt":{"$date":{"$numberLong":"1574922057994"}}}') ;
	}

 
  
 
	function curl_req($url, $method, $data)
	{

		$ch = curl_init($url);

		$CURL_OPTS = array(
			//CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_CONNECTTIMEOUT => 1000,
			CURLOPT_RETURNTRANSFER => true,
			//CURLOPT_TIMEOUT        => 60,
			CURLOPT_POST => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT        => 1000,
			CURLOPT_CUSTOMREQUEST =>  $method ,
			CURLOPT_POSTFIELDS =>  $data,
			CURLOPT_SSL_VERIFYPEER => false,
			//CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
			// CURLOPT_PROXY => 'www-gw.tac.co.th:8080',
			//CURLOPT_PROXYUSERPWD =>'tac_bangkok\putthiwat:Putth1w0rd'
		 );
 
		//$header[] = 'x-requested-with: XMLHttpRequest';
		//$header[] = 'Content-Type:application/json';
		//$header[] = 'Content-Length:'.strlen($data);
		//print_r($header) ;
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		 
		curl_setopt_array($ch, $CURL_OPTS);
		$result = curl_exec($ch);
		curl_close($ch);
		if(!$result) {
			return false;
		}else{
			return $result;
		}
	}

}
