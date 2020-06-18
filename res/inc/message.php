<script type="text/javascript">
(function(){
	var tit='<?php echo $TITLE; ?>';
	var msg='<?php echo $MESSAGE; ?>';
	var aln='<?php echo $ALIGN; ?>';
	try{
		printMsg(tit,msg,aln,-1);
	}
	catch(e){
		window.addEventListener('load',function(){printMsg(tit,msg,aln,-1)},false);
	}
})();
</script>
