<?php
$icons='<a class="tool icon download" href="download.php?file='.html($file).'"><span class="tip">Download</span></a> '.
	'<a class="tool icon zip" href="download.php?file='.html($file).'&compress"><span class="tip">Download Zip</span></a> '.
	'<a class="tool icon pdf" href="#" onclick="PDF_popup(\''.html($file).'\');"><span class="tip">Download PDF</span></a> '.
	'<a class="tool icon print" href="print.php?file='.html($file).'" target="_blank"><span class="tip">Print</span></a> '.
	'<a class="tool icon del" href="index.php?page=Scans&delete=Remove&file='.html(substr($file,5)).'"><span class="tip">Delete</span></a> '.
	'<a class="tool icon edit" href="index.php?page=Edit&file='.html(substr($file,5)).'"><span class="tip">Edit</span></a> '.
	'<span class="tool icon view-off"><span class="tip">View (Disabled)</span></span> '.
	'<a class="tool icon upload" href="#" onclick="return upload(\''.html($file,5).'\')"><span class="tip">Upload to Imgur</span></a> '.
	'<a href="#" onclick="return emailManager(\''.html($file).'\');" class="tool icon email"><span class="tip">Email</span></a>';
if(file_exists("scans/$file")){
	if(!file_exists('config/IMGUR_API_KEY.txt')||substr($file,-3)=='txt'){
		$icons=str_replace('tool icon upload', 'tool icon upload-off', $icons);
	}
	if(substr($file,-3)=='txt'){
		echo "<div class=\"box box-full\"><h2>".html($file)."</h2>";
		echo "<p>$icons</p>";
		echo "<pre class=\"border\" id=\"text-file\">".html(file_get_contents("scans/$file"))."</pre></div>";
		echo '<script type="text/javascript">e=document.getElementById(\'text-file\');if(e.offsetHeight==2)e.innerHTML=\'Tesseract was unable to find any text in the scan.\'</script>';
	}
	else{
		echo "<div class=\"box box-full\"><h2>".html($file)."</h2>";
		echo '<p>'.$icons.'<br/>';
		echo "<a class=\"tool\" href=\"scans/".html($file)."\" target=\"_blank\"><img src=\"scans/".html($file)."\"/><span class=\"tip\">View raw image</span></a></p></div>";
	}
}
else{
	echo ' <br/>';
	echo "<div class=\"box box-full\"><h2>404 Scan Not Found</h2>";
	echo "<p>".str_replace('" href', '-off" href', $icons)."</p>";
	echo "<pre class=\"border\">".html($file)." was not found, it was probally deleted</pre></div>";
}
?><script type="text/javascript">disableIcons();</script>
