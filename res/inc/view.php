<?php
$icons=genIconLinks((object)array('view'=>0),"$file",false);
if(file_exists("scans/file/$file")){
	if(substr($file,-3)=='txt'){
		echo "<div class=\"box box-full\"><h2>".html($file)."</h2>".
			"<p>$icons</p>".
			"<pre class=\"border\" id=\"text-file-".html($file)."\">".html(file_get_contents("scans/file/$file"))."</pre></div>".
			'<script type="text/javascript" src="data:text/javascript;charset=utf-8,'.
			url('(function(e){if(e.offsetHeight==2)e[TC]="Tesseract was unable to find any text in the scan.";})(getID("text-file-'.js($file).'"));').
			'"></script>';// Using Data URI as a dirty trick for security (don't want a separate file for this)
	}
	else{
		echo '<div class="box box-full"><h2>'.html($file).'</h2>'.
			"<p>$icons<br/>".
			'<a class="tool" href="scans/file/'.url($file).'" target="_blank">'.
			(substr($file,-4)=='tiff'?'<h3>Sorry TIFF format can NOT be displayed, please download to view.</h3>':'<img src="scans/file/'.url($file).'"/>').
			'<span class="tip">View raw image</span></a></p></div>';
	}
}
else{
	echo '<br/>'.
		'<div class="box box-full"><h2>404 Scan Not Found</h2>'.
		'<p>'.str_replace('" href', '-off" href', $icons).'</p>'.
		'<pre class="border">'.html($file).' was not found, it was probably deleted</pre></div>';
}
?>
