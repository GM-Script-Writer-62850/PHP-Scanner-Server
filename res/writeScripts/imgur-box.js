(function(){
	"use strict";
	var data=localStorage.getItem("imgur"),id,album=false,ct,M;
	if(data==null)
		return;
	// M is dirty workaround cause every browser does it different
	M=document.body.style;
	if(typeof(M.MozBoxPack)=='string')// Firefox
		M=3;
	else if(typeof(M.msFlexPack)=='string'||typeof(M.msBoxPack)=='string')// IE10+
		M=0;
	else// Chrome and anyone I missed
		M=2;
	function insert(id,t,data){
		if(typeof(data)=='string'){
			var a=data.length==0?'&nbsp;':data;
			id=Array(id,t['del'],t['title']);
			t='12345'+encodeHTML(id[2])+'.';
		}
		else
			var a=false,id=Array(id,t);
		document.write('<div class="box" id="imgur-'+id[0]+'"><h2 style="min-height:32px"><span>'+t.slice(5,t.lastIndexOf('.'))+
			'</span><a href="#" onclick="return imgurDel(\'imgur-'+id[0]+'\',\''+(id[2]?id[0]:id[1])+'\')" class="tool icon del"><span class="tip">Hide</span></a></h2><span class="tool">'+
			(a?'<div class="album" style="margin-bottom:'+M+'px;" onclick="imgurPopup(\''+encodeHTML(id[2])+'\',\''+id[0]+'\')">':'<img alt="'+t+'" src="'+data[t]['big_square']+'" onclick="imgurPopup(\''+t+'\',null)"/>')+
			(a?a+'</div>':'')+'<span class="tip">View '+(a?'Album':'Codes')+'</span></span></div>');
	}
	data=parseJSON(data);
	document.write('<div class="box box-full" id="imgur-uploads"><h2>Imgur Uploads'+
		'<a href="#" onclick="return imgurDel(\'imgur-uploads\',false)" class="tool icon del"><span class="tip">Hide</span></a></h2>');
	for(var x in data){
		if(x=='albums'){
			for(var y in data[x]){
				album='';
				for(var z in data[x][y]['imgs']){
					album+='<img alt="'+data[x][y]['imgs'][z]+'" style="margin-bottom: -'+M+'px;" src="http://i.imgur.com/'+data[x][y]['imgs'][z]+'s.jpg"/>';
				}
				insert(y,data[x][y],album);
			}
		}
		else
			insert(data[x]['imgur_page'].substr(data[x]['imgur_page'].lastIndexOf('/')+1),x,data);
	}
	document.write('</div>');
}());
