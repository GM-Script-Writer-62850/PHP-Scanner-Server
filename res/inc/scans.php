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
	return "$t0 day(s), $t2 hour(s), $t4 minute(s), and $t5 second(s) ago";
}
echo '<script type="text/javascript" src="res/writeScripts/imgur-box.js" id="imgur-box-setup"></script>';
if(count(scandir("scans"))==2){
	Print_Message("No Images","All files have been removed. There are no scanned images to display.",'center');
}
else{
	$CANNERS=json_decode(file_get_contents('config/scanners.json'));
	$ct=count($CANNERS);
	$origin='';
	if($ct>1){
		$html='<option value="-1">Any Scanner</option>';
		for($i=0;$i<$ct;$i++){
			$origin=substr($CANNERS[$i]->{"DEVICE"},0,4)=='net:'?explode(':',$CANNERS[$i]->{"DEVICE"})[1]:'<script type="application/javascript">document.write(document.domain);</script>';
			$html=$html.'<option value="'.$CANNERS[$i]->{"ID"}.'">'.html($CANNERS[$i]->{"NAME"})." on $origin</option>";
		}
		$origin='<div class="footer"><p>Created By: <select onchange="getID(\'filter1\').origin.value=this.value;getID(\'filter2\').origin.value=this.value;">'.$html.'</select></p></div>';
	}
	$html='type="text" size="3" value="0" onkeypress="return validateKey(this,event,null);" onchange="this.value=Number(this.value)||0;"';
	$html='<button class="largeButton" type="submit"><span>Filter</span></button>'.
		'<span>Year(s) ago:</span><input name="y" '.$html.'/><br/>'.
		'<span>Day(s) ago:</span><input name="d" '.$html.'/><br/>'.
		'<span>Hour(s) ago:</span><input name="h" '.$html.'/><br/>'.
		'<span>Minute(s) ago:</span><input name="m" '.$html.'/><br/>'.
		'<span>Second(s) ago:</span><input name="s" '.$html.'/>'.
		'<input type="hidden" name="origin" value="-1"/>'.
		'<input type="hidden" name="page" value="Scans"/>';
	echo '<div class="box box-full dualForm"><h2>Filter By Age</h2><form id="filter2" action="index.php?filter=2" method="POST"><h3>Display Files Newer Than</h3><p>'.$html.
		'</p></form><form id="filter1" class="m" action="index.php?filter=1" method="POST"><h3>Display Files Older Than</h3><p>'.$html.'</p></form>'.$origin.
		'<div class="footer"><p><input type="button" value="Combine Both Filters" onclick="scanFilter(getID(\'filter2\'),getID(\'filter1\'))"/></p></div>'.
		'<div class="footer">Tip: +/- can increase/decrease numbers.</div></div>'. // End block
		'<div class="box box-full"><h2>Bulk Operations</h2><p style="text-align:center;">'.
		genIconLinks((object)array('download'=>0,'edit'=>0),null,true).
		'<br/>Double Click a file name to select/deselect it<br/>'.
		'The order they are selected determines the page order<br/>'.
		'<button onclick="return selectScans(\'excluded\');">Select All</button> '.
		'<button onclick="return selectScans(false);">Invert Selection</button> '.
		'<button onclick="return selectScans(\'included\');">Select None</button>'.
		'</p></div>';
	$FILES=scandir('scans/file');
	$i=array();
	foreach($FILES as $FILE){
		if($FILE=='.'||$FILE=='..')
			continue;
		$i[$FILE]=filemtime("scans/file/$FILE");
	}
	arsort($i);
	$FILES=array_keys($i);
	echo '<div id="scans" class="columns">';
	$filter=Get_Values('filter');
	if(!is_null($filter)){
		$origin=Get_Values('origin');
		if(is_null($origin))//Backwards compatibility with only saved links
			$origin=-1;
		$msg='Here is a fixed <a href="index.php?page=$PAGE&amp;filter=$filter&amp;';
		if($origin>-1){
			$msg='and were created by <span class="tool">'.html($CANNERS[$origin]->{"NAME"}).'<span class="tip">'.html($CANNERS[$origin]->{"DEVICE"}).'</span></span>.<br/>'.$msg;
		}
		$time=time();
		if($filter<3){
			$t=Get_Values('t');
			if(is_null($t))
				$t=Get_Values('y')*31557600+Get_Values('d')*86400+Get_Values('h')*3600+Get_Values('m')*60+Get_Values('s');
			else
				$t=$time-$t;
			$human=ageSeconds2Human($t);
			$time=$time-$t;
			Print_Message('Notice','Only displaying files '.($filter===2?'newer':'older')." than $human".($origin==-1?'.':'')."<br/>".
				$msg."t=$time\">link</a> you can save.",'center');
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
			Print_Message('Notice','Only displaying files newer than '.ageSeconds2Human($t2).($origin==-1?'<br/>and':',<br/>').' older than '.ageSeconds2Human($t1).($origin==-1?'.<br/>':',<br/>').''.
				$msg.'T2='.$time[1].'&amp;T1='.$time[0].'">link</a> you can save.','center');
		}
	}
	for($i=0,$max=count($FILES);$i<$max;$i++){
		$FILE=$FILES[$i];
		if(isset($time)){
			if($origin>-1){
				if(intval(explode('_',$FILE)[1])!=$origin)
					continue;
			}
			if($filter===2){
				if(filemtime("scans/file/$FILE")<$time)
					continue;
			}
			else if($filter===1){
				if(filemtime("scans/file/$FILE")>$time)
					continue;
			}
			else if($filter===3){
				if(!(filemtime("scans/file/$FILE")>$time[1]&&filemtime("scans/file/$FILE")<$time[0]))
					continue;
			}
		}
		$FILE=substr($FILE,5);
		$IMAGE=strlen(substr($FILES[$i],strrpos($FILES[$i],'.')));// char count of file extension +1
		$IMAGE="Preview".substr($FILES[$i],4,$IMAGE*-1).".jpg";
		echo '<div class="box" id="'.html($FILE).'">'.
			'<h2 ondblclick="toggleFile(this);" class="excluded">'.html($FILE).'</h2><p><span>'.
			genIconLinks(null,"Scan_$FILE",false).'</span><br/>'.
			'<a class="tool" target="_blank" href="scans/file/Scan_'.url($FILE).'" style="width:100%;"><img src="scans/thumb/'.url($IMAGE).'" alt="'.html($FILE).'" style="width:100%"/><span class="tip">View raw file</span></a>'.
			'</p></div>';
	}
	echo '</div>';
}
?>
