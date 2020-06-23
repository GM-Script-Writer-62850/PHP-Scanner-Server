<?php
$path='';
if(is_numeric($page)||$page=='Index Of'){
	$path=explode('/',$_SERVER['PHP_SELF']);
	$path=end($path);
	$path=substr($_SERVER['PHP_SELF'],0,strlen($path)*-1-4);
}
$Theme=isset($_COOKIE["theme"])?url($_COOKIE["theme"]):url($GLOBALS['Theme']);
$GLOBALS['CurrentTheme']=$Theme;
$Printer=$GLOBALS['Printer'];
?>
<!DOCTYPE html><html>
<head>
<meta charset="UTF-8"/>
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
echo '<script type="text/javascript">var I='.$GLOBALS['RulerIncrement'].',ReplacePrinter='.($GLOBALS['ReplacePrinter']&&$GLOBALS['Printer'] % 2 !=0?'true':'false').';</script>';
?>
<script type="text/javascript" src="<?php echo $path; ?>res/model-dialog.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>res/cookie.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>res/main.js"></script>
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
</noscript><!-- I am surprised this still works -->
<!--[if IE]><div style="height:auto;" class="message ie center"><h2>Unsupported Browser</h2>
<p>Please install <a href="http://www.mozilla.com/firefox/">Mozilla Firefox</a><sup>(Recommended)</sup>,
	<a href="https://www.google.com/chrome/">Google Chrome</a>, or
	<a href="https://www.microsoft.com/en-us/edge">Microsoft Edge</a><sup>(Windows 10)</sup>.<br/>
	Alternatively, you may use <a href="http://lmgtfy.com/?q=Internet+Explorer+11+Download">Internet Explorer 11</a><sup>(Windows 7+)</sup>.
</p></div><![endif]-->
