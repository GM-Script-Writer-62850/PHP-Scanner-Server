<!-- Select Region Holder -->
<div id="select"></div>

<div id="sidebar">
<form name="scanning" action="index.php" onsubmit="return pre_scan(this,ias,'Scanning');" method="POST">
<input type="hidden" name="page" value="Scan">

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
		}// ,&quot;ADF&quot;:'.($CANNERS[$i]->{"ADF"}?"true":"false").'
		echo '<option class="{&quot;DPI&quot;:&quot;'.$CANNERS[$i]->{"DPI"}.'&quot;,&quot;WIDTH&quot;:'.$CANNERS[$i]->{"WIDTH"}.',&quot;HEIGHT&quot;:'.$CANNERS[$i]->{"HEIGHT"}.',&quot;MODE&quot;:&quot;'.$CANNERS[$i]->{"MODE"}.'&quot;,&quot;SOURCE&quot;:&quot;'.$CANNERS[$i]->{"SOURCE"}.'&quot;}"'.($CANNERS[$i]->{"INUSE"}==1?' disabled="disabled"':'').(isset($CANNERS[$i]->{"SELECTED"})&&$CANNERS[$i]->{"INUSE"}!=1?' selected="selected"':'').' value="'.$CANNERS[$i]->{"ID"}.'">'.$CANNERS[$i]->{"NAME"}.' on '.$loc.'</option>';
	}
?></select></p></div><!-- AJAX in scanner data -->
<script type="text/javascript">scanners=JSON.parse('<?php echo json_encode($CANNERS); ?>');setTimeout("checkScanners()",5000);</script>

</div>

<div class="side_box" id="opt">
<h2>Scanning Options</h2>

<div class="label">
Quality:
</div>
<div class="control" title="Dots Per Inch">
<div class="ie_276228"><select name="quality" class="title"><script type="text/JavaScript">
var dpi='<?php echo $CANNERS[$SEL]->{"DPI"}; ?>'.split('|');
for(var i=0,max=dpi.length;i<max;i++){
	document.write('<option value="'+dpi[i]+'">'+dpi[i]+' '+(isNaN(dpi[i])?'':'dpi')+'</option>');
}
</script>
</select></div>
</div>

<div class="label">
Size:
</div>
<div class="control">
<div class="ie_276228"><select <?php //echo ((($WIDTH=="0"||$WIDTH==NULL)&&($HEIGHT=="0"||$HEIGHT==NULL))===false?'disabled="disabled" ':''); ?>name="size" onchange="paperChange(this);">
<option value="full">Full Scan</option><?php
if(file_exists("config/paper.json")){
	$paper=json_decode(file_get_contents("config/paper.json"));
	foreach($paper as $key=>$val){
		if($CANNERS[$SEL]->{"WIDTH"}>=$val->{"width"}&&$CANNERS[$SEL]->{"HEIGHT"}>=$val->{"height"})
			echo '<option value="'.$val->{"width"}.'-'.$val->{"height"}.'">'.$key.': '.round($val->{"width"}/25.4,2).'x'.round($val->{"height"}/25.4,2).'</option>';
	}
}
else{
	echo '<option value="101.6-152.4">Picture: 4x6</option><option value="215.9-279.4">Paper: 8.5x11</option>';
}
?>
</select></div>
<script type="text/javascript">paper=<?php echo json_encode($paper);?></script>
</div>

<?php
	/*if((($WIDTH=="0"||$WIDTH==NULL)&&($HEIGHT=="0"||$HEIGHT==NULL))===false){
		echo '<div class="label">'.
		'Last Cords:'.
		'</div>'.
		'<div class="control">'.
		'<input type="checkbox" checked="checked" value="'.htmlspecialchars("{\"width\":$M_WIDTH,\"height\":$M_HEIGHT,\"x1\":$X_1,\"y1\":$Y_1}").'" onchange="lastCordsChange(this.value,this.checked)">'.
		'</div>';
	}*/
?>

<div class="label">
Orentation:
</div>
<div class="control">
<select name="ornt" disabled="disabled">
<option value="vert">Vertical</option>
<option value="horz">Horizontal</option>
</select>
</div>

<div class="label">
Mode:
</div>
<div class="control">
<div class="ie_276228"><select name="mode" class="title">
<script type="text/JavaScript">
var modes='<?php echo $CANNERS[$SEL]->{"MODE"}; ?>'.split('|');
for(var i=modes.length-1;i>-1;i--){
	var text;
	switch(modes[i]){
		case 'Gray':
	  		text='Grayscale';break;
		case 'Lineart':
	  		text='Line Art';break;
		default:
			text=modes[i];
	}
	document.write('<option value="'+modes[i]+'">'+text+'</option>');
}
</script>
</select></div>
</div>

<div id="source">
<div class="label">
Source:
</div>
<div class="control">
<div class="ie_276228"><select name="source" class="title">
<script type="text/JavaScript">
var sources='<?php echo $CANNERS[$SEL]->{"SOURCE"}; ?>'.split('|');
for(var i=0,s=sources.length;i<s;i++){
	document.write('<option value="'+sources[i]+'">'+(sources[i]=='ADF'?'Automatic Document Feeder':sources[i])+'</option>');
}
</script>
</script>
</select></div>
</div>
</div>

</div>

<div class="side_box">
<h2>Output Options</h2>

<div class="label">
Brightness:
</div>
<div class="control">
<select name="bright">
<script type="text/JavaScript">
for(var i=-100;i<=100;i+=10){
	document.write('<option '+(i==0?'selected="selected" ':'')+'value="'+i+'">'+i+' %</option>');
}
</script>
</select>
</div>

<div class="label">
Contrast:
</div>
<div class="control">
<select name="contrast">
<script type="text/JavaScript">
for(var i=-100;i<=100;i+=10){
	document.write('<option '+(i==0?'selected="selected" ':'')+'value="'+i+'">'+i+' %</option>');
}
</script>
</select>
</div>

<div class="label">
Rotate:
</div>
<div class="control">
<select name="rotate">
<option value="0">0&deg;</option>
<option value="90">90&deg;</option>
<option value="180">180&deg;</option>
<option value="270">270&deg;</option>
<optgroup label="Non-Square">
<script type="text/JavaScript">
for(var i=5;i<360;i+=5){
	if(i!=90&&i!=180&&i!=270)
		document.write('<option value="'+i+'">'+i+'&deg;</option>');
}
</script>
</optgroup>
</select>
</div>

<div class="label">
Scale:
</div>
<div class="control">
<select name="scale">
<script type="text/JavaScript">
for(var i=0;i<=200;i+=10){
	document.write('<option value="'+i+'"'+(i==100?' selected="selected"':'')+'>'+(i-100)+' %</option>');
}
</script>
</select>
</div>

<div class="label">
File Type:
</div>
<div class="control">
<select name="filetype" onchange="fileChange(this.value)">
<option value="png">*.png</option>
<option value="jpg">*.jpg</option>
<option value="tiff">*.tiff</option>
<option value="txt">*.txt</option>
</select>
</div>

<div style="display:none" id="lang">
<div class="label">
Language:
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
<p>
<input type="hidden" name="loc_maxW"/><input type="hidden" name="loc_maxH"/>
<small>Hint: +/- can increase/decrease numbers.</small>
<div class="label">Width: </div> 
	<div class="control"><input onkeypress="return false" type="text" readonly="readonly" name="loc_width" value="0<?php //echo (isset($M_WIDTH)?$M_WIDTH:0); ?>" size="3"/> pixle(s)</div>
<div class="label">Height: </div>
	<div class="control"><input onkeypress="return false" type="text" readonly="readonly" name="loc_height" value="0<?php //echo (isset($M_HEIGHT)?$M_HEIGHT:0); ?>" size="3"/> pixle(s)</div>
<div class="label">x1 (Left): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_x1" value="0<?php //echo (isset($X_1)?$X_1:0); ?>" size="3"/> pixle(s)</div>
<div class="label">y1 (Top): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_y1" value="0<?php //echo (isset($Y_1)?$Y_1:0); ?>" size="3"/> pixle(s)</div>
<div class="label">x2 (Right): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_x2" value="0" size="3"/> pixle(s)</div>
<div class="label">y2 (Bottom): </div>
	<div class="control"><input onkeypress="return validateKey(this,event,ias);" type="text" name="loc_y2" value="0" size="3"/> pixle(s)</div>
<div align="center"><input type="button" value="Update" onclick="setRegion(ias);"/><input type="button" onclick="clearRegion(ias,true)" value="Clear"/></div>
</p>
</div>

<div class="side_box">
<h2>
Scan Image
</h2>
<p align="center"><input type="submit" value="Scan Image" name="action"><input name="reset" type="reset" value="Reset Options" onclick="clearRegion(ias,false);setTimeout(scanReset,1);"/></p>
</div>

<!-- Save Settings -->
<div class="side_box">
<h2>Settings</h2>
<p>
<input name="set_save" size="15" onkeypress="if(event.which==13||event.keyCode==13){this.nextSibling.click();return false;}"><input onclick="if(this.previousSibling.value==''){return false;}else{document.scanning.removeAttribute('onsubmit');}" type="submit" name="saveas" value="Save">
</p>
<p align="center">
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
		echo "<a class=\"tool icon recent\" onclick=\"lastScan('".html($_COOKIE["scan"])."','".html($_COOKIE["preview"])."','".html($_COOKIE["scanner"])."',this,".(file_exists('config/IMGUR_API_KEY.txt')?'true':'false').");\" href=\"javascript:void(null)\"><span class=\"tip\">Last Scan</span></a>";
	else 
		echo $ls;
}
else
	echo $ls;
?>
</p></div><!-- there are no line breaks on the next line to make the javascript ever so slightly faster -->
<div id="preview_img"><p><img src="inc/images/blank.gif" title="Preview"/><img src="inc/images/blank.gif" title="Scanning" style="z-index:-1;"/></p></div>
</div>
