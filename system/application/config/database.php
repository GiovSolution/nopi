<?php  /* if ( ! defined('BASEPATH')) exit('No direct script access allowed'); */
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "default";
$active_record = TRUE;

$db['default']['hostname'] = "localhost";
$db['default']['username'] = "root";
$db['default']['password'] = "experienc3";
$db['default']['database'] = "nopi_db";
$db['default']['dbdriver'] = "mysql";
$db['default']['dbprefix'] = "";
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = "";
$db['default']['char_set'] = "utf8";
$db['default']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL KI
$db['KI']['hostname'] = "localhost";
$db['KI']['username'] = "root";
$db['KI']['password'] = "experienc3";
$db['KI']['database'] = "miracledb_ki";
$db['KI']['dbdriver'] = "mysql";
$db['KI']['dbprefix'] = "";
$db['KI']['pconnect'] = TRUE;
$db['KI']['db_debug'] = TRUE;
$db['KI']['cache_on'] = FALSE;
$db['KI']['cachedir'] = "";
$db['KI']['char_set'] = "utf8";
$db['KI']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL HR
$db['HR']['hostname'] = "localhost";
$db['HR']['username'] = "root";
$db['HR']['password'] = "experienc3";
$db['HR']['database'] = "miracledb_hr";
$db['HR']['dbdriver'] = "mysql";
$db['HR']['dbprefix'] = "";
$db['HR']['pconnect'] = TRUE;
$db['HR']['db_debug'] = TRUE;
$db['HR']['cache_on'] = FALSE;
$db['HR']['cachedir'] = "";
$db['HR']['char_set'] = "utf8";
$db['HR']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL TP
$db['TP']['hostname'] = "localhost";
$db['TP']['username'] = "root";
$db['TP']['password'] = "experienc3";
$db['TP']['database'] = "miracledb_tp";
$db['TP']['dbdriver'] = "mysql";
$db['TP']['dbprefix'] = "";
$db['TP']['pconnect'] = TRUE;
$db['TP']['db_debug'] = TRUE;
$db['TP']['cache_on'] = FALSE;
$db['TP']['cachedir'] = "";
$db['TP']['char_set'] = "utf8";
$db['TP']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL DPS
$db['DPS']['hostname'] = "localhost";
$db['DPS']['username'] = "root";
$db['DPS']['password'] = "experienc3";
$db['DPS']['database'] = "miracledb_dps";
$db['DPS']['dbdriver'] = "mysql";
$db['DPS']['dbprefix'] = "";
$db['DPS']['pconnect'] = TRUE;
$db['DPS']['db_debug'] = TRUE;
$db['DPS']['cache_on'] = FALSE;
$db['DPS']['cachedir'] = "";
$db['DPS']['char_set'] = "utf8";
$db['DPS']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL MDN
$db['MDN']['hostname'] = "localhost";
$db['MDN']['username'] = "root";
$db['MDN']['password'] = "experienc3";
$db['MDN']['database'] = "miracledb_mdn";
$db['MDN']['dbdriver'] = "mysql";
$db['MDN']['dbprefix'] = "";
$db['MDN']['pconnect'] = TRUE;
$db['MDN']['db_debug'] = TRUE;
$db['MDN']['cache_on'] = FALSE;
$db['MDN']['cachedir'] = "";
$db['MDN']['char_set'] = "utf8";
$db['MDN']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL LBK
$db['LBK']['hostname'] = "localhost";
$db['LBK']['username'] = "root";
$db['LBK']['password'] = "experienc3";
$db['LBK']['database'] = "miracledb_lbk";
$db['LBK']['dbdriver'] = "mysql";
$db['LBK']['dbprefix'] = "";
$db['LBK']['pconnect'] = TRUE;
$db['LBK']['db_debug'] = TRUE;
$db['LBK']['cache_on'] = FALSE;
$db['LBK']['cachedir'] = "";
$db['LBK']['char_set'] = "utf8";
$db['LBK']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL YGK
$db['YGK']['hostname'] = "localhost";
$db['YGK']['username'] = "root";
$db['YGK']['password'] = "experienc3";
$db['YGK']['database'] = "miracledb_ygk";
$db['YGK']['dbdriver'] = "mysql";
$db['YGK']['dbprefix'] = "";
$db['YGK']['pconnect'] = TRUE;
$db['YGK']['db_debug'] = TRUE;
$db['YGK']['cache_on'] = FALSE;
$db['YGK']['cachedir'] = "";
$db['YGK']['char_set'] = "utf8";
$db['YGK']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL MND
$db['MND']['hostname'] = "localhost";
$db['MND']['username'] = "root";
$db['MND']['password'] = "experienc3";
$db['MND']['database'] = "miracledb_mnd";
$db['MND']['dbdriver'] = "mysql";
$db['MND']['dbprefix'] = "";
$db['MND']['pconnect'] = TRUE;
$db['MND']['db_debug'] = TRUE;
$db['MND']['cache_on'] = FALSE;
$db['MND']['cachedir'] = "";
$db['MND']['char_set'] = "utf8";
$db['MND']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL MTA
$db['MTA']['hostname'] = "localhost";
$db['MTA']['username'] = "root";
$db['MTA']['password'] = "experienc3";
$db['MTA']['database'] = "miracledb_MTA";
$db['MTA']['dbdriver'] = "mysql";
$db['MTA']['dbprefix'] = "";
$db['MTA']['pconnect'] = TRUE;
$db['MTA']['db_debug'] = TRUE;
$db['MTA']['cache_on'] = FALSE;
$db['MTA']['cachedir'] = "";
$db['MTA']['char_set'] = "utf8";
$db['MTA']['dbcollat'] = "utf8_general_ci";

// DATABASE LOCAL BLPN
$db['BLPN']['hostname'] = "localhost";
$db['BLPN']['username'] = "root";
$db['BLPN']['password'] = "experienc3";
$db['BLPN']['database'] = "miracledb_blpn";
$db['BLPN']['dbdriver'] = "mysql";
$db['BLPN']['dbprefix'] = "";
$db['BLPN']['pconnect'] = TRUE;
$db['BLPN']['db_debug'] = TRUE;
$db['BLPN']['cache_on'] = FALSE;
$db['BLPN']['cachedir'] = "";
$db['BLPN']['char_set'] = "utf8";
$db['BLPN']['dbcollat'] = "utf8_general_ci";

$db['TH2']['hostname'] = "localhost";
$db['TH2']['username'] = "root";
$db['TH2']['password'] = "experienc3";
$db['TH2']['database'] = "miracledb";
$db['TH2']['dbdriver'] = "mysql";
$db['TH2']['dbprefix'] = "";
$db['TH2']['pconnect'] = TRUE;
$db['TH2']['db_debug'] = TRUE;
$db['TH2']['cache_on'] = FALSE;
$db['TH2']['cachedir'] = "";
$db['TH2']['char_set'] = "utf8";
$db['TH2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE KI
$db['KI2']['hostname'] = "192.168.2.2";
$db['KI2']['username'] = "root";
$db['KI2']['password'] = "experienc3";
$db['KI2']['database'] = "miracledb_ki";
$db['KI2']['dbdriver'] = "mysql";
$db['KI2']['dbprefix'] = "";
$db['KI2']['pconnect'] = TRUE;
$db['KI2']['db_debug'] = TRUE;
$db['KI2']['cache_on'] = FALSE;
$db['KI2']['cachedir'] = "";
$db['KI2']['char_set'] = "utf8";
$db['KI2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE HR
$db['HR2']['hostname'] = "192.168.3.2";
$db['HR2']['username'] = "root";
$db['HR2']['password'] = "experienc3";
$db['HR2']['database'] = "miracledb_hr";
$db['HR2']['dbdriver'] = "mysql";
$db['HR2']['dbprefix'] = "";
$db['HR2']['pconnect'] = TRUE;
$db['HR2']['db_debug'] = TRUE;
$db['HR2']['cache_on'] = FALSE;
$db['HR2']['cachedir'] = "";
$db['HR2']['char_set'] = "utf8";
$db['HR2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE TP
$db['TP2']['hostname'] = "192.168.4.2";
$db['TP2']['username'] = "root";
$db['TP2']['password'] = "experienc3";
$db['TP2']['database'] = "miracledb_tp";
$db['TP2']['dbdriver'] = "mysql";
$db['TP2']['dbprefix'] = "";
$db['TP2']['pconnect'] = TRUE;
$db['TP2']['db_debug'] = TRUE;
$db['TP2']['cache_on'] = FALSE;
$db['TP2']['cachedir'] = "";
$db['TP2']['char_set'] = "utf8";
$db['TP2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE DPS
$db['DPS2']['hostname'] = "192.168.5.2";
$db['DPS2']['username'] = "root";
$db['DPS2']['password'] = "experienc3";
$db['DPS2']['database'] = "miracledb_dps";
$db['DPS2']['dbdriver'] = "mysql";
$db['DPS2']['dbprefix'] = "";
$db['DPS2']['pconnect'] = TRUE;
$db['DPS2']['db_debug'] = TRUE;
$db['DPS2']['cache_on'] = FALSE;
$db['DPS2']['cachedir'] = "";
$db['DPS2']['char_set'] = "utf8";
$db['DPS2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE MDN
$db['MDN2']['hostname'] = "192.168.10.32";
$db['MDN2']['username'] = "root";
$db['MDN2']['password'] = "experienc3";
$db['MDN2']['database'] = "miracledb_mdn";
$db['MDN2']['dbdriver'] = "mysql";
$db['MDN2']['dbprefix'] = "";
$db['MDN2']['pconnect'] = TRUE;
$db['MDN2']['db_debug'] = TRUE;
$db['MDN2']['cache_on'] = FALSE;
$db['MDN2']['cachedir'] = "";
$db['MDN2']['char_set'] = "utf8";
$db['MDN2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE LBK
$db['LBK2']['hostname'] = "192.168.10.54";
$db['LBK2']['username'] = "root";
$db['LBK2']['password'] = "experienc3";
$db['LBK2']['database'] = "miracledb_lbk";
$db['LBK2']['dbdriver'] = "mysql";
$db['LBK2']['dbprefix'] = "";
$db['LBK2']['pconnect'] = TRUE;
$db['LBK2']['db_debug'] = TRUE;
$db['LBK2']['cache_on'] = FALSE;
$db['LBK2']['cachedir'] = "";
$db['LBK2']['char_set'] = "utf8";
$db['LBK2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE YGK
$db['YGK2']['hostname'] = "192.168.10.55";
$db['YGK2']['username'] = "root";
$db['YGK2']['password'] = "experienc3";
$db['YGK2']['database'] = "miracledb_ygk";
$db['YGK2']['dbdriver'] = "mysql";
$db['YGK2']['dbprefix'] = "";
$db['YGK2']['pconnect'] = TRUE;
$db['YGK2']['db_debug'] = TRUE;
$db['YGK2']['cache_on'] = FALSE;
$db['YGK2']['cachedir'] = "";
$db['YGK2']['char_set'] = "utf8";
$db['YGK2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE MND
$db['MND2']['hostname'] = "192.168.10.58";
$db['MND2']['username'] = "root";
$db['MND2']['password'] = "experienc3";
$db['MND2']['database'] = "miracledb_mnd";
$db['MND2']['dbdriver'] = "mysql";
$db['MND2']['dbprefix'] = "";
$db['MND2']['pconnect'] = TRUE;
$db['MND2']['db_debug'] = TRUE;
$db['MND2']['cache_on'] = FALSE;
$db['MND2']['cachedir'] = "";
$db['MND2']['char_set'] = "utf8";
$db['MND2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE MTA
$db['MTA2']['hostname'] = "192.168.7.2";
$db['MTA2']['username'] = "root";
$db['MTA2']['password'] = "experienc3";
$db['MTA2']['database'] = "miracledb_MTA";
$db['MTA2']['dbdriver'] = "mysql";
$db['MTA2']['dbprefix'] = "";
$db['MTA2']['pconnect'] = TRUE;
$db['MTA2']['db_debug'] = TRUE;
$db['MTA2']['cache_on'] = FALSE;
$db['MTA2']['cachedir'] = "";
$db['MTA2']['char_set'] = "utf8";
$db['MTA2']['dbcollat'] = "utf8_general_ci";

// DATABASE ONLINE BLPN
$db['BLPN2']['hostname'] = "192.168.8.2";
$db['BLPN2']['username'] = "root";
$db['BLPN2']['password'] = "experienc3";
$db['BLPN2']['database'] = "miracledb_blpn";
$db['BLPN2']['dbdriver'] = "mysql";
$db['BLPN2']['dbprefix'] = "";
$db['BLPN2']['pconnect'] = TRUE;
$db['BLPN2']['db_debug'] = TRUE;
$db['BLPN2']['cache_on'] = FALSE;
$db['BLPN2']['cachedir'] = "";
$db['BLPN2']['char_set'] = "utf8";
$db['BLPN2']['dbcollat'] = "utf8_general_ci";


$db['smsd']['hostname'] = "localhost";
$db['smsd']['username'] = "root";
$db['smsd']['password'] = "experienc3";
$db['smsd']['database'] = "smsd";
$db['smsd']['dbdriver'] = "mysql";
$db['smsd']['dbprefix'] = "";
$db['smsd']['pconnect'] = FALSE;
$db['smsd']['db_debug'] = TRUE;
$db['smsd']['cache_on'] = FALSE;
$db['smsd']['cachedir'] = "";
$db['smsd']['char_set'] = "utf8";
$db['smsd']['dbcollat'] = "utf8_general_ci";


/* End of file database.php */
/* Location: ./system/application/config/database.php */