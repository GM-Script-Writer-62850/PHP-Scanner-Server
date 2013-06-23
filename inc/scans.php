<div class="box">
<h2 ondblclick="toggleFile(this);" selected="false"><?php 
$FILE=html($FILE);
echo $FILE;
?></h2>
<p>
<a class="tool icon download" href="download.php?file=Scan_<?php echo html($FILE); ?>"><span class="tip">Download</span></a>
<a class="tool icon zip" href="download.php?file=Scan_<?php echo html($FILE); ?>&compress"><span class="tip">Download Zip</span></a>
<a class="tool icon pdf" href="#" onclick="return PDF_popup('Scan_<?php echo html($FILE); ?>');"><span class="tip">Download PDF</span></a>
<a class="tool icon print" href="print.php?file=Scan_<?php echo html($FILE); ?>" target="_blank"><span class="tip">Print</span></a>
<a class="tool icon del" href="index.php?page=Scans&delete=Remove&file=<?php echo html($FILE); ?>"><span class="tip">Delete</span></a>
<a class="tool icon edit" href="index.php?page=Edit&file=<?php echo html($FILE); ?>"><span class="tip">Edit</span></a> 
<a class="tool icon view" href="index.php?page=View&file=Scan_<?php echo html($FILE); ?>"><span class="tip">View</span></a> 
<?php
if(file_exists('config/IMGUR_API_KEY.txt')&&substr($FILE,-3)!='txt')
	echo '<a class="tool icon upload" href="#" onclick="return upload(\'Scan_'.html($FILE).'\');"><span class="tip">Upload to Imgur</span></a>';
else
	echo '<span class="tool icon upload-off"><span class="tip">Upload to Imgur (Disabled)</span></span>';
?> 
<a href="#" onclick="return emailManager('Scan_<?php echo html($FILE); ?>');" class="tool icon email"><span class="tip">Email</span></a>
<br/>
<a class="tool" target="_blank" href="scans/Scan_<?php echo html($FILE); ?>" style="width:100%;"><img src="scans/<?php echo html($IMAGE); ?>" style="width:100%"/><span class="tip">View full size</span></a>
</p>
</div>
