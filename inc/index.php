<?php
// functions from ../index.php
function html($X){
	return htmlspecialchars($X);
}
function url($X){
	return rawurlencode($X);
}
function js($X){
	return str_replace("\n",'\\n',addslashes($X));
}
$PAGE=http_response_code();
$NAME="PHP Scanner Server";
$VER="1.3-8_dev";
$page="Error";
include("header.php");
?>
<div class="box box-full"><h2>HTTP Status Code: <?php echo $PAGE; ?></h2><p class="center"><?php echo $PAGE==200?'Ok, you found me':'That is a error'; ?></p></div>
<?php
include("footer.php");
?></body></html>
