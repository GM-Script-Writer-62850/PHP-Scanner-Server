<?php
if($Printer<2){
	Print_Message("Error","The Printing feature is disabled<br/>The Printer option (in <code>".getcwd()."/config.php</code> on line 12) needs to be set to 2 or 3 to use this page.","center");
	Footer('');
	die('');
}
function convertPHPSizeToBytes($sSize){// http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
    if ( is_numeric( $sSize) ){
       return $sSize;
    }
    $sSuffix = substr($sSize, -1);
    $iValue = substr($sSize, 0, -1);
    switch(strtoupper($sSuffix)){
		case 'P':
			$iValue *= 1024;
		case 'T':
			$iValue *= 1024;
		case 'G':
			$iValue *= 1024;
		case 'M':
			$iValue *= 1024;
		case 'K':
			$iValue *= 1024;
			break;
    }
    return $iValue;
}
function getMaximumFileUploadSize(){// http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
    return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
}
if( isset($_FILES['pdf']) || isset($_POST['raw']) ){
	if(isset($_POST['raw'])){
		$file='/tmp/'.md5(time().rand()).'.txt';
		SaveFile($file,$_POST['raw']);
		include('res/printer.php');
		unlink($file);
	}
	else{
		if(mime_content_type($_FILES['pdf']['tmp_name'])=='application/pdf'){
			$file=$_FILES['pdf']['tmp_name'];
			include('res/printer.php');
		}
		else{
			Print_Message('Error',html($_FILES['pdf']['name'].' does not look like a PDF'),$ALIGN);
		}
	}
	unset($file);
}
include('res/printer.php');
$sel='<select name="printer">';
for($i=0;$i<count($printers);$i=$i+1){
	if(strlen($printers[$i])>0)
		$sel=$sel.'<option value="'.html($printers[$i]).'">'.html($printers[$i]).'</option>';
}
$sel="$sel</select>";
?>

<div class="box box-full">
	<h2>PDF Printing</h2>
	<form action="index.php?page=Printer" method="post" enctype="multipart/form-data">
		<p class="center">
			<input type="file" name="pdf"/> (<?php echo getMaximumFileUploadSize()/1024/1024; ?> Megabyte limit)<br/>
			<input type="submit" name="submit" value="Submit" onclick="this.value='Uploading';"/> to <?php echo $sel; ?>
		</p>
	</form>
</div>
<div class="box box-full">
	<h2>RAW Printing</h2>
	<form action="index.php?page=Printer" method="post" enctype="multipart/form-data">
		<p class="center">
			<textarea name="raw" style="width:calc(100% - 10px);height:300px;"></textarea><br/>
			<input type="submit" name="submit" value="Submit"/> to <?php echo $sel; ?>
		</p>
	</form>
</div>
