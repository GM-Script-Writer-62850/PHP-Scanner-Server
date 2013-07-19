<script type="text/javascript">
(function(){
	var tit='<?php echo $TITLE; ?>';
	var msg='<?php echo $MESSAGE; ?>';
	var aln='<?php echo $ALIGN; ?>';
	try{
		printMsg(tit,msg,aln,-1);
	}
	catch(e){
		try{
			window.addEventListener('load',function(){printMsg(tit,msg,aln,-1)},false);
		}
		catch(e){// for IE
			alert("If you don't like this type of alert use a different browser\n"+tit+"\n"+msg);
		}
	}
})();
</script>
