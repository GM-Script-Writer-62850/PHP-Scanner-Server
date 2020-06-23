
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
	echo '<a href="javascript:void(\'toggleFortune\')" onclick="this.textContent=toggleFortune(this.textContent)?\'Hide\':\'Show\';">'.($_COOKIE["fortune"]?'Hide':'Show').'</a> fortunes. (Refresh to apply)<br/>';
}
?>
<a href="javascript:void('toggleDebug')" id="debug-link" onclick="this.textContent=toggleDebug(false)?'Hide':'Show';"><?php
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
</p><script type="text/javascript">
if(localStorage.getItem('default')!=null)
	document.write('<div class="footer"><p><input type="button" value="Clear Default Settings" onclick="if(!confirm(\'Clear Default Settings\'))return;'+
	'(function(e){localStorage.removeItem(\'default\');printMsg(\'Cleared\',\'Default settings have been deleted\',\'center\',-1);e.parentNode.removeChild(e);})(this.parentNode.parentNode);"/></p></div>');
</script>
</div>

<div class="box"<?php echo $Printer>0?'':' style="display:none;" '; ?>>
	<h2>Printers</h2>
	<form action="index.php" name="printer">
		<input type="hidden" value="Config" name="page">
		<input type="hidden" value="Search-For-Printers" name="action">
		<p>
			The scanner server can also allow you to print to the server's printer without any drivers via the web interface.
			If the printer is configred and advailabe to all users it will be found.<br/>
			<input type="submit" value="Search For Printers"/><br/>
			<a href="index.php?page=Printer&amp;action=List">Printer List</a>
		</p>
	</form>
</div>

</div>
<div class="column">

<div class="box">
<h2>Color Scheme</h2>
<p><span style="margin-right:30px;">Theme Picker:</span><select onchange="if(this.value!='void')changeColor(this.value,true);this.selectedIndex=0;" style="width:102px;">
<option value="void">Please Select</option>
<option value="3C9642.3C7796.3C9642.FFF.3C9642.FFF.000.383838.FFF.F00.FFF" style="background-color:#3c9642;color:#3c9642;">Green</option>
<option value="3C7796.963C8F.3C7796.FFF.3C7796.FFF.000.383838.FFF.F00.FFF" style="background-color:#3c7796;color:#3c7796;">Blue</option>
<option value="963C8f.3C7796.963C8f.FFF.963C8f.FFF.000.383838.FFF.F00.FFF" style="background-color:#963c8f;color:#963c8f;">Purple</option>
<option value="663366.3C7796.636.FFF.636.FFF.000.383838.FFF.F00.FFF" style="background-color:#636;color:#636;">Dark Purple</option>
<option value="000.999.000.FFF.000.FFF.000.383838.FFF.F00.FFF" style="background-color:#000000;color:#000000;">Black</option>
<option value="963.BFBFBF.963.FFF.963.FFF.000.383838.FFF.F00.FFF" style="background-color:#963;color:#963;">Light Brown</option>
<option value="848484.BFBFBF.848484.FFF.848484.FFF.000.383838.FFF.F00.FFF" style="background-color:#848484;color:#848484;">Gray</option>
<option value="383838.838383.383838.FFF.383838.FFF.000.000.FFF.F00.FFF" style="background-color:#383838;color:#383838;">Dark Gray</option>
<option value="FF007E.BB045E.FF007E.FFF.FF007E.FFF.000.383838.FFF.F00.FFF" style="background-color:#ff007e;color:#ff007e;">Pink</option>
<option value="<?php echo $CurrentTheme; ?>">Restore</option>
</select></p>
<?php $CurrentTheme=explode('.',$CurrentTheme);$attrs='class="colorPicker" readonly="readonly" onfocus="if(document.all)this.blur();" onchange="changeColor(this,false);"'; ?>
<div class="footer"><form name="theme" onsubmit="return changeColor(null,true);" action="#"><p style="line-height:23px;">
<span>Background Color:</span>			<input name="BG_COLOR" style="background-color:#<?php echo $CurrentTheme[0]; ?>" value="<?php echo $CurrentTheme[0]; ?>" <?php echo $attrs; ?>/>
<span>Page Background:</span>			<input name="PB_COLOR" style="background-color:#<?php echo $CurrentTheme[3]; ?>" value="<?php echo $CurrentTheme[3]; ?>" <?php echo $attrs; ?>/>
<span>Page Text:</span>					<input name="PT_COLOR" style="background-color:#<?php echo $CurrentTheme[6]; ?>" value="<?php echo $CurrentTheme[6]; ?>" <?php echo $attrs; ?>/>
<span>Header Background:</span>			<input name="HB_COLOR" style="background-color:#<?php echo $CurrentTheme[4]; ?>" value="<?php echo $CurrentTheme[4]; ?>" <?php echo $attrs; ?>/>
<span>Header Text:</span>				<input name="HT_COLOR" style="background-color:#<?php echo $CurrentTheme[5]; ?>" value="<?php echo $CurrentTheme[5]; ?>" <?php echo $attrs; ?>/>
<span>Link Color:</span>				<input name="LC_COLOR" style="background-color:#<?php echo $CurrentTheme[2]; ?>" value="<?php echo $CurrentTheme[2]; ?>" <?php echo $attrs; ?>/>
<span>Link Color (Mouse Over):</span>	<input name="LK_COLOR" style="background-color:#<?php echo $CurrentTheme[1]; ?>" value="<?php echo $CurrentTheme[1]; ?>" <?php echo $attrs; ?>/>
<span>Alert Header Background:</span>	<input name="AH_COLOR" style="background-color:#<?php echo $CurrentTheme[9]; ?>" value="<?php echo $CurrentTheme[9]; ?>" <?php echo $attrs; ?>/>
<span>Alert Header Text:</span>			<input name="AT_COLOR" style="background-color:#<?php echo $CurrentTheme[10]; ?>" value="<?php echo $CurrentTheme[10]; ?>" <?php echo $attrs; ?>/>
<span>Debug Background:</span>			<input name="BB_COLOR" style="background-color:#<?php echo $CurrentTheme[7]; ?>" value="<?php echo $CurrentTheme[7]; ?>" <?php echo $attrs; ?>/>
<span>Debug Text:</span>				<input name="BT_COLOR" style="background-color:#<?php echo $CurrentTheme[8]; ?>" value="<?php echo $CurrentTheme[8]; ?>" <?php echo $attrs; ?>/>
<br/>
<input type="Button" onclick="if(confirm('Are you sure, your current theme will be overwritten'))changeColor('<?php echo $Theme; ?>',true)" value="Restore Default"/> <input type="submit" value="Save"/></p></form></div>
<div class="footer"><p><span style="margin-right:30px;">Color Picker Theme:</span>
<select style="width:61px;" onchange="if(this.value==1)document.body.className='darkPicker';else document.body.removeAttribute('class');Set_Cookie('darkPicker',this.value==1,365.25*10,false,null,null);">
<option value="0">Light</option><option value="1"<?php echo $DarkPicker?' selected="selected"':''; ?>>Dark</option>
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
