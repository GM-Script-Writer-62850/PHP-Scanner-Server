<?php
$Fpdf_loc="/usr/share/php/fpdf/fpdf.php";
function ext2mime($ext){
	switch($ext){
		case "png": return "image/png";
		case "jpg": return "image/jpg";
		case "tiff": return "image/tiff";
		case "txt": return "text/plain";
		case "pdf": return "application/pdf";
		case "bz2": return "application/x-bzip";
		case "zip": return "application/zip";
	}
}
function returnFile($in,$out,$ext){
	header("Pragma: public");
	header("Content-type: ".ext2mime($ext));
	header('Content-Disposition: attachment; filename="'.addslashes($out).'"');
	if(is_file($in)){
		header('Content-Length: '.filesize($in));
		readfile($in);
	}
	else{
		header('Content-Length: '.strlen($in));
		echo $in;
	}
}
if(isset($_GET['file'])){
	if(strpos($_GET['file'], "/")>-1)
		$_GET['file']=substr($_GET['file'],strrpos($_GET['file'],"/")+1);
}
if(isset($_GET['downloadServer'])){
	$file="/tmp/scanner-".md5(time().rand()).".tar.bz2";
	shell_exec("tar cjf '$file' --exclude=\"scans/*\" --exclude=\"config/*.*\" ./");// '--exclude=\"password.md5\"' What was this in there for?
	returnFile($file,'PHP-Scanner-Server-'.$_GET['ver'].'.tar.bz2','bz2');
	@unlink($file);
}
else if((isset($_GET['type'])?$_GET['type']:'')=='pdf'&&!isset($_GET['raw'])){
	header("Content-type: ".ext2mime("pdf"));
	$Pwidth=215.9;
	$Pheight=279.4;
	$fontSize=16;
	$width=$Pwidth;
	$height=$Pheight;
	require($Fpdf_loc);
	$pdf=new FPDF('P','mm',array($width,$height));
	$full=isset($_GET['full']);
	$pdf=new FPDF('P','mm',array($width,$height));
	$marginLeft=$full?0:10;
	$marginTop=$full?0:20;
	$pages=0;
	$files=json_decode($_GET['json']);
	if($files==null)
		$files=array();
	foreach($files as $key => $val){
		$file=$key;
		if(is_numeric(strpos($file, "/")))
			$file=substr($file,strrpos($file,"/")+1);
		$file="Scan_$file";
		if(!is_file("scans/$file"))
			continue;
		$ext=substr($file,strrpos($file,'.')+1);
		$width=$Pwidth;
		$height=$Pheight;
		$pdf->AddPage();
		$pages+=1;
		if($full){
			if($ext=='txt'){
				$pdf->SetFont('Arial','',$fontSize*0.75);
				$pdf->MultiCell(0,5,file_get_contents("scans/$file"),0,"L",false);
			}
			else{
				$image=explode("x",shell_exec("identify -format '%wx%h' ".escapeshellarg("scans/$file")));
				if($height/$width<=$image[1]/$image[0])
					$width=0;
				else
					$height=0;
				$pdf->Image("scans/$file",$marginLeft,$marginTop,$width,$height);
			}
		}
		else{
			$pdf->SetFont('Arial','B',$fontSize);
			$pdf->MultiCell(0,$fontSize,$file,0,"C",false);
			if($ext=='txt'){
				$pdf->SetFont('Arial','',$fontSize*0.75);
				$pdf->MultiCell(0,5,file_get_contents("scans/$file"),0,"L",false);
			}
			else{
				$image=explode("x",shell_exec("identify -format '%wx%h' ".escapeshellarg("scans/$file")));
				$width=$width-($marginLeft*2);
				$height=$height-$marginTop*2-$fontSize*0.75;
				if($height/$width<=$image[1]/$image[0])
					$width=0;
				else
					$height=0;
				$pdf->Image('scans/'.$file,$marginLeft,$marginTop/2+$fontSize,$width,$height);
			}
		}
	}
	if($pages>0){
		$file=$pages>1?"Compilation.pdf":substr($file,0,strlen($ext)*-1)."pdf";
		header('Content-Disposition: attchment; filename="'.$file.'"');
		$pdf->Output($file,'D');
	}
	else{
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',$fontSize);
		$pdf->MultiCell(0,$fontSize,'None of thoes files exist :/',0,"C",false);
		header('Content-Disposition: attchment; filename="Error.pdf"');
		$pdf->Output("Error.pdf",'D');
	}
}
else if(isset($_GET['json'])){
	$files=json_decode($_GET['json']);
	if($files==null)
		$files=array();
	$FILES='';
	$type=isset($_GET['type'])?$_GET['type']:false;
	$ct=0;
	foreach($files as $key => $val){
		$file=$key;
		if(is_numeric(strpos($file, "/")))
			$file=substr($file,strrpos($file,"/")+1);
		$file="Scan_$file";
		if(is_file("scans/$file")){
			$FILES.=escapeshellarg("scans/$file").' ';
			$ct+=1;
		}
	}
	if(strlen($FILES)>0 && is_string($type)){
		$type=$_GET['type'];
		if($type=='pdf'){
			$name=$ct==1?$file:'Compilation.pdf';
			$file='/tmp/'.md5(time().rand()).'.pdf';
			$type='pdf';
			shell_exec("convert $FILES+repage '$file'");// -page Letter -gravity center
		}
		else if($type=='zip'){
			$file='/tmp/'.md5(time().rand()).'.zip';
			$type='zip';
			$name='Compilation.zip';
			shell_exec("zip '$file' $FILES");
		}
		else{
			$type='txt';
			$name='Error.txt';
			$file="Does not support '$type' files";
		}
		returnFile($file,$name,$type);
		if(is_file($file))
			@unlink($file);
	}
	else
		returnFile("No legit file names provided",'404_Error.txt','txt');
}
else if(isset($_GET['file'])){
	if(file_exists("scans/".$_GET['file'])){
		if(isset($_GET['compress'])){
			$file='/tmp/download-'.md5(time().rand()).'.zip';
			shell_exec("cd 'scans' && zip '$file' ".escapeshellarg($_GET['file']));
			returnFile($file,$_GET['file'],'zip');
			@unlink($file);
		}
		else{
			$ext=substr($_GET['file'],strrpos($_GET['file'],".")+1);
			returnFile("scans/".$_GET['file'],$_GET['file'],$ext);
		}
	}
	else
		returnFile("The file ".$_GET['file']." was not found in the scans folder.",'404.txt','txt');
}
else if(isset($_GET['update'])){
	$file=@file_get_contents("https://raw.github.com/GM-Script-Writer-62850/PHP-Scanner-Server/master/README");
	if($file){
		$file=substr($file,strpos($file,'For Version: ')+13);
		$file=substr($file,0,strpos($file,PHP_EOL));
		$vs=version_compare($file,$_GET['update']);// -1 = older, 0 = same, 1 = newer
		echo "{\"state\":\"$vs\",\"version\":\"$file\"}";
		$f=@fopen("config/gitVersion.txt",'w+');
		@fwrite($f,$file);
		@fclose($f);
	}
	else
		echo '{"state":-2,"vs":null}';
}
else
	returnFile("You: Hey download.php I want a download.\nMe: Ok here you go!\nYou: Ha ha, very funny that is not what I meant\nMe: Well maybe if you told me what you want I could give it to you","Reply.txt",'txt');
?>
