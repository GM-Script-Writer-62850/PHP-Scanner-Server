<?php
include('../config.php');
$PAGE=http_response_code();
$path=InsertHeader('Error');
$page=($PAGE==200?"Success":"Error");
?>
<div class="box box-full"><h2>HTTP Status Code: <?php echo $PAGE; ?></h2><p class="center"><?php echo $PAGE==200?'Ok, you found me':'That is a error'; ?></p></div>
<?php
Footer($path);
?></body></html>
