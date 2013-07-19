<?php
$icons='<a class="tool icon download" href="download.php?file='.url($file).'"><span class="tip">Download</span></a> '.
	'<a class="tool icon zip" href="download.php?file='.url($file).'&amp;compress"><span class="tip">Download Zip</span></a> '.
	'<a class="tool icon pdf" href="#" onclick="PDF_popup(\''.html(js(substr($file,5))).'\');"><span class="tip">Download PDF</span></a> '.
	'<a class="tool icon print" href="print.php?file='.url($file).'" target="_blank"><span class="tip">Print</span></a> '.
	'<a class="tool icon del" href="index.php?page=Scans&amp;delete=Remove&amp;file='.url(substr($file,5)).'" onclick="return confirm(\'Delete this scan\')"><span class="tip">Delete</span></a> '.
	'<a class="tool icon edit" href="index.php?page=Edit&amp;file='.url(substr($file,5)).'"><span class="tip">Edit</span></a> '.
	'<span class="tool icon view-off"><span class="tip">View (Disabled)</span></span> '.
	'<a class="tool icon upload" href="#" onclick="return upload(\''.html(js($file,5)).'\')"><span class="tip">Upload to Imgur</span></a> '.
	'<a href="#" onclick="return emailManager(\''.html(js($file)).'\');" class="tool icon email"><span class="tip">Email</span></a>';
if(file_exists("scans/$file")){
	if(substr($file,-3)=='txt'){
		echo "<div class=\"box box-full\"><h2>".html($file)."</h2>";
		echo "<p>$icons</p>";
		echo "<pre class=\"border\" id=\"text-file-".html($file)."\">".html(file_get_contents("scans/$file"))."</pre></div>";
		echo '<script type="text/javascript" src="data:text/javascript;charset=utf-8,'.
			url('e=getID("text-file-'.js($file).'");if(e.offsetHeight==2)e.innerHTML=\'Tesseract was unable to find any text in the scan.\';').
			'"></script>';// Using Data URI as a dirty trick for security (don't want a separate file for this)
	}
	else{
		echo "<div class=\"box box-full\"><h2>".html($file)."</h2>";
		echo "<p>$icons<br/>";
		echo "<a class=\"tool\" href=\"scans/".url($file)."\" target=\"_blank\"><img src=\"scans/".url($file)."\"/><span class=\"tip\">View raw image</span></a></p></div>";
	}
}
else{
	echo ' <br/>';
	echo "<div class=\"box box-full\"><h2>404 Scan Not Found</h2>";
	echo "<p>".str_replace('" href', '-off" href', $icons)."</p>";
	echo "<pre class=\"border\">".html($file)." was not found, it was probally deleted</pre></div>";
}
?>
