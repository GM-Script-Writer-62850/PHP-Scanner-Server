<?php // Global Variables are now stored in config.ini
$cfg=parse_ini_file('config.ini');
$FreeSpaceWarn=(integer)$cfg['FreeSpaceWarn'];
$Fortune=(bool)$cfg['Fortune'];
$ExtraScanners=(bool)$cfg['ExtraScanners'];
$CheckForUpdates=(bool)$cfg['CheckForUpdates'];
$RequireLogin=(bool)$cfg['RequireLogin'];
$SessionDuration=(integer)$cfg['SessionDuration'];
$Theme=(string)$cfg['Theme'];
$DarkPicker=(bool)$cfg['DarkPicker'];
$RulerIncrement=(double)$cfg['RulerIncrement'];
$TimeZone=(string)$cfg['TimeZone'];
$Printer=(integer)$cfg['Printer'];
$ReplacePrinter=(bool)$cfg['ReplacePrinter'];
$BusterPrintBug=(int)$cfg['BusterPrintBug'];
$HomePage=(string)$cfg['HomePage'];
$ShowRawFormat=(bool)$cfg['ShowRawFormat'];
$RawScanFormat=(integer)$cfg['RawScanFormat'];
$NAME=(string)$cfg['NAME'];
$VER=(string)$cfg['VER'];
$SAE_VER=(string)$cfg['SAE_VER'];

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
