<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| Server
|--------------------------------------------------------------------------
|
*/
define('_SCRIPT_NAME', 				$_SERVER['SCRIPT_NAME']);
define('_SERVER_ADDR', 				isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']);
define('_BASE_SCRIPT_NAME', 		str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
define('_HTTPS', 					( ! empty($_SERVER['HTTPS']) 					? $_SERVER['HTTPS'] 					: NULL));
define('_HTTP_X_FORWARDED_PROTO', 	( ! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) 	? $_SERVER['HTTP_X_FORWARDED_PROTO'] 	: NULL));
define('_HTTP_X_FORWARDED_SSL', 	( ! empty($_SERVER['HTTP_X_FORWARDED_SSL']) 	? $_SERVER['HTTP_X_FORWARDED_SSL'] 		: NULL));
define('_HTTP_X_REQUESTED_WITH', 	( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) 	? $_SERVER['HTTP_X_REQUESTED_WITH'] 	: NULL));

/*
|--------------------------------------------------------------------------
| Secure
|--------------------------------------------------------------------------
|
*/
define('_IS_HTTPS', 					( ! empty(_HTTPS) 					&& _HTTPS 					== 'on' 	? TRUE : FALSE));
define('_IS_HTTP_X_FORWARDED_PROTO', 	( ! empty(_HTTP_X_FORWARDED_PROTO) 	&& _HTTP_X_FORWARDED_PROTO 	== 'https' 	? TRUE : FALSE));
define('_IS_HTTP_X_FORWARDED_SSL', 		( ! empty(_HTTP_X_FORWARDED_SSL) 	&& _HTTP_X_FORWARDED_SSL 	== 'on' 	? TRUE : FALSE));
define('_IS_SECURE', 					(_IS_HTTPS 	=== TRUE || _IS_HTTP_X_FORWARDED_PROTO === TRUE || _IS_HTTP_X_FORWARDED_SSL === TRUE ? TRUE : FALSE));
define('_SECURE', 						(_IS_SECURE === TRUE ? 'https://' : 'http://'));

define('_ASSETS_PATH',		'./../assets/');
define('_COMPONENTS_PATH',	'./assets/plugins/');
// define('_BASE_URL',	_SECURE._HTTP_HOST._BASE_SCRIPT_NAME);

defined('MAPCONF')		OR define('MAPCONF',	serialize(parse_ini_file(_COMPONENTS_PATH.'map/longdo/config.ini', TRUE)));
defined('VERSIONING')	OR define('VERSIONING', '?v='.time());

/*
|--------------------------------------------------------------------------
|  Configuration files
|--------------------------------------------------------------------------
|
*/
$dbTable = array(
	'uc_users' ,
	'case_result' ,
	'case_status' ,
	'casetype' ,
	'police_vehicle_status' ,
	'police_skills' ,
	'command' ,
	'department' ,
	'police_station' ,
	'case_inform' ,
	'case_closejob' ,
	'schedule' ,
	'duty' ,
	'casetype_group' ,
	'map_type' ,
	'uc_users_command' ,
	'police_skills',
	'police_vehicle' ,
	'police_vehicle_skill' ,
	'inspection_area' ,
	'uc_users_languae' ,
	'uc_skills' ,
	'personal_group' ,
	'personal_sex' ,
	'personal_skin' ,
	'personal_age' ,
	'personal_hair' ,
	'personal_hair_color' ,
	'personal_shape' ,
	'personal_tall' ,
	'personal_nationality' ,
	'personal_tattoo' ,
	'personal_scar' ,
	'personal_defective' ,
	'personal_accent' ,
	'asset_group' ,
	'vehicle_group' ,
	'vehicle_type' ,
	'vehicle_brand' ,
	'vehicle_model' ,
	'vehicle_color' ,
	'addr_district' ,
	'addr_amphur' ,
	'addr_province' ,
	'pets_group' ,
	'pets_type' ,
	'pets_breed' ,
	'pets_color' ,
	'blacklist_range' ,
	'blacklist_type' ,
	'uc_users_rank' ,
);
defined('_CONFIG')		OR define('_CONFIG',		getcwd().'/../config/');
defined('_CONFIG_USER')	OR define('_CONFIG_USER',	getcwd().'/../config/users/');
defined('_WRITE_TABLE')	OR define('_WRITE_TABLE',	implode(',', $dbTable));
/*
|--------------------------------------------------------------------------
|  System setting
|--------------------------------------------------------------------------
|
*/
defined('_SYS_SETTING')	OR define('_SYS_SETTING',	'http://10.0.106.54/portal191b/system_setting/get_configs_file' );
/*
|--------------------------------------------------------------------------
|  Mdu Notification service 
|--------------------------------------------------------------------------
|
*/
defined('_MDU_NOTI')	OR define('_MDU_NOTI',	'http://10.0.106.50/portal191b/alert' );
defined('_MDU_NOTI_FREEZE_MSG')	OR define('_MDU_NOTI_FREEZE_MSG',	"รถจอดอยู่นิ่งเกิน {freeze} นาที" );
/*
|--------------------------------------------------------------------------
|  Key decrypt
|--------------------------------------------------------------------------
|
*/
defined('key_decrypt')	OR define('key_decrypt',	'xit=k=o'); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| AES encrytion and decrytion
|--------------------------------------------------------------------------
|
*/
define('AES_KEYS',	'callvoice');
define('AES_SIZE',	256);

/*
|--------------------------------------------------------------------------
| MAP Longdo
|--------------------------------------------------------------------------
|
*/
$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
// defined('_MAP_SERVICE')	OR define('_MAP_SERVICE',	$protocol."://mmmap15.longdo.com/POIService/json/search");
// defined('_MAP_SEARCH')	OR define('_MAP_SEARCH',	$protocol."://search.longdo.com/mapsearch/json/search");
//defined('_MAP_ADDRESS')	OR define('_MAP_ADDRESS',	$protocol."://api.longdo.com/map/");
defined('_MAP_SEARCH') 		OR define('_MAP_SEARCH', 	$protocol."://10.0.106.58/mapsearch/json/search");
defined('_MAP_SUGGEST') 	OR define('_MAP_SUGGEST', 	$protocol."://10.0.106.58/mapsearch/json/suggest");
defined('_MAP_ADDRESS') 	OR define('_MAP_ADDRESS', 	$protocol."://10.0.106.58/map2/services/address");
//defined('_MAP_SERVER') 	OR define('_MAP_SERVER', 	$protocol."://10.0.106.58/map2/");
defined('_MAP_SERVER') 	OR define('_MAP_SERVER', 	 "https://map.191.go.th/map2/");
//defined('_MAP_SERVER') 		OR define('_MAP_SERVER', 	 "https://api.longdo.com/map/");
defined('_MAP_KEY')			OR define('_MAP_KEY',		"cc2cfcc6fd6e2673395611b352382892");

// defined('_MAP_KEY')		OR define('_MAP_KEY',		"callvoice");
// defined('_MAP_LIMIT')	OR define('_MAP_LIMIT',		"1000");
// defined('_MAP_LOCALE')	OR define('_MAP_LOCALE',	"th");
// defined('_POI_ALLOW')	OR define('_POI_ALLOW',		"ตำรวจ,police,hospital,สถานพยาบาล");
// defined('_SOUNDDEX')		OR define('_SOUNDDEX',		"http://58.137.172.213:8787/getsoundexall");
/*
|--------------------------------------------------------------------------
| Path Log
|--------------------------------------------------------------------------
|
*/
defined('_LOGS') OR define('_LOGS', getcwd().'/../logs/');