<?php
	if(!isset($langs)){
		$langs=findLangs();
	}
	$LANGS=json_decode(file_get_contents('res/langs.json'));
	for($i=0,$stp=count($langs);$i<$stp;$i++){
		$lang=$langs[$i];
		$Lang=html(!isset($LANGS->{$lang})?$lang:$LANGS->{$lang});
		echo "<option value=\"$lang\"".($lang=='eng'?' selected="selected" ':'').">$Lang</option>";
	}/*
	* Used to generate language file list for README file
	$data=file_get_contents("/res/langs.json");
	$data=json_decode($data);
	foreach($data as $key => $val){
		echo "_ tesseract-ocr-$key        - $val language file for tesseract\n";
	}*/
?>
