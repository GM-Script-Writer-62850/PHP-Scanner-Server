<?php
# This file is used to get a list of printers via AJAX
# The printer.php file in the inc folder does the printer tab
# The download.php in the parent folder does the integrated printing
header('Content-type: plain/txt; charset=UTF-8');
echo str_replace("\n",",",substr(shell_exec('lpstat -a|awk \'{print $1}\''),0,-1));
?>
