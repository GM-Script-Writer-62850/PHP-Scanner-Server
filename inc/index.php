<?php
function html($X){
	return htmlspecialchars($X);// Name is too long and subject to frequent typos
}
$PAGE=http_response_code();
$NAME="PHP Scanner Server";
$VER="1.3-7_dev";
$page="Error";
include("header.php");
?>
<div class="box box-full"><h2>HTTP Status Code: <?php echo $PAGE; ?></h2><p style="text-align:center"><?php echo $PAGE==200?'Ok, you found me':'That is a error'; ?></p></div>
<?php
include("footer.php");
?></body></html>
