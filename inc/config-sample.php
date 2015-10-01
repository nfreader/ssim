<?php

date_default_timezone_set('UTC');

define('GAME_VERSION', '0.1');
define('YEAR_OFFSET', 178); //Years to offset date() displays by. Just for fun.
$year = date('Y') + YEAR_OFFSET;
define('SSIM_DATE',"Hi d.m.$year");

define('DB_METHOD', 'mysql');//Probably won't need to change
define('DB_NAME', 'CHANGEME');
define('DB_USER', 'CHANGEME');
define('DB_PASS', 'CHANGEME');
define('DB_HOST', 'localhost');//Probably won't need to change

//Probably won't need to change,
//unless you want two or more parallel installations
define('TBL_PREFIX', 'ssim_');

//Salt length for hashing passwords
define('PASSWD_SALT_LENGTH', 16);

//Game options
define('SSIM_DEBUG', false);
define('STARTING_CREDITS', 100000);//Number of credits players start with
define('STARTING_LEGAL', 1000);//Number of legal points players start with
define('FUEL_BASE', 100);//Cost of fuel, to be manipulated later on
define('CARGO_PENALTY', 5); //Multiplier for cargo sell violations (max)
define('PIRATE_PENALTY',10); //Multiplier for mission pirating (max)
define('PIRATE_THRESHHOLD', -100); //Legal score at which point the pilot is
//a pirate. Yarr.

if(SSIM_DEBUG === true){
  define('FTL_MULTIPLIER',.1);
} else {
  define('FTL_MULTIPLIER',10);
}


require_once ('autoload.php');
require_once ('arrays.php');
require_once ('functions.php');

$session = new session();
session_set_save_handler($session, true);

header('Content-type: text/html; charset=utf-8');

if (isset($_GET['msg'])) {
  echo '<div class="notification">';
  //echo urldecode($_GET['msg']);
  echo "<script>showText('.notification','".urldecode($_GET['msg'])."',0,5);</script>";
  echo '</div>';
}
