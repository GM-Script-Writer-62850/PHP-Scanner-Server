<?php phpinfo(); ?>
<script type="text/javascript">var TC='textContent',s,t,txt;</script>
<!--[if lt IE 9]><script type="text/javascript">TC='innerText';</script>-->
<script type="text/javascript">// Cleanup
if(typeof document.getElementsByClassName=='function')
	s=document.getElementsByClassName('v');
else{
	t=document.getElementsByTagName('td');
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
</script>
