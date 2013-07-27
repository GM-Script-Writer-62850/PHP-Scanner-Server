<?php
header('Content-type: application/json; charset=UTF-8');
if(!function_exists('curl_version'))
	die('{"album":false,"images":[],"success":false,"error":"<code>php5-curl</code>/<code>php-curl</code> is not installed on the server"}');
function json_curl($data,$type,$anon){// type = upload/image||album ; anon = true|false
	$clientID='65bfadb95e040a0';// Get these here: https://api.imgur.com/oauth2/addclient
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.imgur.com/3/$type.json");
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Client-ID $clientID"));
	$data = curl_exec($curl);
	curl_close($curl);
	if(!$data)
		return false;
	else
		return json_decode($data);
}
if(!isset($_GET['file'])&&!isset($_GET['files']))
	die('{"album":false,"images":[],"success":false,"error":"What exactly am I suposed to do with no user input?"}');
$anon=isset($_GET['anon'])?true:false;// Non-anon is not yet supported, that stuff gets complicated
$success=true;
$files=isset($_GET['file'])?array($_GET['file'] => 1):json_decode($_GET['files']);
$Files=array();
foreach($files as $file => $trash){
	if(is_numeric(strpos($file,'/')))
		$file=substr($file,strrpos($file,'/')+1);
	if(substr($file,0,5)!="Scan_")
		$file="Scan_$file";
	if(file_exists("scans/$file"))
		array_push($Files,$file);
}
if(isset($_GET['album'])&&count($Files)>1){
	$album=json_curl(strlen($_GET['album'])==0?array():array('title' => $_GET['album']),'album',true); // album: https://api.imgur.com/endpoints/album#album-upload
	if(is_bool($album)||is_null($album))
		die('{"album":false,"images":[],"success":false,"error":"Failed to connect to imgur"}');
	if(!$album->{"success"}||!$album->{"data"})
		die(json_encode(array('album' => $album, 'images' => array(), 'success' => false, "error" => false)));
	if($anon&&!$album->{"data"}->{"deletehash"})
		die('{"album":'.json_encode($album).',"images":[],"success":false,"error":"Missing delete hash"}');
	$album->{"data"}->{"title"}=strlen($_GET['album'])>0?$_GET['album']:'Compilation';
	$destination=$anon?$album->{"data"}->{"deletehash"}:$album->{"data"}->{"id"};
}
$images=array();
foreach($Files as $file){
	$image = array(
		'type' => 'base64', 'name' => $file, 'title' => substr($file,5,strrpos($file,'.')-5),
		'description' => 'Uploaded from PHP Scanner Server');// image: https://api.imgur.com/endpoints/image#image-upload
	if(substr($file,-4)=='.txt'){
		$file2='/tmp/'.md5(time().rand()).'.png';
		shell_exec("convert ".escapeshellarg("scans/$file")." '$file2'");
		$image=array_merge($image,array('image' => base64_encode(file_get_contents($file2))));
		@unlink($file2);
	}
	else
		$image=array_merge($image,array('image' => base64_encode(file_get_contents("scans/$file"))));
	if(isset($destination))
		$image=array_merge($image,array('album' => $destination));
	$json=json_curl($image,'image',true);
	if(is_null($json))
		$json=false;
	if(!is_bool($json)){
		$json->{"data"}->{'file'}=$file;
	}
	array_push($images,$json);
	if(is_bool($json)){//no reply
		$success=false;
		break;
	}
	if(!$json->{"success"}){
		$success=false;
		break;
	}
}
if(count($Files)==0)
	echo '{"album":false,"images":[],"success":false,"message":"Invalid File(s)"}';
else
	echo json_encode(array('album' => (isset($destination)?$album:false), 'images' => $images, 'success' => $success, "error" => false));
?>
