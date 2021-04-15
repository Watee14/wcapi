<?php
$serverName = "203.170.193.91";
$connectionOptions = array(
    "Database" => "C3I",
    "Uid" => "c3i",
    "PWD" => "cv1234"
);

//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
if( $conn === false ) {
    die( FormatErrors( sqlsrv_errors()));
}

//Select Query
$tsql= "SELECT * from uc_users";

//Executes the query
$getResults= sqlsrv_query($conn, $tsql);
 
//Error handling
if ($getResults == FALSE)
    die(FormatErrors(sqlsrv_errors()));
?>

<h1> Results : </h1>

<?php
while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
  print_r($row) ;
    echo ("<br/>");
}

sqlsrv_free_stmt($getResults);

function FormatErrors( $errors )
{
    /* Display errors. */
    echo "Error information: <br/>";
    foreach ( $errors as $error )
    {
        echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";
        echo "Code: ".$error['code']."<br/>";
        echo "Message: ".$error['message']."<br/>";
    }
}
?>
<?php
    $serverName = "203.170.193.91";
    $connectionOptions = array(
        "Database" => "C3I",
        "Uid" => "c3i",
        "PWD" => "cv1234"
    );
    //Establishes the connection
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if($conn)
        echo "Connected!"
?>