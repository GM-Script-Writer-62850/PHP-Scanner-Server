<!-- Select Region Holder -->
<div id="select"></div>

<div id="sidebar">
<form name="scanning" action="index.php" onsubmit="return pre_scan(this,ias);" method="POST">

<div class="side_box">
<h2>Scanners</h2>
<div class="ie_276228"><p><select name="scanner" style="width:238px;" onchange="scannerChange(this)"><?php
	$SEL=0;
	for($i=0,$max=count($CANNERS);$i<$max;$i++){
		if(isset($CANNERS[$i]->{"SELECTED"}))
			$SEL=$i;
		if(substr($CANNERS[$i]->{"DEVICE"},0,4)=="net:"){
			$loc=explode(":",$CANNERS[$i]->{"DEVICE"});
			$loc=$loc[1];// The following is disabled for AJAX processing
		/*	if($loc=="127.0.0.1"||$loc==$_SERVER['SERVER_ADDR']||$loc=="localhost") //will need rewrite for ipv6
				continue;//try to filter reduntant scanners (network scanner on the localhost)*/
		}
		else{
			//echo '<!-- '.$_SERVER['SERVER_NAME'].' -->';
			$loc=$_SERVER['SERVER_NAME'];
		}
		$CANNER=clone $CANNERS[$i];
		unset($CANNER->{"INUSE"});
		unset($CANNER->{"ID"});
		unset($CANNER->{"DEVICE"});
		unset($CANNER->{"NAME"});
		unset($CANNER->{"UUID"});
		echo '<option class="'.html(json_encode($CANNER)).'"'.($CANNERS[$i]->{"INUSE"}==1?' disabled="disabled"':'').(isset($CANNERS[$i]->{"SELECTED"})&&$CANNERS[$i]->{"INUSE"}!=1?' selected="selected"':'').' value="'.$CANNERS[$i]->{"ID"}.'">'.$CANNERS[$i]->{"NAME"}.' on '.$loc.'</option>';
	}
	$defSource=explode('|',$CANNERS[$SEL]->{"SOURCE"})[0];
?></select></p></div><!-- AJAX in scanner data -->
<script type="text/javascript">scanners=JSON.parse('<?php echo json_encode($CANNERS); ?>');setTimeout("checkScanners()",5000);</script>

</div>

<div class="side_box" id="opt">
<h2>Scanning Options</h2>

<div id="source">
<div class="label">
<span class="tool">Source<span class="tip">Scan source (such as a document-feeder)</span></span>:
</div>
<div class="control">
<div class="ie_276228"><select name="source" class="title" onchange="sourceChange(this)"></select></div>
</div>
</div>

<div class="label">
<span class="tool">Quality<span class="tip">Resolution</span></span>:
</div>
<div class="control tool">
<div class="ie_276228"><select name="quality" class="upper"></select></div><span class="tip">Dots Per Inch</span>
</div>

<div class="label">
<span class="tool">Size<span class="tip">How big the paper is</span></span>:
</div>
<div class="control tool">
<div class="ie_276228"><select name="size" onchange="paperChange(this);"></select>
</div><span class="tip"><?php echo $CANNERS[$SEL]->{"WIDTH-$defSource"}.' mm x '.$CANNERS[$SEL]->{"HEIGHT-$defSource"}.' mm'; ?></span>
<script type="text/javascript">paper=<?php
echo file_exists("config/paper.json")?file_get_contents("config/paper.json"):'{"Picture":{"height":152.4,"width":101.6},"Paper":{"height":279.4,"width":215.9}}'; ?></script>
</div>

<?php
	/*if((($WIDTH=="0"||$WIDTH==NULL)&&($HEIGHT=="0"||$HEIGHT==NULL))===false){
		echo '<div class="label">'.
		'Last Cords:'.
		'</div>'.
		'<div class="control">'.
		'<input type="checkbox" checked="checked" value="'.html("{\"width\":$M_WIDTH,\"height\":$M_HEIGHT,\"x1\":$X_1,\"y1\":$Y_1}").'" onchange="lastCordsChange(this.value,this.checked)">'.
		'</div>';
	}*/
?>

<div class="label">
<span class="tool">Orientation<span class="tip">Layout</span></span>:
</div>
<div class="control">
<select name="ornt" disabled="disabled">
<option value="vert">Portrait</option>
<option value="horz">Landscape</option>
</select>
</div>

<div class="label">
<span class="tool">Mode<span class="tip">Color Type</span></span>:
</div>
<div class="control">
<div class="ie_276228"><select name="mode" class="title"></select></div>
</div>

<div id="duplex"<?php echo $CANNERS[$SEL]->{"DUPLEX-$defSource"}?'':' style="display:none;"'; ?>>
<div class="label tool">
<span>Duplex<span class="tip">Double Sided Scan</span></span>:
</div>
<div class="control">
<div class="ie_276228"><select name="duplex" class="title">
<option value="false">No</option>
<option value="true">yes</option>
</select></div>
</div>
</div>

</div>

<div class="side_box">
<h2>Output Options</h2>

<div class="label">
<span class="tool">Brightness<span class="tip">Lighting</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include('./inc/writescripts/bright.js'); ?></script>
</div>

<div class="label">
<span class="tool">Contrast<span class="tip">Vividness</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php include('./inc/writescripts/contrast.js'); ?></script>
</div>

<div class="label">
<span class="tool">Rotate<span class="tip">Turn</span></span>:
</div>
<div class="control tool"><script type="text/javascript"><?php include('./inc/writescripts/rotate.js'); ?></script>
</div>

<div class="label">
<span class="tool">Scale<span class="tip">Size/Dimensions</span></span>:
</div>
<div class="control">
<script type="text/JavaScript"><?php echo include('./inc/writescripts/scale.js'); ?></script>
</div>

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

<p><small>Do not rotate unless this is the final scan.</small></p>
</div>

<div class="side_box" id="sel">
<h2>
Select Region
</h2>
<p><input type="hidden" name="loc_maxW"/><input type="hidden" name="loc_maxH"/>
<small>Hint: +/- can increase/decrease numbers.</small></p>
<div class="label">Width: </div>
	<div class="control"><input onkeypress="return false" type="text" readonly="readonly" name="loc_width" value="0<?php //echo (isset($M_WIDTH)?$M_WIDTH:0); ?>" size="3"/> pixle(s)</div>
<div class="label">Height: </div>
	<div class="control"><input onkeypress="return false" type="text" readonly="readonly" name="loc_height" value="0<?php //echo (isset($M_HEIGHT)?$M_HEIGHT:0); ?>" size="3"/> pixle(s)</div>
<div class="label">X<sub>1</sub> (Left): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_x1" value="0<?php //echo (isset($X_1)?$X_1:0); ?>" size="3"/> pixle(s)</div>
<div class="label">Y<sub>1</sub> (Top): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_y1" value="0<?php //echo (isset($Y_1)?$Y_1:0); ?>" size="3"/> pixle(s)</div>
<div class="label">X<sub>2</sub> (Right): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_x2" value="0" size="3"/> pixle(s)</div>
<div class="label">Y<sub>2</sub> (Bottom): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_y2" value="0" size="3"/> pixle(s)</div>
<p class="center"><input type="button" value="Update" onclick="setRegion(ias);"/> <input type="button" onclick="clearRegion(ias,true)" value="Clear"/></p>
</div>

<div class="side_box">
<h2>
Scan Image
</h2>
<p class="center"><input type="hidden" name="page" value="Scan"/>
<input type="submit" value="Scan Image" name="action"> <input name="reset" type="reset" value="Reset Options" onclick="clearRegion(ias,false);setTimeout(scanReset,1);"/></p>
</div>

<!-- Save Settings -->
<div class="side_box">
<h2>Settings</h2>
<p>
<input name="set_save" type="text" size="15" onkeypress="if(event.which==13||event.keyCode==13){this.nextSibling.click();return false;}"/><input onclick="if(this.previousSibling.value==''){return false;}else{document.scanning.removeAttribute('onsubmit');}" type="submit" name="saveas" value="Save" style="margin-left:5px;"/>
</p>
<p class="center">
<?php
	$set=json_decode($file);
	$i=0;
	$max=0;
	foreach($set as $id){
		$max++;
	}
	foreach($set as $id => $opt){
		echo '<a href="javascript:void(0);" onclick="config({';
		$str='';
		foreach($opt as $key => $val){
			$str.="'$key':".(is_numeric($val)?$val:"'$val'").",";
		}
		echo substr($str,0,-1);
		echo '});">'.$id.'</a>';
		$i++;
		if($i<$max)
			echo " | ";
	}
?>
</p>
</div>
</form>

</div>

<!-- Preview Pane -->
<div id="preview">
<div id="preview_links">
<h2>Preview Pane</h2>
<p>
<span class="tool icon download-off"><span class="tip">Download (Disabled)</span></span>
<span class="tool icon zip-off"><span class="tip">Download Zip (Disabled)</span></span>
<span class="tool icon pdf-off"><span class="tip">Download PDF (Disabled)</span></span>
<span class="tool icon print-off"><span class="tip">Print (Disabled)</span></span>
<span class="tool icon del-off"><span class="tip">Delete (Disabled)</span></span>
<span class="tool icon edit-off"><span class="tip">Edit (Disabled)</span></span>
<span class="tool icon view-off"><span class="tip">View (Disabled)</span></span>
<span class="tool icon upload-off"><span class="tip">Upload to Imgur (Disabled)</span></span>
<span class="tool icon email-off"><span class="tip">Email (Disabled)</span></span>
<?php
$ls='<span class="tool icon recent-off"><span class="tip">Last Scan (Disabled)</span></span>';
if(isset($_COOKIE["scan"])&&isset($_COOKIE["preview"])){
	if(file_exists("scans/".$_COOKIE["scan"])&&file_exists("scans/".$_COOKIE["preview"]))
		echo "<a class=\"tool icon recent\" onclick=\"lastScan('".html(js($_COOKIE["scan"]))."','".html(js($_COOKIE["preview"]))."','".html(js($_COOKIE["scanner"]))."',this');\" href=\"javascript:void(null)\"><span class=\"tip\">Last Scan</span></a>";
	else
		echo $ls;
}
else
	echo $ls;
?>
</p></div><!-- there are no line breaks on the next line to make the javascript ever so slightly faster -->
<div id="preview_img"><p><img src="inc/images/blank.gif" title="Preview" alt="Preview"/><img alt="" src="inc/images/blank.gif" title="Scanning" style="z-index:-1;"/></p></div>
</div>
