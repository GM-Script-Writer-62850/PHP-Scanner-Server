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
}
$upLimit=getMaximumFileUploadSize();
?>
<form name="Printer" action="index.php?page=Printer" method="post" enctype="multipart/form-data" onsubmit="return submitPrint(this,<?php echo $upLimit; ?>,false);">
	<input type="hidden" name="format"/>
	<input type="hidden" name="options"/>
	<div id="sidebar" style="min-height:100px;">
		<div class="side_box">
			<h2>Printer Configuration</h2>
			<div id="p_config">
				<script type="text/javascript">
					var printers=<?php 
						$f=file_get_contents('config/printers.json'); 
						echo $f===false?'\'Printers have not been configured, please <a href="index.php?page=Config&action=Search-For-Printers">search for printers</a> on the <a href="index.php?page=Config">Configure</a> page.\'':$f;
					?>;
					if(typeof printers=="object"){
						buildPrinterOptions(printers,getID('p_config'),false);
					}
					else
						document.write(printers);
				</script>
			</div>
		</div>
	</div>
	<div class="box box-wide">
		<h2>PDF Printing</h2>
		<p class="center">
			<input type="file" name="pdf" onchange="submitPrint(Printer,<?php echo $upLimit; ?>,true)"/> (<?php echo $upLimit/1024/1024; ?> Megabyte limit)<br/>
			<input type="submit" name="submit" value="Submit" onclick="Printer.format.value='pdf';this.value='Uploading';"/>
		</p>
	</div>
	<div class="box box-wide">
		<h2>RAW Printing</h2>
		<p class="center">
			<textarea name="raw" style="width:calc(100% - 10px);height:300px;"></textarea><br/>
			<input type="submit" name="submit" value="Submit" onclick="Printer.format.value='raw';"/>
		</p>
	</div>
</form>
