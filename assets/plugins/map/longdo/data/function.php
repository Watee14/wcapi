<?

function curlService($url , $data , $method){
	$ch = curl_init();
	$url = $url ;
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $method );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	//curl_setopt($ch,CURLOPT_HEADER, true);
    $response = curl_exec ($ch);
	//print_r($response) ;
    return  $response ;
}

?>