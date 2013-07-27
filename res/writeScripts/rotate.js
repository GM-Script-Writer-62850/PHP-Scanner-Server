(function(){
	"use strict";
	document.write('<select name="rotate" onchange="rotateChange(this)">'+
		'<option value="0">0&deg;</option>'+
		'<option value="90">90&deg; Clockwise</option>'+
		'<option value="-90">90&deg; Counterclockwise</option>'+
		'<option value="180">180&deg;</option>'+
		'<optgroup label="Clockwise">');
	for(var i=1;i<180;i++){
		if(i!=90)
			document.write('<option value="'+i+'">'+i+'&deg;</option>');
	}
	document.write('</optgroup><optgroup label="Counterclockwise">');
	for(i=-1;i>-180;i--){
		if(i!=-90)
			document.write('<option value="'+i+'">'+(-1*i)+'&deg;</option>');
	}
	document.write('</optgroup></select><span class="tip">Clockwise</span>');
})();
