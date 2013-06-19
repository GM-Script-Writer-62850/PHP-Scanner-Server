
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
<br/><a href="index.php?action=Parallel-Form">Parallel Scanner Configuration</a>
<br/><a href="index.php?page=Device%20Notes">Scanner List</a> | <a href="index.php?page=Access%20Enabler" title="For stubborn scanners">Access Enabler</a>
</p>
</form>
</div>

<div class="box">
<h2>Scanned Files on Server</h2>
<p>
<a href="index.php?page=Scans&delete=Remove">Remove All Scans</a>
</p>
</div>

<div class="box">
<h2>Email Configuration</h2>
<p><input type="submit" value="Edit" onclick="emailManager(null);"/>
<input type="submit" value="Delete" onclick="deleteEmail();" style="float:right;"/></p>
</div>

</div>

<div class="column">
<div class="box">
<h2>Server Scanner Settings</h2>
<p>
<a href="index.php?page=Config&action=Delete-Setting">Remove All Of These Settings</a></p><ul class="simplelist">
<?php
	foreach($file as $id => $opt){
		echo '<li class="boxlist"><a class="tool icon del" href="index.php?page=Config&action=Delete-Setting&value='.html($id).'"><span class="tip">Delete</span></a> <a target="_blank" href="index.php?page=Scan&action=restore&';
		foreach($opt as $key => $val){
			echo html($key).'='.html($val).'&';
		}
		echo '">'.html($id).'</a></li>';
	}
?></ul><p>
If you want to save a setting for your own use, right-click it and save it to your web browser bookmarks.
</p>
</div>

<div class="box">
<h2>Paper Configuration</h2>
<p>
<a href="index.php?page=Config&action=Detect-Paper">Detect paper</a><br/>
<a href="index.php?page=Paper%20Manager">Paper size manager</a><br/>
<?php
if(file_exists("config/paper.json"))
	echo '<a href="index.php?page=Config&action=Delete-Paper">Delete paper settings</a>';
else
	echo 'Paper sizes need to be detected.';
?></p>
</div>

</div>
<div class="column">
<div class="box">
<h2>Color Sceme</h2>
<p>
Select a Color:
<select onchange="if(this.value!='void')changeColor(this.value);">
<option value="void">Please Select</option>
<option value="3c9642x3c7796" style="background-color:#3c9642;color:#3c7796;">Green</option>
<option value="3c7796x963c8f" style="background-color:#3c7796;color:#963c8f;">Blue</option>
<option value="963c8fx3c7796" style="background-color:#963c8f;color:#3c7796;">Purple</option>
<option value="663366x3c7796" style="background-color:#663366;color:#3c7796;">Dark Purple</option>
<option value="000000x999999" style="background-color:#000000;color:#999999;">Black</option>
<option value="996633xbfbfbf" style="background-color:#996633;color:#bfbfbf;">Light Brown</option>
<option value="848484xbfbfbf" style="background-color:#848484;color:#bfbfbf;">Gray</option>
<option value="383838x838383" style="background-color:#383838;color:#838383;">Dark Gray</option>
<option value="ff007exbb045e" style="background-color:#ff007e;color:#bb045e;">Pink</option>
</select>
</p>
<!-- Manual color picker (for theme development)-->
<!--<input value="000000xFFFFFF" onkeypress="if(event.which==13){changeColor(this.value);return false;}"/>-->
</div>

<div class="box">
<h2>Debug Console</h2>
<p><?php
if(isset($Fortune)){
	echo '<a href="javascript:void(\'toggleFortune\')" onclick="this.textContent=toggleFortune(this.textContent)?\'Hide\':\'Show\';">'.($_COOKIE["fortune"]?'Hide':'Show').'</a> fortunes. (Refresh to apply)<br/>';
}
?>
<a href="javascript:void('toggleDebug')" id="debug-link" onclick="this.textContent=toggleDebug(false)?'Hide':'Show';"><?php
if(isset($_COOKIE["debug"])){
	if($_COOKIE["debug"]=='true'){
		echo 'Hide';
	}
	else{
		echo 'Show';
	}
}
else{
	echo 'Show';
}
?></a> the debug log. You can toggle the Debug Log at any time by pressing this:<br/><code>[Ctrl]+[Shift]+[D]</code>
</p>
</div>

<div class="box">
<h2>Imgur Configuration</h2>
<form name="imgur" method="POST" action="index.php?page=Config">
<p>
To upload scans directly to <a target="_blank" href="http://imgur.com">imgur.com</a> you will need a key, you can get one on <a target="_blank" href="http://imgur.com/register/api_anon">this</a> page.<br/>
<input type="hidden" name="action" value="Imgur-Key-Save">
Key: <input type="text" name="key" onfocus="if(this.value=='IMGUR_API_KEY') this.value=''" onblur="if(this.value=='') this.value='IMGUR_API_KEY'" value="<?php echo (file_exists('config/IMGUR_API_KEY.txt')?html(file_get_contents('config/IMGUR_API_KEY.txt')):'IMGUR_API_KEY');?>"/><br/>
<input type="submit" value="Save Key" onclick="if(document.imgur.key.value=='IMGUR_API_KEY'||document.imgur.key.value==''){printMsg('Invalid Key','Please provide a valid key<br>If you are confused you get the key at <a href=&quot;http://imgur.com/register/api_anon&quot; target=&quot;_blank&quot;>imgur.com/register/api_anon</a>','center',0); return false;}else if(confirm('Save new key ('+document.imgur.key.value+')\nThis will overwrite any existing key!')===false){return false;}"/>
<input type="submit" value="Delete Key" onclick="if(confirm('Delete your Imgur API key')){document.imgur.action.value='Imgur-Key-Delete';return true}return false;"/>
</p>
</form>
</div>

</div>
