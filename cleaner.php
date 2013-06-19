<?php
echo "<pre>\n";
$loc=$_SERVER['DOCUMENT_ROOT'].str_replace('cleaner.php','scans',$_SERVER['SCRIPT_NAME']);
$lst=scandir($loc);
for($i=2,$max=count($lst);$i<$max;$i++){
	if($lst[$i]!='.'&&$lst[$i]!='..'){
		if(time()-filemtime($loc.'/'.$lst[$i])>3600){
			unlink($loc.'/'.$lst[$i]);
			echo "Removed: ".$loc.'/'.$lst[$i]."\n";
		}
	}
}
echo "</pre>\n";
?>