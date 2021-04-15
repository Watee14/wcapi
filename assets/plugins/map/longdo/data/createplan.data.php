<?
$data = $_REQUEST ;
print_r($data);
file_put_contents('lineplan.txt', json_encode($data)) ;
?>