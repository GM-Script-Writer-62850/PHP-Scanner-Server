<?php
# This file is used to handle all the print commands
# This file runs all the print commands, ../download.php and inc/printer.php call this page via include
# If your printer needs some specific native options you should edit line 8
# You will want to read up on the CUPS command line printing documentation @ https://www.cups.org/doc/options.html

$lpstat='lpstat -a | awk \'{print $1}\'';// Command used to find printers
$lpDefaults='-o number-up=1 -o page-border=none -o number-up-layout=lrtb';// Default options for lp command

if(!function_exists('busterWorkaround')){
	function busterWorkaround($file,$cfg){
		if($cfg==1)// Faster
			$cmd="pdf2ps '$file' '$file.ps'";
		else{
			$cfg=$cfg*4;
			if($cfg<288)
				$cfg=288;
			$cmd="convert -verbose -density $cfg '$file' '$file'";// DPI = density/4
		}
		if(function_exists('exe'))
			exe($cmd,true);
		else
			$debug="$cmd\n".shell_exec("$cmd 2>&1");
		if($cfg==1){
			unlink($file);
			$file="$file.ps";
		}
		return function_exists('exe')?$file:array($file,$debug);
	}
}
if(function_exists('exe')){// Internal call via inc/printer.php
	if(isset($file)){
		$_POST['quantity']=intval($_POST['quantity']);
		$q=$_POST['quantity']>0?$_POST['quantity']:1;
		$opt=explode(',',$_POST['options']);
		$o=$lpDefaults;
		foreach($opt as $v){
			$o.='-o '.escapeshellarg($v);
		}
		if($GLOBALS['BusterPrintBug'] > 0)
			$file=busterWorkaround($file,$GLOBALS['BusterPrintBug']);
		$cmd='lp -d '.shell($_POST['printer'])." -n $q $o $file";
		Print_Message(
			$_POST['printer'],
			'Your document is being processed:<br/><pre title="'.htmlspecialchars($cmd).'">'.html(
				exe($cmd,true) // Print via Printer page
			).'</pre>',
			'center'
		);
	}
	else
		$printers=array_filter(explode("\n",exe($lpstat,true)));
}
else if(isset($Printer)){ // Internal call via include from ../download.php
	header('Content-type: application/json; charset=UTF-8');
	$_GET['quantity']=intval($_GET['quantity']);
	$q=$_GET['quantity']>0?$_GET['quantity']:1;
	$opt=explode(',',$_GET['options']);
	$o=$lpDefaults;
	foreach($opt as $v){
		$o.=' -o '.escapeshellarg($v);
	}
	$BusterPrintBug=parse_ini_file('config.ini');
	$BusterPrintBug=(int)$BusterPrintBug['BusterPrintBug'];
	$debug=false;
	if($BusterPrintBug > 0){
		$file=busterWorkaround($file,$BusterPrintBug);
		$debug=$file[1];
		$file=$file[0];
	}
	$cmd='lp -d '.escapeshellarg($_GET['printer'])." -n $q $o $file";
	echo json_encode((object)array(
		'printer'=>$_GET['printer'],
		'command'=>$cmd,
		'message'=>shell_exec($cmd), // This line makes it print using the integrated printer
		'debug'=>$debug
	));
}
else{
	$Printer=parse_ini_file(file_exists('res')?'config.ini':'../config.ini');
	$Printer=(integer)$Printer['Printer'];
	if(!function_exists('ext2mime')){// external call via browser
		if($Printer==0){// Check if printer service  is enabled
			header('Content-type: application/json; charset=UTF-8');
			echo '{"error":"Printer service is disabled"}';
		}
		else if(isset($_GET['list'])){// Return list of printers
			header('Content-type: plain/txt; charset=UTF-8');
			echo str_replace("\n",",",substr(shell_exec($lpstat),0,-1));
		}
		else
			echo "Todo: Don't reload printer page, use AJAX";
	}
}
?>
