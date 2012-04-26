
<!-- Content -->

<div class="box">
<h2>Parallel Port Scanner</h2>
<p>
Sorry, <code>scanimage</code> does not detect scanners on parallel ports they must be setup manually.<br/>You must re-scan for scanners after adding with this page.</p>
<form method="POST" action="index.php"><input type="hidden" name="action" value="Parallel-Form"/><p>
Scanner Name: <input type="text" name="name"><br/>
Scanner Device URI: <input type="text" name="device"><br/>
<input type="submit" value="Add Scanner"/></p>
</form>
</div>

<div class="box">
<h2>Parallel Port Scanners</h2>
<ul class="simplelist">
<?php
	for($i=0,$max=count($scan);$i<$max;$i++){
		if($scan[$i]=="."||$scan[$i]=="..")
			continue;
		$json=json_decode(file_get_contents("config/parallel/".$scan[$i]));
		echo '<li class="boxlist"><a class="icon del tool" href="index.php?action=Parallel-Form&file='.$scan[$i].'"><span class="tip">Delete</span></a> <a href="index.php?page=Device%20List&action='.$json->{"DEVICE"}.'">'.$json->{"NAME"}."</a></li>";
	}
?></ul><p><a onclick="printMsg('Searching For Scanners','Please Wait...','center',0);" href="index.php?page=Config&action=Search-For-Scanners"/>Re-scan for scanners</a>
</p>
</div>

<div class="box">
<h2>Mustek Scanners</h2>
<p>
If yours is one this command will be of use finding out it's information<br/>
<code>sudo sane-find-scanner -p</code>
</p>
</div>
