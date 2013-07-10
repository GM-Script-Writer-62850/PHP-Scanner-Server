(function(){
	if(typeof localStorage!="object")
		return;
	var data=localStorage.getItem("imgur"),id;
	if(data==null)
		return;
	data=parseJSON(data);
	document.write('<div class="box box-full" id="imgur-uploads"><h2>Imgur Uploads'+
		'<a href="#" onclick="return imgurDel(\'imgur-uploads\',false)" class="tool icon del"><span class="tip">Hide</span></a></h2>');
	for(var i in data){
		id=data[i]['imgur_page'].substr(data[i]['imgur_page'].lastIndexOf('/')+1);
		document.write('<div class="box" id="imgur-'+id+'"><h2><span>'+i.slice(5,i.lastIndexOf('.'))+
			'</span><a href="#" onclick="return imgurDel(\'imgur-'+id+'\',\''+i+'\')" class="tool icon del"><span class="tip">Hide</span></a></h2>'+
			'<span class="tool"><img alt="'+i+'" src="'+data[i]['big_square']+'" onclick="imgurPopup(\''+i+'\',null)"/><span class="tip">View Codes</span></span></div>');
	}
	document.write('</div>');
}());
