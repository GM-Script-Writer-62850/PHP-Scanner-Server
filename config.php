<?php // Global Varables
$FreeSpaceWarn=2048;// In Megabytes, Warning is displayed if there is less then the amount specified
$Fortune=true;// Enable/disable fortunes in the debug console
$ExtraScanners=false;// Adds sample scanners from ./inc/scanhelp/
$CheckForUpdates=true;// Enables auto update checking
$RequireLogin=false;// Require user to login (A 'geek' could bypass this without too much trouble using JavaScript); Create the user 'root' 1st, also Authorization is root's password
$SessionDuration=86400;// Max time (in seconds) signed in is 24hrs (irrelevant with the above off)
$Theme='3C9642.3C7796.3C9642.FFFFFF.3C9642.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF'; // Default Color Scheme
// End Global Varables

$NAME="PHP Scanner Server";
$VER="1.3-8_dev";
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
	include "res/inc/header.php";
	return $path;
}
function Footer($path) { # Spit out HTML footer
	$title=$GLOBALS['PAGE'];
	include "res/inc/footer.php";
}
?>
