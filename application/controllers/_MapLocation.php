<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MapLocation extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->config('twiggy');
		$this->load->library('twiggy');
		$this->load->model('common_model');
		$this->load->model('config_model');
 		$this->load->model('AES');
 		$this->load->model('Writelogs_model');
	}

	public function getLocation( $latlon = "" , $smhID=""   )
	{
 
		$this->load->model('CryptoJS');
		$x['mobileNo'] = "0809955502";
		$x['timestamp'] = time() ;
		$token = urlencode( base64_encode( json_encode($x)) ) ; 
		/*$data = $this->CryptoJS->cryptoJsAesEncrypt( key_decrypt , json_encode($x) ); 
	 	print_r( urlencode($data)) ; 
		//die();
		$token = urldecode($data) ;
		$token = $this->CryptoJS->cryptoJsAesDecrypt( key_decrypt ,  ($token) ); 
		$token = (json_decode($token,true)) ; 
		print_r($token) ;*/



		$g = new PHP_AES_Cipher();
		$decrypt = $g->decrypt("GeeksforGeeks","1234567891011121", $_REQUEST['token'] );
 
		$token = json_decode( $decrypt , true )  ;
		
		if($token['ani']){

		}else{

			echo "Wrong data";die();
		}
		//print_r( date("Ydm H:i:s" , $token['timekey'] ) ) ;
		$this->twiggy->set('parameter', 	 $decrypt );
		$this->twiggy->set('mobileNo', 	 $token['ani'] );
		$this->twiggy->set('timestamp',  $token['timekey'] );
		//$this->twiggy->set('base_url', 	 _M_SERVICE );
		$this->twiggy->set('base_url', 	 _M_SERVICE );
		$this->twiggy->set('_MAP_KEY', 	 _MAP_KEY );
		
		$this->twiggy->set('_LOCATION_LINK_EXPIRE', 	 _LOCATION_LINK_EXPIRE );
		$this->twiggy->template('map/tracking/getLocation')->display();
		
	}

	public function saveLocation( )
	{
		$req = $_REQUEST ;  
		//print_r($req ) ;
		//echo _LOGS ; 
		if($req['ani']==""){ $req['ani'] = 'blank'; }
		if( file_put_contents(_LOGS."/191nn/reporter/".$req['ani']."_".$req['timekey']  , json_encode($req) ) ){
			$ret['status'] = 0 ;
		}else{
			$ret['status'] = -1 ;
		 
		}
		print_r( json_encode($ret)) ;
	}
	 
	public function saveLocation_2( )
	{
		$time_start = microtime(true);
		//$ret['status'] = "true" ;
		//print_r(json_encode($ret)); die();
		$req = $_REQUEST ;  
		//print_r($req ) ;
		//echo _LOGS ; 
		if($req['ani']==""){ $req['ani'] = 'blank'; }

		$req['latitude'] = number_format(  $req['latitude'] , 6, '.', '');
		$req['longitude'] = number_format(  $req['longitude'] , 6, '.', '');
		$req['altitude'] = number_format(  $req['altitude'] , 6, '.', '');
		
		//$res = $this->curlService( "http://43.255.240.22:8500/sms" , $req, "POST");
		$res = $this->curlService( "http://127.0.0.1:8005/sms" , $req, "POST");

 
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  "WebLocation"  , $_SERVER['REMOTE_ADDR'] , 'Info', 'saveLocation_2', 'sms' , $duration  , 0 , json_encode($req) , $res  , 'MobileApp');



		print_r(  ($res) ) ;
	}

	public function saveLocation_track( )
	{
		//echo '-------';
		header("Access-Control-Allow-Origin: *");
		$time_start = microtime(true);
		//$ret['status'] = "true" ;
		//print_r(json_encode($ret)); die();
		$req = $_REQUEST ;  

 
		$d = date('Ymd');
		$path = _LOGS.'vdoTracking/'.$d  ; 
		if (!file_exists( $path )) {
		    mkdir( $path  , 0777, true);
		}
		 

		//print_r( json_encode( $req) ) ;
		//echo _LOGS ; 
		if($req['ani']==""){ $req['ani'] = 'blank'; }

		$req['latitude'] = number_format(  $req['latitude'] , 6, '.', '');
		$req['longitude'] = number_format(  $req['longitude'] , 6, '.', '');
		$req['altitude'] = number_format(  $req['altitude'] , 6, '.', '');
		
		file_put_contents( $path.'/'.$req['name'] , date('Y/m/d H:i:s')."|".json_encode($req).PHP_EOL , FILE_APPEND) ;
		 
		$time_end = microtime(true);
		$duration = $time_end - $time_start;
		$this->Writelogs_model->wLogs(  "WebLocation"  , $_SERVER['REMOTE_ADDR'] , 'Info', 'saveLocation_track', 'vdo' , $duration  , 0 , json_encode($req) , $res  , 'MobileApp');



		print_r(  json_encode($req) ) ;
	}

	public function viewTracking( $nameMobile=""  )
	{
 		//echo $nameMobile ;
		 
		$this->twiggy->set('nameMobile', 	 $nameMobile );
		 
		$this->twiggy->set('base_url', 	 _M_SERVICE );
		$this->twiggy->set('_MAP_KEY', 	 _MAP_KEY );
		
	  
		$this->twiggy->template('map/tracking/viewTracking')->display();
		
	}

	public function getTracking ( $nameMobile=""  )
	{
 		//print_r($_REQUEST) ;
 		$d = date('Ymd');
		$path = _LOGS.'vdoTracking/'.$d  ; 
		//$data = file_get_contents( $path.'/'.$nameMobile ) ; 

		$file = fopen( $path.'/'.$nameMobile ,"r");
		$dataRet = "";
		$ds = "" ;
		while(! feof($file))
		  {
		  	//echo fgets($file). "<br />";
		  	$str = fgets($file) ; 
		  	
		  	if( $str!="" ){
		  		$ds = explode( "|",  	$str ) ; 
		  		$dataRet = json_decode( $ds[1] , true ) ; 
		  	}
		  	
		   

		  }

		fclose($file);
		//echo "<hr>";
		//print_r($ds ) ;
		$dataRet['date'] = $ds[0] ;
		print_r( json_encode( $dataRet) )  ; 
		
	}

	function curlService($url, $data, $method)
	{
		$ch = curl_init();
		$url = $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		 
		//curl_setopt($ch,CURLOPT_HEADER, TRUE);

		$response = curl_exec($ch);

		//print_r($response);

		return $response;
	}


}


class PHP_AES_Cipher
{
	 static $OPENSSL_CIPHER_NAME = "aes-128-cbc"; //Name of OpenSSL Cipher 
     static $CIPHER_KEY_LEN = 16; //128 bits

    /**
     * Encrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $iv - initialization vector
     * @param type $data - data to encrypt
     * @return encrypted data in base64 encoding with iv attached at end after a :
     */
     function encrypt3() {
        return "CCCC---CCCC";
     }
     function encrypt($key, $iv, $data) {
        if (strlen($key) < PHP_AES_Cipher::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", PHP_AES_Cipher::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > PHP_AES_Cipher::$CIPHER_KEY_LEN) {
            $key = substr($str, 0, PHP_AES_Cipher::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }

        $encodedEncryptedData = base64_encode(openssl_encrypt($data, PHP_AES_Cipher::$OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        //$encryptedPayload = $encodedEncryptedData .":".$encodedIV;
		$encryptedPayload = $encodedEncryptedData;

        return $encryptedPayload;

    }

    /**
     * Decrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $data - data to be decrypted in base64 encoding with iv attached at the end after a :
     * @return decrypted data
     */
     function decrypt($key, $iv, $data) {
        if (strlen($key) < PHP_AES_Cipher::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", PHP_AES_Cipher::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > PHP_AES_Cipher::$CIPHER_KEY_LEN) {
            $key = substr($str, 0, PHP_AES_Cipher::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }

		$encodedIV = base64_encode($iv);

        $parts = explode(':', $data); //Separate Encrypted data from iv.
        $decryptedData = openssl_decrypt(base64_decode($parts[0]), PHP_AES_Cipher::$OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, base64_decode($encodedIV));

        return $decryptedData;
    }

    
}