<?php
$Fpdf_loc="/usr/share/php/fpdf/fpdf.php";
function ext2mime($ext){
	switch($ext){
		case "png": return "image/png";
		case "jpg": return "image/jpg";
		case "tiff": return "image/tiff";
		case "txt": return "text/plane";
	}
}
if(isset($_GET['file'])){
	if(strrpos($_GET['file'], "/")>-1)
		$_GET['file']=substr($_GET['file'],strrpos($_GET['file'],"/")+1);
}
if(isset($_GET['downloadServer'])){
	header("Pragma: public");
	header("Content-type: application/x-bzip");
	$t=time();
	header("Content-Disposition: attachment; filename=\"PHP-Server-Scanner-".addslashes($_GET['ver']).".tar.bz2\"");
	shell_exec("tar cjf /tmp/scanner-$t.tar.bz2 --exclude=\"scans/*\" --exclude=\"config/*.json\" --exclude=\"IMGUR_API_KEY.txt\" --exclude=\"password.md5\" ./");
	$file=file_get_contents("/tmp/scanner-$t.tar.bz2");
	header('Content-Length: '.strlen($file));
	echo $file;
	unlink("/tmp/scanner-$t.tar.bz2");
}
else if(isset($_GET['json'])){
	$files=json_decode($_GET['json']);
	$cmd="convert ";
	header("Pragma: public");
	foreach($files as $key => $val){
		$file="Scan_$key";
		if(strrpos($file, "/")>-1)
			$file=substr($file,strrpos($file,"/")+1);
		if(is_file("scans/$file"))
			$cmd.="scans/$file ";
	}
	if(strlen($cmd)>8){
		$file=md5(time().rand()).'.pdf';
		shell_exec($cmd."/tmp/$file");// -page Letter -gravity center
		header("Content-type: application/pdf");
		header("Content-Disposition: attachment; filename=\"Compilation.pdf\"");
		echo file_get_contents("/tmp/$file");
		@unlink("/tmp/$file");
	}
	else{
		header("Content-type: plain/txt");
		header("Content-Disposition: attachment; filename=\"Error.txt\"");
		echo "No legit file names provided";
	}
}
else if(isset($_GET['file'])){
	if(file_exists("scans/".$_GET['file'])){
		header("Pragma: public");
		if(isset($_GET['compress'])){
			$download=substr($_GET['file'],0,strrpos($_GET['file'],"."));
			header("Content-Disposition: attachment; filename=\"$download.zip\"");
			shell_exec("cd \"scans\" && zip -r \"/tmp/$download.zip\" \"".$_GET['file']."\"");
		        echo file_get_contents("/tmp/$download.zip");
			unlink("/tmp/$download.zip");
		}
		else{
			$ext=substr($_GET['file'],strrpos($_GET['file'],".")+1);
			if(isset($_GET['pdf'])){
				header("Content-type: application/pdf");
				header("Content-Disposition: attachment; filename=\"".substr($_GET['file'],0,strlen($ext)*-1)."pdf\"");
				if(!isset($_GET['full'])){
					$fontSize=16;
					$marginLeft=10;
					$marginTop=20;
					$width=215.9;
					$height=279.4;

					require($Fpdf_loc);

					$pdf=new FPDF('P','mm',array($width,$height));
					$pdf->AddPage();
					$pdf->SetFont('Arial','B',$fontSize);
					$pdf->MultiCell(0,$fontSize,$_GET['file'],0,"C",false);
					if($ext=='txt'){
						$pdf->SetFont('Arial','',$fontSize*0.75);
						$pdf->MultiCell(0,5,file_get_contents("scans/".$_GET['file']),0,"L",false);
					}
					else{
						$image=explode("x",shell_exec("identify -format '%wx%h' \"scans/".$_GET['file']."\""));
						$width=$width-($marginLeft*2);
						$height=$height-$marginTop*2-$fontSize*0.75;
						$pWidth=$width/100;
						$pHeight=$height/100;

						if($pHeight-$pWidth<=$image[0]/100-$image[1]/100)
							$height=0;
						else
							$width=0;
						$pdf->Image('scans/'.$_GET['file'],$marginLeft,$marginTop/2+$fontSize,$width,$height);
					}
					$pdf->Output(substr($_GET['file'],0,strlen($ext)*-1)."pdf",'D');
				}
				else{
					$fontSize=16;
					$marginLeft=0;
					$marginTop=0;
					$width=215.9;
					$height=279.4;

					require($Fpdf_loc);

					$pdf=new FPDF('P','mm',array($width,$height));
					$pdf->AddPage();
					if($ext=='txt'){
						$pdf->SetFont('Arial','',$fontSize*0.75);
						$pdf->MultiCell(0,5,file_get_contents("scans/".$_GET['file']),0,"L",false);
					}
					else{
						$image=explode("x",shell_exec("identify -format '%wx%h' \"scans/".$_GET['file']."\""));
						$pWidth=$width/100;
						$pHeight=$height/100;

						if($pHeight-$pWidth<=$image[0]/100-$image[1]/100)
							$height=0;
						else
							$width=0;
						$pdf->Image('scans/'.$_GET['file'],$marginLeft,$marginTop,$width,$height);
					}
					$pdf->Output(substr($_GET['file'],0,strlen($ext)*-1)."pdf",'D');
				}
			}
			else{
				header("Content-type: ".ext2mime($ext));
				header("Content-Disposition: attachment; filename=\"".$_GET['file']."\"");
				echo file_get_contents("scans/".$_GET['file']);
			}
		}
	}
	else{
		header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
		echo "<html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1>The file ".htmlspecialchars($_GET['file'])." was not found in the scans folder.</body></html>";
	}
}
?>
