<?php
function imgurUpload($filename){
	$data = file_get_contents($filename);

	// $data is file data
	$pvars = array('image' => base64_encode($data), 'key' => file_get_contents('config/IMGUR_API_KEY.txt'));
	$timeout = 30;
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, 'http://api.imgur.com/2/upload.json');
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);

	$xml = curl_exec($curl);

	curl_close ($curl);
	return $xml;
}
$JSON=json_decode("{}");
if(isset($_GET['file'])){
	if(strrpos($_GET['file'], "/")>-1)
		$_GET['file']=substr($_GET['file'],strrpos($_GET['file'],"/")+1);
	$file=$_GET['file'];
	$JSON->{"error"}=json_decode("{}");
	if(substr($file,-3)=='txt'){
		$JSON->{"error"}->{"message"}="Text files can't be Uploaded to a image hosting website.";
		echo json_encode($JSON);
	}
	else if(!file_exists('config/IMGUR_API_KEY.txt')){
		$JSON->{"error"}->{"message"}='No key for <a href="http://imgur.com/register/api_anon" target="_blank">imgur.com</a> was found.';
		echo json_encode($JSON);
	}
	else if(file_exists("scans/$file")){
		$json=json_decode(imgurUpload("scans/$file"));
		if(isset($json->{"error"})){
			if($json->{"error"}->{"message"}=="Invalid API Key"){
				$JSON->{"error"}->{"message"}='Invalid API Key<br/>Go to the <a href="index.php?page=Config"></a> page and create one.';
				echo json_encode($JSON);
			}
			else{
				$JSON->{"error"}->{"message"}=$json->{"error"}->{"message"};
				echo json_encode($JSON);
			}
		}
		else if(isset($json->{"upload"})){
			$json->{"error"}=json_decode('{"message":null}');
			$img=$json->{"upload"}->{"links"}->{"original"};
			$imgP1=substr($img,0,strrpos($img,'.'));
			$imgP2=substr($img,strrpos($img,'.'));
			$json->{"upload"}->{"links"}->{"small_thumbnail"}=$imgP1.'t'.$imgP2;
			$json->{"upload"}->{"links"}->{"medium_thumbnail"}=$imgP1.'m'.$imgP2;
			$json->{"upload"}->{"links"}->{"huge_thumbnail"}=$imgP1.'h'.$imgP2;
			$json->{"upload"}->{"links"}->{"big_square"}=$imgP1.'b'.$imgP2;
			echo json_encode($json);
		}
	}
	else{
		$JSON->{"error"}->{"message"}="$file does not exist.";
		echo json_encode($JSON);
	}
}
else{
	$JSON->{"error"}->{"message"}="No file specified.";
	echo json_encode($JSON);
}
/* Sample result for invalid key
	{
		"error": {
			"message": "Invalid API Key",
			"request": "/2/upload.json",
			"method": "post",
			"format": "json",
			"parameters": "image = iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAABIAAAASABG..."
		}
	}
 * Sample valid key
	 {
		"upload": {
			"image": {
				"name": null,
				"title": null,
				"caption": null,
				"hash": "33sqk",
				"deletehash": "cJhJhSj3BGJRuyL",
				"datetime": "2012-03-07 19:50:25",
				"type": "image/png",
				"animated": "false",
				"width": 16,
				"height": 16,
				"size": 367,
				"views": 0,
				"bandwidth": 0
			},
			"links": {
				"original": "http://i.imgur.com/33sqk.png",
				"imgur_page": "http://imgur.com/33sqk",
				"delete_page": "http://imgur.com/delete/cJhJhSj3BGJRuyL",
				"small_square": "http://i.imgur.com/33sqks.jpg",
				"large_thumbnail": "http://i.imgur.com/33sqkl.jpg"
			}
		}
	}
*/
?>
