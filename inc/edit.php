<!-- Select Region Holder -->
<div id="select"></div>

<div id="sidebar">
<form name="scanning" action="index.php" onsubmit="return pre_scan(this,ias);" method="POST">
<input type="hidden" name="page" value="Edit">
<input type="hidden" name="edit" value="1">
<input type="hidden" name="file" value="<?php echo html($file); ?>">
<div class="side_box">
<h2>Output Options</h2>

<div class="label">
<span class="tool">File Type<span class="tip">Format</span></span>:
</div>
<div class="control">
<select name="filetype" onchange="fileChange(this.value)">
<option value="png">Portable Network Graphic: *.png</option>
<option value="jpg">Joint Photography Group: *.jpg</option>
<option value="tiff">Tagged Image File Format: *.tiff</option>
<option value="txt">Text File: *.txt</option>
</select>
<script type="text/javascript">document.scanning.filetype.value='<?php echo substr($file,strrpos($file,'.')+1); ?>';</script>
</div>

<div style="display:none" id="lang">
<div class="label">
<span class="tool">Language<span class="tip">Relating to the document</span></span>:
</div>
<div class="control">
<select name="lang">
<?php include('inc/langs.php'); ?>
</select>
</div>
</div>

<div class="label">
<span class="tool">Brightness<span class="tip">Lighting</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include "inc/writescripts/bright.js"; ?></script>
</div>

<div class="label">
<span class="tool">Contrast<span class="tip">Vividness</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include "inc/writescripts/contrast.js"; ?></script>
</div>

<div class="label">
<span class="tool">Mode<span class="tip">Color Type</span></span>:
</div>
<div class="control">
<select name="mode" class="title">
<option value="color">Color</option>
<option value="gray">Grayscale</option>
<option value="lineart">Line Art</option>
</select>
</div>

<div class="label">
<span class="tool">Rotate<span class="tip">Turn</span></span>:
</div>
<div class="control tool">
<script type="text/JavaScript"><?php include "inc/writescripts/rotate.js"; ?></script>
</div>

<div class="label">
<span class="tool">Scale<span class="tip">Size/Dimensions</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include "inc/writescripts/scale.js"; ?></script>
</div>

</div>

<div class="side_box">
<h2>
Crop Image
</h2>
<p>
<input type="hidden" name="loc_maxW"/><input type="hidden" name="loc_maxH"/>
<small>Hint: +/- can increase/decrease numbers.</small></p>
<div class="label">Width: </div>
	<div class="control"><input onkeypress="return false" type="text" readonly="readonly" name="loc_width" value="0" size="3"/> pixle(s)</div>
<div class="label">Height: </div>
	<div class="control"><input onkeypress="return false" type="text" readonly="readonly" name="loc_height" value="0" size="3"/> pixle(s)</div>
<div class="label">X<sub>1</sub> (Left): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_x1" value="0" size="3"/> pixle(s)</div>
<div class="label">Y<sub>1</sub> (Top): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_y1" value="0" size="3"/> pixle(s)</div>
<div class="label">X<sub>2</sub> (Right): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_x2" value="0" size="3"/> pixle(s)</div>
<div class="label">Y<sub>2</sub> (Bottom): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_y2" value="0" size="3"/> pixle(s)</div>
<p class="center i"><input type="button" value="Update" onclick="setRegion(ias);"/> <input type="button" onclick="clearRegion(ias,true)" value="Clear"/><br/>
<small>Crop is applied before rotate</small></p>
</div>

<div class="side_box">
<h2>
Save Image
</h2>
<p class="center"><input type="submit" value="Save Changes" name="action"/> <input type="reset" value="Reset Options" onclick="clearRegion(ias,false)"/></p>
</div>
</form>
<?php echo $FILETYPE=='txt'?'<script type="text/javascript">document.scanning.action.disabled=true;</script>':''; ?>
</div>

<!-- Preview Pane -->
<div id="preview">
<div id="preview_links">
<h2><?php echo html($file); ?></h2>
<p>
<a class="tool icon download" href="download.php?file=Scan_<?php echo url($file); ?>"><span class="tip">Download</span></a>
<a class="tool icon zip" href="download.php?file=Scan_<?php echo url($file); ?>&amp;compress"><span class="tip">Download Zip</span></a>
<a class="tool icon pdf" href="#" onclick="PDF_popup('<?php echo html(js($file)); ?>');"><span class="tip">Download PDF</span></a>
<a class="tool icon print" href="print.php?file=Scan_<?php echo url($file); ?>" target="_blank"><span class="tip">Print</span></a>
<a class="tool icon del" href="index.php?page=Scans&amp;delete=Remove&amp;file=<?php echo url($file); ?>" onclick="return confirm('Delete this scan, This is NOT a do not save button')"><span class="tip">Delete</span></a>
<?php
if(substr($file,-3)=='txt')
	echo '<a class="tool icon edit" href="index.php?page=Edit&amp;file='.url($file).'"><span class="tip">Edit</span></a> ';
else
	echo '<span class="tool icon edit-off"><span class="tip">Edit (Disabled)</span></span> ';
?>
<a class="tool icon view" href="index.php?page=View&amp;file=Scan_<?php echo url($file); ?>"><span class="tip">View</span></a>
<a class="tool icon upload" href="#" onclick="return upload('Scan_<?php echo html(js($file,5)); ?>')"><span class="tip">Upload to Imgur</span></a>
<a href="#" onclick="return emailManager('Scan_<?php echo html(js($file)); ?>');" class="tool icon email"><span class="tip">Email</span></a>
</p></div><!-- There are no line breaks on the next line to make the javascript ever so slightly faster -->
<div id="preview_img"><p><img alt="Preview" src="scans/Preview_<?php echo url(substr($file,0,strrpos($file,'.'))); ?>.jpg" title="Preview"/><img style="z-index:-1;" src="inc/images/blank.gif" title="Processing" alt=""/></p></div>
</div>
