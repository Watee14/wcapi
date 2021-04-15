<?
$data = file_get_contents("php://input"); 
print_r( urldecode($data) ) ;


//$img = explode('image=' , $data);
/*$img = str_replace('%3A', ':', $img[1]) ;
$img = str_replace('%2F', '/', $img) ;
$img = str_replace('%3B', ';', $img) ;
$img = str_replace('%2C', ',', $img) ;*/

$data = urldecode($data);
 
 base64_to_jpeg($data, "xxx.jpg") ;
function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}
function saveImage($base64img){
    define('UPLOAD_DIR', '../uploads/');
    $base64img = str_replace('data:image/jpeg;base64,', '', $base64img);
    $data = base64_decode($base64img);
    $file = UPLOAD_DIR . '123123123.jpg';
    file_put_contents($file, $data);
}

 
?>