<!DOCTYPE html><html><head><meta charset="UTF-8"/><link rel="shortcut icon" href="res/images/favicon.png"/>
<style type="text/css">body,div{text-align:center;}div{padding:20px;display:inline-block;}.break div{padding:0;width:100%;page-break-after:always;}img{max-width:100%;}</style><title><?php
if(isset($_GET['file'])){
	$files=(array)json_decode('{"'.$_GET['file'].'":1}');
	$prefix='';
}
else if(isset($_GET['json'])){
	$files=json_decode($_GET['json']);
	$prefix='Scan_';
}
else
	die('<title>Missing File</title></head><body><h1>Error</h1>No file(s) specified</body></html>');
if(count($files)==1){
	foreach($files as $file => $val)
		echo htmlspecialchars(substr($file,strlen($prefix),strrpos($file,'.')-strlen($prefix)));
}
else
	echo "Compilation";
?></title></head><body onload="if(total>1){if(confirm('Press OK, for only 1 image per page\nPress Cancel for as many as will fit per page')){document.body.className='break';}}window.print();setTimeout(function(){window.close();},1)"><?php // setTimeout is for chrome
$ct=0;
foreach($files as $file => $val){
	$ct++;
	echo '<div>';
	$file=$prefix.$file;
	if(file_exists("scans/file/$file")){
		$ext=substr($file,strrpos($file,".")+1);
		if($ext=="txt"){
			echo "<pre>".htmlspecialchars(file_get_contents("scans/file/$file"))."</pre>";
		}
		else if($ext=="tiff"){
			continue;
		}
		else{
			$file=htmlspecialchars($file);
			echo "<img alt=\"$file\" src=\"scans/file/$file\">";
		}
	}
	else
		echo '<hr><h2>404 Not Found</h1>The requested file <code>scans/file/'.htmlspecialchars($file).'</code> does not exist.<hr>';
	echo '</div>';
}
echo '<script type="text/javascript">var total='.$ct.';</script>';
?></body></html>
