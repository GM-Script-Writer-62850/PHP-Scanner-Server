<?php // Global Varables
$FreeSpaceWarn=2048;// In Megabytes, Warning is displayed if there is less then the amount specified
$Fortune=true;// Enable/disable fortunes in the debug console
$ExtraScanners=false;// Adds sample scanners from ./inc/scanhelp/
$CheckForUpdates=true;// Enables auto update checking
$RequireLogin=false;// Require user to login (A 'geek' could bypass this without too much trouble using JavaScript); Create the user 'root' 1st, also Authorization is root's password
$SessionDuration=86400;// Max time (in seconds) signed in is 24hrs (irrelevant with the above off)
$Theme='383838.B84E40.407EB4.202020.408080.FF0.FFF.3B133B.FFF.F00.FFF'; // Default Color Scheme
$DarkPicker=true;// Use the dark color picker by default (It is part of the theme manager)
$RulerIncrement=25.4;// Controls the rulers number increments relative to millimeters [25.4=inches (25.4 mm = 1 in), 10=centimeters (10 mm = 1 cm)]
$TimeZone='';// Time zone override (used with scan file names) List of settings is on this page: http://www.php.net/manual/en/datetime.configuration.php#ini.date.timezone
// Do not edit stuff below this line

$NAME="PHP Scanner Server";
$VER="1.3-11";
$SAE_VER="1.4"; // Scanner Access Enabler version

// Login Stuff
$Auth=true;
if($RequireLogin){
	if(!isset($_COOKIE['Authenticated']))
		$Auth=false;
	else if(time()>intval($_COOKIE['Authenticated'])+$SessionDuration)// NOT FOR USE ON 32BIT OS IN 2038 http://en.wikipedia.org/wiki/Year_2038_problem
		$Auth=false;
}

// A few functions I need even on error pages
function html($X){
	return htmlspecialchars($X);
}
function url($X){
	return rawurlencode($X);
}
function js($X){
	return str_replace("\n",'\\n',addslashes($X));
}
function InsertHeader($title) { # Spit out HTML header
	$page=$GLOBALS['PAGE'];
	$GLOBALS['DarkPicker']=$DarkPicker=isset($_COOKIE['darkPicker'])?$_COOKIE['darkPicker']=='true':$GLOBALS['DarkPicker'];
	include "res/inc/header.php";
	return $path;
}
function Footer($path) { # Spit out HTML footer
	$title=$GLOBALS['PAGE'];
	include "res/inc/footer.php";
}
?>
