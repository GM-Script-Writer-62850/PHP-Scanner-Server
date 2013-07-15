<!DOCTYPE html><html><?php $path=is_numeric($GLOBALS['PAGE'])?'/':''; ?>
<head>
<meta charset="UTF-8"/>
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="chrome=1"><![endif]-->
<title><?php echo html($GLOBALS['NAME'].' ~ '.$GLOBALS['PAGE'].' - '.$page); ?></title>
<link id="style" rel="stylesheet" href="<?php echo $path; ?>inc/style.php<?php
if(isset($_COOKIE["colors"])){
	echo "?colors=".url($_COOKIE["colors"]);
}
?>" type="text/css"/>
<?php
if($GLOBALS['PAGE']=='Config')
	echo '<link id="style_new" rel="stylesheet" href="inc/style.php'.(isset($_COOKIE["colors"])?'?colors='.$_COOKIE["colors"]:'').'" type="text/css"/>';
?>
<link rel="shortcut icon" href="<?php echo $path; ?>inc/images/favicon.png"/>
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>jquery.imgareaselect-0.9.10/css/imgareaselect-animated.css"/>
<script type="text/javascript" src="<?php echo $path; ?>jquery.imgareaselect-0.9.10/scripts/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>jquery.imgareaselect-0.9.10/scripts/jquery.imgareaselect.pack.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>inc/model-dialog.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>inc/cookie.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>inc/main.js"></script>
<!--[if lt IE 9]><script type="text/javascript">TC='innerText';var ie8=false;</script><link rel="stylesheet" type="text/css" href="<?php echo $path; ?>inc/ie.css"/><![endif]-->
</head>
<body>
<div id="blanket" style="display:none;background-color:transparent;"><div id="popUpDiv" style="opacity:0;"></div></div>
<div id="container">

<div id="header">

<div class="tab<?php echo in_array($GLOBALS['PAGE'],Array("Config","About","Paper Manager","Access Enabler","Device Notes","Parallel-Form","PHP Information"))?' active':''; ?>">
<a href="<?php echo $path; ?>index.php?page=Config">Configure</a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright bottom"></div>
</div>

<div class="tab<?php echo in_array($GLOBALS['PAGE'],Array("Scans","View","Edit"))?' active':''; ?>">
<a href="<?php echo $path; ?>index.php?page=Scans">Scanned Files</a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright bottom"></div>
</div>

<div class="tab<?php echo $GLOBALS['PAGE']=="Scan"?' active':''; ?>">
<a href="<?php echo $path; ?>index.php?page=Scan">Use Scanner</a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright right bottom"></div>
</div>

<div class="tab<?php echo $GLOBALS['PAGE']=="Login"||is_numeric($GLOBALS['PAGE'])?' active':''; ?>">
<a href="/"><?php echo $_SERVER['SERVER_NAME']; ?></a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright bottom"></div>
</div>

</div>

<div id="new_mes"> </div>

<noscript id="nojs">
<div style="height:auto;" class="message">
<h2>JavaScript Disabled</h2>
<p class="center">This application requires JavaScript to function. Please enable JavaScript, then reload this page.<?php echo $GLOBALS['PAGE']=='Login'?'<br/><b>LOGIN REQUIRES JAVASCRIPT</b>':''; ?></p>
</div>
</noscript>
<!--[if IE 8]><script type="text/javascript">ie8=true</script><![endif]-->
<!--[if lt IE 9]><div style="height:auto;" class="message ie center"><h2><noscript>Unsupported Browser</noscript><script type="text/javascript">document.write(ie8?'Notice: It is Recommended That you Upgrade Your Browser':'Error: Legacy Browsers are NOT Supported');</script></h2>
<p><noscript>This browser is unusable!</noscript><script type="text/javascript">document.write(ie8?'While you browser will technically works, everything is displays looks horrible.':'You can view the list of supported browsers in the <a href="index.php?page=About">release notes</a>.');</script><br/>
Please install <a href="http://www.mozilla.com/firefox/">Mozilla Firefox</a> (Recommended) alternatively, you may use<br/>
<a href="http://lmgtfy.com/?q=Internet+Explorer+10+Download">Internet Explorer 10</a> (Windows 7 and 8 only) or
 <a href="http://code.google.com/chrome/chromeframe/">Google Chrome Frame</a> <sup><i>EOL January 2014</i></sup> for Internet Explorer.<br/>
<a href="http://lmgtfy.com/?q=Internet+Explorer+9+Download">Internet Explorer 9</a> will function, but you don't get all the fancy eye candy.
</p></div><![endif]-->
