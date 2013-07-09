<div class="box" id="<?php
echo html($FILE);
?>">
<h2 ondblclick="toggleFile(this);" selected="false"><?php echo html($FILE); ?></h2>
<p>
<a class="tool icon download" href="download.php?file=Scan_<?php echo url($FILE); ?>"><span class="tip">Download</span></a>
<a class="tool icon zip" href="download.php?file=Scan_<?php echo url($FILE); ?>&compress"><span class="tip">Download Zip</span></a>
<a class="tool icon pdf" href="#" onclick="return PDF_popup('<?php echo html(js($FILE)); ?>');"><span class="tip">Download PDF</span></a>
<a class="tool icon print" href="print.php?file=Scan_<?php echo url($FILE); ?>" target="_blank"><span class="tip">Print</span></a>
<a class="tool icon del" href="index.php?page=Scans&delete=Remove&file=<?php echo url($FILE); ?>" onclick="return delScan('<?php echo html(js($FILE)); ?>',true)"><span class="tip">Delete</span></a>
<a class="tool icon edit" href="index.php?page=Edit&file=<?php echo url($FILE); ?>"><span class="tip">Edit</span></a>
<a class="tool icon view" href="index.php?page=View&file=Scan_<?php echo url($FILE); ?>"><span class="tip">View</span></a>
<a class="tool icon upload" href="#" onclick="return upload('Scan_<?php echo html(js($FILE)); ?>');"><span class="tip">Upload to Imgur</span></a>
<a href="#" onclick="return emailManager('Scan_<?php echo html(js($FILE)); ?>');" class="tool icon email"><span class="tip">Email</span></a>
<br/>
<a class="tool" target="_blank" href="scans/Scan_<?php echo url($FILE); ?>" style="width:100%;"><img src="scans/<?php echo url($IMAGE); ?>" style="width:100%"/><span class="tip">View raw file</span></a>
</p>
</div>
