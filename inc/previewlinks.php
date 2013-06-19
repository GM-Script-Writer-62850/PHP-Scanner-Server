
<?php
        header("Content-Type: text/javascript");
        $file=substr($_GET['file'],5);
?>
var file="<?php echo addslashes($file); ?>";
getID("preview_links").innerHTML="<h2>"+file+"</h2>\
<p>\
        <a href=\"#\" onclick=\"PDF_popup('Scan_"+file+"');\" class=\"tool icon1 pdf1\"><span class=\"tip\">Download PDF</span></a> \
</p>";
