
<!-- Content -->
<div class="column">
<div class="box">
<h2>Scanners</h2>
<form name="scanner" action="index.php">
<input type="hidden" name="page" value="Config">
<p>
If there are no avalible scanners, you can click the button below to search for scanners on the server.
<br/>
If none show up, your scanner may not be supported by Xsane.
<br/>
Make sure all scanners are plugged in and turned on.
<br/><input type="hidden" name="action" value="Search-For-Scanners">
<input type="submit" value="Search For Scanners" onclick="printMsg('Searching For Scanners','Please Wait...','center',0);"/>
<br/><a href="index.php?page=Parallel-Form">Parallel Scanner Configuration</a>
<br/><a href="index.php?page=Device%20Notes">Scanner List</a> | <a href="index.php?page=Access%20Enabler" title="For stubborn scanners">Access Enabler</a>
</p>
</form>
</div>

<div class="box">
<h2>Debug Console</h2>
<p><?php
if(isset($Fortune)){
	echo '<a href="javascript:void(\'toggleFortune\')" onclick="this[TC]=toggleFortune(this[TC])?\'Hide\':\'Show\';">'.($_COOKIE["fortune"]?'Hide':'Show').'</a> fortunes. (Refresh to apply)<br/>';
}
?>
<a href="javascript:void('toggleDebug')" id="debug-link" onclick="this[TC]=toggleDebug(false)?'Hide':'Show';"><?php
if(isset($_COOKIE["debug"]))
	echo $_COOKIE["debug"]=='true'?'Hide':'Show';
else
	echo 'Show';
?></a> the Debug Console. You can toggle the Debug Console at any time by pressing this:<br/><code>[Ctrl]+[Shift]+[D]</code>
</p>
</div>

<div class="box">
<h2>Trouble Shooting</h2>
<p>
If you are having issues loading the Scanned Files page due to over population you can <a href="index.php?page=Scans&amp;delete=Remove" onclick="return confirm('Delete all scanned files?')">
Remove All the Scans</a> with that link or you can use <a href="index.php?page=Scans&amp;filter=1&amp;t=0">this link</a> so you can use the scan filter. If you are having another issue you may want to read the <a href="index.php?page=About">Release Notes</a> or
 take a look at the <a href="index.php?page=PHP%20Information">PHP Configuration</a>.
</p>
</div>

</div>

<div class="column">

<div class="box">
<h2>Paper Configuration</h2>
<p>
<a href="index.php?page=Config&amp;action=Detect-Paper"<?php echo file_exists('config/paper.json')?' onclick="return confirm(\'Replace all known paper sizes\')"':'' ?>>Detect paper</a><br/>
<a href="index.php?page=Paper%20Manager">Paper size manager</a><br/>
<?php
if(file_exists("config/paper.json"))
	echo '<a href="index.php?page=Config&amp;action=Delete-Paper" onclick="return confirm(\'Delete all known paper sizes\')">Delete paper settings</a>';
else
	echo 'Paper sizes need to be detected.';
?></p>
</div>

<div class="box">
<h2>Server Scanner Settings</h2>
<p>
<a href="index.php?page=Config&amp;action=Delete-Setting" onclick="return confirm('Delete all settings?')">Remove All Of These Settings</a></p><ul class="simplelist">
<?php
	foreach($file as $id => $opt){
		echo '<li class="boxlist"><a class="tool icon del" href="index.php?page=Config&amp;action=Delete-Setting&amp;value='.url($id).'" onclick="return confirm(\'Delete this setting: &#92;n'.html(js($id)).'\')"><span class="tip">Delete</span></a> <a target="_blank" href="index.php?page=Scan&amp;action=restore';
		foreach($opt as $key => $val){
			echo '&amp;'.url($key).'='.url($val);
		}
		echo '">'.html($id).'</a></li>';
	}
?></ul><p>
If you want to save a setting for your own use, right-click it and save it to your web browser's bookmarks.
</p>
</div>

</div>
<div class="column">

<div class="box">
<h2>Color Scheme</h2>
<p><span style="margin-right:30px;">Theme Picker:</span><select onchange="if(this.value!='void')changeColor(this.value,true);">
<option value="void">Please Select</option>
<option value="3C9642.3C7796.3C9642.FFFFFF.3C9642.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#3c9642;color:#3c7796;">Green</option>
<option value="3C7796.963C8F.3C7796.FFFFFF.3C7796.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#3c7796;color:#963c8f;">Blue</option>
<option value="963C8f.3C7796.963C8f.FFFFFF.963C8f.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#963c8f;color:#3c7796;">Purple</option>
<option value="663366.3C7796.663366.FFFFFF.663366.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#663366;color:#3c7796;">Dark Purple</option>
<option value="000000.999999.000000.FFFFFF.000000.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#000000;color:#999999;">Black</option>
<option value="996633.BFBFBF.996633.FFFFFF.996633.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#996633;color:#bfbfbf;">Light Brown</option>
<option value="848484.BFBFBF.848484.FFFFFF.848484.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#848484;color:#bfbfbf;">Gray</option>
<option value="383838.838383.383838.FFFFFF.383838.FFFFFF.000000.000000.FFFFFF.FF0000.FFFFFF" style="background-color:#383838;color:#838383;">Dark Gray</option>
<option value="FF007E.BB045E.FF007E.FFFFFF.FF007E.FFFFFF.000000.383838.FFFFFF.FF0000.FFFFFF" style="background-color:#ff007e;color:#bb045e;">Pink</option>
</select></p>
<?php $Theme=explode('.',$Theme);$attrs='class="colorPicker" readonly="readonly" onchange="changeColor(this,false);"'; ?>
<div class="footer"><form name="theme" onsubmit="try{changeColor(null,true);}catch(e){alert(e)}return false" action="#"><p style="line-height:23px;">
<span>Background Color:</span>			<input name="BG_COLOR" style="background-color:#<?php echo $Theme[0]; ?>" value="<?php echo $Theme[0]; ?>" <?php echo $attrs; ?>/>
<span>Page Background:</span>			<input name="PB_COLOR" style="background-color:#<?php echo $Theme[3]; ?>" value="<?php echo $Theme[3]; ?>" <?php echo $attrs; ?>/>
<span>Page Text:</span>					<input name="PT_COLOR" style="background-color:#<?php echo $Theme[6]; ?>" value="<?php echo $Theme[6]; ?>" <?php echo $attrs; ?>/>
<span>Header Background:</span>			<input name="HB_COLOR" style="background-color:#<?php echo $Theme[4]; ?>" value="<?php echo $Theme[4]; ?>" <?php echo $attrs; ?>/>
<span>Header Text:</span>				<input name="HT_COLOR" style="background-color:#<?php echo $Theme[5]; ?>" value="<?php echo $Theme[5]; ?>" <?php echo $attrs; ?>/>
<span>Link Color:</span>				<input name="LC_COLOR" style="background-color:#<?php echo $Theme[2]; ?>" value="<?php echo $Theme[2]; ?>" <?php echo $attrs; ?>/>
<span>Link Color (Mouse Over):</span>	<input name="LK_COLOR" style="background-color:#<?php echo $Theme[1]; ?>" value="<?php echo $Theme[1]; ?>" <?php echo $attrs; ?>/>
<span>Alert Header Background:</span>	<input name="AH_COLOR" style="background-color:#<?php echo $Theme[9]; ?>" value="<?php echo $Theme[9]; ?>" <?php echo $attrs; ?>/>
<span>Alert Header Text:</span>			<input name="AT_COLOR" style="background-color:#<?php echo $Theme[10]; ?>" value="<?php echo $Theme[10]; ?>" <?php echo $attrs; ?>/>
<span>Debug Background:</span>			<input name="BB_COLOR" style="background-color:#<?php echo $Theme[7]; ?>" value="<?php echo $Theme[7]; ?>" <?php echo $attrs; ?>/>
<span>Debug Text:</span>				<input name="BT_COLOR" style="background-color:#<?php echo $Theme[8]; ?>" value="<?php echo $Theme[8]; ?>" <?php echo $attrs; ?>/>
<br/>
<input type="submit" value="Save"/></p></form></div>
<div class="footer"><p><span style="margin-right:30px;">Color Picker Themer:</span>
<select style="width:61px;" onchange="if(this.value==1){document.body.className='darkPicker';Set_Cookie('darkPicker',true,365.25*10,false,null,null);}else{document.body.removeAttribute('class');Delete_Cookie('darkPicker',false);};">
<option value="0">Light</option>
<option value="1"<?php echo isset($_COOKIE['darkPicker'])?' selected="selected"':''; ?>>Dark</option>
</select></p></div>
</div>

<div class="box">
<h2>Email Configuration</h2>
<p><input type="submit" value="Edit" onclick="emailManager(null);"/>
<input type="submit" value="Delete" onclick="deleteEmail();" style="float:right;"/></p>
</div>

<div class="box">
<h2>Update Checker</h2>
<p class="center">
<input type="button" onclick="updateCheck('<?php echo html(js($VER)); ?>',this)" value="Check for Updates"/>
</p>
</div>

</div>
