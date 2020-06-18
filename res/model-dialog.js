/* mod of http://www.pat-burt.com/csspopup.js */
"use strict";
function toggle(div_id){
	var el = getID(div_id);
	if(el.style.display == 'none'){
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
	var viewportheight,viewportheight,blanket_height,blanket,popUpDiv,popUpDiv_height;
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
	blanket = getID(popUpDivVar);
	blanket.style.height = blanket_height + 'px';
	popUpDiv = blanket.childNodes[0];
	popUpDiv_height=viewportheight/2-popUpDiv.offsetHeight/2;
	popUpDiv.style.top = (popUpDiv_height<0?0:popUpDiv_height) + 'px';
}
function window_pos(popUpDivVar,width){
	var viewportwidth,window_width,popUpDiv;
	if(typeof window.innerWidth != 'undefined'){
		viewportwidth = window.innerHeight;
	}
	else{
		viewportwidth = document.documentElement.clientHeight;
	}
	if((viewportwidth > document.body.parentNode.scrollWidth) && (viewportwidth > document.body.parentNode.clientWidth)){
		window_width = viewportwidth;
	}
	else{
		if(document.body.parentNode.clientWidth > document.body.parentNode.scrollWidth){
			window_width = document.body.parentNode.clientWidth;
		}
		else{
			window_width = document.body.parentNode.scrollWidth;
		}
	}
	popUpDiv = getID(popUpDivVar).childNodes[0];
	window_width=window_width/2-width/2;
	popUpDiv.style.left = window_width + 'px';
	popUpDiv.style.width=width+'px';
}
function popup(windowname,width){
	toggle(windowname);
	window_pos(windowname,width);
	blanket_size(windowname);
}
