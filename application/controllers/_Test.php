<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller
{
    var $mssql;
    function __construct()
    {
        parent::__construct();
        //$this->mssql = $this->load->database ( 'mssql', TRUE );
    }

    function chkArea($lat = "" , $lon="" ){
      set_time_limit(0); 
      $time_start = microtime(true);
      $output ="" ;
      if($lat!="" && $lon!=""){
        $str =  ' '.$lat.'  '.  $lon ;
        //$str = '"C:\Program Files\Python38\python.exe" '.$str ; // For Lab


        //$command = escapeshellcmd('python /home/cv/pyChkAreaTH/main.py '. $str );
        $output = shell_exec( 'python /home/cv/pyChkAreaTH/main.py '. $str );
        $output = str_replace( "u'" , "'" , $output ) ;
        $output = str_replace( "'" , "\"" , $output ) ;
        //echo $output;

      }else{
          $output = '{"message":"NoData"}';
      }
      echo "<pre>" ;
       print_r( json_decode ($output ,true ) ) ;
      $time_end = microtime(true);
      $duration = $time_end - $time_start;
      echo "Duration : ". number_format(  $duration , 3 , '.', '') ;
    }

    function get_some_mysql_rows(){
       //use  $this->db for default
       $query = $this->db->query('select * from mysql_table');
       //...
    }
    function xx(){
    	echo 'Connecting..!';
 		//print_r( $this->db ) ;

    	/*print_r($this->mssql) ; */
$xx = $this->db->query("SELECT * from uc_users");

print_r($xx->result_array()) ;
    	die();

		$server = "203.170.193.91";
		$username = 'c3i';
		$password = 'cv1234';
		$database = 'C3I';

	    $connectionOptions = array(
	        "Database" => $database ,
	        "Uid" => $username  ,
	        "PWD" => $password
	    );
	    //Establishes the connection
	    $conn = sqlsrv_connect($server, $connectionOptions);


	    $tsql= "SELECT * from uc_users";
	    $getResults= sqlsrv_query($conn, $tsql);
	    while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
  print_r($row) ;
    echo ("<br/>");
}
	    if($conn) { echo "Connected!"; }






	}

  function system_setting()
  {
    $system_setitng = json_decode($this->curlService(array('url' => _SYS_SETTING, 'method' => 'GET')), TRUE);
    echo '<pre>';
    print_r($system_setitng);
    echo '</pre>';
  }

  function curlService($data){
    $ch = curl_init();
    $url = $data['url'] ;
    $time = 5 ;
      curl_setopt($ch, CURLOPT_URL, $url );
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $data['method'] );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data['data']);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //curl_setopt($ch,CURLOPT_HEADER, true);
    curl_setopt($ch,CURLOPT_TIMEOUT,$time);

      $response = curl_exec ($ch);

      curl_close ($ch);

      return  $response ;
  }

 }


