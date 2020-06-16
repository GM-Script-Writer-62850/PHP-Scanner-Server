<?php
$preview="Preview_".substr($file,0,-3)."jpg";
if(isset($_POST['file-text'])){ // 1_Mar_8_2012~11-22-41.txt  1_Mar_8_2012~11-22-41-edit-42.txt
	$edit=strpos($file,'-edit-');
	$name=(is_bool($edit)?substr($file,0,-4):substr($file,0,$edit));
	$int=1;
	while(file_exists("scans/thumb/Preview_$name-edit-$int.jpg")){
		$int++;
	}
	copy("scans/thumb/$preview","scans/thumb/Preview_$name-edit-$int.jpg");
	if(SaveFile("scans/file/Scan_$name-edit-$int.txt",$_POST['file-text'])){
		Print_Message("Saved","You have successfully edited $file",'center');
		$file="$name-edit-$int.txt";
	}
}
echo "<div class=\"box box-full\" id=\"text-editor\"><div id=\"preview_links\"></div>".
	"<img src=\"scans/thumb/$preview\"><br/>".
	'<form action="index.php?page=Edit&file='.$file.'" method="POST"><textarea name="file-text">'.html(file_get_contents("scans/file/Scan_$file"))."</textarea><br/>".
	'<input value="Save" type="submit"/><input type="button" value="Cancel" onclick="history.go(-1);"/></forum></div>';
Update_Links("Scan_$file",$PAGE);
?>
