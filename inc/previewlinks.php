<?php
	header("Content-Type: text/javascript");
	$file=substr($_GET['file'],5);
?>
var fileJS="<?php echo str_replace("\n",'\\n',addslashes($file)); ?>";
var fileURL="<?php echo rawurlencode($file); ?>";
getID("preview_links").innerHTML="<h2><?php echo htmlspecialchars($file); ?></h2>\
<p>\
	<a href=\"download.php?file=Scan_"+fileURL+"\" class=\"tool icon download\"><span class=\"tip\">Download</span></a> \
	<a href=\"download.php?file=Scan_"+fileURL+"&amp;compress\" class=\"tool icon zip\"><span class=\"tip\">Download Zip</span></a> \
	<a href=\"#\" onclick=\"PDF_popup('"+fileJS+"');\" class=\"tool icon pdf\"><span class=\"tip\">Download PDF</span></a> \
	<a href=\"print.php?file=Scan_"+fileURL+"\" target=\"_blank\" class=\"tool icon print\" target=\"_blank\"><span class=\"tip\">Print</span></a> \
	<a href=\"index.php?page=Scans&amp;delete=Remove&amp;file="+fileURL+"\" class=\"tool icon del\" onclick=\"return confirm('Delete this scan')\"><span class=\"tip\">Delete</span></a> \
	<?php
	if($_GET['page']=="Edit")
		echo '<span class=\\"tool icon edit-off\\"><span class=\\"tip\\">Edit (Disabled)</span></span> \\';
	else
		echo '<a href=\\"index.php?page=Edit&amp;file="+file+"\\" class=\\"tool icon edit\\"><span class=\\"tip\\">Edit</span></a> \\';
	?>
	<a href=\"index.php?page=View&amp;file=Scan_"+fileURL+"\" class=\"tool icon view\"><span class=\"tip\">View</span></a> \
	<a href=\"#\" class=\"tool icon upload\" onclick=\"return upload('Scan_"+fileJS+"')\"><span class=\"tip\">Upload to Imgur</span></a> \
	<a href=\"#\" onclick=\"return emailManager('Scan_"+fileJS+"');\" class=\"tool icon email\"><span class=\"tip\">Email</span></a> \
</p>";
