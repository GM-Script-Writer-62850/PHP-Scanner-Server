<?php
if(!isset($_SERVER['REDIRECT_URL']))
	$here=substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/'));
else{
	$here=$_SERVER['REDIRECT_URL'];
	if(is_dir("../$here"))
		$here=substr($here,0,strrpos($here,'/'));
	else
		die(include('status.php'));
}
header("HTTP/1.1 200 OK");
include("../config.php");
function get_Type($ext){
	switch($ext){
		case "..": return "parent";
		case "json": return "txt";
		case "css": return "css";
		case "png": return "image";
		case "jpg": return "image";
		case "jpeg": return "image";
		case "gif": return "image";
		case "tif": return "image";
		case "pdf": return "pdf";
		case "tiff": return "image";
		case "tar": return "archive";
		case "7z": return "archive";
		case "rar": return "archive";
		case "bz2": return "archive";
		case "gz": return "archive";
		case "lzma": return "archive";
		case "zip": return "archive";
		case "php": return "script";
		case "mp4": return "video";
		case "mkv": return "video";
		case "wmv": return "video";
		case "ogv": return "video";
		case "avi": return "video";
		case "mpeg": return "video";
		case "mpg": return "video";
		case "mov": return "video";
		case "flv": return "video";
		case "txt": return "txt";
		case "js": return "script";
		case "exe": return "bin";
		case "bin": return "bin";
		case "c": return "txt";
		case "patch": return "patch";
		case "diff": return "patch";
		case "mp3": return "sound";
		case "wav": return "sound";
		case "oga": return "sound";
		case "flac": return "sound";
		case "wma": return "sound";
		case "ogg": return "sound";
	}
	return 'idk';
}
$PAGE='Index Of';
$path=InsertHeader($here);
?>
<div class="box box-full"><h2>Index Of <?php echo $here; ?></h2><ul>
<?php
function recursive_scandir($dir,$level,$max){
	if($level>=$max)
		return '<li class="none">Recursion Limit</li>';
	$scan=@scandir($dir);
	if($scan===false){
		return '<li class="dir">Access Denied</li>';
	}
	$files='';
	$folders='';
	$end=null;
	foreach($scan as $file){
		if($file==='.'||($file==='..'&&$level>0))
			continue;
		if(is_file("$dir/$file")){
			$end=explode('.',$file);
			$end=end($end);
			$files.='<li class="'.get_Type($end).'"><a href="'.str_replace(' ',"%20",html("../$dir/$file")).'">'.html($file).'</a></li>';
		}
		else if($file==='..')
			$folders.='<li class="parent"><a href="'.$file.'/">Parent Directory</a></li>';
		else
			$folders.='<li class="dir"><a href="'.str_replace(' ',"%20",html("../$dir/$file/")).'">'.html($file).'</a><ul>'.recursive_scandir("$dir/$file",$level+1,$max).'</ul></li>';
	}
	return (count($scan)==2?$folders.'<li class="none">Empty Directory</li>':$folders).$files;
}
echo recursive_scandir("..$here",0,5);
?></ul></div><?php footer($path); ?>
