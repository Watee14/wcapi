<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------------
| This file will contain the settings needed to access your Mongo database.
|
|
| ------------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| ------------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['write_concerns'] Default is 1: acknowledge write operations.  ref(http://php.net/manual/en/mongo.writeconcerns.php)
|	['journal'] Default is TRUE : journal flushed to disk. ref(http://php.net/manual/en/mongo.writeconcerns.php)
|	['read_preference'] Set the read preference for this connection. ref (http://php.net/manual/en/mongoclient.setreadpreference.php)
|	['read_preference_tags'] Set the read preference for this connection.  ref (http://php.net/manual/en/mongoclient.setreadpreference.php)
|
| The $config['mongo_db']['active'] variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
*/
switch (ENVIRONMENT) {
	case 'production':
	$config['mongo_db']['active'] = "production";
	break;	
	default:
	$config['mongo_db']['active'] = 'default';
	break;
}
if ($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	
	unset($_GET);
	$postdata = file_get_contents("php://input") ;
	
	$x = explode("&", $postdata ) ;

	foreach ($x as $key => $value) {
		$realvalue = explode("=", $value );
		$_REQUEST[$realvalue[0]] = $realvalue[1] ;
		$realvalue = '';
	}

}

if ((isset($_REQUEST['region']) && isset($_REQUEST['tenant'])) && (!empty($_REQUEST['region'])&& !empty($_REQUEST['tenant']))) {
	
	$database= 'wc_'.$_REQUEST['region'].'_'.$_REQUEST['tenant']; 

}
else {
	
	$database = 'default';
}



$config['mongo_db']['default']['no_auth'] = TRUE;
$config['mongo_db']['default']['hostname'] = '139.59.252.39';
$config['mongo_db']['default']['port'] = '30000';
$config['mongo_db']['default']['username'] = ' ';
$config['mongo_db']['default']['password'] = ' ';
$config['mongo_db']['default']['database'] = $database;
$config['mongo_db']['default']['db_debug'] = FALSE;
$config['mongo_db']['default']['return_as'] = 'array';
$config['mongo_db']['default']['write_concerns'] = (int)1;
$config['mongo_db']['default']['journal'] = TRUE;
$config['mongo_db']['default']['read_preference'] = NULL;
$config['mongo_db']['default']['read_preference_tags'] = NULL;

$config['mongo_db']['production']['no_auth'] = TRUE;
$config['mongo_db']['production']['hostname'] = '139.59.252.39';
$config['mongo_db']['production']['port'] = '30000';
$config['mongo_db']['production']['username'] = ' ';
$config['mongo_db']['production']['password'] = ' ';
$config['mongo_db']['production']['database'] = $database;
$config['mongo_db']['production']['db_debug'] = FALSE;
$config['mongo_db']['production']['return_as'] = 'array';
$config['mongo_db']['production']['write_concerns'] = (int)1;
$config['mongo_db']['production']['journal'] = TRUE;
$config['mongo_db']['production']['read_preference'] = NULL;
$config['mongo_db']['production']['read_preference_tags'] = NULL;

/* End of file database.php */
/* Location: ./application/config/database.php */
