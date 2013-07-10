(function(){
	var iframe=getID('phpinfo'),s,txt,content;
	iframe.onload=function(){
		content=iframe.contentWindow?iframe.contentWindow.document:iframe.contentDocument;
		if(typeof content.getElementsByClassName=='function')
			s=content.getElementsByClassName('v');
		else{
			t=content.getElementsByTagName('td');
			s=Array();
			for(var i in t){
				if(t[i].className=='v')
					s.push(t[i]);
			}
		}
		for(var i in s){
			txt=s[i][TC];
			if(typeof txt=='undefined')
				continue;
			if(txt.indexOf(' ')==-1){
				if(txt.indexOf(',')>-1)
					s[i][TC]=txt.replace(/,/g,', ');
			}
		}
	};
})();
