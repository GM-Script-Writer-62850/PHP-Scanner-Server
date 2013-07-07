var ias, previewIMG, scanners, paper, filesLst={},TC='textContent';// TC can be changed to 'innerText' see header.php
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
		zIndex: 1,
		rotating: false
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
function pre_scan(form,ias){
	previewIMG.style.zIndex=-1;
	previewIMG.nextSibling.removeAttribute('style');
	previewIMG.parentNode.style.height=previewIMG.offsetHeight+3+'px';
	form.loc_maxW.value=previewIMG.offsetWidth;
	form.loc_maxH.value=previewIMG.offsetHeight;
	ele=getID('select');
	if(ele)
		ele.style.display='none';
	if(!document.scanning.scanner)
		return;
	if(document.scanning.scanner.disabled){
		document.scanning.scanner.removeAttribute('disabled');
		setTimout(function(){document.scanning.scanner.setAttribute('disabled','disabled');},250);
	}
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
	if(document.scanning.scanner.value!=json['scanner']&&document.scanning.scanner.disabled){
		var str='';
		for(var i in json){
			str+="&"+i+'='+encodeURIComponent(json[i]);
		}
		document.location.href='index.php?page=Scan&action=restore'+str;
		return;
	}
	for(var i in json){
		document.scanning[i].value=json[i];
		sendE(document.scanning[i],'change');
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
	if(ias.getOptions()["rotating"])
		return false;
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
function lastScan(scan,preview,scanner,ele){
	generic=scan.slice(5);
	previewIMG.src='scans/'+preview;
	ias.setOptions({'enable': true });
	ias.update();
	getID('sel').removeAttribute('style');
	document.scanning.scanner.value=scanner;
	sendE(document.scanning.scanner,'change');
	document.scanning.scanner.disabled='disabled';
	sendE(document.scanning.size,'change');
	ele.parentNode.parentNode.innerHTML='<h2>'+generic+'</h2><p><a class="tool icon download" href="download.php?file='+scan+'"><span class="tip">Download</span></a> '+
		'<a class="tool icon zip" href="download.php?file='+scan+'&compress"><span class="tip">Download Zip</span></a> '+
		'<a class="tool icon pdf" href="#" onclick="PDF_popup(\''+generic+'\');"><span class="tip">Download PDF</span></a> '+
		'<a class="tool icon print" href="print.php?file='+scan+'" target="_blank"><span class="tip">Print</span></a> '+
		'<a class="tool icon del" href="index.php?page=Scans&amp;delete=Remove&amp;file='+generic+'" onclick="return confirm(\'Delete this scan\')"><span class="tip">Delete</span></a> '+
		'<a class="tool icon edit" href="index.php?page=Edit&amp;file='+generic+'"><span class="tip">Edit</span></a> '+
		'<a class="tool icon view" href="index.php?page=View&amp;file='+scan+'"><span class="tip">View</span></a> '+
		'<a class="tool icon upload" href="#" onclick="return upload(\''+scan+'\')"><span class="tip">Upload to Imgur</span></a> '+
		'<a href="#" onclick="return emailManager(\''+scan+'\');" class="tool icon email"><span class=\"tip\">Email</span></a> '+
		'<span class="tool icon recent-off"><span class="tip">Last Scan (Disabled)</span></span></p>';
}
function encodeHTML(string){// http://stackoverflow.com/questions/24816/escaping-html-strings-with-jquery#answer-12034334
	var entityMap={
		"&": "&amp;",
		"<": "&lt;",
		">": "&gt;",
		'"': '&quot;',
		"'": '&#39;',
		"/": '&#x2F;'
	};
	return String(string).replace(/[&<>"'\/]/g,function(s){return entityMap[s];});
}
function checkScanners(){
	if(typeof XMLHttpRequest!='function'||typeof JSON=='undefined'){
		return printMsg('Sorry',"Your browser does not support the XMLHttpRequest function and the JSON object so this page can not check if the scanner is in-use or not in real time.<br/>You have 3 choices: Ignore This, update your browser, and switch browsers",'center',0);
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
						delete(scan[i]["UUID"]);
						str+='<option'+(scan[i]["INUSE"]==1?' disabled="disabled"':'')+' class="'+encodeHTML(JSON.stringify(scan[i]))+'" value="'+scan[i]["ID"]+'">'+scan[i]["NAME"]+' on '+loc+'</option>';
					}
					scanners=scan;
					loc=document.scanning.scanner.selectedIndex;
					if(document.all)// http://support.microsoft.com/kb/276228
						document.scanning.scanner.parentNode.innerHTML='<p><select onchange="scannerChange(this)" style="width:238px;" name="scanner">'+str+'</select></p>';
					else
						document.scanning.scanner.innerHTML=str;
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
	if(r!=-1&& typeof ele.insertBefore=='function')
		ele.insertBefore(div,ele.childNodes[0]);
	else
		ele.appendChild(div);
	div.style.height='auto';
	return false;
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
		printMsg('Invald Javascript Object Notation:','<textarea onclick="this.select()" style="width:100%;height:80px;">'+encodeHTML(jsonTXT)+'</textarea><br/>If you are reading this please report it as a bug. Please copy/paste the above, something as simple as a line break can cause errors here. If want to read this I suggest pasting it onto <a target="_blank" href="http://jsonlint.com/">jsonlint.com</a>.','center',0);
	}
}
function scannerChange(ele){
	var info=parseJSON(ele.childNodes[ele.selectedIndex].className);
	var html='',text;
	sources=info['SOURCE'].split('|');
	for(i=0,s=sources.length;i<s;i++){
		switch(sources[i]){
			case 'ADF': text='Automatic Document Feeder';break;
			case 'Auto': text='Automatic';break;
			default: text=sources[i];
		}
		html+='<option value="'+sources[i]+'">'+text+'</option>';
	}
	if(document.all)// http://support.microsoft.com/kb/276228	
		document.scanning.source.parentNode.innerHTML='<select name="source" class="title" onchange="sourceChange(this)">'+html+'</select>';
	else
		document.scanning.source.innerHTML=html;
	if(document.scanning.source.value=='Inactive')
		document.scanning.source.setAttribute('disabled','disabled');
	else
		document.scanning.source.removeAttribute('disabled');
	sourceChange(document.scanning.source);
}
function sourceChange(ele){
	var info,text,html,html2,html3,dpi;
	info=document.scanning.scanner;
	info=parseJSON(info.childNodes[info.selectedIndex].className);
	// Change Mode
	html='';
	modes=info['MODE-'+ele.value].split('|');
	for(i=modes.length-1;i>-1;i--){
		switch(modes[i]){
			case 'Gray':
		  		text='Grayscale';break;
			case 'Lineart':
		  		text='Line Art';break;
			default:
				text=modes[i];
		}
		html+='<option value="'+modes[i]+'">'+text+'</option>';
	}
	// Change Paper Size
	width=info['WIDTH-'+ele.value];
	height=info['HEIGHT-'+ele.value];
	html2='<option value="full" title="'+width+' mm x '+height+' mm">Full Scan: '+roundNumber(width/25.4,2)+'" x '+roundNumber(height/25.4,2)+'"</option>';
	for(var i in paper){
		if(width>=paper[i]['width']&&height>=paper[i]['height'])
			html2+='<option value="'+paper[i]['width']+'-'+paper[i]['height']+'" title="'+paper[i]['width']+' mm x '+paper[i]['height']+' mm">'+i+': '+roundNumber(paper[i]['width']/25.4,2)+'" x '+roundNumber(paper[i]['height']/25.4,2)+'"</option>';
	}
	// Change Quality
	html3='';
	dpi=info['DPI-'+ele.value].split('|');
	for(var i=0,max=dpi.length;i<max;i++)
		html3+='<option value="'+dpi[i]+'">'+dpi[i]+' '+(isNaN(dpi[i])?'':'DPI')+'</option>';
	// Apply Changes
	if(document.all){// http://support.microsoft.com/kb/276228
		document.scanning.size.parentNode.innerHTML='<select onchange="paperChange(this);" name="size">'+html2+'</select>';
		document.scanning.mode.parentNode.innerHTML='<select name="mode" class="title">'+html+'</select>';
		document.scanning.quality.parentNode.innerHTML='<select name="quality" class="upper">'+html3+'</select>';
	}
	else{
		document.scanning.size.innerHTML=html2;
		document.scanning.mode.innerHTML=html;
		document.scanning.quality.innerHTML=html3;
	}
	if(info['DUPLEX-'+ele.value])
		getID('duplex').removeAttribute('style');
	else
		getID('duplex').style.display='none';
	sendE(document.scanning.size,'change');
}
function paperChange(ele){
	ele.parentNode.nextSibling[TC]=ele.childNodes[ele.selectedIndex].title;
	if(ele.value=='full'){
		document.scanning.ornt.selectedIndex=0;
		document.scanning.ornt.disabled='disabled';
		return;
	}
	var json=document.scanning.scanner.childNodes[document.scanning.scanner.selectedIndex].className;
	json=parseJSON(json);
	var width=json['WIDTH-'+document.scanning.source.value];
	var height=json['HEIGHT-'+document.scanning.source.value];
	// Set Orientation
	var paper=ele.value.split('-');
	if(Number(paper[0])>height||Number(paper[1])>width){
		document.scanning.ornt.selectedIndex=0;
		document.scanning.ornt.disabled='disabled';
	}
	else
		document.scanning.ornt.removeAttribute('disabled');
}
function rotateChange(ele){
	var val=ele.value;
	ele.nextSibling[TC]=(val==180?'Upside-down':(val<0?'Counterclockwise':'Clockwise'));
	var prefixes = 't WebkitT MozT OT msT'.split(' ');
	for(var prefix in prefixes){
		if(typeof document.body.style[prefixes[prefix]+'ransform']!="undefined"){
			prefix=prefixes[prefix]+'ransform';
			break;
		}
	}
	if(typeof prefix=="number"||val==0)
		return;
	ele=previewIMG;
	if(ele.src.indexOf('inc/images/blank.gif')>-1)
		return;
	ias.setOptions({ "hide": true, "disable": true, "fadeSpeed": false, "rotating": true });
	ele.style[prefix]='rotate('+val+'deg)';// To DO add scale(X%)
	setTimeout(function(){// We can not leave it rotated, it brutally screws up cropping
		ele.style[prefix]='';
		setTimeout(function(){
			ias.setOptions({ "hide": false, "disable": false, "fadeSpeed": 850, "rotating": false });
			if(document.scanning.loc_width.value>0&&document.scanning.loc_height.value>0)
				setRegion(ias);
		},800);// 800ms is the animation duration in the css
	},2000);// Should be long enough to see how it looks, given there is a 800ms animation
}
function changeBrightContrast(){// Webkit based only :(
	// Does not work properly so lets disable it: brightness/contrast have a screwed up/illogical max %
	//if(typeof document.body.style.webkitFilter!='string') 
		return;
	if(previewIMG.src.indexOf('inc/images/blank.gif')>-1)
		return;
	previewIMG.style.webkitFilter='brightness('+(Number(document.scanning.bright.value)+100)+'%) contrast('+(Number(document.scanning.contrast.value)+100)+'%)';
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
	if(show)
		div.style.display='inline';
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
				debugLink[TC]='Show';
			return false;
		}
		else{
			debug.style.display='inline';
			Set_Cookie( 'debug', true, 1, '/', '', '' );
			if(keyboard&&debugLink)
				debugLink[TC]='Hide';
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
	// not all browsers support efficient code, I am looking at you IE <.<
	try{// most efficient
		var icons=document.evaluate("//a[contains(@class,'tool icon')][contains(@class,'-off')]",document,null,6,null);
		for(var i=0;i<icons.snapshotLength;i++){
			var icon=icons.snapshotItem(i);
			icon.href="javascript:void();";
			icon.setAttribute("onclick","return false;");
			icon.setAttribute('style','cursor:inherit;');
			icon.childNodes[0][TC]+=" (Disabled)";
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
				icons[i].childNodes[0][TC]+=" (Disabled)";
			}
		}
	}
}
function PDF_popup(files){
	if(typeof files=='string'){
		files='{"'+files.replace(/"/g,'\"')+'":1}';
	}
	else{
		if(typeof JSON=='undefined')
			return printMsg('Sorry',"Your browser does not support the JSON object so you can not download scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
		var ct=0;
		for(var i in files){
			ct++;
			break;
		}
		if(ct==0)
			return printMsg('Error','No files selected','center',-1);
		files=JSON.stringify(files);
	}
	files=encodeURIComponent(files);
	getID("blanket").childNodes[0].innerHTML='How would you prefer for your PDF download?<br/>\
		A scan placed on the page with a title or<br/>\
		a would you prefer the scan as the page.<br/>\
		<a href="download.php?json='+files+'&type=pdf" onclick="setTimeout(\'toggle(\\\'blanket\\\')\',100);">\
		<button><img src="inc/images/pdf-scaled.png" width="106" height="128"/></button></a>\
		<a href="download.php?json='+files+'&type=pdf&full" onclick="setTimeout(\'toggle(\\\'blanket\\\')\',100);">\
			<button><img src="inc/images/pdf-full.png" width="106" height="128"/></button></a>\
		<br/><a style="text-decoration:none;" href="download.php?json='+files+'&type=pdf&raw" onclick="setTimeout(\'toggle(\\\'blanket\\\')\',100);">\
		<input type="button" value="I don\'t care just give me a PDF" style="width:261px"/></a>\
		<br/><input type="button" value="Cancel" style="width:261px;" onclick="toggle(\'blanket\')"/>';
	popup('blanket',290);
	return false;
}
function toggleFile(file){
	if(!filesLst[file[TC]]){
		filesLst[file[TC]]=1;
		file.setAttribute('selected',true);
	}
	else{
		delete(filesLst[file[TC]]);
		file.setAttribute('selected',false);
	}
}
function bulkDownload(link,type){
	if(typeof JSON=='undefined')
		return printMsg('Sorry',"Your browser does not support the JSON object so you can not download scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
	var ct=0;
	for(var i in filesLst){
		ct++;
		break;
	}
	if(ct>0){
		link.href="download.php?type="+encodeURIComponent(type)+"&json="+encodeURIComponent(JSON.stringify(filesLst));
		return true;
	}
	else
		return printMsg('Error','No files selected','center',-1);
}
function bulkPrint(link){
	if(typeof JSON=='undefined')
		return printMsg('Sorry',"Your browser does not support the JSON object so you can not print scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
	var ct=0;
	for(var i in filesLst){
		ct++;
		break;
	}
	if(ct>0){
		window.open("print.php?json="+encodeURIComponent(JSON.stringify(filesLst)));
		return true;
	}
	else
		return printMsg('Error','No files selected','center',-1);
}
function bulkDel(){
	var p='Delete all of these';
	for(var i in filesLst)
		p+="\n"+i;
	if(p.length==19)
		return printMsg('Error','No files selected','center',-1);
	if(!confirm(p))
		return false;
	var files2=filesLst;
	for(var i in files2){
		if(delScan(i,false))
			return printMsg('Error','Unsupported Browser','center',-1);
		delete(filesLst[i]);
	}
	return false;
}
function bulkView(link){
	if(typeof JSON=='undefined')
		return printMsg('Sorry',"Your browser does not support the JSON object so you can not view scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
	var ct=0;
	for(var i in filesLst){
		ct++;
		break;
	}
	if(ct>0){
		document.location="index.php?page=View&json="+encodeURIComponent(JSON.stringify(filesLst));
		return true;
	}
	else
		return printMsg('Error','No files selected','center',-1);
}
function storeImgurUploads(img){
	if(typeof localStorage!="object")
		return false;
	var data=localStorage.getItem('imgur'),id,b,a,ele,ele2,div,f;
	data=parseJSON(data==null?'{}':localStorage.getItem('imgur'));
	ele=getID('imgur-uploads');
	if(!ele){
		ele=getID('imgur-box-setup');
		if(ele){
			ele2=document.createElement('div');
			ele2.className='box box-full';
			ele2.id='imgur-uploads';
			ele2.innerHTML='<h2>Imgur Uploads<a href="#" onclick="return imgurDel(\'imgur-uploads\',false)" class="tool icon del"><span class="tip">Hide</span></a></h2>';
			ele.parentNode.insertBefore(ele2,ele);
			ele=ele2;
		}
	}
	for(var i in img){
		if(typeof img[i] == 'boolean')
			continue;
		id=img[i]["data"]["id"];
		b="http://i.imgur.com/"+id;
		a='.jpg';
		f=img[i]["data"]["file"];
		data[f]={
			"original": img[i]["data"]["link"],
			"imgur_page": "http://imgur.com/"+id,
			"delete_page": "http://imgur.com/delete/"+img[i]["data"]["deletehash"],
			"small_square": b+"s"+a,
			"large_thumbnail": b+"l"+a,
			"small_thumbnail": b+"t"+a,
			"medium_thumbnail": b+"m"+a,
			"huge_thumbnail": b+"h"+a,
			"big_square": b+"b"+a
		};
		if(!ele)
			continue;
		div=document.createElement('div');
		div.className="box";
		div.id='imgur-'+id;
		div.innerHTML='<h2><span>'+f.slice(5,f.lastIndexOf('.'))+'</span><a href="#" onclick="return '+
			'imgurDel(\'imgur-'+id+'\',\''+f+'\')" class="tool icon del"><span class="tip">Hide</span></a></h2>'+
			'<img src="'+data[f]['big_square']+'" onclick="imgurPopup(\''+f+'\',null)"/>';
		ele.appendChild(div);
	}
	localStorage.setItem('imgur',JSON.stringify(data));
	
}
function bulkUpload(){
	var ct=0;
	for(var i in filesLst){
		ct++;
		if(ct>1)
			break;
	}
	if(ct==1)
		return upload("Scan_"+i);
	else if(ct==0)
		return printMsg('Error','No files selected','center',-1);
	if(typeof JSON=='undefined'||typeof XMLHttpRequest!='function')
		return printMsg('Sorry',"Your browser does not support the JSON object and the XMLHttpRequest function so you can not upload scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
	var title=prompt("Upload New Album to imgur.com\nYou can give it a title (optional):",'Scan Compilation');
	if(title==null)
		return false;
	var now=new Date().getTime();
	printMsg('Uploading<span id="upload-'+now+'"></span>','Please Wait...<br/>This could take a while depending on the file size of the scan and the upload speed at '+document.domain+'<br/>When imgur is under heady load this can take a very long time','center',0);
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var json=parseJSON(httpRequest.responseText);
				if(json['success']){
					printMsg('Success','All '+json['images'].length+' image(s) were uploaded to your new <a href="http://imgur.com/a/'+
						json['album']['data']['id']+'" target="_blank">album</a><br>You delete hash is <i>'+json['album']['data']['deletehash']+
						'</i>. Sorry, I do not know the URL to delete albums. XP','center',0);
					storeImgurUploads(json['images']);
				}
				else{
					if(json['images'].length==0){
						if(!json['album'])
							printMsg('Failed to create Album','Unknown connection error','center',0);
						else
							printMsg('Failed to populate Album',json['album']['data']['error']+(json['album']['status']==200?'':'<br/>'+json['album']['status']+' Error detected'),'center',0);
					}
					else{
						printMsg('Image Upload Error',(json['images'].length-2)+' image(s) were uploaded to your <a href="http://imgur.com/a/'+
							json['album']['data']['id']+'" target="_blank">album</a> before a error occurred<br>You delete hash is <i>'+
							json['album']['data']['deletehash']+'</i>. Sorry, I do not know the URL to delete albums. XP','center',0);
						if(json['images'].length-2>0)
							storeImgurUploads(json['images']);
					}
				}
				var btn=getID('upload-'+now);
				if(btn)
					sendE(btn.nextSibling,'click');
			}
			else
				printMsg('Upload Error','Got a '+httpRequest.status+' error<br/>If you don\'t know what that means and want to know click <a target="_blank" href="http://www.w3.org/Protocols/HTTP/HTRESP.html">here</a>.','center',0);
		}
	};
	httpRequest.open('GET', 'imgur.php?anon=true&album='+encodeURIComponent(title)+'&nocache='+now+'&files='+encodeURIComponent(JSON.stringify(filesLst)));
	httpRequest.send(null);
	return true;
}
function selectScans(b){
	try{
		var scans=document.evaluate("//div[@id='scans']/div/h2[@selected='"+b+"']",document,null,6,null);
		for(var i=0;i<scans.snapshotLength;i++)
			toggleFile(scans.snapshotItem(i));
	}
	catch(e){// Screw you IE, screw you
		var list=getID('scans').getElementsByTagName('h2'),stat;
		for(var i=0,ct=list.length;i<ct;i++){
			stat=list[i].getAttribute('selected');
			if(stat==b.toString())
				toggleFile(list[i]);
		}
	}
	return false;
}
function upload(file){
	if(typeof XMLHttpRequest!='function')
		return printMsg('Sorry',"Your browser does not support the XMLHttpRequest function so you can not upload scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
	if(getID(file)){
		popup('blanket',365);
		return false;
	}
	var test=true;
	if(typeof localStorage=='object'){
		json=localStorage.getItem('imgur');
		json=parseJSON(json==null?'{}':json);
		if(json[file]){
			test=false;
			if(confirm("'"+file.substr(5)+"' has been uploaded already!\nOK = Upload Again\nCancel = View Upload dialog")===false)
				return imgurPopup(file,json[file]);
		}
	}
	if(test){
		if(confirm("Upload '"+file.substr(5)+"' to imgur.com")===false)
			return false;
	}
	var now=new Date().getTime();
	printMsg('Uploading<span id="upload-'+now+'"></span>','Please Wait...<br/>This could take a while depending on the file size of the scan and the upload speed at '+document.domain,'center',0);
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var json=parseJSON(httpRequest.responseText);
				if(!json['images'][0])
					printMsg('Upload Error','Failed to connect to imgur','center',0);
				else if(json['images'][0]['error'])
					printMsg('Upload Error',json['images'][0]['error']['message'],'center',0);
				else{
					var id=json['images'][0]["data"]["id"],b="http://i.imgur.com/"+id,a='.jpg';
					var links={"original": json['images'][0]["data"]["link"],
						"imgur_page": "http://imgur.com/"+id,
						"delete_page": "http://imgur.com/delete/"+json['images'][0]["data"]["deletehash"],
						"small_square": b+"s"+a,
						"large_thumbnail": b+"l"+a,
						"small_thumbnail": b+"t"+a,
						"medium_thumbnail": b+"m"+a,
						"huge_thumbnail": b+"h"+a,
						"big_square": b+"b"+a};
					imgurPopup(file,links);
					storeImgurUploads(json['images']);
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
	httpRequest.open('GET', 'imgur.php?anon=true&image=true&nocache='+now+'&file='+encodeURIComponent(file));
	httpRequest.send(null);
	return false;
}
function imgurPopup(file,links){
	if(links==null){
		links=parseJSON(localStorage.getItem('imgur'));
		links=links[file];
	}
	getID("blanket").childNodes[0].innerHTML='<h2 style="font-size:12px;">'+file.substr(5)+' is on Imgur</h2>'+
		'<div id="imgur-data"><div><img id="'+encodeHTML(file)+'" style="float:left;margin-right:5px;" src="'+links['small_square']+'" width="90" height="90"/>'+
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
function imgurDel(id,img){
	if(img===false){
		if(confirm("Are you sure you want to hide ALL imgur uploads?\nThis only deletes the images from this page,\nnot imgur.")===false)
			return false;
		localStorage.removeItem('imgur');
	}
	else if(confirm("Are you sure you want to hide that image?\nThis only deletes the image from this page,\nnot imgur.")===false)
		return false;
	e=getID(id);
	if(e)
		e.parentNode.removeChild(e);
	e=localStorage.getItem('imgur');
	if(e==null)
		return false;
	e=parseJSON(e);
	delete(e[img]);
	e=JSON.stringify(e);
	if(e.length>2)
		localStorage.setItem('imgur',e);
	else{
		localStorage.removeItem('imgur');
		e=getID('imgur-uploads');
		if(e)
			e.parentNode.removeChild(e);
	}
	return false;
}
function emailManager(file){
	var storeSupport=(typeof localStorage=="object"&&typeof JSON=="object"?true:false),data=false;
	if(file==null&&!storeSupport)
		return printMsg('Sorry',"Your browser does not support saving email settings<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
	if(typeof XMLHttpRequest!='function')
		return printMsg('Sorry',"Your browser does not support the XMLHttpRequest function so you can not email scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
	if(file=='Scan_Compilation'){
		if(typeof JSON=='undefined')
			return printMsg('Sorry',"Your browser does not support the JSON object so you can not upload scans with that button.<br/>You have 3 choices: ignore, update your browser, and switch browsers",'center',0);
		var files_ct=0;
		for(var i in filesLst)
			files_ct++;
		if(files_ct==0)
			return printMsg('Error','No files selected','center',-1);
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
	'<input type="hidden" name="'+(file=='Scan_Compilation'?'json':'file')+'" value="'+(file=='Scan_Compilation'?encodeHTML(JSON.stringify(filesLst)):file)+'"/>'+
	'<div class="label">'+(file?'From':'Email')+':</div><div class="control"><input type="text" onchange="configEmail(this.value)" name="from" value="johndoe@gmail.com"/></div>'+
	(file?'<div class="label">Subject:</div><div class="control"><input type="text" name="title" value="[Scanned '+(file=='Scan_Compilation'?'Compilation':(file.substr(-3)!='txt'?'Image':'Text'))+'] '+(file=='Scan_Compilation'?files_ct+' Scans':file.substr(5))+'"/></div>':'')+
	(file?'<div class="label">To:</div><div class="control"><input type="text" name="to" value=""/></div>':'')+
	'<div class="label">Password:</div><div class="control"><input type="password" name="pass" ondblclick="this.type=(this.type==\'text\'?\'password\':\'text\')" autocomplete="off"/></div>'+
	'<div class="label">Host:</div><div class="control"><input type="text" name="host" value="smtp.gmail.com"/></div>'+
	'<div class="label">Prefix:</div><div class="control tool"><select name="prefix"><option value="ssl">SSL</option><option value="tls">TLS</option><option value="plain">None</option></select><span class="tip" style="display:none"></span></div>'+
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
		data=parseJSON(data);
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
	var data={},val;
	if(ele.from.value.indexOf('@')==-1){
		alert('Invalid From Email Address');
		return false;
	}
	val=(ele.json?ele.json.value:ele.file.value);
	if(val!='null'){
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
	if(val!='null')
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
					var s='display:none';
					var t=document.email.prefix.nextSibling;
					if(data["prefix"]=="SSL"||data["prefix"]=="STARTTLS"){
						document.email.prefix.selectedIndex=0;
						t.setAttribute('style',s);
					}
					else if(data["prefix"]=="TLS"){
						document.email.prefix.selectedIndex=1;
						t.setAttribute('style',s);
					}
					else if(data["prefix"]=="plain"){
						document.email.prefix.selectedIndex=2;
						t.setAttribute('style',s);
					}
					else{
						t.innerHTML='The autoconfigure<br/>database said<br>something about<br/>"'+data["prefix"]+'"';
						t.removeAttribute('style');
					}
					if(data['type']!='smtp')
						printMsg('Please File a bug report','Your email provider ('+addr+') is not supported, if you do support can be added for it','center',-1);
				}
			}
		}
	};
	httpRequest.open('GET', 'email.php?domain='+encodeURIComponent(addr));
	httpRequest.send(null);
}
function sendEmail(ele){
	if(typeof XMLHttpRequest!='function')
		return printMsg('Error','Your browser does not support <a href="http://www.w3schools.com/xml/xml_http.asp" target="_blank">XMLHttpRequest</a>, so you can not use this feature','center',0);
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
	var params = (ele.file?"file="+encodeURIComponent(ele.file.value):"json="+encodeURIComponent(ele.json.value))+
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
	else
		printMsg('Error',"Your browser does not even support saveing email settings, much less deleting them.",'center',0);
}
function delScan(file,prompt){
	if(prompt){
		if(!confirm("Are you sure you want to delete "+file))
			return false;
	}
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
					if(filesLst[file])
						delete(filesLst[file]);
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
function updateCheck(vs,e){
	if(typeof XMLHttpRequest!='function')
		printMsg('Error','Your browser does not support <a href="http://www.w3schools.com/xml/xml_http.asp" target="_blank">XMLHttpRequest</a>, so you can not use this feature','center',0);
	if(e===true)
		printMsg('Update Available','Version '+vs+' is available for <a target="_blank" href="https://github.com/GM-Script-Writer-62850/PHP-Scanner-Server/wiki/Change-Log">download</a>','center',-1);
	if(e)
		e.setAttribute('disabled','disabled');
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				if(e)
					e.removeAttribute('disabled');
				var data=parseJSON(httpRequest.responseText);
				if(data["state"]==1)
					printMsg('Update Available','Version '+data["version"]+' is available for <a target="_blank" href="https://github.com/GM-Script-Writer-62850/PHP-Scanner-Server/wiki/Change-Log">download</a>','center',-1);
				else if(data["state"]==0&&e)
					printMsg('Up to Date','Your current version of '+data["version"]+' is the latest available','center',-1);
				else if(data["state"]==-1&&e)
					printMsg('Custom Copy','Your current version is newer than the latest version','center',-1);
				else if(e)
					printMsg('Error','There was a error connecting to <a href="http://github.com">github.com</a>','center',-1);
			}
			else if(e){
				printMsg('Error '+httpRequest.status,'Failed to connect to '+document.domain,'center',-1);
				e.removeAttribute('disabled');
			}
		}
	};
	httpRequest.open('GET', 'download.php?update='+encodeURIComponent(vs)+'&'+new Date().getTime());
	httpRequest.send(null);
}
function enableColumns(ele,e,b){ // They work flawlessly in Firefox so it does not call this function
	if(e!=null){
		ele=getID(ele);
		if(ele.className){// there is a class name
			if(ele.className=='columns'){
				ele.removeAttribute('class');// disable
				if(e){
					e.nextSibling[TC]='Enable';
					Delete_Cookie( 'columns', '/', '' );
				}
			}
			else if(ele.className.indexOf('columns')==-1){
				ele.className+=' columns';// enable
				if(e){
					e.nextSibling[TC]='Disable';
					Set_Cookie( 'columns', true, 1, '/', '', '' );
				}
			}
			else{
				ele.className=ele.className.substring(0,ele.className.indexOf(' columns'));// Disable preserve original class name
				if(e){
					e.nextSibling[TC]='Enable';
					Delete_Cookie( 'columns', '/', '' );
				}
			}
		}
		else{// enable
			ele.className='columns';
			e.nextSibling[TC]='Disable';
			Set_Cookie( 'columns', true, 1, '/', '', '' );
		}
		return false;
	}
	else if(typeof document.body.style.WebkitColumnGap=="string"||typeof document.body.style.columnGap=="string"){
		printMsg('CSS3 Columns','Your browser supports them, but they do not work as expected.<br/>'+
			'You can try them out by clicking <span class="tool"><a href="#" onclick="return enableColumns(\''+ele+'\',this,null);">here</a><span class="tip">'+(b?'Disable':'Enable')+'</span></span>.<br/>'+
			'Oh, and by the way they work in <a href="http://www.mozilla.org/en-US/firefox/all.html" target="_blank">Firefox</a> flawlessly.','center',-1);
		if(b)
			enableColumns(ele,false,null)
	}
}
function login(form){
	if(typeof XMLHttpRequest!='function')
		return printMsg('Error','Your browser does not support <a href="http://www.w3schools.com/xml/xml_http.asp" target="_blank">XMLHttpRequest</a>, so you can not use this feature','center',0);
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var json=parseJSON(httpRequest.responseText);
				printMsg(json["error"]?'Error':'Success',json["message"]+(json["error"]?'':"<br/>You may now access the server by clicking links"),'center',0);
			}
			else{
				printMsg('Sending Error','Got a '+httpRequest.status+' error<br/>If you don\'t know what that means and want to know click <a target="_blank" href="http://www.w3.org/Protocols/HTTP/HTRESP.html">here</a>.','center',0);
			}
			sendE(getID('email-'+now).nextSibling,'click');
		}
	};
	httpRequest.open('POST', "inc/login.php");
	var params = "json=1"+
		"&mode="+encodeURIComponent(form.mode.value)+
		"&name="+encodeURIComponent(form.name.value)+
		"&pass="+encodeURIComponent(form.pass.value);
	if(form.auth)
		params+="&auth="+encodeURIComponent(form.auth.value);
	if(form.newp)
		params+="&newp="+encodeURIComponent(form.newp.value);
	httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	httpRequest.setRequestHeader("Content-length", params.length);
	httpRequest.setRequestHeader("Connection", "close");
	httpRequest.send(params);
	return false;
}
document.onkeyup=function(event){
	if(event.ctrlKey&&(event.which==68||event.keyCode==68))// [Ctrl]+[Shift]+[D]
		toggleDebug(true);
}
