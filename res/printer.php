<?php
# This file is used to handle all the print commands
# This file runs all the print commands, ../download.php and inc/printer.php call this page via include
# If your printer needs some special options used you want to edit lines 28 and 47
# You will want to read up on the CUPS command line printing documentation @ http://www.cups.org/documentation.php/options.html

$lpstat='lpstat -a | awk \'{print $1}\'';// Command used to find printers
if(!function_exists('busterWorkaround')){
	function busterWorkaround($file,$cfg){
		if($cfg==1)// ~4x faster
			$cmd="pdf2ps '$file' '$file.ps'";
		else// Better quality maybe?
			$cmd="convert '$file' '$file'";
		if(function_exists('exe'))
			exe($cmd,true);
		else
			shell_exec($cmd);
		if($cfg==1){
			unlink($file);
			$file="$file.ps";
		}
		return $file;
	}
}
if(function_exists('exe')){// Internal call via inc/printer.php
	if(isset($file)){
		$_POST['quantity']=intval($_POST['quantity']);
		$q=$_POST['quantity']>0?$_POST['quantity']:1;
		$o=escapeshellarg($_POST['options']);
		if($GLOBALS['BusterPrintBug'] > 0)
			$file=busterWorkaround($file,$GLOBALS['BusterPrintBug']);
		$cmd='lp -d '.shell($_POST['printer'])." -n $q -o $o $file";
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
	$o=escapeshellarg($_GET['options']);
	$BusterPrintBug=parse_ini_file('config.ini');
	$BusterPrintBug=(int)$BusterPrintBug['BusterPrintBug'];
	if($BusterPrintBug > 0){
		$file=busterWorkaround($file,$BusterPrintBug);
	}
	$cmd='lp -d '.escapeshellarg($_GET['printer'])." -n $q -o $o $file";
	echo json_encode((object)array(
		'printer'=>$_GET['printer'],
		'command'=>$cmd,
		'message'=>shell_exec($cmd) // This line makes it print using the integrated printer
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
