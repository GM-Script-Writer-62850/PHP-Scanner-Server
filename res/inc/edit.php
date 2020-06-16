<!-- Select Region Holder -->
<div id="select"></div>

<div id="sidebar">
<form name="scanning" action="index.php" onsubmit="return pre_scan(this);" method="POST">
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
<?php include('res/inc/langs.php'); ?>
</select>
</div>
</div>

<div class="label">
<span class="tool">Brightness<span class="tip">Lighting</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include "res/writeScripts/bright.js"; ?></script>
</div>

<div class="label">
<span class="tool">Contrast<span class="tip">Vividness</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include "res/writeScripts/contrast.js"; ?></script>
</div>

<div class="label">
<span class="tool">Mode<span class="tip">Color Type</span></span>:
</div>
<div class="control">
<select name="mode" class="title">
<option value="color">Color</option>
<option value="gray">Grayscale</option>
<option value="lineart">Line Art</option>
<option value="negate">Negative</option>
</select>
</div>

<div class="label">
<span class="tool">Rotate<span class="tip">Turn</span></span>:
</div>
<div class="control tool">
<script type="text/JavaScript"><?php include "res/writeScripts/rotate.js"; ?></script>
</div>

<div class="label">
<span class="tool">Scale<span class="tip">Size/Dimensions</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include "res/writeScripts/scale.js"; ?></script>
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
<div class="label">X<sub>1</sub> (Left):</div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" onchange="this.value=Number(this.value)||0;" type="text" name="loc_x1" value="0" size="3"/> pixle(s)</div>
<div class="label">Y<sub>1</sub> (Top):</div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" onchange="this.value=Number(this.value)||0;" type="text" name="loc_y1" value="0" size="3"/> pixle(s)</div>
<div class="label">X<sub>2</sub> (Right):</div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" onchange="this.value=Number(this.value)||0;" type="text" name="loc_x2" value="0" size="3"/> pixle(s)</div>
<div class="label">Y<sub>2</sub> (Bottom):</div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" onchange="this.value=Number(this.value)||0;" type="text" name="loc_y2" value="0" size="3"/> pixle(s)</div>
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
</div>

<!-- Preview Pane -->
<div id="preview" class="box-wide">
<div id="preview_links">
<h2><?php echo html($file); ?></h2>
<p>
<?php echo genIconLinks((object)array('edit'=>0),"Scan_$file",false); ?>
</p></div><!-- There are no line breaks on the next line to make the JavaScript ever so slightly faster -->
<div id="preview_img"><p><img alt="Preview" src="scans/thumb/Preview_<?php echo url(substr($file,0,strrpos($file,'.'))); ?>.jpg" title="Preview"/><img style="z-index:-1;" src="res/images/blank.gif" title="Processing" alt=""/></p></div>
</div>
