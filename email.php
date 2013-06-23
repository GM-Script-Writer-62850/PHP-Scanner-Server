<?php
if(isset($_POST['file'])){
	/*
	foreach($_POST as $key => $value){// This if for debuging
		echo "$key = $value\n";
	}*/

	$scan = $_POST['file'];
	if(strrpos($scan, "/")>-1)
		$scan =substr($scan,strrpos($scan,"/")+1);
		
	if(!file_exists("scans/$scan")){
		echo '{"title":"404 Not Found","message":"That scan no longer exists"}';
		die();
	}
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPDebug = 0; // enables SMTP debug information (for testing) // 0 = no errors or messages // 1 = errors and messages // 2 = messages only
	$mail->SMTPAuth = true; // enable SMTP authentication
	$mail->SMTPSecure = $_POST['prefix']; // sets the prefix to the server
	$mail->Host = $_POST['host']; // sets the host of the SMTP server
	$mail->Port = $_POST['port']; // set the SMTP port for the email server
	$mail->Username = $_POST['from']; // username
	$mail->Password = $_POST['pass']; // password
	$mail->SetFrom($_POST['from']); // who sent it

	//SendTO
	$to=explode(",",str_replace(" ", "", $_POST['to']));
	$list='';
	for($i=0,$stp=count($to);$i<$stp;$i++){
		if($stp==1)
			$mail->AddAddress($to[$i]);
		else
			$mail->AddBCC($to[$i]);
		if($stp==2)
			$list.=$to[$i].($i==$stp-2?' and ':', ');
		else
			$list.=$to[$i].($i==$stp-2?', and ':', ');
	}
	
	$mail->Subject = $_POST['title']; // set title
	$mail->IsHTML(true);
	if(substr($scan,-3)!='txt'){
		$mail->AddEmbeddedImage("scans/".$scan, $scan);
		$mail->Body = "<h1>Scanned with PHP Scanner Server</h1><img src=\"cid:".htmlspecialchars($scan)."\"/>";
		$mail->AltBody="Please view this in html instead of plain text.";
	}
	else{
		$filedata=file_get_contents("scans/$scan");
		$mail->Body = "<h1>Scanned with PHP Scanner Server</h1>".htmlspecialchars($filedata);
		$mail->AltBody="Scanned with PHP Scanner Server\n$filedata";
	}

	if(!$mail->Send()){
		$json=json_decode('{"title":"Email NOT sent!"}');
		$json->{"message"}=$mail->ErrorInfo;
		echo json_encode($json);
	}
	else{
		$json=json_decode('{"title":"Email sent!"}');
		$json->{"message"}=$_POST['from']." has sent <i>".$_POST['title']."</i> to ".substr($list,0,-2);
		echo json_encode($json);
	}
}
else if(isset($_GET['domain'])){
	$data=@file_get_contents("https://autoconfig.thunderbird.net/v1.1/".$_GET['domain']);
	if($data){
		$JSON=json_decode('{}');
		$data=simplexml_load_string($data);
		$data=$data->{"emailProvider"}->{"outgoingServer"};
		$JSON->{"port"}=(int)$data->{"port"};
		$JSON->{"host"}=(string)$data->{"hostname"};
		//$JSON->{"prefix"}=(string)$data->{"prefixType"}; # No idea what this is good for @.@
		$JSON->{"type"}=(string)$data->attributes()->{"type"};
		echo json_encode($JSON);
	}
	else{
		echo '{"error":404}';
	}
}
else{
	echo '{"title":"404 Not Found","message":"No scan specified"}';
}
?>
