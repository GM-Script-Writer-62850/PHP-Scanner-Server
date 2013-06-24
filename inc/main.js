var ias, previewIMG, scanners, paper, password, files={};
$(document).ready(function () {
	e=$('img[title="Preview"]');
	previewIMG=e[0];
	if(!previewIMG)
		return;
	ias=e.imgAreaSelect({
		handles: true,
		onSelectEnd: storeRegion,
		instance: true,
		enable: true,
		disable: ((previewIMG.src.indexOf('inc/images/blank.gif')>-1)?true:false),
		fadeSpeed: 850,
		parent: 'div#select',
		zIndex: 1
	});
	if(previewIMG){
		if(previewIMG.src.indexOf('inc/images/blank.gif')>-1){
			getID('sel').style.display='none';
			document.scanning.rotate.title="If you plan to crop do this on the final scan";
		}
	}
});
function getID(id){
	return document.getElementById(id);
}
function pre_scan(form,ias,t){
	previewIMG.style.zIndex=-1;
	previewIMG.nextSibling.removeAttribute('style');
	previewIMG.parentNode.style.height=previewIMG.offsetHeight+3+'px';
	form.loc_maxW.value=previewIMG.offsetWidth;
	form.loc_maxH.value=previewIMG.offsetHeight;
	ele=getID('select');
	if(ele)
		ele.style.display='none';
	return true;
}
function sendE(ele,e){
	try{
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent(e, true, true);
		ele.dispatchEvent(evt);
	}
	catch(err){//stoupid IE
		ele.fireEvent('on'+e);
	}
}
function config(json){
	for(var i in json){
		eval("document.scanning."+i+".value='"+json[i]+"'");
		eval("sendE(document.scanning."+i+",'change')");
	}
}
function stripSelect(){
	document.scanning.loc_width.value=0;
	document.scanning.loc_height.value=0;
	ias.setOptions({ "hide": true,"disable": true });
	ias.update();
	getID("sel").style.display='none';
}
function clearRegion(ias,set){
	if(set){
		document.scanning.loc_width.value=0;
		document.scanning.loc_height.value=0;
		document.scanning.loc_x1.value=0;
		document.scanning.loc_y1.value=0;
		document.scanning.loc_x2.value=0;
		document.scanning.loc_y2.value=0;
	}
	ias.setOptions({ "hide": true });
	ias.update();
}
function storeRegion(img, sel){
	document.scanning.loc_width.value=sel.width;
	document.scanning.loc_height.value=sel.height;
	document.scanning.loc_x1.value=sel.x1;
	document.scanning.loc_y1.value=sel.y1;
	document.scanning.loc_x2.value=sel.x2;
	document.scanning.loc_y2.value=sel.y2;
}
function setRegion(ias){
	//Code to counter user stupidty and innocent mistakes
	var ele=previewIMG;
	var img_W=ele.offsetWidth;
	var img_H=ele.offsetHeight;
	var x1=Math.abs(document.scanning.loc_x1.value);
	var y1=Math.abs(document.scanning.loc_y1.value);
	var x2=Math.abs(document.scanning.loc_x2.value);
	var y2=Math.abs(document.scanning.loc_y2.value);
	if(x1>img_W)
		x1=img_W;
	if(y1>img_H)
		y1=img_H;
	if(x2>img_W)
		x2=img_W;
	if(y2>img_H)
		y2=img_H;
	for(var i=0;i<2;i++){
		if(x1>x2){
			x2=x1;
			x1=Math.abs(document.scanning.loc_x2.value);
		}
		if(y1>y2){
			y2=y1;
			y1=Math.abs(document.scanning.loc_y2.value);
		}
	}
	ias.setSelection(x1,y1,x2,y2);
	ias.setOptions({ show: true });
	ias.update();
	storeRegion(null, ias.getSelection());
}
function validateKey(ele,e,ias){
	if(e.keyCode)
		e.which=e.keyCode;// Stoupid IE needs to follow the standards
	if(e.which==13){// Enter
		setRegion(ias);
		return false;
	}
	else if(e.which==43){// +
		ele.value++;
		return false;
	}
	else if(e.which==45){// -
		ele.value--;
		return false;
	}
	else if(!isNaN(String.fromCharCode(e.which))||e.which==8||e.which==0){// number or backspace or unknown
		return true;
	}
	else{// anything else (mostly letters)
		return false;
	}
}
function changeColor(colors){
	var O=getID('style');
	var N=getID('style_new');
	O.href=N.href;
	N.href='inc/style.php?colors='+colors+'&nocache='+new Date().getTime();
	document.body.setAttribute('onunload',"if(!document.cookie)alert('The color theme was not saved because you have cookies disabled')");
}
function Set_Cookie( name, value, expires, path, domain, secure ){// http://techpatterns.com/downloads/javascript_cookies.php
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ){
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );

	document.cookie = name + "=" +escape( value ) +
		( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
		( ( path ) ? ";path=" + path : "" ) +
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}
function lastScan(scan,preview,scanner,ele,imgur){
	generic=scan.slice(5);
	previewIMG.src='scans/'+preview;
	ias.setOptions({'enable': true });
	ias.update();
	getID('sel').removeAttribute('style');
	document.scanning.scanner.value=scanner;
	sendE(document.scanning.scanner,'change');
	sendE(document.scanning.size,'change');
	ele.parentNode.parentNode.innerHTML='<h2>'+generic+'</h2><p><a class="tool icon download" href="download.php?file='+scan+'"><span class="tip">Download</span></a> '+
		'<a class="tool icon zip" href="download.php?file='+scan+'&compress"><span class="tip">Download Zip</span></a> '+
		'<a class="tool icon pdf" href="#" onclick="PDF_popup(\''+scan+'\');"><span class="tip">Download PDF</span></a> '+
		'<a class="tool icon print" href="print.php?file='+scan+'" target="_blank"><span class="tip">Print</span></a> '+
		'<a class="tool icon del" href="index.php?page=Scans&amp;delete=Remove&amp;file='+generic+'" onclick="return confirm(\'Delete this scan\')"><span class="tip">Delete</span></a> '+
		'<a class="tool icon edit" href="index.php?page=Edit&amp;file='+generic+'"><span class="tip">Edit</span></a> '+
		'<a class="tool icon view" href="index.php?page=View&amp;file='+scan+'"><span class="tip">View</span></a> '+
		(imgur?'<a class="tool icon upload" href="#" onclick="return upload(\''+scan+'\')"><span class="tip">Upload to Imgur</span></a> ':'<span class="tool icon upload-off"><span class="tip">Upload to Imgur (Disabled)</span></span> ')+
		'<a href="#" onclick="return emailManager(\''+scan+'\');" class="tool icon email"><span class=\"tip\">Email</span></a> '+
		'<span class="tool icon recent-off"><span class="tip">Last Scan (Disabled)</span></span></p>';
}
function checkScanners(){// Does not support IE8 and below
	if(typeof XMLHttpRequest!='function'||typeof JSON=='undefined'){
		printMsg('Sorry',"Your browser does not support the XMLHttpRequest function and the JSON object so this page can not check if the scanner is in-use or not in real time.<br/>You have 3 choices: Ignore This, update your browser, and switch browsers",'center',0);
		return;
	}
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var scan=parseJSON(httpRequest.responseText);
				var loc, str;
				str='';
				if(scanners.length!=scan.length){
					for(var i=0,m=scan.length;i<m;i++){
						if(scan[i]["DEVICE"].substr(0,4)=="net:"){
							loc=scan[i]["DEVICE"].split(':');
							loc=loc[1];
						}
						else{
							loc=document.domain;
						}
						delete(scan[i]["INUSE"]);
						delete(scan[i]["ID"]);
						delete(scan[i]["DEVICE"]);
						delete(scan[i]["NAME"]);
						str+='<option'+(scan[i]["INUSE"]==1?' disabled="disabled"':'')+' class="'+$.text(JSON.stringify(scan[i]))+'" value="'+scan[i]["ID"]+'">'+scan[i]["NAME"]+' on '+loc+'</option>';
					}
					scanners=scan;
					loc=document.scanning.scanner.selectedIndex;
					if(document.all){// http://support.microsoft.com/kb/276228
						document.scanning.scanner.parentNode.innerHTML='<p><select onchange="scannerChange(this)" style="width:238px;" name="scanner">'+str+'</select></p>';
					}
					else{
						document.scanning.scanner.innerHTML=str;
					}
					document.scanning.scanner.selectedIndex=loc;
					alert("The number of scanners connected to the server has been altered\nPlease double check the scanner you are using");
				}
				else{
					for(var i=0,m=scanners.length;i<m;i++){
						if(scan[i]['INUSE']!=scanners[i]['INUSE']){
							if(scan[i]['INUSE']==1)
								document.scanning.scanner.childNodes[i].setAttribute("disabled","disabled");
							else
								document.scanning.scanner.childNodes[i].removeAttribute('disabled');
						}
					}
					scanners=scan;
				}
			}
			setTimeout("checkScanners()",5000);
		}
	};
	httpRequest.open('GET', 'config/scanners.json?cacheBust='+new Date().getTime(), true);
	httpRequest.send(null);
}
function printMsg(t,m,a,r){
	var div=document.createElement('div');
	var ele=getID('new_mes');
	div.className="message";
	div.innerHTML="<h2>"+t+'<a class="close icon tool del" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);return false;" href="#"><span class="tip">Close</span></a>'+"</h2><div"+(a!='center'?' style="text-align:'+a+';"':'')+">"+m+"</div>";
	if(r!=-1)
		ele.insertBefore(div,ele.childNodes[0]);
	else
		ele.appendChild(div);
	div.style.height='auto';
}
function roundNumber(num,dec){// http://forums.devarticles.com/javascript-development-22/javascript-to-round-to-2-decimal-places-36190.html#post71368
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}
function parseJSON(jsonTXT){
	try{
		if(typeof(JSON)=='object')
			return JSON.parse(jsonTXT);
		else
			return eval('('+jsonTXT+')');
	}
	catch(e){
		printMsg('Invald Javascript Object Notation:','<textarea onclick="this.select()" style="width:100%;height:80px;">'+jsonTXT+'</textarea><br/>If you are reading this please report it as a bug. Please copy/paste the above, something as simple as a line break can cause errors here. If want to read this I suggest pasting it onto <a target="_blank" href="http://jsonlint.com/">jsonlint.com</a>.','center',0);
	}
}
function scannerChange(ele){
	var info,dpi,html,html2,html3,text,width,height;
	info=parseJSON(ele.childNodes[ele.selectedIndex].className);
	sources=info['SOURCE'].split('|');
	width=info['WIDTH'];
	height=info['HEIGHT'];
	html='<option value="full">Full Scan</option>';// string length (39) is used a few lines down
	for(var i in paper){
		if(width>=paper[i]['width']&&height>=paper[i]['height'])
			html+='<option value="'+paper[i]['width']+'-'+paper[i]['height']+'" title="'+paper[i]['width']+' mm x '+paper[i]['height']+' mm">'+i+': '+roundNumber(paper[i]['width']/25.4,2)+'" x '+roundNumber(paper[i]['height']/25.4,2)+'"</option>';
	}
	html2='';
	modes=info['MODE'].split('|');
	for(i=modes.length-1;i>-1;i--){
		switch(modes[i]){
			case 'Gray':
		  		text='Grayscale';break;
			case 'Lineart':
		  		text='Line Art';break;
			default:
				text=modes[i];
		}
		html2+='<option value="'+modes[i]+'">'+text+'</option>';
	}
	html3='';
	for(i=0,s=sources.length;i<s;i++){
		html3+='<option value="'+sources[i]+'">'+(sources[i]=='ADF'?'Automatic Document Feeder':sources[i])+'</option>';
	}
	if(document.all){// http://support.microsoft.com/kb/276228
		if(html.length>39)
			document.scanning.size.parentNode.innerHTML='<select onchange="paperChange(this);" name="size">'+html+'</select>';
		document.scanning.mode.parentNode.innerHTML='<select name="mode" class="title">'+html2+'</select>';
		document.scanning.source.parentNode.innerHTML='<select name="source" class="title">'+html3+'</select>';
	}
	else{
		if(html.length>39)
			document.scanning.size.innerHTML=html;
		document.scanning.mode.innerHTML=html2;
		document.scanning.source.innerHTML=html3;
	}
	if(document.scanning.source.value=='Inactive')
		document.scanning.source.setAttribute('disabled','disabled');
	else
		document.scanning.source.removeAttribute('disabled');
	sourceChange(document.scanning.source);
	sendE(document.scanning.size,'change');
}
function sourceChange(ele){
	var info=document.scanning.scanner;
	info=parseJSON(info.childNodes[info.selectedIndex].className);
	var html='';
	var dpi=info['DPI-'+ele.value].split('|');
	for(var i=0,max=dpi.length;i<max;i++)
		html+='<option value="'+dpi[i]+'">'+dpi[i]+' '+(isNaN(dpi[i])?'':'dpi')+'</option>';
	if(document.all){// http://support.microsoft.com/kb/276228
		document.scanning.quality.parentNode.innerHTML='<select name="quality" class="upper">'+html+'</select>';
	}
	else{
		document.scanning.quality.innerHTML=html;
	}
}
function paperChange(ele){
	if(ele.value=='full'){
		document.scanning.ornt.selectedIndex=0;
		document.scanning.ornt.disabled='disabled';
		return;
	}
	var json=document.scanning.scanner.childNodes[document.scanning.scanner.selectedIndex].className;
	json=parseJSON(json);
	var width=json['WIDTH'];
	var height=json['HEIGHT'];
	var paper=ele.value.split('-');
	if(Number(paper[0])>height||Number(paper[1])>width){
		document.scanning.ornt.selectedIndex=0;
		document.scanning.ornt.disabled='disabled';
	}
	else{
		document.scanning.ornt.removeAttribute('disabled');
	}
}
function fileChange(type){
	if(type=='txt')
		getID('lang').removeAttribute('style');
	else
		getID('lang').style.display='none';
}
function Debug(html,show){
	var div=document.createElement('div');
	div.id="debug";
	if(show){
		div.style.display='inline';
	}
	div.className="box box-full";
	div.innerHTML='<h2>Debug Console</h2><pre>'+decodeURIComponent(html)+'</pre>';
	var nojs=getID('nojs');
	nojs.parentNode.insertBefore(div,nojs);
}
function toggleDebug(keyboard){
	var debug=getID('debug');
	var debugLink=getID('debug-link');
	if(debug){
		if(debug.style.display=='inline'){
			debug.removeAttribute('style');
			Set_Cookie( 'debug', false, 1, '/', '', '' );
			if(keyboard&&debugLink)
				debugLink.textContent='Show';
			return false;
		}
		else{
			debug.style.display='inline';
			Set_Cookie( 'debug', true, 1, '/', '', '' );
			if(keyboard&&debugLink)
				debugLink.textContent='Hide';
			return true;
		}
	}
}
function toggleFortune(e){
	e=(e=='Hide'?false:true)
	Set_Cookie( 'fortune', e, 1, '/', '', '' );
	return e;
}
function scanReset(){
	sendE(document.scanning.scanner,'change');
	sendE(document.scanning.quality,'change');
	sendE(document.scanning.size,'change');
	sendE(document.scanning.filetype,'change');
}
/*function lastCordsChange(json,state){
	// This is related to lines 52, 69-78,219,221, 223, and 225 of scan.php it is a attept to add a option is use the last scan's coordinates (incomplete and I changed my mind on making it)
	// It will still need to disabled when/if the scanner is changed and including the coords at page load is buged and attempting to scan results in a invalid input security error
	if(state){
		json=parseJSON(json);
		json["x2"]=0;//these 2 are only used in the UI and have no direct impact in the backend
		json["y2"]=0;//leaving them as 0 does not hurt anything since this area is not used in the UI at this point
		storeRegion(null,json);
		document.scanning.size.setAttribute('disabled','disabled');
	}
	else{
		storeRegion(null,{"width":0,"height":0,"x1":0,"y1":0,"x2":0,"y2":0});
		document.scanning.size.removeAttribute('disabled');
	}
}*/
function disableIcons(){// Converts disabled icons to act like disabled icons
	// not all browsers support efficient code
	try{// most efficient
		var icons=document.evaluate("//a[contains(@class,'tool icon')][contains(@class,'-off')]",document,null,6,null);
		for(var i=0;i<icons.snapshotLength;i++){
			var icon=icons.snapshotItem(i);
			icon.href="javascript:void();";
			icon.setAttribute("onclick","return false;");
			icon.setAttribute('style','cursor:inherit;');
			icon.childNodes[0].textContent+=" (Disabled)";
		}
	}
	catch(e){
		if(typeof document.getElementsByClassName=='function')//second most efficent
			var icons=document.getElementsByClassName('tool icon');
		else{// very inefficient 
			var a=document.getElementsByTagName('a'),icons=Array();
			for(var i=0;i<a.length;i++){
				if(a[i].className.indexOf('tool icon')>-1){
					icons.push(a[i]);
				}
			}
		}
		for(var i=0;i<icons.length;i++){
			if(icons[i].className.indexOf('-off')>-1){
				if(icons[i].tagName.toUpperCase()!='A')
					continue;
				icons[i].href="javascript:void();";
				icons[i].setAttribute("onclick","return false;");
				icons[i].setAttribute('style','cursor:inherit;');
				icons[i].childNodes[0].textContent+=" (Disabled)";
			}
		}
	}
}

document.onkeyup=function(event){
	if(event.ctrlKey&&(event.which==68||event.keyCode==68))// [Ctrl]+[Shift]+[D]
		toggleDebug(true);
}
/* start http://www.pat-burt.com/csspopup.js */
function toggle(div_id){
	var el = getID(div_id);
	if( el.style.display == 'none' ){
		el.style.display = 'block';
		setTimeout(function(){
			el.style.backgroundColor="";
			el.childNodes[0].style.opacity=1;
		},100);
	}
	else{
		el.style.display = 'none';
		el.style.backgroundColor="transparent";
		el.childNodes[0].style.opacity=0;
	}
}
function blanket_size(popUpDivVar){
	if(typeof window.innerWidth != 'undefined'){
		viewportheight = window.innerHeight;
	}
	else{
		viewportheight = document.documentElement.clientHeight;
	}
	if((viewportheight > document.body.parentNode.scrollHeight) && (viewportheight > document.body.parentNode.clientHeight)){
		blanket_height = viewportheight;
	}
	else{
		if(document.body.parentNode.clientHeight > document.body.parentNode.scrollHeight){
			blanket_height = document.body.parentNode.clientHeight;
		}
		else{
			blanket_height = document.body.parentNode.scrollHeight;
		}
	}
	var blanket = getID(popUpDivVar);
	blanket.style.height = blanket_height + 'px';
	var popUpDiv = blanket.childNodes[0];
	popUpDiv_height=viewportheight/2-popUpDiv.offsetHeight/2;
	popUpDiv.style.top = popUpDiv_height + 'px';
}
function window_pos(popUpDivVar,width){
	if (typeof window.innerWidth != 'undefined'){
		viewportwidth = window.innerHeight;
	}
	else{
		viewportwidth = document.documentElement.clientHeight;
	}
	if((viewportwidth > document.body.parentNode.scrollWidth) && (viewportwidth > document.body.parentNode.clientWidth)){
		window_width = viewportwidth;
	}
	else{
		if (document.body.parentNode.clientWidth > document.body.parentNode.scrollWidth){
			window_width = document.body.parentNode.clientWidth;
		}
		else{
			window_width = document.body.parentNode.scrollWidth;
		}
	}
	var popUpDiv = getID(popUpDivVar).childNodes[0];
	window_width=window_width/2-width/2;
	popUpDiv.style.left = window_width + 'px';
	popUpDiv.style.width=width+'px';
}
function popup(windowname,width){
	toggle(windowname);
	window_pos(windowname,width);
	blanket_size(windowname);
}
/* end http://www.pat-burt.com/csspopup.js */
function PDF_popup(file){
	getID("blanket").childNodes[0].innerHTML='How would you prefer for your PDF download?<br/>\
		A scan placed on the page with a title or<br/>\
		a would you prefer the scan as the page.<br/>\
		<a href="download.php?file='+file+'&pdf" onclick="setTimeout(\'toggle(\\\'blanket\\\')\',100);">\
		<button><img src="inc/images/pdf-scaled.png" width="106" height="128"/></button></a>\
		<a href="download.php?file='+file+'&pdf&full" onclick="setTimeout(\'toggle(\\\'blanket\\\')\',100);">\
			<button><img src="inc/images/pdf-full.png" width="106" height="128"/></button></a>\
		<br/><input type="button" value="Cancel" style="width:261px;" onclick="toggle(\'blanket\')"/>';
	popup('blanket',290);
	return false;
}
function toggleFile(file){
	if(!files[file.textContent]){
		files[file.textContent]=1;
		file.setAttribute('selected',true);
	}
	else{
		delete(files[file.textContent]);
		file.setAttribute('selected',false);
	}
}
function makePDF(link){
	var ct=0;
	for(var i in files)
		ct++;
	if(ct>0){
		link.href="download.php?json="+encodeURIComponent(JSON.stringify(files));
		return true;
	}
	else{
		printMsg('Error','No files selected','center',-1);
		return false;
	}
}
function selectScans(b){
	var scans=document.evaluate("//div[@id='scans']/div/h2[@selected='"+b+"']",document,null,6,null);
	for(var i=0;i<scans.snapshotLength;i++)
		toggleFile(scans.snapshotItem(i));
	return false;
}
function upload(file){
	if(typeof XMLHttpRequest!='function'){
		printMsg('Sorry',"Your browser does not support the XMLHttpRequest function so you can not upload scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
		return false;
	}
	if(getID(file)){
		popup('blanket',365);
		return true;
	}
	if(confirm("Upload '"+file.substr(5)+"' to imgur.com")===false)
		return false;
	var now=new Date().getTime();
	printMsg('Uploading<span id="upload-'+now+'"></span>','Please Wait...<br/>This could take a while depending on the file size of the scan and the upload speed at '+document.domain,'center',0);
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var json=parseJSON(httpRequest.responseText);
				if(json['error']['message']!=null){
					printMsg('Upload Error',json['error']['message'],'center',0);
				}
				else{
					var html='';
					for(var i in json['upload']['links']){
						html+=i+"\n\t"+json['upload']['links'][i]+"\n";
					}
					var links=json['upload']['links'];
					getID("blanket").childNodes[0].innerHTML='<h2 style="font-size:12px;">'+file.substr(5)+' is on Imgur</h2>'+
						'<div id="imgur-data"><div><img id="'+file+'" style="float:left;margin-right:5px;" src="'+json['upload']['links']['small_square']+'" width="90" height="90"/>'+
						'<ul style="list-style:none;">'+
						'<li>View on Imgur:<ul><li><a href="'+links['imgur_page']+'" target="_blank">'+links['imgur_page'].substr(7)+'</a></li></ul></li>'+
						'<li>Direct Link:<ul><li><a href="'+links['original']+'" target="_blank">'+links['original'].substr(7)+'</a></li></ul></li>'+
						'<li>Delete Link:<ul><li><a href="'+links['delete_page']+'" target="_blank">'+links['delete_page'].substr(7)+'</a></li></ul></li></ul></div>'+
						'<h2 style="font-size:12px;text-align:center;">Embed Codes</h2><div style="width:100%;overflow-x:scroll;white-space: nowrap;">'+
						'<div class="codes"><h2>Original</h2><ul>'+
						'<li>Direct Link (email & IM)<ul><li><input onclick="this.select();" type="text" value="'+links['original']+'"/></li></ul></li>'+
						'<li>HTML Image (websites & blogs)<ul><li><input onclick="this.select();" type="text" value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['original']+'&quot; alt=&quot;&quot; title=&quot;Hosted by imgur.com&quot; /&gt;&lt;/a&gt;"/></li></ul></li>'+
						'<li>BBCode (message boards & forums)<ul><li><input onclick="this.select();" type="text" value="[IMG]'+links['original']+'[/IMG]"/></li></ul></li>'+
						'<li>Linked BBCode (message boards)<ul><li><input onclick="this.select();" type="text" value="[URL='+links['imgur_page']+'][IMG]'+links['original']+'[/IMG][/URL]"/></li></ul></li>'+
						'<li>Markdown Link (reddit comment)<ul><li><input onclick="this.select();" type="text" value="[Imgur]('+links['original']+')"/></li></ul></li>'+
						'</ul></div>'+
						'<div class="codes"><h2>Huge Thumbnail</h2><ul>'+
						'<li>Direct Link (email & IM)<ul><li><input onclick="this.select();" type="text" value="'+links['huge_thumbnail']+'"/></li></ul></li>'+
						'<li>HTML Image (websites & blogs)<ul><li><input onclick="this.select();" type="text" value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['huge_thumbnail']+'&quot; alt=&quot;&quot; title=&quot;Hosted by imgur.com&quot; /&gt;&lt;/a&gt;"/></li></ul></li>'+
						'<li>BBCode (message boards & forums)<ul><li><input onclick="this.select();" type="text" value="[IMG]'+links['huge_thumbnail']+'[/IMG]"/></li></ul></li>'+
						'<li>Linked BBCode (message boards)<ul><li><input onclick="this.select();" type="text" value="[URL='+links['imgur_page']+'][IMG]'+links['huge_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
						'<li>Markdown Link (reddit comment)<ul><li><input onclick="this.select();" type="text" value="[Imgur]('+links['huge_thumbnail']+')"/></li></ul></li>'+
						'</ul></div>'+
						'<div class="codes"><h2>Large Thumbnail</h2><ul>'+
						'<li>Direct Link (email & IM)<ul><li><input onclick="this.select();" type="text" value="'+links['large_thumbnail']+'"/></li></ul></li>'+
						'<li>HTML Image (websites & blogs)<ul><li><input onclick="this.select();" type="text" value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['large_thumbnail']+'&quot; alt=&quot;&quot; title=&quot;Hosted by imgur.com&quot; /&gt;&lt;/a&gt;"/></li></ul></li>'+
						'<li>BBCode (message boards & forums)<ul><li><input onclick="this.select();" type="text" value="[IMG]'+links['large_thumbnail']+'[/IMG]"/></li></ul></li>'+
						'<li>Linked BBCode (message boards)<ul><li><input onclick="this.select();" type="text" value="[URL='+links['imgur_page']+'][IMG]'+links['large_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
						'<li>Markdown Link (reddit comment)<ul><li><input onclick="this.select();" type="text" value="[Imgur]('+links['large_thumbnail']+')"/></li></ul></li>'+
						'</ul></div>'+
						'<div class="codes"><h2>Medium Thumbnail</h2><ul>'+
						'<li>Direct Link (email & IM)<ul><li><input onclick="this.select();" type="text" value="'+links['medium_thumbnail']+'"/></li></ul></li>'+
						'<li>HTML Image (websites & blogs)<ul><li><input onclick="this.select();" type="text" value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['medium_thumbnail']+'&quot; alt=&quot;&quot; title=&quot;Hosted by imgur.com&quot; /&gt;&lt;/a&gt;"/></li></ul></li>'+
						'<li>BBCode (message boards & forums)<ul><li><input onclick="this.select();" type="text" value="[IMG]'+links['medium_thumbnail']+'[/IMG]"/></li></ul></li>'+
						'<li>Linked BBCode (message boards)<ul><li><input onclick="this.select();" type="text" value="[URL='+links['imgur_page']+'][IMG]'+links['medium_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
						'<li>Markdown Link (reddit comment)<ul><li><input onclick="this.select();" type="text" value="[Imgur]('+links['medium_thumbnail']+')"/></li></ul></li>'+
						'</ul></div>'+
						'<div class="codes"><h2>Small Thumbnail</h2><ul>'+
						'<li>Direct Link (email & IM)<ul><li><input onclick="this.select();" type="text" value="'+links['small_thumbnail']+'"/></li></ul></li>'+
						'<li>HTML Image (websites & blogs)<ul><li><input onclick="this.select();" type="text" value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['small_thumbnail']+'&quot; alt=&quot;&quot; title=&quot;Hosted by imgur.com&quot; /&gt;&lt;/a&gt;"/></li></ul></li>'+
						'<li>BBCode (message boards & forums)<ul><li><input onclick="this.select();" type="text" value="[IMG]'+links['small_thumbnail']+'[/IMG]"/></li></ul></li>'+
						'<li>Linked BBCode (message boards)<ul><li><input onclick="this.select();" type="text" value="[URL='+links['imgur_page']+'][IMG]'+links['small_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
						'<li>Markdown Link (reddit comment)<ul><li><input onclick="this.select();" type="text" value="[Imgur]('+links['small_thumbnail']+')"/></li></ul></li>'+
						'</ul></div>'+
						'<div class="codes"><h2>Big Square</h2><ul>'+
						'<li>Direct Link (email & IM)<ul><li><input onclick="this.select();" type="text" value="'+links['big_square']+'"/></li></ul></li>'+
						'<li>HTML Image (websites & blogs)<ul><li><input onclick="this.select();" type="text" value="&lt;a href=&quot;'+links['big_page']+'&quot;&gt;&lt;img src=&quot;'+links['big_square']+'&quot; alt=&quot;&quot; title=&quot;Hosted by imgur.com&quot; /&gt;&lt;/a&gt;"/></li></ul></li>'+
						'<li>BBCode (message boards & forums)<ul><li><input onclick="this.select();" type="text" value="[IMG]'+links['big_square']+'[/IMG]"/></li></ul></li>'+
						'<li>Linked BBCode (message boards)<ul><li><input onclick="this.select();" type="text" value="[URL='+links['imgur_page']+'][IMG]'+links['big_square']+'[/IMG][/URL]"/></li></ul></li>'+
						'<li>Markdown Link (reddit comment)<ul><li><input onclick="this.select();" type="text" value="[Imgur]('+links['big_square']+')"/></li></ul></li>'+
						'</ul></div>'+
						'<div class="codes" style="border: none;"><h2>Small Square</h2><ul>'+
						'<li>Direct Link (email & IM)<ul><li><input onclick="this.select();" type="text" value="'+links['small_square']+'"/></li></ul></li>'+
						'<li>HTML Image (websites & blogs)<ul><li><input onclick="this.select();" type="text" value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['small_square']+'&quot; alt=&quot;&quot; title=&quot;Hosted by imgur.com&quot; /&gt;&lt;/a&gt;"/></li></ul></li>'+
						'<li>BBCode (message boards & forums)<ul><li><input onclick="this.select();" type="text" value="[IMG]'+links['small_square']+'[/IMG]"/></li></ul></li>'+
						'<li>Linked BBCode (message boards)<ul><li><input onclick="this.select();" type="text" value="[URL='+links['imgur_page']+'][IMG]'+links['small_square']+'[/IMG][/URL]"/></li></ul></li>'+
						'<li>Markdown Link (reddit comment)<ul><li><input onclick="this.select();" type="text" value="[Imgur]('+links['small_square']+')"/></li></ul></li>'+
						'</ul></div>'+
						'</div></div><input type="button" value="Close" style="width:100%;" onclick="toggle(\'blanket\')"/>';
					popup('blanket',365);
				}
				var btn=getID('upload-'+now);
				if(btn)
					sendE(btn.nextSibling,'click');
			}
			else{
				printMsg('Upload Error','Got a '+httpRequest.status+' error<br/>If you don\'t know what that means and want to know click <a target="_blank" href="http://www.w3.org/Protocols/HTTP/HTRESP.html">here</a>.','center',0);
			}
		}
	};
	httpRequest.open('GET', 'imgur.php?file='+encodeURIComponent(file));
	httpRequest.send(null);
	return true;
}
function emailManager(file){
	var storeSupport=(typeof localStorage=="object"&&typeof JSON=="object"?true:false),data=false;
	if(file==null&&!storeSupport){
		printMsg('Sorry',"Your browser does not support saving email settings<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
		return false;
	}
	if(typeof XMLHttpRequest!='function'){
		printMsg('Sorry',"Your browser does not support the XMLHttpRequest function so you can not email scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
		return false;
	}
	var html='<div id="email"><h2>'+(file?'Email: '+file.substr(5):'Configure Email')+'</h2>'+
	'<div class="security"><h2>Security Notice</h2><ul>';
	if(storeSupport){
		html+='<li>The remember me option will store your e-mail login data in your <a href="http://dev.w3.org/html5/webstorage/#dom-localstorage" target="_blank">local storage</a> in plain text (unencrypted).</li>'+
		(file?'<li>If you leave it unchecked your login date will not be saved and you will have to re-enter it every time.</li>':'')+
		'<li>Anyone with access to your account on this computer can get your password if you use '+(file?'remember me':'save it')+'.</li>'+
		(file?'<li>You can delete your saved data on the <a href="index.php?page=Config"/>Configure</a> page.</li>':'');
		data=localStorage.getItem("email");
	}
	html+='<li>You can double click the password blank to see the password.</li>'+
	(document.location.protocol=='http:'?'<li>This does not use a secure connection to get your login from your browser to the server.</li>':'')+'</ul></div>'+
	'<form name="email" target="_blank" action="email.php" onsubmit="return validateEmail(this);">'+
	'<input type="hidden" name="file" value="'+file+'"/>'+
	'<div class="label">'+(file?'From':'Email')+':</div><div class="control"><input type="text" onchange="configEmail(this.value)" name="from" value="johndoe@gmail.com"/></div>'+
	(file?'<div class="label">Subject:</div><div class="control"><input type="text" name="title" value="[Scanned '+(file.substr(-3)!='txt'?'Image':'Text')+'] '+file.substr(5)+'"/></div>':'')+
	(file?'<div class="label">To:</div><div class="control"><input type="text" name="to" value=""/></div>':'')+
	'<div class="label">Password:</div><div class="control"><input type="password" name="pass" ondblclick="this.type=(this.type==\'text\'?\'password\':\'text\')" autocomplete="off"/></div>'+
	'<div class="label">Host:</div><div class="control"><input type="text" name="host" value="smtp.gmail.com"/></div>'+
	'<div class="label">Prefix:</div><div class="control"><select name="prefix"><option value="tls">TLS</option><option value="ssl">SSL</option></select></div>'+
	'<div class="label">Port:</div><div class="control"><input type="text" name="port" value="587"/></div>';
	if(storeSupport){
		html+='<div class="label">Remember Me:</div><div class="control"><input '+(file?'':'checked="checked" ')+'id="email-nopass" onchange="if(this.checked){getID(\'email-pass\').checked=false}'+(file?'':'else if(getID(\'email-nopass\').checked){getID(\'email-pass\').checked=true}')+'" type="checkbox" name="store"/> <small>(Exclude Password)</small></div>'+
		'<div class="label">Remember Me:</div><div class="control"><input id="email-pass" onchange="if(this.checked){getID(\'email-nopass\').checked=false}'+(file?'':'else if(getID(\'email-pass\').checked){getID(\'email-nopass\').checked=true}')+'" type="checkbox" name="storepass"/> <small>(Include Password)</small></div>';
	}
	html+='<input type="submit" value="'+(file?'Send':'Save')+'"/><input style="float:right;" type="button" value="Cancel" onclick="toggle(\'blanket\')"/>'+
	'</form>'+
	'<div class="help"><h2>Help Links</h2><p><a target="_blank" href="http://www.google.com">Google</a><br/>eg: What are Yahoo\'s smtp settings</p></div>'+
	(file?'<div class="help"><h2>Tips</h2><p>Send to multiple people by separating addresses with a comma.</p></div>':'')+
	'</div>';
	getID("blanket").childNodes[0].innerHTML=html
	if(data){
		data=JSON.parse(data);
		document.email.from.value=data["from"];
		document.email.pass.value=data["pass"];
		document.email.host.value=data["host"];
		document.email.prefix.value=data["prefix"];
		document.email.port.value=data["port"];
		document.email.store.checked=data["store"];
		document.email.storepass.checked=data["storepass"];
	}
	popup('blanket',420);
	return false;
}
function validateEmail(ele){
	var data={};
	if(ele.from.value.indexOf('@')==-1){
		alert('Invalid From Email Address');
		return false;
	}
	if(ele.file.value!='null'){
		var recipients=ele.to.value.replace(/ /g,"").split(',');
		for(var i=0,stp=recipients.length;i<stp;i++){
			if(recipients[i].indexOf('@')==-1){
				alert('Invalid To Email Address:\n"'+recipients[i]+'"');
				return false;
			}
		}
		if(!confirm('Are you ready to send "'+ele.title.value+'" to:\n'+recipients.join("\n")+'\nfrom '+ele.from.value)){
			return false;
		}
	}
	if(ele.store.checked||ele.storepass.checked){
		data["from"]=ele.from.value;
		data["pass"]=(ele.storepass.checked?ele.pass.value:'');
		data["host"]=ele.host.value;
		data["prefix"]=ele.prefix.value;
		data["port"]=ele.port.value;
		data["store"]=ele.store.checked;
		data["storepass"]=ele.storepass.checked;
		localStorage.setItem("email",JSON.stringify(data));
	}
	if(ele.file.value!='null')
		sendEmail(ele);
	toggle('blanket');
	return false;
}
function configEmail(addr){
	if(addr.indexOf('@')==-1||typeof XMLHttpRequest!='function')
		return;
	else
		addr=addr.substr(addr.indexOf('@')+1);
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var data=parseJSON(httpRequest.responseText);
				if(!data["error"]){
					document.email.port.value=data["port"];
					document.email.host.value=data["host"];
					if(data['type']!='smtp')
						printMsg('Please File a bug report','Your email provider is not supported, if you do support can be added for it','center',-1);
				}
			}
		}
	};
	httpRequest.open('GET', 'email.php?domain='+encodeURIComponent(addr));
	httpRequest.send(null);
}
function sendEmail(ele){
	if(typeof XMLHttpRequest!='function'){
		printMsg('Error','Your browser does not support <a href="http://www.w3schools.com/xml/xml_http.asp" target="_blank">XMLHttpRequest</a>, so you can not use this feature','center',0);
	}
	var now=new Date().getTime();
	printMsg('Sending Email<span id="email-'+now+'"></span>','Please Wait...<br/>This could take a while depending on the file size of the scan and the upload speed at '+document.domain,'center',0);
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var json=parseJSON(httpRequest.responseText);
				printMsg(json["title"],json["message"],'center',0);
			}
			else{
				printMsg('Sending Error','Got a '+httpRequest.status+' error<br/>If you don\'t know what that means and want to know click <a target="_blank" href="http://www.w3.org/Protocols/HTTP/HTRESP.html">here</a>.','center',0);
			}
			sendE(getID('email-'+now).nextSibling,'click');
		}
	};
	httpRequest.open('POST', 'email.php');
	var params = "file="+encodeURIComponent(ele.file.value)+
		"&from="+encodeURIComponent(ele.from.value)+
		"&to="+encodeURIComponent(ele.to.value)+
		"&title="+encodeURIComponent(ele.title.value)+
		"&pass="+encodeURIComponent(ele.pass.value)+
		"&host="+encodeURIComponent(ele.host.value)+
		"&prefix="+encodeURIComponent(ele.prefix.value)+
		"&port="+encodeURIComponent(ele.port.value);
	httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	httpRequest.setRequestHeader("Content-length", params.length);
	httpRequest.setRequestHeader("Connection", "close");
	httpRequest.send(params);
	return true;
}
function deleteEmail(){
	if(typeof localStorage=="object"&&typeof JSON=="object"){
		if(confirm("Delete Saved Email settings")){
			localStorage.removeItem("email");
			printMsg('Success',"Your Email login data has been delted!",'center',0);
		}
	}
	else{
		printMsg('Error',"Your browser does not even support saveing email settings, much less deleting them.",'center',0);
	}
}
function delScan(file){
	if(!confirm("Are you sure you want to delete "+file))
		return false;
	if(typeof XMLHttpRequest!='function'){
		return true;
	}
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var data=parseJSON(httpRequest.responseText);
				if(data['state']==0){
					printMsg('File Deleted',"The file "+data['file']+" has been removed.",'center',0);
					var del=getID(file);
					del.parentNode.removeChild(del);
				}
				else
					printMsg('Error 404',"Unable to find "+data['file']+" in the scans folder or permission is denied",'center',0);
			}
			else{
				printMsg('Error '+httpRequest.status,'Got a '+httpRequest.status+' error<br/>If you don\'t know what that means and want to know click <a target="_blank" href="http://www.w3.org/Protocols/HTTP/HTRESP.html">here</a>.','center',0);
			}
		}
	};
	httpRequest.open('GET', 'cleaner.php?file='+file);
	httpRequest.send(null);
	return false;
}
