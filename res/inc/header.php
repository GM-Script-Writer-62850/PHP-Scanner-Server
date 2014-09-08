<?php
$path=is_numeric($page)||$page=='Index Of'?substr($_SERVER['PHP_SELF'],0,strlen(end(explode('/',$_SERVER['PHP_SELF'])))*-1-4):'';
$Theme=isset($_COOKIE["theme"])?url($_COOKIE["theme"]):$GLOBALS['Theme'];
$GLOBALS['CurrentTheme']=$Theme;
$Printer=$GLOBALS['Printer'];
?>
<!DOCTYPE html><html>
<head>
<meta charset="UTF-8"/>
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="chrome=1"><![endif]-->
<title><?php echo html($GLOBALS['NAME'].' ~ '.$page.' - '.$title); ?></title>
<link rel="shortcut icon" href="<?php echo $path; ?>res/images/favicon.png"/>
<link rel="stylesheet" href="<?php echo $path; ?>res/style.php?theme=<?php echo $Theme; ?>" type="text/css"/>
<script type="text/javascript" src="<?php echo $path; ?>jquery/jquery.min.js"></script>
<?php
if(in_array($page,Array("Scan","Edit")))
	echo '<link rel="stylesheet" type="text/css" href="'.$path.'jquery/imgareaselect-0.9.10/css/imgareaselect-animated.css"/>'."\n".
		'<script type="text/javascript" src="'.$path.'jquery/imgareaselect-0.9.10/scripts/jquery.imgareaselect.min.js"></script>';
else if($page=='Config')
	echo '<style id="style_old" type="text/css"></style><style id="style_new" type="text/css"></style>'."\n".
		'<link rel="stylesheet" media="screen" type="text/css" href="'.$path.'jquery/colorpicker-custom/css/colorpicker.css"/>'."\n".
			'<script type="text/javascript" src="'.$path.'jquery/colorpicker-custom/js/colorpicker.js"></script>';
else if($page=='Index Of')
	echo '<link rel="stylesheet" type="text/css" href="'.$path.'res/indexOf.css"/>';
echo '<script type="text/javascript">var I='.$GLOBALS['RulerIncrement'].';</script>';
?>
<script type="text/javascript" src="<?php echo $path; ?>res/model-dialog.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>res/cookie.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>res/main.js"></script>
<!--[if lt IE 9]><script type="text/javascript">TC='innerText';var ie8=false;</script><link rel="stylesheet" type="text/css" href="<?php echo $path; ?>res/ie.css"/><![endif]-->
</head>
<body<?php echo $DarkPicker?' class="darkPicker"':''; ?>>
<div id="blanket" style="display:none;background-color:transparent;"><div id="popUpDiv" style="opacity:0;"></div></div>

<div id="container">

<div id="header">
<span><span>Scanner<br/><span>Server</span></span></span>
<div class="tab<?php echo in_array($page,Array("Config","About","Paper Manager","Access Enabler","Device Notes","Parallel-Form","PHP Information"))?' active':''; ?>">
<a href="<?php echo $path; ?>index.php?page=Config">Configure</a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright bottom"></div>
</div>

<div <?php echo $Printer==0||$Printer==1?'style="display:none;" ':'' ?>class="tab<?php echo $page=="Printer"?' active':''; ?>">
<a href="<?php echo $path; ?>index.php?page=Printer">Printer</a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright right bottom"></div>
</div>

<div class="tab<?php echo in_array($page,Array("Scans","View","Edit"))?' active':''; ?>">
<a href="<?php echo $path; ?>index.php?page=Scans">Scanned Files</a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright bottom"></div>
</div>

<div class="tab<?php echo $page=="Scan"?' active':''; ?>">
<a href="<?php echo $path; ?>index.php?page=Scan">Scanner</a>
<div class="topleft top"></div>
<div class="bottomleft bottom"></div>
<div class="topright top"></div>
<div class="bottomright right bottom"></div>
</div>

<div class="tab<?php echo in_array($page,Array("Index Of","Login"))||is_numeric($page)?' active':''; ?>">
<a title="<?php echo html($_SERVER['SERVER_NAME']); ?>" href="/"<?php echo $GLOBALS['RequireLogin']&&$GLOBALS['Auth']&&$page!='Login'?' onclick="Delete_Cookie(\'Authenticated\',false)">Logout':'>'.$_SERVER['SERVER_NAME']; ?></a>
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
<p class="center">This application requires JavaScript to function. Please enable JavaScript, then reload this page.<?php echo $page=='Login'?'<br/><b>LOGIN REQUIRES JAVASCRIPT</b>':''; ?></p>
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
