<?php
	header("Content-Type: text/javascript");
	$file=substr($_GET['file'],5);
?>
var file="<?php echo addslashes($file); ?>";
getID("preview_links").innerHTML="<h2>"+file+"</h2>\
<p>\
	<a href=\"download.php?file=Scan_"+file+"\" class=\"tool icon download\"><span class=\"tip\">Download</span></a> \
	<a href=\"download.php?file=Scan_"+file+"&compress\" class=\"tool icon zip\"><span class=\"tip\">Download Zip</span></a> \
	<a href=\"#\" onclick=\"PDF_popup('"+file+"');\" class=\"tool icon pdf\"><span class=\"tip\">Download PDF</span></a> \
	<a href=\"print.php?file=Scan_"+file+"\" target=\"_blank\" class=\"tool icon print\" target=\"_blank\"><span class=\"tip\">Print</span></a> \
	<a href=\"index.php?page=Scans&delete=Remove&file="+file+"\" class=\"tool icon del\" onclick=\"return confirm('Delete this scan')\"><span class=\"tip\">Delete</span></a> \
	<?php
	if($_GET['page']=="Edit")
		echo '<span class=\\"tool icon edit-off\\"><span class=\\"tip\\">Edit (Disabled)</span></span> \\';
	else
		echo '<a href=\\"index.php?page=Edit&file="+file+"\\" class=\\"tool icon edit\\"><span class=\\"tip\\">Edit</span></a> \\';
	?>
	<a href=\"index.php?page=View&file=Scan_"+file+"\" class=\"tool icon view\"><span class=\"tip\">View</span></a> \
	<a href=\"#\" class=\"tool icon upload\" onclick=\"return upload('Scan_"+file+"')\"><span class=\"tip\">Upload to Imgur</span></a> \
	<a href=\"#\" onclick=\"return emailManager('Scan_"+file+"');\" class=\"tool icon email\"><span class=\"tip\">Email</span></a> \
</p>";
