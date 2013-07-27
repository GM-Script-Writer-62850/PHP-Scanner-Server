(function(){
	"use strict";
	document.write('<select name="scale">');
	for(var i=0;i<=200;i++){
		document.write('<option value="'+i+'"'+(i==100?' selected="selected"':'')+'>'+(i-100)+' %</option>');
	}
	document.write('</select>');
})();
