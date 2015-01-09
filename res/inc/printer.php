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
if(isset($_POST['format'])){
	if( $_POST['format']=='raw' && strlen($_POST['raw'])>0 ){
		$file='/tmp/'.md5(time().rand()).'.txt';
		SaveFile($file,$_POST['raw']);
		include('res/printer.php');
		unlink($file);
	}
	else if($_POST['format']=='raw'){
		Print_Message('Error','You forgot to include the text to print...','center');
	}
	else if( $_POST['format']=='pdf' && isset($_FILES['pdf']) ){
		if(strlen($_FILES['pdf']['name'])==0){
			Print_Message('Error','You forgot to include the PDF File to print...','center');
		}
		else if(mime_content_type($_FILES['pdf']['tmp_name'])=='application/pdf'){
			$file=$_FILES['pdf']['tmp_name'];
			include('res/printer.php');
		}
		else{
			Print_Message('Error',html($_FILES['pdf']['name']).' does not look like a PDF','center');
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
					if(typeof printers=="object")
						buildPrinterOptions(printers,getID('p_config'),localStorage.getItem('lastPrinter'));
					else{
						document.write(printers);
						window.addEventListener('load',function(){
							Printer.pdf.setAttribute('disabled',true);
							Printer.submit[0].setAttribute('disabled',true);
							Printer.submit[1].setAttribute('disabled',true);
							Printer.raw.value="Please read the message to the left";
						},false);
					}
				</script>
			</div>
		</div>
	</div>
	<div class="box box-wide">
		<h2>PDF Printing</h2>
		<p class="center">
			<input type="file" name="pdf" onchange="submitPrint(Printer,<?php echo $upLimit; ?>,true)"/> (<?php echo $upLimit/1024/1024; ?> Megabyte limit)<br/>
			<input type="submit" name="submit" value="Print PDF" onclick="Printer.format.value='pdf';this.value='Uploading';"/>
		</p>
	</div>
	<div class="box box-wide">
		<h2>RAW Printing (Plain Text)</h2>
		<p class="center">
			Please paste or type your text in the space below.<br/>
			<textarea name="raw" style="width:calc(100% - 10px);height:300px;"></textarea><br/>
			<input type="submit" name="submit" value="Print RAW Text" onclick="Printer.format.value='raw';"/>
		</p>
	</div>
</form>
