(function(){
	if(typeof localStorage!="object")
		return;
	var data=localStorage.getItem("imgur"),id,album=false,ct;
	if(data==null)
		return;
	function insert(id,t,data){
		if(typeof(data)=='string'){
			var a=data.length==0?'&nbsp;':data;
			id=Array(id,t['del'],t['title']);
			t='12345'+encodeHTML(id[2])+'.';
		}
		else
			var a=false,id=Array(id,t);
		document.write('<div class="box" id="imgur-'+id[0]+'"><h2 style="min-height:32px"><span>'+t.slice(5,t.lastIndexOf('.'))+
			'</span><a href="#" onclick="return imgurDel(\'imgur-'+id[0]+'\',\''+id[0]+'\')" class="tool icon del"><span class="tip">Hide</span></a></h2><span class="tool">'+
			(a?'<div class="album" onclick="imgurPopup(\''+encodeHTML(id[2])+'\',\''+id[0]+'\')">':'<img alt="'+t+'" src="'+data[t]['big_square']+'" onclick="imgurPopup(\''+t+'\',null)"/>')+
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
					album+='<img alt="'+data[x][y]['imgs'][z]+'" src="http://i.imgur.com/'+data[x][y]['imgs'][z]+'s.jpg"/>';
				}
				insert(y,data[x][y],album);
			}
		}
		else
			insert(data[x]['imgur_page'].substr(data[x]['imgur_page'].lastIndexOf('/')+1),x,data);
	}
	document.write('</div>');
}());
