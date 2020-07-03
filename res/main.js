"use strict";
var ias, previewIMG, scanners, checkTimeout, rotateTimer, paper, ruler=false, ctxA, ctxB, filesLst={};
$(document).ready(function (){
	var e=$('img[title="Preview"]');
	previewIMG=e[0];
	if(previewIMG){
		ias=e.imgAreaSelect({
			handles: true,
			onSelectEnd: storeRegion,
			instance: true,
			enable: true,
			disable: (!ruler&&(previewIMG.src.indexOf('res/images/blank.gif')>-1)?true:false),
			fadeSpeed: 850,
			parent: 'div#select',
			zIndex: 1,
			rotating: false,
		});
		if(previewIMG){
			if(previewIMG.src.indexOf('res/images/blank.gif')>-1&&!ruler){
				getID('sel').style.display='none';
				document.scanning.rotate.title="If you plan to crop do this on the final scan";
			}
		}
	}
	else if(typeof($().ColorPicker)=="function"){
		var pickers=$('.colorPicker').ColorPicker({
			onSubmit:function(hsb,hex,rgb,el){
				el.value=hex;
				$(el).ColorPickerHide();
				sendE(el,'change');
			},
			onShow:function(colpkr){
				$(colpkr)['fade'+(colpkr.style.display=='block'?'Out':'In')](800);
				return false;
			},
			onHide: function(colpkr){
				$(colpkr).fadeOut(800);
				return false;
			}
		});
		document.theme.reset();
		for(var i=0;i<11;i++)// 11 is the total number of color input fields
			$(pickers[i]).ColorPickerSetColor(pickers[i].value);
	}
});
function getID(id){
	return document.getElementById(id);
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
function changeColor(x,save){
	var fields=Array('BG','LK','LC','PB','HB','HT','PT','BB','BT','AH','AT'),str='';
	if(typeof(x)=='string'){
		x=x.split('.');
		for(var i in fields){
			document.theme[fields[i]+'_COLOR'].value=x[i];
			document.theme[fields[i]+'_COLOR'].style.backgroundColor='#'+x[i];
			$(document.theme[fields[i]+'_COLOR']).ColorPickerSetColor(x[i]);
		}
	}
	else if(!save)
		x.style.backgroundColor='#'+x.value;
	for(var i in fields)
		str+=document.theme[fields[i]+'_COLOR'].value+'.';
	var O=getID('style_old'),
		N=getID('style_new');
	if(N.textContent.length>0)
		O.textContent=N.textContent;
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				N.textContent=httpRequest.responseText.replace(/url\("images/g,'url("res/images');
				if(save&&x==null)
					printMsg('Saved: Your color scheme has been saved',"This is your theme's data you can put in the configuration file:<br/>"+str.slice(0,-1).toUpperCase()+
						'<br/>Themes will last 10 years or untill you delete the cookie.','center',-1);
			}
			else
				printMsg('Error '+httpRequest.status,' Failed to connect to '+document.domain,'center',-1);
		}
	};
	httpRequest.open('GET', 'res/style.php?theme='+str.slice(0,-1)+(save?'&save='+new Date().getTime():''));
	httpRequest.send(null);
	document.body.setAttribute('onbeforeunload',save?"if(!document.cookie)return confirm('The color theme was not saved because you have cookies disabled\\nPress OK to leave or Cancel to stay')":"if(!confirm('You did not save your color scheme\\nPress OK to leave or Cancel to stay'))return false");
	return false;
}
function pre_scan(form){
	var ele=document.activeElement;
	if(ele.value!="Save" && ele.type=="submit"){// If not saving settings
		previewIMG.style.zIndex=-1;
		previewIMG.nextSibling.removeAttribute('style');
		previewIMG.parentNode.style.height=previewIMG.offsetHeight+3+'px';
		form.loc_maxW.value=previewIMG.offsetWidth;
		form.loc_maxH.value=previewIMG.offsetHeight;
		ele=getID('select');
		if(ele)
			ele.style.display='none';
		if(!document.scanning.scanner)
			return true;
	}
	if(document.scanning.scanner.disabled){
		document.scanning.scanner.removeAttribute('disabled');
		setTimeout(function(){document.scanning.scanner.setAttribute('disabled','disabled');},250);
	}
	clearTimeout(checkTimeout);
	checkTimeout=false;
	return true;
}
function addRuler(){
	var container,span;
	ctxA=document.createElement('canvas');
	if(!ctxA.getContext)
		return canvas=null;
	container=getID('preview_img');
	container.className='tool';
	container.appendChild(getID('select'));
	span=document.createElement('span');
	span.className="tip center";
	if(I==10||I==25.4)
		span.textContent=(I==10?'Centimeters':'Inches');
	else
		span.innerHTML="Millimeters<hr/>"+I;
	container.appendChild(span);
	span.style.left=-1-$(span).outerWidth()+'px';
	span=document.createElement('span');
	ctxA.width=450;
	ctxA.height=60;
	span.appendChild(ctxA);
	span.className='tip rule';
	container.appendChild(span);
	span=span.cloneNode(true);
	ctxB=span.childNodes[0];
	ctxB.width=60;
	ctxB.height=471;
	container.appendChild(span);
	ruler=true;
}
function updateRulers(XY){
	if(!ruler) return;
	function drawLine(ctx,xStart,yStart,xEnd,yEnd){
		ctx.beginPath();
		ctx.moveTo(xStart,yStart);
		ctx.lineTo(xEnd,yEnd);
		ctx.stroke();
		ctx.closePath();
	}
	function drawRuler(ctx,size){
		var width=ctx.width,
			height=ctx.height,
			i,I,p,px,dec,big=false;
		ctx=ctx.getContext('2d');
		ctx.clearRect(0,0,width,height);
		ctx.fillStyle='#FFF';
		ctx.strokeStyle='#FFF';
		ctx.lineWidth=1;
		ctx.font="10px Arial";
		if(width>height){
			if(width/size<20)
				big=true;
			p=size<6?.125:(size<35?.25:(size>54?4:.5));
			for(i=0;i<=size;i+=p){
				px=parseInt(i/size*width)+.5;
				px=px>width?width-.5:px;
				I=parseInt(i);
				if(i==I){
					drawLine(ctx,px,height,px,height-40);
					if(!big||i/2==parseInt(i/2)){
						if(i>9)
							ctx.fillText(i,px+7>width?width-11:px-7,height-50);
						else
							ctx.fillText(i,i==0?0:(px+2.5>width?width-5:px-2.5),height-50);
					}
				}
				else{
					dec=i-I;
					drawLine(ctx,px,height,px,height-(dec==.5?20:(dec==.25||dec==.75?10:5)));
				}
			}
		}
		else{
			if(height/size<12)
				big=true;
			p=size<7?.125:(size<45?.25:(size>64?4:.5));
			for(i=0;i<=size;i+=p){
				px=parseInt(i/size*height)+.5;
				px=px>height?height-.5:px;
				I=parseInt(i);
				if(i==I){
					drawLine(ctx,width,px,width-40,px);
					if(!big||i/2==parseInt(i/2))
						ctx.fillText(i,i<10?3:0,i==0?6.5:(px+6>height?height:px+3));
				}
				else{
					dec=i-I;
					drawLine(ctx,width,px,width-(dec==.5?20:(dec==.25||dec==.75?10:5)),px);
				}
			}
		}
	}
	drawRuler(ctxA,XY[0]/I);
	drawRuler(ctxB,XY[1]/I);
}
function sendE(ele,e){
	var evt = document.createEvent("HTMLEvents");
	evt.initEvent(e, true, true);
	ele.dispatchEvent(evt);

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
	// Code to counter user stupidity and innocent mistakes, not that I have the right to call anyone stupid whit how bad my spelling is
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
	if(e.which==13){// Enter
		if(ias!=null)
			setRegion(ias);
		return false;
	}
	if(e.which==43){// +
		ele.value++;
		sendE(ele,'change');
		return false;
	}
	if(e.which==45){// -
		if(ele.value>0){
			ele.value--;
			sendE(ele,'change');
		}
		return false;
	}
	if(!isNaN(String.fromCharCode(e.which))||e.which==8||e.which==0)// number, backspace, or unknown
		return true;
	// anything else (mostly letters)
	return false;
}
function lastScan(data,ele,html){
	var generic=data.raw.slice(5),
		scan=data.scan;
	previewIMG.src='scans/thumb/'+data.preview;
	if(!ruler){
		ias.setOptions({'enable': true });
		ias.update();
		getID('sel').removeAttribute('style');
	}
	config(data.fields);
	ele.parentNode.parentNode.innerHTML='<h2>'+generic+'</h2><p>'+html+'</p>';
	document.scanning.scanner.disabled=true;
	document.scanning.reset.disabled=true;
	return false;
}
function buildScannerOptions(json){
	var str='',id,name,loc,sel=0;
	for(var i=0,m=json.length;i<m;i++){
		loc = json[i]["DEVICE"].substr(0,4)=="net:" ? json[i]["DEVICE"].split(':')[1] : document.domain;
		if(json[i]["SELECTED"])
			sel=i;
		str+='<option'+(json[i]["INUSE"]==1?' disabled="disabled"':'')+(json[i]["SELECTED"]?' selected="selected"':'')+' value="'+json[i]["ID"]+'">'+json[i]["NAME"]+' on '+loc+'</option>';
	}
	document.scanning.scanner.innerHTML=str;
	return sel;
}
function checkScanners(){
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){
				var scan=parseJSON(httpRequest.responseText);
				if(JSON.stringify(scanners)!=JSON.stringify(scan)){// Something changed
					var l=document.scanning.scanner.selectedIndex,oldDevice,newDevice,ele,index,found=false,inUse=false,update=(scanners.length==scan.length?false:true);
					oldDevice=scanners[l]['UUID']==null?scanners[l]["DEVICE"]:scanners[l]['UUID'];
					for(var i=0,m=scan.length;i<m;i++){// If update becomes true the list is rebuilt
						newDevice=scan[i]['UUID']==null?scan[i]["DEVICE"]:scan[i]['UUID'];
						if(scanners[i]&&!update){// Does the original list even have this many entries?
							if(newDevice==(scanners[i]['UUID']==null?scanners[i]["DEVICE"]:scanners[i]['UUID'])){// Is this scanner the same scanner as before
								ele=document.scanning.scanner.childNodes[i];
								l=ele.disabled;
								if(scan[i]['INUSE']!=(l?1:0))// Do I need to disable/enable this entry
									ele.disabled=!l;
							}
							else
								update=true;
						}
						else
							update=true;
						if(newDevice==oldDevice){// Found current scanner
							index=i;
							found=true;
							if(scan[i]['INUSE']==1)
								inUse=true;
						}
						if(found&&update)
							break;
					}
					scanners=scan;// Update global variable
					if(!found||update){// Do I need to rebuild the list
						buildScannerOptions(scan);
						if(found)
							document.scanning.scanner.selectedIndex=index;
						else{
							printMsg('Information',"The scanner you had selected is no longer available",'center',-1);
							sendE(document.scanning.scanner,'change');
						}
					}
					if(inUse)
						printMsg('Information',"The scanner currently selected is being used by someone right now.",'center',-1);
				}
			}
			if(checkTimeout!==false)
				checkTimeout=setTimeout(checkScanners,5000);
		}
	};
	httpRequest.open('GET', 'config/scanners.json?cacheBust='+new Date().getTime(), true);
	httpRequest.send(null);
}
function buildPrinterOptions(json,p,P){
	var printer,i,opt,val,DIV,SEL,OPT;
	p.innerHTML='';

	DIV=document.createElement('div');
	DIV.style.display='inline-block';
	DIV.innerHTML='<div class="label">Printer: </div><div class="control"></div>';
	SEL=document.createElement('select');
	SEL.name="printer";
	SEL.addEventListener('change',function(){
			buildPrinterOptions(json,p,this.value);
	},false);
	DIV.childNodes[1].appendChild(SEL);
	p.appendChild(DIV);
	for(printer in json['printers']){
		OPT=document.createElement('option');
		OPT.value=printer;
		OPT.textContent=printer+(json['locations'][printer]?' ('+json['locations'][printer]+')':'');
		if(printer==P)
			OPT.setAttribute('selected','selected');
		SEL.appendChild(OPT);
	}
	printer=P?P:SEL.value;

	DIV=document.createElement('div');
	DIV.style.display='inline-block';
	DIV.innerHTML='<div class="label">Quantity:</div><div class="control"></div>';
	SEL=document.createElement('select');
	SEL.name='quantity';
	for(i=0;i<100;i++){
		OPT=document.createElement('option');
		OPT.value=i+1;
		OPT.textContent=i+1;
		SEL.appendChild(OPT);
	}
	DIV.childNodes[1].appendChild(SEL);
	p.appendChild(DIV);

	for(i in json['printers'][printer]){
		DIV=document.createElement('div');
		DIV.style.display='inline-block';
		DIV.innerHTML='<div class="label">'+json['printers'][printer][i]['name']+':</div><div class="control"></div>';
		SEL=document.createElement('select');
		SEL.name=json['printers'][printer][i]['id'];
		for(val in json['printers'][printer][i]['value']){
			OPT=document.createElement('option');
			OPT.value=json['printers'][printer][i]['value'][val];
			OPT.textContent=json['printers'][printer][i]['value'][val];
			if(json['printers'][printer][i]['name']=="Output Mode"){// Human friendly names for Color option (at-least for HP Printers)
				if(OPT.textContent=="RGB")
					OPT.textContent="Color";
				else if(OPT.textContent=="CMYGray")
					OPT.textContent="High Quality Grayscale";
				else if(OPT.textContent=="KGray")
					OPT.textContent="Black Only Grayscale";
			}
			if(json['printers'][printer][i]['default']==json['printers'][printer][i]['value'][val])
				OPT.setAttribute('selected','selected');
			SEL.appendChild(OPT);
		}
		DIV.childNodes[1].appendChild(SEL);
		p.appendChild(DIV);
	}
}
function genPrintOptions(p){
	p=p.getElementsByTagName('select');
	var i,opt=Array();
	for(i=p.length-1;i>0;i--){
		if(p[i].name!='quantity')
			opt.push(p[i].name+'='+p[i].value);
	}
	return opt.join();
}
function submitPrint(o,limit,test){
	if(o.format.value=='pdf'||test===true){
		if(o.pdf.files[0].size>limit){
			printMsg("Error",o.pdf.files[0].name+" is over the "+(limit/1024/1024)+' MB limit!','center',-1);
			return false;
		}
		if(test===false)
			return;
	}
	o.options.value=genPrintOptions(p);
	if(localStorage)
		localStorage.setItem("lastPrinter", o.printer.value);
}
function printMsg(t,m,a,r){// if r is -1 message goes at the top of the message list
	var div=document.createElement('div');
	var ele=getID('new_mes');
	div.className="message";
	div.innerHTML="<h2>"+t+'<a class="icon tool del" href="#">'+
		'<span class="tip">Close</span></a></h2><div'+(a!='center'?' style="text-align:'+a+';"':'')+">"+m+"</div>";
	div.getElementsByTagName('a')[0].addEventListener('click',function(){
		if(!this.onclick){
			(function(e){
				e.setAttribute('style','height:0;opacity:0;margin-bottom:0;');
				setTimeout(function(){
					e.parentNode.removeChild(e);
				},800);
			})(this.parentNode.parentNode);
			this.setAttribute('onclick','return false;');
			this.removeChild(this.childNodes[0]);
		}
		return false;
	},false);
	if(r!=-1)
		ele.insertBefore(div,ele.childNodes[0]);
	else
		ele.appendChild(div);
	div.style.height=div.scrollHeight+'px';
	div.style.opacity=1;
	setTimeout(function(){if(div)div.style.overflow='visible';},800);// 800ms is the animation duration in the css
	return false;
}
function roundNumber(num,dec){// http://forums.devarticles.com/javascript-development-22/javascript-to-round-to-2-decimal-places-36190.html#post71368
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}
function parseJSON(jsonTXT){
	try{
		return JSON.parse(jsonTXT);
	}
	catch(e){
		return printMsg('Invald Javascript Object Notation:','<textarea onclick="this.select()" style="width:100%;height:80px;">'+encodeHTML(jsonTXT)+'</textarea><br/>If you are reading this please report it as a bug. Please copy/paste the above, something as simple as a line break can cause errors here. If want to read this I suggest pasting it onto <a target="_blank" href="http://jsonlint.com/">jsonlint.com</a>.','center',0);
	}
}
function inArray(arr,val){
	for(var i=0;i<arr.length;i++){
		if(arr[i]===val){
			return true;
		}
	}
	return false;
}
function scannerChange(ele){
	var form=document.scanning, val=form.source.value, def=false, settings={},
		info,html='',text,sources;
	settings=localStorage.getItem('default');
	def=settings!=null;
	settings=def?parseJSON(settings):{};
	if(val==''&&def){// JS about to populate the select menus
		if(form.scanner.value!=settings.scanner){
			form.scanner.value=settings.scanner;
			for(var i=0,s=form.scanner.childNodes.length;i<s;i++){
				def=form.scanner.childNodes[i];
				if(def.value==settings.scanner)
					def.setAttribute('selected','selected');
				else
					def.removeAttribute('selected');
			}
			return scannerChange(ele);
		}
		config({
			'bright':settings.bright,
			'contrast':settings.contrast,
			'rotate':settings.rotate,
			'scale':settings.scale,
			'filetype':settings.filetype,
			'lang':settings.lang
		});
	}
	info=scanners[ele.selectedIndex];
	sources=info['SOURCE'].split('|');
	for(var i=0,s=sources.length;i<s;i++){
		switch(sources[i]){
			case 'ADF': text='Automatic Document Feeder';break;
			case 'Auto': text='Automatic';break;
			default: text=sources[i];
		}
		html+='<option value="'+sources[i]+'"'+(def?(settings.source==sources[i]?' selected="selected"':''):'')+'>'+text+'</option>';
	}
	form.source.innerHTML=html;
	if(inArray(sources,val))
		form.source.value=val;
	if(form.source.value=='Inactive')
		form.source.setAttribute('disabled','disabled');
	else
		form.source.removeAttribute('disabled');
	sourceChange(form.source);
}
function sourceChange(ele){
	var i,max,text,html1,html2,html3,html4,dpi,modes,valA,valB,valC,valD,duplex,papers,size,width,height,
		info=document.scanning.scanner,settings,def=false;
	info=scanners[info.selectedIndex];
	settings=localStorage.getItem('default');
	def=settings!=null;
	settings=def?parseJSON(settings):{};
	// Change Mode
	html1='';
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
		html1+='<option value="'+modes[i]+'"'+(def?(settings.mode==modes[i]?' selected="selected"':''):'')+'>'+text+'</option>';
	}
	// Change Paper Size
	papers=Array();
	width=info['WIDTH-'+ele.value];
	height=info['HEIGHT-'+ele.value];
	html2='<option value="full" title="'+width+' mm x '+height+' mm">Full Scan: '+roundNumber(width/25.4,2)+'" x '+roundNumber(height/25.4,2)+'"</option>';
	for(i in paper){// Similar stuff in PDF_popup function
		if(width>=paper[i]['width']&&height>=paper[i]['height']){
			size=paper[i]['width']+'-'+paper[i]['height'];
			html2+='<option value="'+size+'" title="'+paper[i]['width']+' mm x '+paper[i]['height']+' mm"'+
				(def?(settings.size==size?' selected="selected"'+(settings.size=''):''):(i=='Letter'?' selected="selected"':''))+'>'
				+i+': '+roundNumber(paper[i]['width']/25.4,2)+'" x '+roundNumber(paper[i]['height']/25.4,2)+'"</option>';
			papers.push(size);
		}
	}
	// Change Quality
	html3='';
	dpi=info['DPI-'+ele.value].split('|');
	for(i=0,max=dpi.length;i<max;i++)
		html3+='<option value="'+dpi[i]+'"'+(def?(settings.quality==dpi[i]?' selected="selected"':''):'')+'>'+dpi[i]+' '+(isNaN(dpi[i])?'':'DPI')+'</option>';
	// Change Duplex
	duplex=typeof(info['DUPLEX-'+ele.value])=='boolean'?Array(false,true):info['DUPLEX-'+ele.value].split('|');
	html4='';
	for(i in duplex)
		html4+='<option value="'+duplex[i]+'"'+(def?(settings.duplex==duplex[i]?' selected="selected"':''):'')+'>'+
			(typeof(duplex[i])=='boolean'?(duplex[i]?'Yes':'No'):duplex[i])+'</option>';
	// Apply Changes
	valA=document.scanning.mode.value;
	valB=document.scanning.size.value;
	valC=document.scanning.quality.value;
	valD=document.scanning.duplex.value;
	document.scanning.mode.innerHTML=html1;
	document.scanning.size.innerHTML=html2;
	document.scanning.quality.innerHTML=html3;
	document.scanning.duplex.innerHTML=html4;
	if(inArray(modes,valA))
		document.scanning.mode.value=valA;
	if(inArray(papers,valB))
		document.scanning.size.value=valB;
	if(inArray(dpi,valC))
		document.scanning.quality.value=valC;
	if(info['DUPLEX-'+ele.value]!==false){
		getID('duplex').removeAttribute('style');
		if(inArray(duplex,valD))
			document.scanning.duplex.value=valD;
	}
	else
		getID('duplex').style.display='none';
	sendE(document.scanning.size,'change');
}
function paperChange(ele){
	ele.nextSibling.textContent=ele.childNodes[ele.selectedIndex].title;
	var json=scanners[document.scanning.scanner.selectedIndex],
		width=json['WIDTH-'+document.scanning.source.value],
		height=json['HEIGHT-'+document.scanning.source.value];
	if(ele.value=='full'){
		document.scanning.ornt.selectedIndex=0;
		document.scanning.ornt.disabled='disabled';
			updateRulers(Array(width,height));
		return;
	}
	var sheet=ele.value.split('-');
	// Set Orientation
	if(Number(sheet[0])>height||Number(sheet[1])>width){
		document.scanning.ornt.selectedIndex=0;
		document.scanning.ornt.disabled='disabled';
	}
	else{
		document.scanning.ornt.removeAttribute('disabled');
		var settings=localStorage.getItem('default');
		if(settings!=null){
			settings=parseJSON(settings);
			document.scanning.ornt.value=settings.ornt;
		}
	}
	updateRulers(document.scanning.ornt.value=='vert'?sheet:sheet.reverse());
}
function layoutChange(b){
	var sheet=document.scanning.size.value.split('-');
	if(b)
		updateRulers(sheet);
	else
		updateRulers(sheet.reverse());
}
function rotateChange(ele){
	if(!previewIMG)
		return;
	var val=Number(ele.value);
	ele.nextSibling.textContent=(val==180?'Upside-down':(val<0?'Counterclockwise':'Clockwise'));
	ele=previewIMG;
	if(ele.src.indexOf('res/images/blank.gif')>-1)
		return;
	ias.setOptions({ "hide": true, "disable": true, "fadeSpeed": false, "rotating": true });
	// If you hava  a fear of numbers do not even try to read this function
	clearTimeout(rotateTimer);
	ele.style.transform='rotate('+val+'deg) scale('+
		(function(deg,h,w){ // Credit: http://userscripts.org/topics/127570?page=1#posts-502266 (http://jsfiddle.net/swU6Z/)
			// scale = sin(phi) / sin(phi + theta)
			// phi being the original rectangle's first diagonal's angle
			var theta=Math.abs(deg*Math.PI/180),
				phi=Math.atan(1/Math.max(w/h,h/w)),
				psi=theta<Math.PI/2?phi+theta:phi-theta;
			return Math.abs(Math.sin(phi)/Math.sin(psi));
		})(val,ele.offsetHeight,ele.offsetWidth)+')';
	rotateTimer=setTimeout(function(){// We can not leave it rotated, it brutally screws up cropping
		ele.style.transform='';
		rotateTimer=setTimeout(function(){
			ias.setOptions({ "hide": false, "disable": false, "fadeSpeed": 850, "rotating": false });
			if(document.scanning.loc_width.value>0&&document.scanning.loc_height.value>0)
				setRegion(ias);
		},805);// 800ms is the animation duration in the css
	},2000);// Should be long enough to see how it looks, given there is a 800ms animation
}
function changeBrightContrast(){
	// Does not work properly so lets disable it: brightness/contrast have a screwed up/illogical max %
	// Seriously 0 to 100 scales like a percentage to darken, but brightening is 101 to infinity
	if(true)return;// Disable, delete this line to enable
	if(previewIMG.src.indexOf('res/images/blank.gif')>-1)
		return;
	if(typeof(document.body.style.filter)!='string')// IE 11 does not support this
		return;
	previewIMG.style.filter='brightness('+(Number(document.scanning.bright.value)+100)+'%) contrast('+(Number(document.scanning.contrast.value)+100)+'%)';
}
function fileChange(type){
	if(type=='txt'){
		if(document.scanning.lang.value==''){
			document.scanning.filetype.children[3].disabled=true;
			printMsg('Tesseract Error','Saving to Text File format requires tesseract, it may not be installed!','center',-1);
			document.scanning.filetype.selectedIndex=0;
		}
		else
			getID('lang').removeAttribute('style');
	}
	else
		getID('lang').style.display='none';
}
function Debug(html,show){
	var div=document.createElement('div');
	div.id="debug";
	if(show)
		div.style.display='inherit';
	div.className="box box-full";
	div.innerHTML='<h2>Debug Console</h2><pre>'+html+'</pre>';
	var nojs=getID('nojs');
	nojs.parentNode.insertBefore(div,nojs);
}
function toggleDebug(keyboard){
	var debug=getID('debug');
	var debugLink=getID('debug-link');
	if(debug){
		if(debug.style.display=='inherit'){
			debug.removeAttribute('style');
			Set_Cookie('debug',false,1,false,null,null);
			if(keyboard&&debugLink)
				debugLink.textContent='Show';
			return false;
		}
		else{
			debug.style.display='inherit';
			Set_Cookie('debug',true,1,false,null,null);
			if(keyboard&&debugLink)
				debugLink.textContent='Hide';
			return true;
		}
	}
}
function toggleFortune(e){
	e=(e=='Hide'?false:true)
	Set_Cookie('fortune',e,1,false,null,null);
	return e;
}
function scanReset(){
	scannerChange(document.scanning.scanner);
	sendE(document.scanning.filetype,'change');
}
/*function lastCordsChange(json,state){
	// This is related to lines 52, 69-78,219,221, 223, and 225 of scan.php it is a attempt to add a option is use the last scan's coordinates (incomplete and I changed my mind on making it)
	// It will still need to disabled when/if the scanner is changed and including the coords at page load is bugged and attempting to scan results in a invalid input security error
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
			icon.childNodes[0].textContent+=" (Disabled)";
		}
	}
	catch(e){// IE 11
		var icons=document.getElementsByClassName('tool icon');
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
function PDF_popup(files,print){
	if(print===true&&!ReplacePrinter){
		var note='',ct=0;
		print=false;
		if(typeof(files)=='string'){
			ct=1;
			if(files.indexOf('.tiff')==-1)
				print=true;// not a problem to ask
		}
		else{
			var tiff=Array();
			for(var i in files){
				ct++;
				if(i.substr(-4)=='tiff')
					tiff.push(i);
			}
			if(ct!=tiff.length&&tiff.length>0){
				print=true;
				note='\nThese are TIFF files:\n'+tiff.toString('\n')+'\n\nThey will ONLY print using the Server Printer\n';
			}
			else if(tiff==0)
				print=true;// integrated only
		}
		if(print&&ct>0&&confirm("Press OK to use your system's printer.\nPress Cancel to open the Server Print Dialog."+note)){
			if(typeof(files)!='string'){
				window.open("print.php?json="+JSON.stringify(files));
			}
			return true;
		}
		print=true;
	}
	function populateSelect(ele){
		var opt,ele=getID('PDF_PAPER');
		ele.removeChild(ele.children[0]);
		for(var i in paper){// Similar code in changeSource function
			opt=document.createElement('option');
			opt.value=paper[i]['width']+'-'+paper[i]['height'];
			if(i=='Letter')
				opt.selected="selected";
			opt.title=paper[i]['width']+' mm x '+paper[i]['height']+' mm';
			opt.textContent=i+': '+roundNumber(paper[i]['width']/25.4,2)+'" x '+roundNumber(paper[i]['height']/25.4,2)+'"';
			ele.appendChild(opt);
		}
	}
	if(typeof(files)=='string')
		files='{"'+files.replace(/"/g,'\"')+'":1}';
	else if(files.tagName=='form'||files.tagName=='FORM'){
		var p=getID('p_config'),url;
		if(p){
			p=genPrintOptions(p);
			localStorage.setItem('lastPrinter',files.printer.value);
		}
		url='download.php?type=pdf&json='+files.files.value+'&size='+files.size.value+'&'+files.format.value+
			(p?'&printer='+encodeURIComponent(files.printer.value)+'&quantity='+files.quantity.value+'&options='+p:'');
		if(p){
			var httpRequest = new XMLHttpRequest();
			httpRequest.onreadystatechange = function(){
				if(httpRequest.readyState==4){
					if(httpRequest.status==200){
						var json=parseJSON(httpRequest.responseText),
							debug=getID('debug').childNodes[1],
							text=debug.textContent;
						text=text.substr(0,text.indexOf('$')+2);
						debug.textContent+=json['command']+'\n'+json['message']+text;
						printMsg(encodeHTML(json['printer']),'Your document is being processed:<br/><pre>'+encodeHTML(json['message'])+'</pre>','center',0);
						if(json['debug']){
							debug.textContent+=json['debug']+text;
						}
					}
					else
						printMsg('Error','A '+httpRequest.status+' error was encountered.','center',0);
				}
			};
			httpRequest.open('GET', url);
			httpRequest.send(null);
		}
		else// Apparently if I use the files.action property it sends format=files.format.value instead of files.format.value so I will just use window.open
			window.open(url);
		toggle('blanket');
		return false;
	}
	else{
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
	getID("blanket").childNodes[0].innerHTML='<form onsubmit="return PDF_popup(this,false)" target="_blank" action="#" method="GET">'+
		(print?'<div id="p_config" style="float:right;width:260px;text-align:left;overflow-y:auto;overflow-x:hidden;"></div>':'')+'How would you prefer for your PDF '+
		(print?'<b>printed</b>':'download')+'?<br/>A scan placed on the page with a title or<br/><input type="hidden" name="files" value="'+files+'"/>\
		<input type="hidden" name="format" value=""/>a would you prefer the scan as the page.<br/>Paper Type: <select id="PDF_PAPER" name="size" style="width:190px;">\
		<option value="">Loading...</option></select><button type="submit"><img src="res/images/pdf-scaled.png" width="106" height="128" alt="With title"/></button>\
		<button type="submit" onclick="this.parentNode.format.value=\'full\';"><img src="res/images/pdf-full.png" width="106" height="128" alt="Fill page with scan"/></button>\
		<br/><input type="submit" onclick="this.parentNode.format.value=\'raw\';" value="I don\'t care just '+(print?'print it':'give me a PDF')+'" style="width:261px"/>\
		<br/><input type="button" value="Cancel" style="width:261px;" onclick="toggle(\'blanket\')"/></form>';
	if(paper==null){
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function(){
			if(httpRequest.readyState==4){
				paper=httpRequest.status==200?parseJSON(httpRequest.responseText):{"Paper":{"height":279.4,"width":215.9},"Picture":{"height":152.4,"width":101.6}};
				populateSelect();
			}
		};
		httpRequest.open('GET', 'config/paper.json?nocache='+new Date().getTime());
		httpRequest.send(null);
	}
	else
		populateSelect();
	if(print){
		var httpRequest2 = new XMLHttpRequest();
		httpRequest2.onreadystatechange = function(){
			if(httpRequest2.readyState==4){
				var ele=getID('p_config');
				ele.style.maxHeight=ele.parentNode.offsetHeight+'px';
				if(httpRequest2.status==200)
					buildPrinterOptions(parseJSON(httpRequest2.responseText),ele,localStorage.getItem('lastPrinter'));
				else if(httpRequest2.status==404)
					alert('Error:\nPrinter(s) have not been searched for, please visit the Configure page!');
			}
		};
		httpRequest2.open('GET', 'config/printers.json?nocache='+new Date().getTime());
		httpRequest2.send(null);
	}
	popup('blanket',print?550:310);
	return false;
}
function toggleFile(file){
	if(!filesLst[file.textContent]){
		filesLst[file.textContent]=1;
		file.className="included";
	}
	else{
		delete(filesLst[file.textContent]);
		file.className="excluded";
	}
}
function bulkDownload(link,type){
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
	var ct=0;
	for(var i in filesLst){
		if(i.substr(-4)=='tiff'){
			if(!confirm('Warning TIFF files are not supported!\nThe file: '+i+'\nWill NOT be printed.\nPress OK to skip this file.\n\n'+
				'If you want to print this file I suggest downloading a PDF file to print or using the Edit page to save it as anything else.'))
				return;
		}
		else
			ct++;
	}
	if(ct>0){
		window.open("print.php?json="+encodeURIComponent(JSON.stringify(filesLst)));
		return true;
	}
	else
		return printMsg('Error','No files selected','center',-1);
}
function bulkDel(){
	var p='Delete all of these:';
	for(var i in filesLst)
		p+="\n"+i;
	if(p.length==20)
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
function getImgurBox(){
	var ele=getID('imgur-uploads');
	if(!ele){
		ele=getID('imgur-box-setup');
		if(ele){
			var ele2=document.createElement('div');
			ele2.className='box box-full';
			ele2.id='imgur-uploads';
			ele2.innerHTML='<h2>Imgur Uploads<a href="#" onclick="return imgurDel(\'imgur-uploads\',false)" class="tool icon del"><span class="tip">Hide</span></a></h2>';
			ele.parentNode.insertBefore(ele2,ele);
			return ele2;
		}
	}
	return ele;
}
function storeImgurAlbum(id,imgs){
	var data=localStorage.getItem('imgur'),a='';
	data=(data==null?{"albums":{}}:parseJSON(data));
	if(!data['albums'])
		data['albums']={};
	data['albums'][id[0]]={"del":id[1],"title":id[2],"imgs":imgs};
	for(var i in imgs)
		a+='<img alt="'+imgs[i]+'" src="http://i.imgur.com/'+imgs[i]+'s.jpg"/>';
	var div=document.createElement('div');
	div.className="box";
	div.id='imgur-'+id[0];
	div.innerHTML='<h2 style="min-height:32px"><span>'+encodeHTML(id[2])+'</span><a href="#" onclick="return '+
		'imgurDel(\'imgur-'+id[0]+'\',\''+id[0]+'\')" class="tool icon del"><span class="tip">Hide</span></a></h2>'+
		'<span class="tool"><div class="album" onclick="imgurPopup(\''+encodeHTML(id[2])+'\',\''+id[0]+'\')">'+a+'</div><span class="tip">View Album</span></span>';
	getImgurBox().appendChild(div);
	localStorage.setItem('imgur',JSON.stringify(data));
}
function storeImgurUploads(img){
	var data=localStorage.getItem('imgur'),id,b,a,ele,ele2,div,f;
	data=(data==null?{}:parseJSON(data));
	ele=getImgurBox();
	for(var i in img){
		if(typeof(img[i])=='boolean')
			continue;
		if(!img[i]['success'])
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
			'<span class="tool"><img alt="'+data[f]['big_square']+'" src="'+data[f]['big_square']+'" onclick="imgurPopup(\''+f+'\',null)"/><span class="tip">View Codes</span></span>';
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
	var title=prompt("Upload New Album to imgur.com\nYou can give it a title (optional):",'Scan Compilation');
	if(title==null)
		return false;
	var now=new Date().getTime();
	printMsg('Uploading<span id="upload-'+now+'"></span>','Please Wait...<br/>This could take a while depending on the file size of the scan and the upload speed at '+document.domain+'<br/>When imgur is under heady load this can take a very long time','center',0);
	var httpRequest = new XMLHttpRequest();
	httpRequest.onreadystatechange = function(){
		if(httpRequest.readyState==4){
			if(httpRequest.status==200){//printMsg('Debug',encodeHTML(httpRequest.responseText),'center',0);			
				var json=parseJSON(httpRequest.responseText),ids=false,c=0;
				if(!json)
					return false;
				if(json['success'])
					ids=Array();
				else{
					if(json['images'].length==0){
						if(!json['album'])
							printMsg('Failed to create Album',json['error'],'center',0);
						else
							printMsg('Failed to create Album',json['album']['data']['error']+(json['album']['status']==200?'':'<br/>'+json['album']['status']+' Error detected'),'center',0);
					}
					else{
						printMsg('Image Upload Error',(json['images'].length-1)+' image(s) were uploaded to your <a href="http://imgur.com/a/'+
							json['album']['data']['id']+'" target="_blank">album</a> before a error occurred<br/>You delete hash is <i>'+
							json['album']['data']['deletehash']+'</i>. Sorry, I do not know the URL to delete albums.<br/>The error message was: '+
							(json['images'][json['images'].length-1]?json['images'][json['images'].length-1]["data"]["error"]:'Connection failure'),'center',0);
						delete(json['images'][json['images'].length-1]);
						if(json['images'].length>0)
							ids=Array();
					}
				}
				if(ids!==false){
					for(var i in json['images']){
						if(c>3)
							break;
						ids.push(json['images'][i]["data"]["id"]);
						c++;
					}
					storeImgurAlbum(Array(json['album']['data']['id'],json['album']['data']['deletehash'],json['album']['data']['title']),ids);
					storeImgurUploads(json['images']);
					imgurPopup(json['album']['data']['title'],json['album']['data']['id']);
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
	return false;
}
function selectScans(b){
	try{
		var scans=document.evaluate("//div[@id='scans']/div/h2"+(b?"[@class='"+b+"']":''),document,null,6,null);
		for(var i=0;i<scans.snapshotLength;i++)
			toggleFile(scans.snapshotItem(i));
	}
	catch(e){// Screw you IE 11, screw you
		var list=getID('scans').getElementsByTagName('h2'),stat;
		for(var i=0,ct=list.length;i<ct;i++){
			stat=list[i].className;
			if(!b)
				toggleFile(list[i])
			else if(stat==b)
				toggleFile(list[i]);
		}
	}
	return false;
}
function upload(file){
	if(getID(file)){
		popup('blanket',365);
		return false;
	}
	var test=true,json;
	json=localStorage.getItem('imgur');
	json=(json==null?{}:parseJSON(json));
	if(json[file]){
		test=false;
		if(confirm("'"+file.substr(5)+"' has been uploaded already!\nOK = Upload Again\nCancel = View Upload dialog")===false)
			return imgurPopup(file,json[file]);
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
			if(httpRequest.status==200){//printMsg('Debug',encodeHTML(httpRequest.responseText),'center',0);
				var json=parseJSON(httpRequest.responseText);
				if(!json)
					return false;
				if(!json['images'][0])
					printMsg('Upload Error','Failed to connect to imgur'+(json["error"]?'<br/>'+json["error"]:''),'center',0);
				else if(json['images'][0]['data']['error'])
					printMsg('Upload Error: '+json['images'][0]['status'],'Imgur said: '+json['images'][0]['data']['error'],'center',0);
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
	var attrs='onclick="void(setClipboard(this)?null:this.select());" readonly="readonly" type="text"';
	if(typeof(links)=='string'){
		links='http://imgur.com/a/'+links;
		file=encodeHTML(file);
		getID("blanket").childNodes[0].innerHTML='<div style="float:left;width:270px;text-align:left;">'+
			'<h2 class="center" style="font-size:12px;">'+file+'</h2><ul style="list-style:none;padding:0;margin:0;margin-top:90px">'+
			'<li>View on Imgur:<ul><li><a href="'+links+'" target="_blank">'+file+'</a></li></ul></li>'+
			'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links+'"/></li></ul></li>'+
			'<li>HTML Link (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links+'&quot;&gt;'+file+'&lt;/a&gt;"/></li></ul></li>'+
			'<li>BBCode Link (message boards & forums)<ul><li><input '+attrs+' value="[URL='+links+']'+file+'[/URL]"/></li></ul></li>'+
			'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' value="['+file+']('+links+')"/></li></ul></li>'+
			'</ul></div><iframe style="float:right;border-radius:5px;" width="400" height="400" frameborder="0" src="'+links+'/embed?pub=true&w=400"></iframe>'+
			'<input type="button" value="Close" style="width:100%;" onclick="toggle(\'blanket\')"/>';
		popup('blanket',675);
		return false;
	}
	if(links==null){
		links=parseJSON(localStorage.getItem('imgur'));
		links=links[file];
	}
	getID("blanket").childNodes[0].innerHTML='<h2 style="font-size:12px;">'+file.substr(5)+' is on Imgur</h2>'+
		'<div id="imgur-data"><div><img id="'+encodeHTML(file)+'" alt="'+encodeHTML(file)+'" style="float:left;margin-right:5px;" src="'+links['small_square']+'" width="90" height="90"/>'+
		'<ul style="list-style:none;">'+
		'<li>View on Imgur:<ul><li><a href="'+links['imgur_page']+'" target="_blank">'+links['imgur_page'].substr(7)+'</a></li></ul></li>'+
		'<li>Direct Link:<ul><li><a href="'+links['original']+'" target="_blank">'+links['original'].substr(7)+'</a></li></ul></li>'+
		'<li>Delete Link:<ul><li><a href="'+links['delete_page']+'" target="_blank">'+links['delete_page'].substr(7)+'</a></li></ul></li></ul></div>'+
		'<h2 style="font-size:12px;" class="center">Embed Codes</h2><div id="imgur-scroller"><div></div></div><div id="imgur-codes">'+
		'<div class="codes"><h2>Original</h2><ul>'+
		'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links['original']+'"/></li></ul></li>'+
		'<li>HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;img src=&quot;'+links['original']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;"/></li></ul></li>'+
		'<li>Linked HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['original']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;&lt;/a&gt;"/></li></ul></li>'+
		'<li>BBCode (message boards & forums)<ul><li><input '+attrs+' value="[IMG]'+links['original']+'[/IMG]"/></li></ul></li>'+
		'<li>Linked BBCode (message boards & blogs)<ul><li><input '+attrs+' value="[URL='+links['imgur_page']+'][IMG]'+links['original']+'[/IMG][/URL]"/></li></ul></li>'+
		'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' value="[Imgur]('+links['original']+')"/></li></ul></li>'+
		'</ul></div>'+
		'<div class="codes"><h2>Huge Thumbnail</h2><ul>'+
		'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links['huge_thumbnail']+'"/></li></ul></li>'+
		'<li>HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;img src=&quot;'+links['huge_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;"/></li></ul></li>'+
		'<li>Linked HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['huge_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;&lt;/a&gt;"/></li></ul></li>'+
		'<li>BBCode (message boards & forums)<ul><li><input '+attrs+' value="[IMG]'+links['huge_thumbnail']+'[/IMG]"/></li></ul></li>'+
		'<li>Linked BBCode (message boards & blogs)<ul><li><input '+attrs+' value="[URL='+links['imgur_page']+'][IMG]'+links['huge_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
		'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' type="text" value="[Imgur]('+links['huge_thumbnail']+')"/></li></ul></li>'+
		'</ul></div>'+
		'<div class="codes"><h2>Large Thumbnail</h2><ul>'+
		'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links['large_thumbnail']+'"/></li></ul></li>'+
		'<li>HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;img src=&quot;'+links['large_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;"/></li></ul></li>'+
		'<li>Linked HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['large_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;&lt;/a&gt;"/></li></ul></li>'+
		'<li>BBCode (message boards & forums)<ul><li><input '+attrs+' value="[IMG]'+links['large_thumbnail']+'[/IMG]"/></li></ul></li>'+
		'<li>Linked BBCode (message boards & blogs)<ul><li><input '+attrs+' value="[URL='+links['imgur_page']+'][IMG]'+links['large_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
		'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' value="[Imgur]('+links['large_thumbnail']+')"/></li></ul></li>'+
		'</ul></div>'+
		'<div class="codes"><h2>Medium Thumbnail</h2><ul>'+
		'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links['medium_thumbnail']+'"/></li></ul></li>'+
		'<li>HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;img src=&quot;'+links['medium_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;"/></li></ul></li>'+
		'<li>Linked HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['medium_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;&lt;/a&gt;"/></li></ul></li>'+
		'<li>BBCode (message boards & forums)<ul><li><input '+attrs+' value="[IMG]'+links['medium_thumbnail']+'[/IMG]"/></li></ul></li>'+
		'<li>Linked BBCode (message boards & blogs)<ul><li><input '+attrs+' value="[URL='+links['imgur_page']+'][IMG]'+links['medium_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
		'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' value="[Imgur]('+links['medium_thumbnail']+')"/></li></ul></li>'+
		'</ul></div>'+
		'<div class="codes"><h2>Small Thumbnail</h2><ul>'+
		'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links['small_thumbnail']+'"/></li></ul></li>'+
		'<li>HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;img src=&quot;'+links['small_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;"/></li></ul></li>'+
		'<li>Linked HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['small_thumbnail']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;&lt;/a&gt;"/></li></ul></li>'+
		'<li>BBCode (message boards & forums)<ul><li><input '+attrs+' value="[IMG]'+links['small_thumbnail']+'[/IMG]"/></li></ul></li>'+
		'<li>Linked BBCode (message boards & blogs)<ul><li><input '+attrs+' value="[URL='+links['imgur_page']+'][IMG]'+links['small_thumbnail']+'[/IMG][/URL]"/></li></ul></li>'+
		'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' value="[Imgur]('+links['small_thumbnail']+')"/></li></ul></li>'+
		'</ul></div>'+
		'<div class="codes"><h2>Big Square</h2><ul>'+
		'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links['big_square']+'"/></li></ul></li>'+
		'<li>HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;img src=&quot;'+links['big_square']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;"/></li></ul></li>'+
		'<li>Linked HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['big_square']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;&lt;/a&gt;"/></li></ul></li>'+
		'<li>BBCode (message boards & forums)<ul><li><input '+attrs+' value="[IMG]'+links['big_square']+'[/IMG]"/></li></ul></li>'+
		'<li>Linked BBCode (message boards & blogs)<ul><li><input '+attrs+' value="[URL='+links['imgur_page']+'][IMG]'+links['big_square']+'[/IMG][/URL]"/></li></ul></li>'+
		'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' value="[Imgur]('+links['big_square']+')"/></li></ul></li>'+
		'</ul></div>'+
		'<div class="codes" style="border: none;"><h2>Small Square</h2><ul>'+
		'<li>Direct Link (email & IM)<ul><li><input '+attrs+' value="'+links['small_square']+'"/></li></ul></li>'+
		'<li>HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;img src=&quot;'+links['small_square']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;"/></li></ul></li>'+
		'<li>Linekd HTML Image (websites & blogs)<ul><li><input '+attrs+' value="&lt;a href=&quot;'+links['imgur_page']+'&quot;&gt;&lt;img src=&quot;'+links['small_square']+'&quot; alt=&quot;'+links['imgur_page']+'&quot; title=&quot;Hosted by imgur.com&quot;/&gt;&lt;/a&gt;"/></li></ul></li>'+
		'<li>BBCode (message boards & forums)<ul><li><input '+attrs+' value="[IMG]'+links['small_square']+'[/IMG]"/></li></ul></li>'+
		'<li>Linked BBCode (message boards & blogs)<ul><li><input '+attrs+' value="[URL='+links['imgur_page']+'][IMG]'+links['small_square']+'[/IMG][/URL]"/></li></ul></li>'+
		'<li>Markdown Link (reddit comment)<ul><li><input '+attrs+' value="[Imgur]('+links['small_square']+')"/></li></ul></li>'+
		'</ul></div>'+
		'</div></div><input type="button" value="Close" style="width:100%;" onclick="toggle(\'blanket\')"/>';
	popup('blanket',365);
	try{// Thanks to imgAreaSelect jQuery is here lets try using it :)
		var scroll=$('#imgur-scroller');
		var codes=$('#imgur-codes');
		scroll[0].childNodes[0].style.width=(codes[0].childNodes[0].offsetWidth*7-1)+'px';
		scroll.scroll(function(){codes.scrollLeft(scroll.scrollLeft());});
	}
	catch(e){// And if it messes up we have this
		getID('imgur-scroller').style.display='none';
		getID('imgur-codes').style.overflowX='scroll';
	}
	return false;
}

function imgurDel(id,img){
	if(img===false){
		if(confirm("Are you sure you want to hide ALL imgur uploads?\nThis only deletes the images from this page,\nnot imgur.")===false)
			return false;
		localStorage.removeItem('imgur');
	}
	else if(confirm("Are you sure you want to hide that image?\nThis only deletes the image from this page,\nnot imgur.")===false)
		return false;
	var e=getID(id);
	if(e)
		e.parentNode.removeChild(e);
	e=localStorage.getItem('imgur');
	if(e==null)
		return false;
	e=parseJSON(e);
	if(!e[img]){
		delete(e['albums'][img]);
	}
	else
		delete(e[img]);
	e=JSON.stringify(e);
	if(e.length>2&&e!='{"albums":{}}')
		localStorage.setItem('imgur',e);
	else{
		localStorage.removeItem('imgur');
		e=getID('imgur-uploads');
		if(e)
			e.parentNode.removeChild(e);
	}
	return false;
}
function setClipboard(e){// Everyone except MS considers this a security hole, thus this is IE only; I refuse to use flash circumvent this security feature
	// I can't figure out how to get navigator.clipboard to work on firefox or chrome
	if(!window.clipboardData)
		return false;
	if(window.clipboardData.setData('Text',e.value)){
		var span=document.createElement('span');
		span.textContent="Copied";
		span.className="tip";
		span.style.display="block";
		e.parentNode.className="tool";
		e.parentNode.appendChild(span);
		setTimeout(function(){
			e.parentNode.removeChild(span);
		},1600);// relative to transitionTime in style.php
		return true;
	}
	return false;
}
function emailManager(file){
	var data=false;
	if(file=='Scan_Compilation'){
		var files_ct=0;
		for(var i in filesLst)
			files_ct++;
		if(files_ct==0)
			return printMsg('Error','No files selected','center',-1);
	}
	var html='<div id="email"><h2>'+(file?'Email: '+file.substr(5):'Configure Email')+'</h2>'+
	'<div class="security"><h2>Security Notice</h2><ul>'+
	'<li>The remember me option will store your e-mail login data in your <a href="http://dev.w3.org/html5/webstorage/#dom-localstorage" target="_blank">local storage</a> in plain text (unencrypted).</li>'+
		(file?'<li>If you leave it unchecked your login date will not be saved and you will have to re-enter it every time.</li>':'')+
		'<li>Anyone with access to your account on this computer can get your password if you use '+(file?'remember me':'save it')+'.</li>'+
		(file?'<li>You can delete your saved data on the <a href="index.php?page=Config"/>Configure</a> page.</li>':'');
		data=localStorage.getItem("email")+
	'<li>You can double click the password blank to see the password.</li>'+
	(document.location.protocol=='http:'?'<li>This does not use a secure connection to get your login from your browser to the server.</li>':'')+'</ul></div>'+
	'<form name="email" target="_blank" action="email.php" onsubmit="return validateEmail(this);" method="POST">'+
	'<input type="hidden" name="'+(file=='Scan_Compilation'?'json':'file')+'" value="'+(file=='Scan_Compilation'?encodeHTML(JSON.stringify(filesLst)):file)+'"/>'+
	'<div class="label">'+(file?'From':'Email')+':</div><div class="control"><input type="text" onchange="configEmail(this.value)" name="from" value="johndoe@gmail.com"/></div>'+
	(file?'<div class="label">Subject:</div><div class="control"><input type="text" name="title" value="[Scanned '+(file=='Scan_Compilation'?'Compilation':(file.substr(-3)!='txt'?'Image':'Text'))+'] '+(file=='Scan_Compilation'?files_ct+' Scans':file.substr(5))+'"/></div>'+
		'<div class="label">To:</div><div class="control"><input type="text" name="to" value=""/></div>'+
		'<div class="label">Message:</div><div class="control"><textarea name="body"></textarea></div>':'')+
	'<div class="label">Password:</div><div class="control"><input type="password" name="pass" ondblclick="this.type=(this.type==\'text\'?\'password\':\'text\')" autocomplete="off"/></div>'+
	'<div class="label">Host:</div><div class="control"><input type="text" name="host" value="smtp.gmail.com"/></div>'+
	'<div class="label">Prefix:</div><div class="control tool"><select name="prefix"><option value="ssl">SSL</option><option value="tls">TLS</option><option value="plain">None</option></select><span class="tip" style="display:none"></span></div>'+
	'<div class="label">Port:</div><div class="control"><input type="text" name="port" value="587"/></div>'+
	'<div class="label">Remember Me:</div><div class="control"><input '+(file?'':'checked="checked" ')+'id="email-nopass" onchange="if(this.checked){getID(\'email-pass\').checked=false}'+(file?'':'else if(getID(\'email-nopass\').checked){getID(\'email-pass\').checked=true}')+'" type="checkbox" name="store"/> <small>(Exclude Password)</small></div>'+
		'<div class="label">Remember Me:</div><div class="control"><input id="email-pass" onchange="if(this.checked){getID(\'email-nopass\').checked=false}'+(file?'':'else if(getID(\'email-pass\').checked){getID(\'email-nopass\').checked=true}')+'" type="checkbox" name="storepass"/> <small>(Include Password)</small></div>'+
	'<input type="submit" value="'+(file?'Send':'Save')+'"/><input style="float:right;" type="button" value="Cancel" onclick="toggle(\'blanket\')"/>'+
	'</form>'+
	'<div class="help"><h2>Help Links</h2><p><a target="_blank" href="http://www.google.com">Google</a><br/>eg: What are Yahoo\'s smtp settings</p></div>'+
	'<div class="help"><h2>Tips</h2><p>'+(file?'Send to multiple people by separating addresses with a comma.<br/>':'')+'Host, prefix, and port are auto detected when you change the '+(file?'From field':'Email Address')+'.</p></div>'+
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
	if(addr.indexOf('@')==-1)
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
						t.innerHTML='The autoconfigure<br/>database said<br/>something about<br/>"'+data["prefix"]+'"';
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
	var httpRequest,params,
		now=new Date().getTime();
	printMsg('Sending Email<span id="email-'+now+'"></span>','Please Wait...<br/>This could take a while depending on the file size of the scan and the upload speed at '+document.domain,'center',0);
	httpRequest = new XMLHttpRequest();
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
	params = (ele.file?"file="+encodeURIComponent(ele.file.value):"json="+encodeURIComponent(ele.json.value))+
		"&from="+encodeURIComponent(ele.from.value)+
		"&to="+encodeURIComponent(ele.to.value)+
		"&title="+encodeURIComponent(ele.title.value)+
		"&body="+encodeURIComponent(ele.body.value)+
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
	if(confirm("Delete Saved Email settings")){
		localStorage.removeItem("email");
		printMsg('Success',"Your Email login data has been delted!",'center',0);
	}
}
function delScan(file,prompt){
	if(prompt){
		if(!confirm("Are you sure you want to delete:\n"+file))
			return false;
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
					if(del)
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
	if(e===true)
		return printMsg('Update Available',
			'Version '+vs+' is available for <a target="_blank" href="https://github.com/GM-Script-Writer-62850/PHP-Scanner-Server/wiki/Change-Log">download</a>'+
				(vs.indexOf('_dev')>-1?'<br/>This is a developmental version, it may have some bugs, wanna try it?':''),
			'center',
			-1
		);
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
					printMsg('Update Available',
						'Version '+data["version"]+' is available for <a target="_blank" href="https://github.com/GM-Script-Writer-62850/PHP-Scanner-Server/wiki/Change-Log">download</a>'+
							(data["version"].indexOf('_dev')>-1?'<br/>This is a developmental version, it may have some bugs, wanna try it?':''),
						'center',
						-1
					);
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
					e.nextSibling.textContent='Enable';
					Delete_Cookie('columns',false);
				}
			}
			else if(ele.className.indexOf('columns')==-1){
				ele.className+=' columns';// enable
				if(e){
					e.nextSibling.textContent='Disable';
					Set_Cookie('columns',true,1,false,null,null);
				}
			}
			else{
				ele.className=ele.className.substring(0,ele.className.indexOf(' columns'));// Disable preserve original class name
				if(e){
					e.nextSibling.textContent='Enable';
					Delete_Cookie('columns',false);
				}
			}
		}
		else{// enable
			ele.className='columns';
			if(e){
				e.nextSibling.textContent='Disable';
				Set_Cookie('columns',true,1,false,null,null);
			}
		}
		return false;
	}
	else if(typeof(document.body.style.WebkitColumnGap)=="string"||typeof(document.body.style.columnGap)=="string"){
		printMsg('CSS3 Columns','Your browser supports them, but they do not work as expected.<br/>'+
			'You can try them out by clicking <span class="tool"><a href="#" onclick="return enableColumns(\''+ele+'\',this,null);">here</a><span class="tip">'+(b?'Disable':'Enable')+'</span></span>.<br/>'+
			'Oh, and by the way they work in <a href="http://www.mozilla.org/en-US/firefox/all.html" target="_blank">Firefox</a> flawlessly.','center',-1);
		if(b)
			enableColumns(ele,false,null)
	}
}
function login(form){
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
		}
	};
	httpRequest.open('POST', "res/inc/login.php");
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
function scanFilter(f2,f1){
	var names={'y':31557600,'d':86400,'h':3600,'m':60,'s':1},f1Total=0,f2Total=0;
	for(var i in names){
		f1Total+=Number(f1[i].value*names[i]);
		f2Total+=Number(f2[i].value*names[i]);
	}
	if(f1Total>f2Total)
		return alert("That combination can't have any results");
	document.location.href="index.php?page=Scans&filter=3&origin="+f1['origin'].value+"&t1="+f1Total+"&t2="+f2Total;
}
function setDefault(form){
	localStorage.setItem('default',JSON.stringify({
		"scanner":form.scanner.value,
		"source":form.source.value,
		"duplex":form.duplex.value,
		"quality":form.quality.value,
		"size":form.size.value,
		"ornt":form.ornt.value,
		"mode":form.mode.value,
		"bright":form.bright.value,
		"contrast":form.contrast.value,
		"rotate":form.rotate.value,
		"scale":form.scale.value,
		"filetype":form.filetype.value,
		"lang":form.lang.value
	}));
	printMsg('Saved','Your current scanning options (excluding select region) will now be used by default on this browser,<br/>'+
		'you can delete them from the <a href="index.php?page=Config">Configure tab</a>','center',-1)
}
document.onkeyup=function(event){
	if(event.ctrlKey&&(event.which==68||event.keyCode==68))// [Ctrl]+[Shift]+[D]
		toggleDebug(true);
}
