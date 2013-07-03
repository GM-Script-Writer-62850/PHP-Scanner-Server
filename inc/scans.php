<div class="box" id="<?php 
$FILE=html($FILE);
echo $FILE; 
?>">
<h2 ondblclick="toggleFile(this);" selected="false"><?php echo $FILE; ?></h2>
<p>
<a class="tool icon download" href="download.php?file=Scan_<?php echo $FILE; ?>"><span class="tip">Download</span></a>
<a class="tool icon zip" href="download.php?file=Scan_<?php echo $FILE; ?>&compress"><span class="tip">Download Zip</span></a>
<a class="tool icon pdf" href="#" onclick="return PDF_popup('Scan_<?php echo $FILE; ?>');"><span class="tip">Download PDF</span></a>
<a class="tool icon print" href="print.php?file=Scan_<?php echo $FILE; ?>" target="_blank"><span class="tip">Print</span></a>
<a class="tool icon del" href="index.php?page=Scans&delete=Remove&file=<?php echo $FILE; ?>" onclick="return delScan('<?php echo $FILE; ?>',true)"><span class="tip">Delete</span></a>
<a class="tool icon edit" href="index.php?page=Edit&file=<?php echo $FILE; ?>"><span class="tip">Edit</span></a>
<a class="tool icon view" href="index.php?page=View&file=Scan_<?php echo $FILE; ?>"><span class="tip">View</span></a>
<a class="tool icon upload" href="#" onclick="return upload('Scan_<?php echo $FILE; ?>');"><span class="tip">Upload to Imgur</span></a>
<a href="#" onclick="return emailManager('Scan_<?php echo $FILE; ?>');" class="tool icon email"><span class="tip">Email</span></a>
<br/>
<a class="tool" target="_blank" href="scans/Scan_<?php echo $FILE; ?>" style="width:100%;"><img src="scans/<?php echo html($IMAGE); ?>" style="width:100%"/><span class="tip">View raw file</span></a>
</p>
</div>
