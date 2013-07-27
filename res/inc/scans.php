<?php
# Display Thumbnails of scanned images, if any
function ageSeconds2Human($t){
	$t0=floor($t/86400);
	$t1=$t-($t0*86400);
	$t0=number_format($t0);
	$t2=floor($t1/3600);
	$t3=$t1-($t2*3600);
	$t4=floor($t3/60);
	$t5=$t3-($t4*60);
	return "$t0 day(s), $t2 hour(s), $t4 minute(s), and $t5 second(s) ago.";
}
echo '<script type="text/javascript" src="res/writeScripts/imgur-box.js" id="imgur-box-setup"></script>';
if(count(scandir("scans"))==2){
	Print_Message("No Images","All files have been removed. There are no scanned images to display.",'center');
}
else{
	$html='type="text" size="3" value="0" onkeypress="return validateKey(this,event,null);" onchange="this.value=Number(this.value)||0;"';
	$html='<button class="largeButton" type="submit"><span>Filter</span></button>'.
		'<span>Year(s) ago:</span><input name="y" '.$html.'/><br/>'.
		'<span>Day(s) ago:</span><input name="d" '.$html.'/><br/>'.
		'<span>Hour(s) ago:</span><input name="h" '.$html.'/><br/>'.
		'<span>Minute(s) ago:</span><input name="m" '.$html.'/><br/>'.
		'<span>Second(s) ago:</span><input name="s" '.$html.'/>'.
		'<input type="hidden" name="page" value="Scans"/>';
	echo '<div class="box box-full dualForm"><h2>Filter By Age</h2><form id="filter2" action="index.php?filter=2" method="POST"><h3>Display Files Newer Than</h3><p>'.$html.
		'</p></form><form id="filter1" class="m" action="index.php?filter=1" method="POST"><h3>Display Files Older Than</h3><p>'.$html.'</p></form>'.
		'<div class="footer"><p><input type="button" value="Combine Both Filters" onclick="scanFilter(getID(\'filter2\'),getID(\'filter1\'))"/></p></div>'.
		'<div class="footer">Tip: +/- can increase/decrease numbers.</div></div>'.
		'<div class="box box-full"><h2>Bulk Operations</h2><p style="text-align:center;">'.
		genIconLinks((object)array('download'=>0,'edit'=>0),null,true).
		'<br/>Double Click a file name to select/deselect it<br/>'.
		'The order they are selected determines the page order<br/>'.
		'<button onclick="return selectScans(\'excluded\');">Select All</button> '.
		'<button onclick="return selectScans(false);">Invert Selection</button> '.
		'<button onclick="return selectScans(\'included\');">Select None</button>'.
		'</p></div>';
	$FILES=explode("\n",substr(exe("cd 'scans'; ls 'Preview'*",true),0,-1));
	echo '<div id="scans">';
	$filter=Get_Values('filter');
	if(!is_null($filter)){
		$time=time();
		if($filter<3){
			$t=Get_Values('t');
			if(is_null($t))
				$t=Get_Values('y')*31557600+Get_Values('d')*86400+Get_Values('h')*3600+Get_Values('m')*60+Get_Values('s');
			else
				$t=$time-$t;
			$human=ageSeconds2Human($t);
			$time=$time-$t;
			Print_Message('Notice','Only displaying files '.($filter===2?'newer':'older')." than $human<br/>".
				"Here is a fixed <a href=\"index.php?page=$PAGE&amp;filter=$filter&amp;t=$time\">link</a> you can save.",'center');
		}
		else{
			$T2=Get_Values('T2');
			if(is_null($T2)){
				$t2=Get_Values('t2');
				$t1=Get_Values('t1');
			}
			else{
				$t2=$time-$T2;
				$t1=$time-Get_Values('T1');
			}
			$time=array($time-$t1,$time-$t2);
			Print_Message('Notice','Only displaying files newer than '.ageSeconds2Human($t2).'<br/>and older than '.ageSeconds2Human($t1).'<br/>'.
				'Here is a fixed <a href="index.php?page=Scans&amp;filter=3&amp;T2='.$time[1].'&amp;T1='.$time[0].'">link</a> you can save.','center');
		}
	}
	for($i=0,$max=count($FILES);$i<$max;$i++){
		$FILE=$FILES[$i];
		if(isset($time)){
			if($filter===2){
				if(filemtime("scans/$FILE")<$time)
					continue;
			}
			else if($filter===1){
				if(filemtime("scans/$FILE")>$time)
					continue;
			}
			else if($filter===3){
				if(!(filemtime("scans/$FILE")>$time[1]&&filemtime("scans/$FILE")<$time[0]))
					continue;
			}
		}
		$FILE=substr($FILE,7,-3);
		$FILE=substr(exe("cd 'scans'; ls ".shell("Scan$FILE").'*',true),5,-1);//Should only have one file listed
		$IMAGE=$FILES[$i];
		echo '<div class="box" id="'.html($FILE).'">'.
			'<h2 ondblclick="toggleFile(this);" class="excluded">'.html($FILE).'</h2><p><span>'.
			genIconLinks(null,"Scan_$FILE",false).'</span><br/>'.
			'<a class="tool" target="_blank" href="scans/Scan_'.url($FILE).'" style="width:100%;"><img src="scans/'.url($IMAGE).'" alt="'.html($FILE).'" style="width:100%"/><span class="tip">View raw file</span></a>'.
			'</p></div>';
	}
	echo '</div><script type="text/javascript">'.
		'if(typeof document.body.style.MozColumnGap=="string")'.
			'getID("scans").className="columns";'.// At least someone knows how to do something right
		'else '.
			'enableColumns("scans",null,'.(isset($_COOKIE["columns"])?'true':'false').');</script>';
}
?>
