(function(){
	var iframe=getID('phpinfo'),s,txt,content,ct;
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
			if(s[i].parentNode.parentNode.offsetWidth>600){// Oversized
				if(txt.indexOf(' ')==-1||txt.indexOf('theme=')>-1){// No spaces or cookie
					if(txt.indexOf(',')>-1){//Has Commas
						s[i][TC]=txt.replace(/,/g,', ');
					}
					else{
						ct=s[i].parentNode.childNodes.length;
						s[i].setAttribute('style','max-width:'+((600-s[i].parentNode.childNodes[0].offsetWidth-ct*5)/(ct-1))+'px;word-wrap:break-word;word-break:break-all;');
					}
				}
			}
		}
	};
})();
