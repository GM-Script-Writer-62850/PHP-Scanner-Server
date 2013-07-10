document.write('<select name="contrast" onchange="changeBrightContrast()">');
for(var i=-100;i<=100;i++){
	document.write('<option '+(i==0?'selected="selected" ':'')+'value="'+i+'">'+i+' %</option>');
}
document.write('</select>');
