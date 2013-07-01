<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head><meta http-equiv="Content-type" content="text/html; charset=UTF-8"/><link rel="shortcut icon" href="inc/images/favicon.png"/><style type="text/css">div{text-align:center;padding:20px;display:inline-block;/*page-break-after:always;*/}</style><title><?php
if(isset($_GET['file'])){
	$files=json_decode('{"'.$_GET['file'].'":1}');
	$prefix='';
}
else if(isset($_GET['json'])){
	$files=json_decode($_GET['json']);
	$prefix='Scan_';
}
else
	die('<title>Missing File</title></head><body><h1>Error</h1>No file(s) specifyed</body></html>');
if(count($files)==1){
	foreach($files as $file => $val)
		echo htmlspecialchars(substr($file,strlen($prefix),strrpos($file,'.')-strlen($prefix)));
}
else
	echo "Compilation";
echo '</title></head><body onload="window.print();window.close();">';
foreach($files as $file => $val){
	echo '<div>';
	$file=$prefix.$file;
	if(file_exists("scans/$file")){
		$ext=substr($file,strrpos($file,".")+1);
		if($ext=="txt"){
			echo "<pre>".htmlspecialchars(file_get_contents("scans/$file"))."</pre>";
		}
		else{
			$file=htmlspecialchars($file);
			echo "<img src=\"scans/$file\">";
		}
	}
	else
		echo '<hr><h2>404 Not Found</h1>The requested file <code>scans/'.htmlspecialchars($file).'</code> does not exist.<hr>';
	echo '</div>';
}
?></body></html>
