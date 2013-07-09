<?php
$maxAge=86400;// max age in seconds, scans older that this will be deleted
if(isset($_GET['file'])){
	if(!is_null(strrpos($_GET['file'], "/")))
		$_GET['file']=substr($_GET['file'],strrpos($_GET['file'],"/")+1);
	$file=$_GET['file'];
	$file0=substr($file,0,strrpos($file,"."));
	echo '{"state":'.((@unlink("scans/Preview_$file0.jpg")&&@unlink("scans/Scan_$file"))?0:1).',"file":"'.$file.'"}';
}
else{
	echo "<pre>\n";
	$loc=$_SERVER['DOCUMENT_ROOT'].str_replace('cleaner.php','scans',$_SERVER['SCRIPT_NAME']);
	$lst=scandir($loc);
	for($i=2,$max=count($lst);$i<$max;$i++){
		if($lst[$i]!='.'&&$lst[$i]!='..'){
			if(substr($lst[$i],0,8)=="Preview_")
				continue;
			if(time()-filemtime("$loc/".$lst[$i])>$maxAge){
				if(@unlink("$loc/".$lst[$i]))
					echo "Removed: $loc/".$lst[$i]."\n";
				$lst[$i]='Preview_'.substr($lst[$i],5,strrpos($lst[$i],'.')-5).'.jpg';
				if(@unlink("$loc/".$lst[$i]))
					echo "Removed: $loc/".$lst[$i]."\n";
			}
		}
	}
	echo "</pre>";
}
?>
