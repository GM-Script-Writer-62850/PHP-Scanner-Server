<div id="paper-list" class="box box-full"><h2>Paper List</h2><?php
if(file_exists("config/paper.json")){
	$paper=json_decode(file_get_contents("config/paper.json"));
}
else{
	$paper=json_decode('{}');
}
$del=Get_Values('delete');
$add=Get_Values('add');
$N_width=Get_Values('width');
$N_height=Get_Values('height');
if($N_height==null&&$N_width==null){
	$N_width=Get_Values('Mwidth')/25.4;
	$N_height=Get_Values('Mheight')/25.4;
}
if($del!=null){
	if(isset($paper->{$del})){
		unset($paper->{$del});
	}
}
if($add!=null&&$N_width!=null&&$N_height!=null){
	if(!isset($paper->{$add})&&is_numeric($N_width)&&is_numeric($N_height)){
		if($N_width>$N_height){
			$tmp=$N_height;
			$N_height=$N_width;
			$N_width=$tmp;
		}
		$paper->{$add}=json_decode('{"width":'.($N_width*25.4).',"height":'.($N_height*25.4).'}');
		$msg=false;
	}
	else{
		$msg=true;
	}
}
if($add!=null||$del!=null){
	SaveFile("config/paper.json",json_encode($paper));
}
echo '<ul>';
$ct=0;
foreach($paper as $key=>$val){
	$ct++;
	echo '<li><a class="tool icon del" href="index.php?page=Paper%20Manager&delete='.html($key).'"><span class="tip">Delete</span></a> '.$key.' <div class="code tool">'.number_format(round($val->{"width"}/25.4,2),2,'.',',').'x'.str_pad(number_format(round($val->{"height"}/25.4,2),2,'.',','),5,' ',STR_PAD_LEFT).'<span class="tip">'.$val->{"width"}.'x'.$val->{"height"}.' millimeters</span></div></li>';
}
echo "</ul>";
if($ct==0)
	echo "There are no paper sizes on file.";
if($del!=null){
	$del=html($del);
	Print_Message("Deleted:","The paper size $del has been deleted.","center");
}
if($add!=null&&$N_width!=null&&$N_height!=null){
	if(!$msg){
		Print_Message("Added:","The paper size $add has been created.","center");
	}
	else{
		if(!is_numeric($N_width)||!is_numeric($N_height)){
			Print_Message("Error:","One or more of these is not a number:<br/><code>$N_width</code><br/><code>$N_height</code>.","center");
		}
		else{
			Print_Message("Error:","The paper size $add already exists.","center");
		}
	}
}
?></div>
<div id="paperForm" class="box box-full"><h2>New Paper Maker</h2><form action="index.php?page=Paper%20Manager" method="POST"><p>
<span>Paper Name:</span><input type="text" name="add"/><br/>
<span>Paper Width:</span><input type="text" name="width"/> inches<br/>
<span>Paper Height:</span><input type="text" name="height"/> inches<br/>
<input type="submit" value="Add paper size"/>
</p></form><form class="m" action="index.php?page=Paper%20Manager" method="POST"><p>
<span>Paper Name:</span><input type="text" name="add"/></span><br/>
<span>Paper Width:</span><input type="text" name="Mwidth"/> millimeters<br/>
<span>Paper Height:</span><input type="text" name="Mheight"/> millimeters<br/>
<input type="submit" value="Add paper size"/>
</p></form></div>
