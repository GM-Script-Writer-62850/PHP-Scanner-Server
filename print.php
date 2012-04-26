<html><?php
if(isset($_GET['file'])){
	if(strrpos($_GET['file'], "/")>-1)
		$_GET['file']=substr($_GET['file'],strrpos($_GET['file'],"/")+1);
}
else{
	echo '<head><title>Missing File</title></head><body><h1>Error</h1>No file specifyed</body></html>';
	die();
}
$file=$_GET['file'];
if(file_exists('scans/'.$file)){
	$ext=substr($file,strrpos($file,".")+1);
	echo '<head><title>'.htmlspecialchars(substr($file,0,(strlen($ext)+1)*-1)).'</title></head><body onload="window.print();window.close();">';
	$file=htmlspecialchars($file);
	if($ext=="txt"){
		echo "<pre>".file_get_contents('scans/'.$file)."</pre>";
	}
	else{
		echo "<img src=\"scans/$file\">";
	}
}
else{
	echo '<head><title>404 Missing File</title></head><body><h1>404 Not Found</h1>The requested file <code>scans/'.htmlspecialchars($file).'</code> does not exist.';
}
?></body></html>