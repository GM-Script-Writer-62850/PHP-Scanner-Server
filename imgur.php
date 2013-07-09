<?php
// Album Sample: {"album":{"data":{"id":"nmoNU","deletehash":"OVTHAA9q54go9qN"},"success":true,"status":200},"images":[{"data":{"id":"9miCuT2","deletehash":"hUZMx7pkHFncK4A","link":"http:\/\/i.imgur.com\/9miCuT2.png"},"success":true,"status":200},{"data":{"id":"FReOqEz","deletehash":"vlbGDmxQ38rzXcz","link":"http:\/\/i.imgur.com\/FReOqEz.png"},"success":true,"status":200}],"success":true}
function json_curl($data,$type,$anon){// type = upload/image||album ; anon = true|false
	$clientID='65bfadb95e040a0';
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
	$album=json_curl($_GET['album']==0?array():array('title' => $_GET['album']),'album',true); // album: https://api.imgur.com/endpoints/album#album-upload
	if(is_bool($album)||is_null($album))
		die('{"album":false,"images":[],"success":false}');
	if(!$album->{"success"})
		die(json_encode(array('album' => $album, 'images' => false, 'success' => false)));
	if($anon&&!$album->{"data"}->{"deletehash"})
		die('{"album":false,"images":[],"success":false}');
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
	echo json_encode(array('album' => (isset($destination)?$album:false), 'images' => $images, 'success' => $success));
?>
